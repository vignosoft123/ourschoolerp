<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schoolwisestrengthreport extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('classes_m');
		$this->load->model('section_m');
		$this->load->model('studentrelation_m');
		$this->load->model('schoolyear_m');
		$language = $this->session->userdata('lang');
		$this->lang->load('schoolwisestrengthreport', $language);
	}

	public function index() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css',
			),
			'js' => array(
				'assets/highcharts/highcharts.js',
				'assets/highcharts/highcharts-more.js',
				'assets/highcharts/exporting.js',
				'assets/select2/select2.js',
			)
		);
		$this->data['classes'] = $this->classes_m->general_get_classes();
		$this->data["subview"] = "report/schoolwisestrength/SchoolwisestrengthreportView";
		$this->load->view('_layout_main', $this->data);
	}

	public function getSection() {
		$classesID = $this->input->post('classesID');
		echo "<option value='0'>".$this->lang->line("schoolwisestrengthreport_all_sections")."</option>";
		if ((int)$classesID > 0) {
			$sections = $this->section_m->general_get_order_by_section(array('classesID' => $classesID));
			if (customCompute($sections)) {
				foreach ($sections as $section) {
					echo "<option value='".$section->sectionID."'>".$section->section."</option>";
				}
			}
		}
	}

	public function getReport() {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';

		$classesID    = (int) $this->input->post('classesID');
		$sectionID    = (int) $this->input->post('sectionID');
		$schoolyearID = $this->session->userdata('defaultschoolyearID');

		$this->data['classesID']  = $classesID;
		$this->data['sectionID']  = $sectionID;
		$this->data['classes']    = pluck($this->classes_m->general_get_classes(), 'classes', 'classesID');
		$this->data['sections']   = pluck($this->section_m->general_get_section(), 'section', 'sectionID');
		$this->data['schoolyear'] = $this->schoolyear_m->get_single_schoolyear(array('schoolyearID' => $schoolyearID));

		$this->data = array_merge($this->data, $this->buildStrength($schoolyearID, $classesID, $sectionID));

		$retArray['render'] = $this->load->view('report/schoolwisestrength/SchoolwisestrengthreportReport', $this->data, true);
		$retArray['status'] = TRUE;

		echo json_encode($retArray);
		exit;
	}

	/**
	 * Aggregates class/section/gender strength and caste strength for the given filter scope.
	 * Shared by getReport() (AJAX preview) and export_excel() so both stay in sync.
	 */
	private function buildStrength($schoolyearID, $classesID, $sectionID) {
		$rows = $this->studentrelation_m->get_strength_by_class_section($schoolyearID, $classesID, $sectionID);

		$classAgg     = [];
		$sectionNames = [];
		$totalBoys    = 0;
		$totalGirls   = 0;

		if (customCompute($rows)) {
			foreach ($rows as $row) {
				$cid = $row->srclassesID;
				if (!isset($classAgg[$cid])) {
					$classAgg[$cid] = array(
						'classesID'       => $cid,
						'classes'         => $row->classes,
						'classes_numeric' => (int)$row->classes_numeric,
						'sections'        => array(),
						'sectionIDs'      => array(),
						'totalM'          => 0,
						'totalF'          => 0,
					);
				}

				$secName = $row->section;
				if (!isset($classAgg[$cid]['sections'][$secName])) {
					$classAgg[$cid]['sections'][$secName] = array('M' => 0, 'F' => 0);
				}
				$classAgg[$cid]['sectionIDs'][$row->srsectionID] = TRUE;

				$sexKey = (strtolower($row->sex) === 'female') ? 'F' : 'M';
				$classAgg[$cid]['sections'][$secName][$sexKey] += (int)$row->cnt;
				$classAgg[$cid]['total'.$sexKey] += (int)$row->cnt;
				$sectionNames[$secName] = TRUE;

				if ($sexKey === 'F') { $totalGirls += (int)$row->cnt; } else { $totalBoys += (int)$row->cnt; }
			}
		}

		uasort($classAgg, function($a, $b) { return $a['classes_numeric'] <=> $b['classes_numeric']; });

		$sectionNameList = array_keys($sectionNames);
		sort($sectionNameList, SORT_NATURAL | SORT_FLAG_CASE);

		$totalSections = 0;
		foreach ($classAgg as $c) {
			$totalSections += count($c['sectionIDs']);
		}

		$casteRows = $this->studentrelation_m->get_strength_by_caste($schoolyearID, $classesID, $sectionID);
		$casteAgg  = [];
		if (customCompute($casteRows)) {
			foreach ($casteRows as $row) {
				$label = (isset($row->caste) && trim((string)$row->caste) !== '') ? trim($row->caste) : $this->lang->line('schoolwisestrengthreport_not_specified');
				if (!isset($casteAgg[$label])) { $casteAgg[$label] = array('M' => 0, 'F' => 0); }
				$sexKey = (strtolower($row->sex) === 'female') ? 'F' : 'M';
				$casteAgg[$label][$sexKey] += (int)$row->cnt;
			}
		}
		uasort($casteAgg, function($a, $b) { return ($b['M'] + $b['F']) <=> ($a['M'] + $a['F']); });

		return array(
			'classAgg'        => $classAgg,
			'sectionNameList' => $sectionNameList,
			'casteAgg'        => $casteAgg,
			'totalStudents'   => $totalBoys + $totalGirls,
			'totalBoys'       => $totalBoys,
			'totalGirls'      => $totalGirls,
			'totalClasses'    => count($classAgg),
			'totalSections'   => $totalSections,
		);
	}

	public function pdf() {
		$classesID    = (int) $this->uri->segment(3);
		$sectionID    = (int) $this->uri->segment(4);
		$schoolyearID = $this->session->userdata('defaultschoolyearID');

		$this->data['classesID']    = $classesID;
		$this->data['sectionID']    = $sectionID;
		$this->data['schoolyear']   = $this->schoolyear_m->get_single_schoolyear(array('schoolyearID' => $schoolyearID));
		$this->data['panel_title']  = $this->lang->line('panel_title');

		$this->data = array_merge($this->data, $this->buildStrength($schoolyearID, $classesID, $sectionID));

		$html = $this->load->view('report/schoolwisestrength/SchoolwisestrengthReportPDF', $this->data, true);

		// NOTE: not using Admin_Controller::reportPDF() here — it loads the PDF stylesheet via
		// file_get_contents(base_url(...)), a self-HTTP(S) fetch of this app's own asset URL that
		// was silently returning empty in this environment (no error, CSS just never reached mPDF).
		// Reading the same file straight off disk sidesteps that self-fetch entirely.
		// See the mPDF CSS Gotcha in srinivas_project_structure.md (Section 4).
		$stylesheet = file_get_contents(FCPATH.'assets/pdf/LTR/schoolwisestrengthreport.css');

		$this->load->library('mhtml2pdf');
		$this->mhtml2pdf->folder('uploads/report/');
		$this->mhtml2pdf->filename('Report');
		$this->mhtml2pdf->paper('a4', 'portrait');
		$this->mhtml2pdf->html($html);
		$this->mhtml2pdf->create('view', $this->data['panel_title'], $stylesheet);
	}

	public function export_excel() {
		$classesID    = (int) $this->input->get('classesID');
		$sectionID    = (int) $this->input->get('sectionID');
		$schoolyearID = $this->session->userdata('defaultschoolyearID');

		$strength = $this->buildStrength($schoolyearID, $classesID, $sectionID);
		$classAgg        = $strength['classAgg'];
		$sectionNameList = $strength['sectionNameList'];
		$casteAgg        = $strength['casteAgg'];

		$this->load->library('phpspreadsheet');
		$sheet = $this->phpspreadsheet->spreadsheet->getActiveSheet();
		$sheet->setTitle('Class Section Strength');

		$colIdx = 1;
		$sheet->setCellValueByColumnAndRow($colIdx++, 1, 'Class');
		foreach ($sectionNameList as $secName) {
			$sheet->setCellValueByColumnAndRow($colIdx++, 1, $secName.' (Boys)');
			$sheet->setCellValueByColumnAndRow($colIdx++, 1, $secName.' (Girls)');
		}
		$sheet->setCellValueByColumnAndRow($colIdx++, 1, 'Total Boys');
		$sheet->setCellValueByColumnAndRow($colIdx++, 1, 'Total Girls');
		$sheet->setCellValueByColumnAndRow($colIdx++, 1, 'Total Strength');
		$lastColIdx = $colIdx - 1;

		$row = 2;
		$grandBoys = 0; $grandGirls = 0; $grandBySection = [];
		foreach ($classAgg as $c) {
			$colIdx = 1;
			$sheet->setCellValueByColumnAndRow($colIdx++, $row, $c['classes']);
			foreach ($sectionNameList as $secName) {
				$b = $c['sections'][$secName]['M'] ?? 0;
				$g = $c['sections'][$secName]['F'] ?? 0;
				$sheet->setCellValueByColumnAndRow($colIdx++, $row, $b ?: '-');
				$sheet->setCellValueByColumnAndRow($colIdx++, $row, $g ?: '-');
				$grandBySection[$secName]['M'] = ($grandBySection[$secName]['M'] ?? 0) + $b;
				$grandBySection[$secName]['F'] = ($grandBySection[$secName]['F'] ?? 0) + $g;
			}
			$sheet->setCellValueByColumnAndRow($colIdx++, $row, $c['totalM']);
			$sheet->setCellValueByColumnAndRow($colIdx++, $row, $c['totalF']);
			$sheet->setCellValueByColumnAndRow($colIdx++, $row, $c['totalM'] + $c['totalF']);
			$grandBoys  += $c['totalM'];
			$grandGirls += $c['totalF'];
			$row++;
		}

		$colIdx = 1;
		$sheet->setCellValueByColumnAndRow($colIdx++, $row, 'Grand Total');
		foreach ($sectionNameList as $secName) {
			$sheet->setCellValueByColumnAndRow($colIdx++, $row, $grandBySection[$secName]['M'] ?? 0);
			$sheet->setCellValueByColumnAndRow($colIdx++, $row, $grandBySection[$secName]['F'] ?? 0);
		}
		$sheet->setCellValueByColumnAndRow($colIdx++, $row, $grandBoys);
		$sheet->setCellValueByColumnAndRow($colIdx++, $row, $grandGirls);
		$sheet->setCellValueByColumnAndRow($colIdx++, $row, $grandBoys + $grandGirls);

		$lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIdx);
		$sheet->getStyle('A1:'.$lastColLetter.'1')->applyFromArray(array(
			'font' => array('bold' => true, 'color' => array('argb' => 'FFFFFF')),
			'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => '366092')),
		));
		foreach (range(1, $lastColIdx) as $c) {
			$sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
		}

		$casteStartRow = $row + 3;
		$sheet->setCellValueByColumnAndRow(1, $casteStartRow, 'Caste Category');
		$sheet->setCellValueByColumnAndRow(2, $casteStartRow, 'Boys');
		$sheet->setCellValueByColumnAndRow(3, $casteStartRow, 'Girls');
		$sheet->setCellValueByColumnAndRow(4, $casteStartRow, 'Total');
		$sheet->getStyle('A'.$casteStartRow.':D'.$casteStartRow)->applyFromArray(array(
			'font' => array('bold' => true, 'color' => array('argb' => 'FFFFFF')),
			'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => '366092')),
		));
		$cr = $casteStartRow + 1;
		foreach ($casteAgg as $label => $counts) {
			$sheet->setCellValueByColumnAndRow(1, $cr, $label);
			$sheet->setCellValueByColumnAndRow(2, $cr, $counts['M']);
			$sheet->setCellValueByColumnAndRow(3, $cr, $counts['F']);
			$sheet->setCellValueByColumnAndRow(4, $cr, $counts['M'] + $counts['F']);
			$cr++;
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="SchoolWiseStrengthReport.xlsx"');
		header('Cache-Control: max-age=0');
		$this->phpspreadsheet->output($this->phpspreadsheet->spreadsheet);
	}
}
