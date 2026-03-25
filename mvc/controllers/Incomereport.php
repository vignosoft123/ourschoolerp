<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Incomereport extends Admin_Controller {

	public function __construct() {
		parent::__construct();

        $this->load->model("income_m");
        $this->load->model("incomecategories_m");
        $language = $this->session->userdata('lang');
		$this->lang->load('incomereport', $language);
	}

	public function rules() {
		$rules = array(
            array(
	                'field' => 'fromdate',
	                'label' => $this->lang->line('incomereport_fromdate'),
	                'rules' => 'trim|xss_clean|callback_date_valid|callback_unique_date'
	        ),
	        array(
	                'field' => 'todate',
	                'label' => $this->lang->line('incomereport_todate'),
	                'rules' => 'trim|xss_clean|callback_date_valid'
	        ),
	        array(
	                'field' => 'incomecategoriesID',
	                'label' => 'Income Category',
	                'rules' => 'trim|xss_clean|numeric'
	        )
		);
		return $rules;
	}

	public function send_pdf_to_mail_rules() {
		$rules = array(
		    array(
	                'field' => 'fromdate',
	                'label' => $this->lang->line('incomereport_fromdate'),
                    'rules' => 'trim|xss_clean'
	        ),
	        array(
	                'field' => 'todate',
	                'label' => $this->lang->line('incomereport_todate'),
                    'rules' => 'trim|xss_clean'
	        ),
	        array(
	                'field' => 'to',
	                'label' => $this->lang->line('incomereport_to'),
	                'rules' => 'trim|required|xss_clean|valid_email'
	        ),
	        array(
	                'field' => 'subject',
	                'label' => $this->lang->line('incomereport_subject'),
	                'rules' => 'trim|required|xss_clean'
	        ),
	        array(
	                'field' => 'message',
	                'label' => $this->lang->line('incomereport_message'),
	                'rules' => 'trim|xss_clean'
	        ),
	        
		);
		return $rules;
	}

	public function index() {
		$this->data['headerassets'] = array(
            'css' => array(
                'assets/datepicker/datepicker.css',
                'assets/select2/css/select2.css',
                'assets/select2/css/select2-bootstrap.css'
            ),
            'js' => array(
                'assets/datepicker/datepicker.js',
                'assets/select2/select2.js'
            )
		);
		$this->data['income_categories'] = $this->incomecategories_m->get_order_by_incomecategories(array('status' => 0));
        $this->data["subview"] = "report/income/IncomeReportView";
		$this->load->view('_layout_main', $this->data);
	}

	public function getIncomereport() {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';
		// if(permissionChecker('incomereport')) {
			if($_POST) {
				$rules = $this->rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
				    $retArray['status'] = FALSE;
				    echo json_encode($retArray);
				    exit;
				} else {
                    $this->data['fromdate'] = $this->input->post('fromdate');
					$this->data['todate'] = $this->input->post('todate');
					$this->data['incomecategoriesID'] = $this->input->post('incomecategoriesID');

					$incomesArray = $this->income_m->get_all_incomes_for_report($this->input->post());
                    $this->data['incomes'] = $incomesArray;
					$retArray['render'] = $this->load->view('report/income/IncomeReport',$this->data,true);
					$retArray['status'] = TRUE;
					echo json_encode($retArray);
					exit;
				}
			} else {
				$retArray['status'] = TRUE;
				echo json_encode($retArray);
				exit;
			}
		// } else {
			$retArray['render'] =  $this->load->view('report/reporterror', $this->data, true);
			$retArray['status'] = TRUE;
			echo json_encode($retArray);
			exit;
		// }
	}

	public function pdf() {
		// if(permissionChecker('incomereport')) {
			$fromdate = htmlentities(escapeString($this->uri->segment(3)));
			$todate   = htmlentities(escapeString($this->uri->segment(4)));
			$incomecategoriesID = htmlentities(escapeString($this->uri->segment(5)));
			
			$this->data['fromdate'] = ($fromdate != '') ? date('d-m-Y', $fromdate) : '';
			$this->data['todate']   = ($todate != '') ? date('d-m-Y', $todate) : '';
			$this->data['incomecategoriesID'] = ($incomecategoriesID != '') ? $incomecategoriesID : 0;

			$postArray = [];
			if($fromdate !='' && $todate != '') {
				$postArray['fromdate'] = date('d-m-Y',$fromdate);
				$postArray['todate']   = date('d-m-Y',$todate);
				$postArray['incomecategoriesID'] = $this->data['incomecategoriesID'];
			}

			$this->data['incomes'] = $this->income_m->get_all_incomes_for_report($postArray);
			$this->reportPDF('productpurchasereport.css', $this->data, 'report/income/IncomeReportPDF');
		// } else {
		// 	$this->data["subview"] = "error";
		// 	$this->load->view('_layout_main', $this->data);
		// }
	}

	public function send_pdf_to_mail() {
		$retArray['status'] = FALSE;
		// if(permissionChecker('incomereport')) {
			if($_POST) {
				$rules = $this->send_pdf_to_mail_rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
					echo json_encode($retArray);
					exit;
				} else {
					$to = $this->input->post('to');
					$subject = $this->input->post('subject');
					$message = $this->input->post('message');

					$this->data['fromdate'] = strtotime($this->input->post('fromdate'));
					$this->data['todate']   = strtotime($this->input->post('todate'));
					$this->data['incomecategoriesID'] = $this->input->post('incomecategoriesID');

					$this->data['incomes'] = $this->income_m->get_all_incomes_for_report($this->input->post());

					$this->reportSendToMail('productpurchasereport.css', $this->data, 'report/income/IncomeReportPDF', $to, $subject, $message);
					$retArray['status'] = TRUE;
					echo json_encode($retArray);
				}
			} else {
				$retArray['message'] = $this->lang->line('incomereport_permissionmethod');
				echo json_encode($retArray);
				exit;
			}
		// } else {
		// 	$retArray['message'] = $this->lang->line('incomereport_permission');
		// 	echo json_encode($retArray);
		// 	exit;
		// }
	}

	public function xlsx() {
		$this->load->library('phpspreadsheet');
		$sheet = $this->phpspreadsheet->spreadsheet->getActiveSheet();
		$sheet->getDefaultColumnDimension()->setWidth(25);
		$sheet->getDefaultRowDimension()->setRowHeight(25);
		$sheet->getColumnDimension('C')->setWidth(35);
		$sheet->getRowDimension('1')->setRowHeight(25);
		$sheet->getRowDimension('2')->setRowHeight(25);

		$data = $this->xmlData();

		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="incomereport.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$this->phpspreadsheet->output($this->phpspreadsheet->spreadsheet);
	} 

	private function xmlData() {
		$fromdate = htmlentities(escapeString($this->uri->segment(3)));
		$todate   = htmlentities(escapeString($this->uri->segment(4)));
		$incomecategoriesID = htmlentities(escapeString($this->uri->segment(5)));
		
		$this->data['fromdate'] = ($fromdate != '') ? $fromdate : '';
		$this->data['todate']   = ($todate != '') ? $todate : '';
		$this->data['incomecategoriesID'] = ($incomecategoriesID != '') ? $incomecategoriesID : 0;

		$postArray = [];
		if($fromdate !='' && $todate != '') {
			$postArray['fromdate'] = date('d-m-Y',$fromdate);
			$postArray['todate']   = date('d-m-Y',$todate);
			$postArray['incomecategoriesID'] = $this->data['incomecategoriesID'];
		}

		$this->data['incomes'] = $this->income_m->get_all_incomes_for_report($postArray);
		$this->data['income_categories'] = $this->incomecategories_m->get_incomecategories();
		
		return $this->generateXML($this->data);
	}

    private function generateXML($arrays) {
        extract($arrays);
        if(customCompute($incomes)) {
            $sheet = $this->phpspreadsheet->spreadsheet->getActiveSheet();

            if($fromdate != '' && $todate != '' ) { 
                $fdate = "From Date : ";
                $fdate .= date('d M Y',$fromdate);

                $tdate = "To Date : ";
                $tdate .= date('d M Y',$todate);

                $sheet->setCellValue('A1',$fdate);
                $sheet->setCellValue('F1',$tdate);
            } elseif($incomecategoriesID != 0) {
                $category = "Category : ";
                foreach($income_categories as $income_category) {
                    if($income_category->incomecategoriesID == $incomecategoriesID) {
                        $category .= $income_category->name;
                    }
                }
                $sheet->setCellValue('A1',$category);
            } else {
                $sheet->getRowDimension('1')->setVisible(false);
            }

            $headers = array();
            $headers['slno'] = $this->lang->line('incomereport_slno');
            $headers['name'] = $this->lang->line('incomereport_name');
            $headers['category'] = "Category";
            $headers['date'] = $this->lang->line('incomereport_date');
            $headers['user'] = $this->lang->line('incomereport_user');
            $headers['note'] = $this->lang->line('incomereport_note');
            $headers['amount'] = $this->lang->line('incomereport_amount');

            $i=0;
            $bodys = array();
            $total_amount = 0;
            foreach($incomes as $income) {
                $bodys[$i][] = $i+1;
                $bodys[$i][] = $income['name'];
                $bodys[$i][] = $income['category_name'];
                $bodys[$i][] = date('d M Y', strtotime($income['date']));
                $bodys[$i][] = $income['uname'];
                $bodys[$i][] = $income['note'];
                $bodys[$i][] = number_format($income['amount'],2);
                $total_amount += $income['amount'];
                $i++;
            }

            $bodys[$i][] = "Grand Total";
            $bodys[$i][] = "";
            $bodys[$i][] = "";
            $bodys[$i][] = "";
            $bodys[$i][] = "";
            $bodys[$i][] = "";
            $bodys[$i][] = number_format($total_amount,2);

            if(customCompute($headers)) {
                $row = 2;
                $column = "A";
                foreach($headers as $header) {
                    $sheet->setCellValue($column.$row, $header);
                    $column++;
                }
            }

            if(customCompute($bodys)) {
                $row = 3;
                foreach($bodys as $rows) {
                    $column = 'A';
                    foreach ($rows as $value) {
                        $sheet->setCellValue($column.$row, $value);
                        $column++;
                    }
                    $row++;
                }
            }

            $styleArray = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' =>[
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]
                ]
            ];
            $sheet->getStyle('A1:G2')->applyFromArray($styleArray);

            $styleArray = [
                'font' => [
                    'bold' => FALSE,
                ],
                'alignment' =>[
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]
                ]
            ];
            $styleColumn = $row-2;
            $sheet->getStyle('A3:G'.$styleColumn)->applyFromArray($styleArray);

            $styleArray = [
                'font' => [
                    'bold' => TRUE,
                ],
                'alignment' =>[
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]
                ]
            ];
            $styleColumn = $row-1;
            $sheet->getStyle('A'.$styleColumn.':G'.$styleColumn)->applyFromArray($styleArray);

            $startmerge = "A".$styleColumn;
            $endmerge = "F".$styleColumn;
            $sheet->mergeCells("$startmerge:$endmerge");
            $sheet->mergeCells("A1:F1");

        } else {
          redirect('incomereport');
        }
    }

    public function date_valid($date) {
        if($date) {
            if(strlen($date) < 10) {
                $this->form_validation->set_message("date_valid", "%s is not valid dd-mm-yyyy.");
                return FALSE;
            } else {
                $arr = explode("-", $date);
                $dd = $arr[0];
                $mm = $arr[1];
                $yyyy = $arr[2];
                if(checkdate($mm, $dd, $yyyy)) {
                    return TRUE;
                } else {
                    $this->form_validation->set_message("date_valid", "%s is not valid dd-mm-yyyy.");
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    public function unique_date() {
        $fromdate = $this->input->post('fromdate');
        $todate   = $this->input->post('todate');

        $startingdate = $this->data['schoolyearsessionobj']->startingdate;
        $endingdate = $this->data['schoolyearsessionobj']->endingdate;

        if($fromdate != '' && $todate == '') {
            $this->form_validation->set_message("unique_date", "The to date field not be empty .");
            return FALSE;
        }

        if($fromdate == '' && $todate != '') {
            $this->form_validation->set_message("unique_date", "The from date field not be empty .");
            return FALSE;
        }

        if($fromdate != '' && $todate != '') {
            if(strtotime($fromdate) > strtotime($todate)) {
                $this->form_validation->set_message("unique_date", "The from date can not be upper than todate .");
                return FALSE;
            }

            if((strtotime($fromdate) < strtotime($startingdate)) || (strtotime($fromdate) > strtotime($endingdate))) {
                $this->form_validation->set_message("unique_date", "The from date are invalid .");
                return FALSE;
            }

            if((strtotime($todate) < strtotime($startingdate)) || (strtotime($todate) > strtotime($endingdate))) {
                $this->form_validation->set_message("unique_date", "The to date are invalid .");
                return FALSE;
            }
            return TRUE;
        }

        return TRUE;
    }

}
