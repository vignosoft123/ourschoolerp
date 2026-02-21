<?php

class Balancefeesreport extends Admin_Controller{	
	public function __construct() {
		parent::__construct();
		$this->load->model('classes_m');
		$this->load->model('feetypes_m');
		$this->load->model('section_m');
		$this->load->model('student_m');
		$this->load->model('schoolyear_m');
		$this->load->model('invoice_m');
		$this->load->model('studentrelation_m');
		$this->load->model('weaverandfine_m');
		$this->load->model('parents_m');
		$this->load->model('payment_m');
		$this->load->model('village_m');

		$language = $this->session->userdata('lang');
		$this->lang->load('balancefeesreport', $language);
	}

	public function rules() {
		$rules = array(
			array(
				'field'=>'classesID',
				'label'=>$this->lang->line('balancefeesreport_class'),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field'=>'sectionID',
				'label'=>$this->lang->line('balancefeesreport_section'),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field'=>'studentID',
				'label'=>$this->lang->line('balancefeesreport_student'),
				'rules' => 'trim|xss_clean'
			)
		);
		return $rules;
	}

	public function send_pdf_to_mail_rules() {
		$rules = array(
			array(
				'field'=>'classesID',
				'label'=>$this->lang->line('balancefeesreport_class'),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field'=>'sectionID',
				'label'=>$this->lang->line('balancefeesreport_section'),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field'=>'studentID',
				'label'=>$this->lang->line('balancefeesreport_student'),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field'=>'to',
				'label'=>$this->lang->line('balancefeesreport_to'),
				'rules' => 'trim|required|xss_clean|valid_email'
			),
			array(
				'field'=>'subject',
				'label'=>$this->lang->line('balancefeesreport_subject'),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field'=>'message',
				'label'=>$this->lang->line('balancefeesreport_message'),
				'rules' => 'trim|xss_clean'
			)
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

		$this->data['date'] = date("d-m-Y");		
		$this->data['feetypes'] = $this->feetypes_m->get_feetypes();
		$this->data['classes'] = $this->classes_m->general_get_classes();
		$this->data['villeges'] = $this->village_m->get_active_villages();
		// print_r($this->data['villeges']);die;
		$this->data["subview"] = "report/balancefees/BalanceFeesReportView";
		$this->load->view('_layout_main', $this->data);
	}

	public function getSection() {
		$classesID = $this->input->post('classesID');
		if((int)$classesID) {
			$allSection = $this->section_m->general_get_order_by_section(array('classesID' => $classesID));
			echo "<option value='0'>", $this->lang->line("balancefeesreport_please_select"),"</option>";
			if(customCompute($allSection)) {
				foreach ($allSection as $value) {
					echo "<option value=\"$value->sectionID\">",$value->section,"</option>";
				}
			}
		}
	}

	public function getStudent() {
		$classesID = $this->input->post('classesID');
		$sectionID = $this->input->post('sectionID');
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		
		echo "<option value='0'>", $this->lang->line("balancefeesreport_please_select"),"</option>";
		if((int)$classesID && (int)$sectionID && (int)$schoolyearID) {
			$students = $this->studentrelation_m->get_order_by_studentrelation(array('srclassesID' => $classesID,'srsectionID' => $sectionID, 'srschoolyearID' => $schoolyearID));
			if(customCompute($students)) {
				foreach($students  as $student) {
					echo "<option value=\"$student->srstudentID\">",$student->srname,"</option>";
				}
			}
		}
	}

	public function getBalanceFeesReport() {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';

		if(permissionChecker('balancefeesreport')) {
			if($_POST) {
				$rules = $this->rules();
				$this->form_validation->set_rules($rules);
			    if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
				    echo json_encode($retArray);
				    exit;
				} else {

					$schoolyearID = $this->session->userdata('defaultschoolyearID');
					$_POST['schoolyearID'] = $schoolyearID;
					$villageID    = $this->input->post('villageID'); 
					$classesID    = $this->input->post('classesID'); 
					$sectionID    = $this->input->post('sectionID'); 
					$studentID    = $this->input->post('studentID'); 
					// $feetypeID    = $this->input->post('feetypeID'); 

					$this->data['classesID']    = $classesID;
					$this->data['villageID']    = $villageID;
					$this->data['sectionID']    = $sectionID;
					$this->data['studentID']    = $studentID;
					$this->data['schoolyearID'] = $schoolyearID; 
					// $this->data['feetypeID'] = $feetypeID; 

					$feetypeIDs = $this->input->post('feetypeID'); // this will now be an array

					$this->data['feetypeIDs'] = $feetypeIDs;

					$studentArray = [];
					if((int)$classesID) {
						$studentArray['srclassesID'] = $classesID;
					}
					if((int)$sectionID) {
						$studentArray['srsectionID'] = $sectionID;
					}
					if((int)$studentID) {
						$studentArray['srstudentID'] = $studentID;
					}

					if((int)$villageID) {
						$studentArray['villageID'] = $villageID;
					}

					$studentArray['srschoolyearID'] = $schoolyearID;

					$this->db->order_by('srclassesID','ASC');
					// $this->data['students'] = pluck($this->studentrelation_m->get_order_by_studentrelation($studentArray),'obj','srstudentID');

					// $this->data['students'] = pluck($this->studentrelation_m->get_studentrelation_join_student($studentArray),'obj','srstudentID');

					$allStudents = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');
					$perPage     = 25;
					$totalStudents = customCompute($allStudents);
					$this->data['totalStudents'] = $totalStudents;
					$this->data['perPage']       = $perPage;
					$this->data['startIndex']    = 0;
					// For initial load, only send first page of students
					$this->data['students'] = ($totalStudents > $perPage) ? array_slice($allStudents, 0, $perPage, true) : $allStudents;

					$this->data['classes'] = pluck($this->classes_m->general_get_classes(),'classes','classesID');
					$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');
					$this->data['feetypes'] = ($this->feetypes_m->general_get_fee_multi($feetypeIDs));
					// dd($this->data['feetypes']);
		
					$this->data['totalAmountAndDiscount'] = $this->totalAmountAndDiscustomCompute($this->invoice_m->get_all_balancefees_for_report_multi($this->input->post()));

					// echo "<pre>";print_r($this->data['totalAmountAndDiscount']);die;

					// $this->data['totalPayment'] = $this->totalPaymentAndWeaver($this->payment_m->get_order_by_payment(array('schoolyearID' => $schoolyearID)));

					
					$this->data['totalPayment'] = $this->totalPaymentAndWeaver($this->payment_m->get_order_by_payment_new_multi($schoolyearID,$feetypeIDs,$studentID));

					$this->data['totalPayment_split'] = $this->totalPaymentAndWeaver_split($this->payment_m->get_order_by_payment_new_multi($schoolyearID,$feetypeIDs,$studentID));

					// echo "<pre>=========";print_r($this->data['totalPayment_split']);die;

					$this->data['totalweavar'] = $this->totalWeaver($this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID'=>$schoolyearID)));

					// echo "<pre>=========";print_r($this->data['totalweavar']);die;

					$retArray['render'] = $this->load->view('report/balancefees/BalanceFeesReport', $this->data, true);
					$retArray['status'] = TRUE;
					echo json_encode($retArray);
					exit;
				}
			} else {
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['render'] =  $this->load->view('report/reporterror', $this->data, true);
			$retArray['status'] = TRUE;
			echo json_encode($retArray);
			exit;
		}
	}

	public function getBalanceFeesReportLazy() {
		$retArray['status'] = FALSE;
		$retArray['rows']   = '';
		$retArray['hasMore'] = FALSE;
		$retArray['nextOffset'] = 0;

		if(permissionChecker('balancefeesreport')) {
			if($_POST) {
				$rules = $this->rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
					echo json_encode($retArray);
					exit;
				} else {
					$schoolyearID = $this->session->userdata('defaultschoolyearID');
					$_POST['schoolyearID'] = $schoolyearID;
					$villageID    = $this->input->post('villageID'); 
					$classesID    = $this->input->post('classesID'); 
					$sectionID    = $this->input->post('sectionID'); 
					$studentID    = $this->input->post('studentID'); 
					$offset       = (int) $this->input->post('offset');

					$this->data['classesID']    = $classesID;
					$this->data['villageID']    = $villageID;
					$this->data['sectionID']    = $sectionID;
					$this->data['studentID']    = $studentID;
					$this->data['schoolyearID'] = $schoolyearID; 

					$feetypeIDs = $this->input->post('feetypeID'); // this will now be an array
					$this->data['feetypeIDs'] = $feetypeIDs;

					$studentArray = [];
					if((int)$classesID) {
						$studentArray['srclassesID'] = $classesID;
					}
					if((int)$sectionID) {
						$studentArray['srsectionID'] = $sectionID;
					}
					if((int)$studentID) {
						$studentArray['srstudentID'] = $studentID;
					}

					if((int)$villageID) {
						$studentArray['villageID'] = $villageID;
					}

					$studentArray['srschoolyearID'] = $schoolyearID;

					$this->db->order_by('srclassesID','ASC');
					$allStudents = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');
					$perPage     = 25;
					$totalStudents = customCompute($allStudents);

					// Calculate the slice for this page
					if($offset < 0) $offset = 0;
					if($offset > $totalStudents) $offset = $totalStudents;

					$this->data['students']    = ($totalStudents > 0) ? array_slice($allStudents, $offset, $perPage, true) : [];
					$this->data['classes']     = pluck($this->classes_m->general_get_classes(),'classes','classesID');
					$this->data['sections']    = pluck($this->section_m->general_get_section(),'section','sectionID');
					$this->data['feetypes']    = ($this->feetypes_m->general_get_fee_multi($feetypeIDs));
					$this->data['totalAmountAndDiscount'] = $this->totalAmountAndDiscustomCompute($this->invoice_m->get_all_balancefees_for_report_multi($this->input->post()));
					$this->data['totalPayment']         = $this->totalPaymentAndWeaver($this->payment_m->get_order_by_payment_new_multi($schoolyearID,$feetypeIDs,$studentID));
					$this->data['totalPayment_split']   = $this->totalPaymentAndWeaver_split($this->payment_m->get_order_by_payment_new_multi($schoolyearID,$feetypeIDs,$studentID));
					$this->data['totalweavar']          = $this->totalWeaver($this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID'=>$schoolyearID)));
					$this->data['startIndex']           = $offset;

					$retArray['rows'] = $this->load->view('report/balancefees/BalanceFeesReportRows', $this->data, true);
					$nextOffset = $offset + $perPage;
					$retArray['nextOffset'] = ($nextOffset > $totalStudents) ? $totalStudents : $nextOffset;
					$retArray['hasMore']    = ($nextOffset < $totalStudents);
					$retArray['status']     = TRUE;
					echo json_encode($retArray);
					exit;
				}
			} else {
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['status'] = FALSE;
			echo json_encode($retArray);
			exit;
		}
	}

	public function getFeeDueSlipReport() {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';

		if(permissionChecker('balancefeesreport')) {
			if($_POST) {
				$schoolyearID = $this->session->userdata('defaultschoolyearID');
				$classesID    = $this->input->post('classesID'); 
				$sectionID    = $this->input->post('sectionID'); 
				$studentIDs   = $this->input->post('studentID'); 
				$feetypeIDs   = $this->input->post('feetypeID'); 
				$slip_date    = $this->input->post('slip_date');
				$due_date     = $this->input->post('due_date');

				$this->data['classesID']    = $classesID;
				$this->data['sectionID']    = $sectionID;
				$this->data['schoolyearID'] = $schoolyearID;
				$this->data['slip_date']    = $slip_date;
				$this->data['due_date']     = $due_date;

				$studentArray = [];
				if((int)$classesID) {
					$studentArray['srclassesID'] = $classesID;
				}
				if((int)$sectionID) {
					$studentArray['srsectionID'] = $sectionID;
				}
				if(!empty($studentIDs)) {
					if(is_array($studentIDs) && !in_array('0', $studentIDs)) {
						$this->db->where_in('srstudentID', $studentIDs);
					}
				}
				$studentArray['srschoolyearID'] = $schoolyearID;

				$this->db->order_by('srclassesID','ASC');
				$allStudents = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');
				
				$this->data['students'] = $allStudents;
				$this->data['classes']  = pluck($this->classes_m->general_get_classes(),'classes','classesID');
				$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');
				
				$invoiceParams = [
					'classesID' => $classesID,
					'sectionID' => $sectionID,
					'schoolyearID' => $schoolyearID,
					'feetypeID' => $feetypeIDs
				];
				
				$this->data['totalAmountAndDiscount'] = $this->totalAmountAndDiscustomCompute($this->invoice_m->get_all_balancefees_for_report_multi($invoiceParams));
				$this->data['totalPayment'] = $this->totalPaymentAndWeaver($this->payment_m->get_order_by_payment_new_multi($schoolyearID, $feetypeIDs, $studentIDs));
				$this->data['totalweavar']  = $this->totalWeaver($this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID'=>$schoolyearID)));
				
				$parentIDs = [];
				foreach($allStudents as $s) {
					$parentIDs[] = $s->parentID;
				}
				if(!empty($parentIDs)) {
					$parents = pluck($this->parents_m->get_where_in_parents($parentIDs, 'parentsID'), 'name', 'parentsID');
					$this->data['parents'] = $parents;
				} else {
					$this->data['parents'] = [];
				}

				$retArray['render'] = $this->load->view('report/balancefees/FeeDueSlipReport', $this->data, true);
				$retArray['status'] = TRUE;
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['status'] = FALSE;
		}
		echo json_encode($retArray);
		exit;
	}



	private function totalAmountAndDiscustomCompute($arrays) {
		$totalAmountAndDiscount = [];
		if(customCompute($arrays)) {
			foreach($arrays as $key => $array) {
				if(isset($totalAmountAndDiscount[$array->studentID]['amount'])) {
					$totalAmountAndDiscount[$array->studentID]['amount'] += $array->amount;
				} else {
					$totalAmountAndDiscount[$array->studentID]['amount'] = $array->amount;
				}
				$totalAmountAndDiscount[$array->studentID]['type'][] = $array->feetype.':'.$array->amount;

				if(isset($totalAmountAndDiscount[$array->studentID]['discount'])) {

					// $discount = (($array->amount / 100) * $array->discount);
					$discount = $array->discount; 

					$totalAmountAndDiscount[$array->studentID]['discount'] += $discount;
				} else {
					// $discount = (($array->amount / 100) * $array->discount);
					$discount = $array->discount;
					
					$totalAmountAndDiscount[$array->studentID]['discount'] = $discount;
				}
			}
		}
		return $totalAmountAndDiscount;
	}

	private function totalPaymentAndWeaver($arrays) {
		// echo "<pre>"; print_r($arrays);
		$totalPayment = [];
		if(customCompute($arrays)) {
			foreach($arrays as $key => $array) {
				if(isset($totalPayment[$array->studentID]['payment'])) {
					$totalPayment[$array->studentID]['payment'] += $array->total_paid;
				} else {
					$totalPayment[$array->studentID]['payment'] = $array->total_paid;
				}
			}
		}
		return ($totalPayment);
	}

	private function totalWeaver($arrays) {
		$totalWeaver = [];
		if(customCompute($arrays)) {
			foreach ($arrays as $array) {
				if(isset($totalWeaver[$array->studentID]['weaver'])) {
					$totalWeaver[$array->studentID]['weaver'] += $array->weaver;
				} else {
					$totalWeaver[$array->studentID]['weaver'] = $array->weaver; 
				}
			}
		}
		return $totalWeaver;
	}

	private function totalPayment($arrays, $schoolyearID) {
		$weaverandfine = pluck($this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID'=>$schoolyearID)),'obj','paymentID');
		$retArray = [];
		if(customCompute($arrays)) {
			foreach ($arrays as $array) {
				if(isset($retArray[$array->invoiceID])) {
					$oldAmount = $retArray[$array->invoiceID];
					$oldAmount += $array->paymentamount;
					$retArray[$array->invoiceID] = (int) $oldAmount;
					if(isset($weaverandfine[$array->paymentID])) {
						$oldAmount = $retArray[$array->invoiceID];
						$oldAmount += $weaverandfine[$array->paymentID]->weaver;
						$retArray[$array->invoiceID] = (int) $oldAmount;
					}
				} else {
					$retArray[$array->invoiceID] = (int) $array->paymentamount;
					if(isset($weaverandfine[$array->paymentID])) {
						$oldAmount = $retArray[$array->invoiceID];
						$oldAmount += $weaverandfine[$array->paymentID]->weaver;
						$retArray[$array->invoiceID] = (int) $oldAmount;
					}
				}
			}
		}

		return $retArray;
	}

	public function pdf() {
		if(permissionChecker('balancefeesreport')) { 
			$classesID = htmlentities(escapeString($this->uri->segment(3)));
			$sectionID = htmlentities(escapeString($this->uri->segment(4)));
			$studentID = htmlentities(escapeString($this->uri->segment(5)));

			if((int)($classesID >= 0) || (int)($sectionID >= 0) || (int)($studentID >= 0)) {
				$postArray = [];
				$schoolyearID = $this->session->userdata('defaultschoolyearID');
				$postArray['schoolyearID'] = $schoolyearID;
				$postArray['classesID'] = $classesID;
				$postArray['sectionID'] = $sectionID;
				$postArray['studentID'] = $studentID;

				$this->data['classesID'] = $classesID;
				$this->data['sectionID'] = $sectionID;
				$this->data['classes'] = pluck($this->classes_m->general_get_classes(),'classes','classesID');
				$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');

				$studentArray = [];
				if((int)$classesID) {
					$studentArray['srclassesID'] = $classesID;
				}
				if((int)$sectionID) {
					$studentArray['srsectionID'] = $sectionID;
				}
				if((int)$studentID) {
					$studentArray['srstudentID'] = $studentID;
				}
				$studentArray['srschoolyearID'] = $schoolyearID;

				$this->db->order_by('srclassesID','ASC');
				// $this->data['students'] = pluck($this->studentrelation_m->get_order_by_studentrelation($studentArray),'obj','srstudentID');

				// $this->data['students'] = pluck($this->studentrelation_m->get_studentrelation_join_student($studentArray),'obj','srstudentID');

				$this->data['students'] = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');


				$this->data['totalAmountAndDiscount'] = $this->totalAmountAndDiscustomCompute($this->invoice_m->get_all_balancefees_for_report($postArray));
				$this->data['totalPayment'] = $this->totalPaymentAndWeaver($this->payment_m->get_order_by_payment(array('schoolyearID' => $schoolyearID)));
				$this->data['totalweavar'] = $this->totalWeaver($this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID'=>$schoolyearID)));

				$this->reportPDF('balancefeesreport.css', $this->data, 'report/balancefees/BalanceFeesReportPDF');
			} else {
				$this->data["subview"] = "error";
				$this->load->view('_layout_main', $this->data);	
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function xlsx() {
		if(permissionChecker('balancefeesreport')) {
			$this->load->library('phpspreadsheet');

			$sheet = $this->phpspreadsheet->spreadsheet->getActiveSheet();
			$sheet->getDefaultColumnDimension()->setWidth(25);
			$sheet->getDefaultRowDimension()->setRowHeight(25);
			$sheet->getColumnDimension('A')->setWidth(20);
			$sheet->getRowDimension('1')->setRowHeight(25);
			$sheet->getRowDimension('2')->setRowHeight(25);
			
			$data = $this->xmlData();

			// Redirect output to a client’s web browser (Xlsx)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="balancefeereport.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0

			$this->phpspreadsheet->output($this->phpspreadsheet->spreadsheet);
		} else {
			$this->data["subview"] = "errorpermission";
			$this->load->view('_layout_main', $this->data);
		}
	}

	private function xmlData() {
		if(permissionChecker('balancefeesreport')) { 
			$classesID = htmlentities(escapeString($this->uri->segment(3)));
			$sectionID = htmlentities(escapeString($this->uri->segment(4)));
			$studentID = htmlentities(escapeString($this->uri->segment(5)));

			if((int)($classesID >= 0) || (int)($sectionID >= 0) || (int)($studentID >= 0)) {
				$postArray = [];
				$schoolyearID = $this->session->userdata('defaultschoolyearID');
				$postArray['schoolyearID'] = $schoolyearID;
				$postArray['classesID'] = $classesID;
				$postArray['sectionID'] = $sectionID;
				$postArray['studentID'] = $studentID;

				$this->data['classesID'] = $classesID;
				$this->data['sectionID'] = $sectionID;
				$this->data['classes'] = pluck($this->classes_m->general_get_classes(),'classes','classesID');
				$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');

				$studentArray = [];
				if((int)$classesID) {
					$studentArray['srclassesID'] = $classesID;
				}
				if((int)$sectionID) {
					$studentArray['srsectionID'] = $sectionID;
				}
				if((int)$studentID) {
					$studentArray['srstudentID'] = $studentID;
				}
				$studentArray['srschoolyearID'] = $schoolyearID;

				$this->db->order_by('srclassesID','ASC');
				// $this->data['students'] = pluck($this->studentrelation_m->get_order_by_studentrelation($studentArray),'obj','srstudentID');

				$this->data['students'] = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');


				$this->data['totalAmountAndDiscount'] = $this->totalAmountAndDiscustomCompute($this->invoice_m->get_all_balancefees_for_report($postArray));
				$this->data['totalPayment'] = $this->totalPaymentAndWeaver($this->payment_m->get_order_by_payment(array('schoolyearID' => $schoolyearID)));
				$this->data['totalweavar'] = $this->totalWeaver($this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID'=>$schoolyearID)));
				return $this->generateXML($this->data);															
			} else {
				$this->data["subview"] = "error";
				$this->load->view('_layout_main', $this->data);	
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	private function generateXML($data) {
		extract($data);
		$sheet = $this->phpspreadsheet->spreadsheet->getActiveSheet();
		if(customCompute($students)) {
			$maxColumnCount = 9;
			if($classesID == 0) {
				$maxColumnCount = 10;
			}

			if($sectionID == 0) {
				$maxColumnCount = 10;
			}

			if($classesID == 0 && $sectionID == 0) {
				$maxColumnCount = 11;
			}

			$headerColumn = "A";
        	for($i= 1; $i < $maxColumnCount; $i++) {
	        	$headerColumn++;
	        }

	        $row = 1;
	        $column = 'A';

	        //Here Will Be Header Info

	        if($classesID >= 0) {
				$className  = $this->lang->line('balancefeesreport_class');
				$className .= ' : ';
				$className .= isset($classes[$classesID]) ? $classes[$classesID] : $this->lang->line('balancefeesreport_all_class');
				
				$sectionName  = $this->lang->line('balancefeesreport_section'); 				
				$sectionName .= " : ";
				$sectionName .= isset($sections[$sectionID]) ? $sections[$sectionID] : $this->lang->line('balancefeesreport_all_section');
			
				$sheet->setCellValue('A'.$row, $className);
				$sheet->setCellValue($headerColumn.$row, $sectionName);
			} else {
				$sheet->getRowDimension('1')->setVisible(false);
			}

	        //Make Header Data Array
	        $headers = array();
	        $headers['hash_id'] = "#";
	        $headers['student_name'] = $this->lang->line('balancefeesreport_name');
	        $headers['admission_number'] = $this->lang->line('balancefeesreport_registerNO');

	        if($classesID == 0) { 
	        	$headers['class'] = $this->lang->line('balancefeesreport_class');
	        } 

	        if($sectionID == 0) { 
	        	$headers['section'] = $this->lang->line('balancefeesreport_section');
	        }

	        $headers['roll'] = $this->lang->line('balancefeesreport_roll');
	        $headers['total_amount'] = $this->lang->line('balancefeesreport_fees_amount');
	        $headers['discount'] = $this->lang->line('balancefeesreport_discount');
	        $headers['total_paid'] = $this->lang->line('balancefeesreport_paid');
	        $headers['total_weaver'] = $this->lang->line('balancefeesreport_weaver');
	        $headers['due'] = $this->lang->line('balancefeesreport_balance');



	        //Make Xml Header Array
			$column = 'A';    		
    		$row = 2;
	        foreach($headers as $header) {
	        	$sheet->setCellValue($column.$row,$header);
	            $column++;
	        }


	        $studentArray = [];
	        $totalAmountArray = [];
	        $i = 0;

	        $totalAmount = 0;
            $totalDiscount = 0;
            $totalPayments = 0;
            $totalWeaver = 0;
            $totalBalance = 0;

	      	foreach ($students as $student) {
	      		$i++;
	      		$studentArray[$i]['srno']   =  $i;
	      		$studentArray[$i]['srname'] = $student->srname;
	      		$studentArray[$i]['srregisterNO'] = $student->srregisterNO;

	      		if($classesID == 0) {
                    $studentArray[$i]['classes']  = isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : '';
                }

                if($sectionID == 0) { 
                	$studentArray[$i]['section'] = isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : '';
                }

	      		$studentArray[$i]['srroll'] = $student->srroll;
	      		$studentArray[$i]['amount'] = isset($totalAmountAndDiscount[$student->srstudentID]['amount']) ? number_format($totalAmountAndDiscount[$student->srstudentID]['amount'],2) : '0';
	      		$studentArray[$i]['discount'] = isset($totalAmountAndDiscount[$student->srstudentID]['discount']) ? number_format($totalAmountAndDiscount[$student->srstudentID]['discount'],2) : '0';
	      		$studentArray[$i]['payment'] = isset($totalPayment[$student->srstudentID]['payment']) ? number_format($totalPayment[$student->srstudentID]['payment'],2) : '0';
	      		$studentArray[$i]['weaver'] = isset($totalweavar[$student->srstudentID]['weaver']) ? number_format($totalweavar[$student->srstudentID]['weaver'],2) : '0';

	      		$Amount = 0;
                $Discount = 0;
                $Payment = 0;
                $Weaver = 0;

                if(isset($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
                    $Amount = $totalAmountAndDiscount[$student->srstudentID]['amount'];
                    $totalAmount += $Amount;
                }

                if(isset($totalAmountAndDiscount[$student->srstudentID]['discount'])) {
                    $Discount = $totalAmountAndDiscount[$student->srstudentID]['discount'];
                    $totalDiscount += $Discount;
                }

                if(isset($totalPayment[$student->srstudentID]['payment'])) {
                    $Payment = $totalPayment[$student->srstudentID]['payment'];
                    $totalPayments += $Payment;
                }

                if(isset($totalweavar[$student->srstudentID]['weaver'])) {
                    $Weaver = $totalweavar[$student->srstudentID]['weaver'];
                    $totalWeaver += $Weaver;
                }

                $Balance = ($Amount - $Discount) - ($Payment+$Weaver);

                $totalBalance += $Balance;

                $studentArray[$i]['balance'] = number_format($Balance,2);
	      	}

	      	$i++;

	      	$studentArray[$i]['srno'] = '';
	      	$studentArray[$i]['srname'] = '';
	      	$studentArray[$i]['srregisterNO'] = '';
	      	if($classesID == 0) {
	      		$studentArray[$i]['classes'] = '';
	     	}

	     	if($sectionID == 0) { 
	        	$studentArray[$i]['section'] = '';
	        }

	      	$studentArray[$i]['srroll'] = '';
	      	$studentArray[$i]['amount'] = number_format($totalAmount,2);
	      	$studentArray[$i]['discount'] = number_format($totalDiscount,2);
	      	$studentArray[$i]['payment'] = number_format($totalPayments,2);
	      	$studentArray[$i]['weaver'] = number_format($totalWeaver,2);
	      	$studentArray[$i]['balance'] = number_format($totalBalance,2);

	        //Make Here Xml Body
	        $row  = 3;
	        if(customCompute($studentArray)) {
	        	foreach($studentArray as $studentArray) {
	        		$column = "A";
	        		foreach($studentArray as $value) {
	        			$sheet->setCellValue($column.$row,$value);
	            		$column++;
	        		}
	        		$row++;
	        	}
	        }

	        if(customCompute($totalAmountArray)) {
	        	foreach($totalAmountArray as $value) {
	        		$sheet->setCellValue($column.$row,$value);
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

			$sheet->getStyle('A1:'.$headerColumn.'2')->applyFromArray($styleArray);


			$styleArray = [
			    'font' => [
			        'bold' => false,
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

			$decrementrow = $row-2;
			$sheet->getStyle('A3:'.$headerColumn.$decrementrow)->applyFromArray($styleArray);

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
			$decrementrow = $decrementrow+1;
			$sheet->getStyle('A'.$decrementrow.':'.$headerColumn.$decrementrow)->applyFromArray($styleArray);

			$headerColumn = chr(ord($headerColumn) - 1);  //Decreament Header Section Column
			$mergeCellsColumn = $headerColumn.'1';
			$sheet->mergeCells("B1:$mergeCellsColumn");

			
			$row = $row-1;
			
			$sheet->setCellValue('A'.$row, $this->lang->line('balancefeesreport_grand_total').(!empty($this->data['siteinfos']->currency_code) ? ' ('.$this->data['siteinfos']->currency_code.')' : ''));
			
			$startMergeCellsColumn = 'A'.$row;
			$headerColumn = chr(ord($headerColumn) - 4);
			$endMergeCellsColumn = $headerColumn.$row;
			$sheet->mergeCells("$startMergeCellsColumn:$endMergeCellsColumn");
		} else {
			redirect(base_url('balancefeesreport'));
		}
	}

	public function date_valid($date) {
		if($date) {
			if(strlen($date) < 10) {
				$this->form_validation->set_message("date_valid", "The %s is not valid dd-mm-yyyy.");
		     	return FALSE;
			} else {
		   		$arr = explode("-", $date);
		        $dd = $arr[0];
		        $mm = $arr[1];
		        $yyyy = $arr[2];
		      	if(checkdate($mm, $dd, $yyyy)) {
		      		return TRUE;
		      	} else {
		      		$this->form_validation->set_message("date_valid", "The %s is not valid dd-mm-yyyy.");
		     		return FALSE;
		      	}
		    }
		}
		return TRUE;
	}

	public function unique_date() {
		$fromdate = $this->input->post('fromdate');
		$todate   = $this->input->post('todate');

		$startingdate = $this->data['schoolyearobj']->startingdate;
		$endingdate = $this->data['schoolyearobj']->endingdate;

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

	public function send_pdf_to_mail() {
		$retArray['status'] = FALSE;
		$retArray['message'] = '';

		if(permissionChecker('balancefeesreport')) { 
			if($_POST) {
				$rules = $this->send_pdf_to_mail_rules();
				$this->form_validation->set_rules($rules);

			    if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
				    echo json_encode($retArray);
				    exit;
				} else {
					$schoolyearID = $this->session->userdata('defaultschoolyearID');
					$classesID = $this->input->post('classesID');
					$sectionID = $this->input->post('sectionID');
					$studentID = $this->input->post('studentID');

					$this->data['schoolyearID'] = $schoolyearID; 
					$this->data['classesID'] = $classesID;
					$this->data['sectionID'] = $sectionID;
					$this->data['studentID'] = $studentID;

					$postArray = [];
					$postArray['schoolyearID'] = $schoolyearID;
					$postArray['classesID'] = $classesID;
					$postArray['sectionID'] = $sectionID;
					$postArray['studentID'] = $studentID;

					$to      = $this->input->post('to'); 
					$subject = $this->input->post('subject'); 
					$message = $this->input->post('message');


					$studentArray = [];
					if((int)$classesID) {
						$studentArray['srclassesID'] = $classesID;
					}
					if((int)$sectionID) {
						$studentArray['srsectionID'] = $sectionID;
					}
					if((int)$studentID) {
						$studentArray['srstudentID'] = $studentID;
					}
					$studentArray['srschoolyearID'] = $schoolyearID;

					$this->db->order_by('srclassesID','ASC');
					// $this->data['students'] = pluck($this->studentrelation_m->get_order_by_studentrelation($studentArray),'obj','srstudentID');

					$this->data['students'] = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');

					$this->data['classes']  = pluck($this->classes_m->general_get_classes(),'classes','classesID');
					$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');

					$this->data['totalAmountAndDiscount'] = $this->totalAmountAndDiscustomCompute($this->invoice_m->get_all_balancefees_for_report($postArray));
					$this->data['totalPayment'] = $this->totalPaymentAndWeaver($this->payment_m->get_order_by_payment(array('schoolyearID' => $schoolyearID)));
					$this->data['totalweavar'] = $this->totalWeaver($this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID'=>$schoolyearID)));

					$this->reportSendToMail('balancefeesreport.css', $this->data, 'report/balancefees/BalanceFeesReportPDF', $to, $subject, $message);
					$retArray['status'] = TRUE;
					$retArray['message'] = 'Success';
					echo json_encode($retArray);
			    	exit;
				}
			} else {
				$retArray['status'] = FALSE;
				$retArray['message'] = $this->lang->line('balancefeesreport_permissionmethod');
				echo json_encode($retArray);
		    	exit;
			}
		} else {
			$retArray['status'] = FALSE;
			$retArray['message'] = $this->lang->line('balancefeesreport_permission');
			echo json_encode($retArray);
	    	exit;
		}
	}

	/*private function totalPaymentAndWeaver_split($arrays) {
		$totalPayment = [];
		
		// Check if the input array is not empty
		if(customCompute($arrays)) {
			// Loop through the array to calculate the total payments by student and fee type
			foreach ($arrays as $key => $array) {
				// Check if the student ID and fee type already exist in the totalPayment array
				if (isset($totalPayment[$array->studentID][$array->feetype])) {
					// Add the payment amount to the existing fee type for this student
					$totalPayment[$array->studentID][$array->feetype] += $array->paymentamount;
				} else {
					// If not, create a new entry for this student and fee type
					$totalPayment[$array->studentID][$array->feetype] = $array->paymentamount;
				}
			}
		}
	
		// Return the total payments grouped by student ID and fee type
		return $totalPayment;
	}
	*/

	// private function totalPaymentAndWeaver_split($arrays) {
	// 	// Initialize the array to hold the total payments and remaining amounts by student ID
	// 	$totalPaymentByStudent = [];
	
	// 	// Check if the input array is not empty
	// 	if (customCompute($arrays)) {
	// 		// Loop through each payment record
	// 		foreach ($arrays as $key => $array) {
	// 			// Calculate the total payment amount for each student and fee type
	// 			if (isset($totalPaymentByStudent[$array->studentID][$array->feetype])) {
	// 				// Add the payment amount to the existing fee type
	// 				$totalPaymentByStudent[$array->studentID][$array->feetype]['paid'] += $array->paymentamount;
	// 			} else {
	// 				// Initialize the fee type entry with the payment amount
	// 				$totalPaymentByStudent[$array->studentID][$array->feetype] = [
	// 					'paid' => $array->paymentamount,
	// 					'total' => $array->amount,
	// 					'remaining' => $array->amount - $array->paymentamount
	// 				];
	// 			}
	
	// 			// Recalculate the remaining amount for each fee type
	// 			$totalPaymentByStudent[$array->studentID][$array->feetype]['remaining'] = 
	// 				$totalPaymentByStudent[$array->studentID][$array->feetype]['total'] -
	// 				$totalPaymentByStudent[$array->studentID][$array->feetype]['paid'];
	// 		}
	// 	}
	
	// 	// Return the total payments and remaining amounts by student ID and fee type
	// 	return $totalPaymentByStudent;
	// }
	

	private function totalPaymentAndWeaver_split_original($arrays) {
		// Initialize the array to hold the total payments and remaining amounts by student ID
		$totalPaymentByStudent = [];
	
		// Check if the input array is not empty
		if (customCompute($arrays)) {
			// Loop through each payment record
			foreach ($arrays as $key => $array) {
				// If the student and fee type already have an entry, update the payment amount and remaining
				if (isset($totalPaymentByStudent[$array->studentID][$array->feetype])) {
					// Add the payment amount to the existing fee type
					$totalPaymentByStudent[$array->studentID][$array->feetype]['paid'] += $array->paymentamount;
				} else {
					// Initialize the fee type entry with the payment amount
					$totalPaymentByStudent[$array->studentID][$array->feetype] = [
						'paid' => $array->paymentamount,
						'total' => $array->amount,  // Keep the total fee intact (without weaver deduction)
						'remaining' => $array->amount - $array->paymentamount  // Calculate the initial remaining balance
					];
				}
	
				// Recalculate the remaining amount for each fee type considering the weaver
				$totalPaymentByStudent[$array->studentID][$array->feetype]['remaining'] = 
					$totalPaymentByStudent[$array->studentID][$array->feetype]['total'] - 
					$totalPaymentByStudent[$array->studentID][$array->feetype]['paid'];
	
				// Subtract the weaver from the remaining balance
				$totalPaymentByStudent[$array->studentID][$array->feetype]['remaining'] -= $array->weaver;
			}
		}
	
		// Return the total payments and remaining amounts by student ID and fee type
		return $totalPaymentByStudent;
	}

	private function totalPaymentAndWeaver_split($arrays) {
    $totalPaymentByStudent = [];

    if (customCompute($arrays)) {
        foreach ($arrays as $array) {
            if (isset($totalPaymentByStudent[$array->studentID][$array->feetype])) {
                // already exists, update
                $totalPaymentByStudent[$array->studentID][$array->feetype]['paid'] += $array->total_paid;
                $totalPaymentByStudent[$array->studentID][$array->feetype]['discount_plus_weaver'] += ($array->discount + $array->weaver);

                $totalPaymentByStudent[$array->studentID][$array->feetype]['remaining'] = 
                    $totalPaymentByStudent[$array->studentID][$array->feetype]['total'] 
                    - $totalPaymentByStudent[$array->studentID][$array->feetype]['paid'] 
                    - $totalPaymentByStudent[$array->studentID][$array->feetype]['discount_plus_weaver'];

            } else {
                // first time insert
                $totalPaymentByStudent[$array->studentID][$array->feetype] = [
                    'paid' => $array->total_paid,
                    'total' => $array->amount,
                    'discount_plus_weaver' => $array->discount + $array->weaver,
                    'remaining' => $array->amount - $array->total_paid - ($array->discount + $array->weaver)
                ];
            }
        }
    }

    return $totalPaymentByStudent;
}


private function totalPaymentAndWeaver_split_working($arrays) {
    $totalPaymentByStudent = [];

    if (customCompute($arrays)) {
		echo "<pre>";print_r($arrays);die;
        foreach ($arrays as $array) {
            if (isset($totalPaymentByStudent[$array->studentID][$array->feetype])) {
                // already exists, update
                $totalPaymentByStudent[$array->studentID][$array->feetype]['paid'] += $array->total_paid;
                $totalPaymentByStudent[$array->studentID][$array->feetype]['remaining'] = 
                    $totalPaymentByStudent[$array->studentID][$array->feetype]['total'] 
                    - $totalPaymentByStudent[$array->studentID][$array->feetype]['paid'];
            } else {
                // first time insert
                $totalPaymentByStudent[$array->studentID][$array->feetype] = [
                    'paid' => $array->total_paid,
                    'total' => $array->amount,
                    'remaining' => $array->amount - $array->total_paid
                ];
            }

            // apply weaver after calculation
            $totalPaymentByStudent[$array->studentID][$array->feetype]['remaining'] -= $array->weaver;
        }
    }

    return $totalPaymentByStudent;
}




/*	private function totalPaymentAndWeaver_split($arrays) {
		$totalPaymentByStudent = [];
	
		if (customCompute($arrays)) {
			foreach ($arrays as $key => $array) {
				if (!isset($totalPaymentByStudent[$array->studentID][$array->feetype])) {
					$totalPaymentByStudent[$array->studentID][$array->feetype] = [
						'paid'                 => $array->paymentamount,
						'total'                => $array->amount,
						'discount'             => $array->discount,
						'weaver'               => $array->weaver,
						'total_discount_weaver'=> $array->discount + $array->weaver,
						'remaining'            => $array->amount - $array->paymentamount - $array->discount - $array->weaver
					];
				} else {
					$record = &$totalPaymentByStudent[$array->studentID][$array->feetype];
	
					$record['paid']     += $array->paymentamount;
					$record['discount'] += $array->discount;
					$record['weaver']   += $array->weaver;
					$record['total_discount_weaver'] = $record['discount'] + $record['weaver'];
					$record['remaining'] = $record['total'] - $record['paid'] - $record['discount'] - $record['weaver'];
				}
			}
		}
	
		return $totalPaymentByStudent;
	}

	*/
	
	
    public function all_class_wise() {
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

		$this->data['date'] = date("d-m-Y");		
		$this->data['classes'] = $this->classes_m->general_get_classes();

		// If filters are submitted, process them
		if($_POST) {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$_POST['schoolyearID'] = $schoolyearID;
			
			$villageID    = $this->input->post('villageID'); 
			$classesID    = $this->input->post('classesID'); 
			$sectionID    = $this->input->post('sectionID'); 
			$studentID    = $this->input->post('studentID'); 
			$feetypeIDs   = $this->input->post('feetypeID');

			// Handle array inputs - convert to single values for model compatibility
			$postData = array('schoolyearID' => $schoolyearID);
			
			// For class filter, if multiple classes selected, we'll process each separately
			// For now, use the first selected class for the invoice query
			if(!empty($classesID) && is_array($classesID) && $classesID[0] != '0') {
				$postData['classesID'] = $classesID[0]; // Use first selected class
			}
			
			if(!empty($sectionID) && is_array($sectionID) && $sectionID[0] != '0') {
				$postData['sectionID'] = $sectionID[0]; // Use first selected section
			}
			
			if(!empty($studentID)) {
				$postData['studentID'] = $studentID;
			}
			
			if(!empty($feetypeIDs) && is_array($feetypeIDs)) {
				$postData['feetypeID'] = $feetypeIDs; // This can handle arrays
			}

			// Use the exact same data source as getBalanceFeesReport
			$invoiceData = $this->invoice_m->get_all_balancefees_for_report_multi($postData);
			$paymentData = $this->payment_m->get_order_by_payment_new_multi($schoolyearID, $feetypeIDs, $studentID);
			$weaverData = $this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID' => $schoolyearID));

			// Process the same way as getBalanceFeesReport
			$totalAmountAndDiscount = $this->totalAmountAndDiscustomCompute($invoiceData);
			$totalPayment = $this->totalPaymentAndWeaver($paymentData);
			$totalweavar = $this->totalWeaver($weaverData);

			// Group by class
			$classTotals = [];
			$classes = pluck($this->classes_m->general_get_classes(),'classes','classesID');

			// Get all students that match the criteria
			$studentArray = [];
			
			// Handle multi-select filters for student filtering
			if(!empty($classesID) && is_array($classesID)) {
				$selectedClasses = array_filter($classesID, function($id) { return $id != '0'; });
				if(!empty($selectedClasses)) {
					// We'll filter students by multiple classes later
				}
			}
			
			if(!empty($sectionID) && is_array($sectionID)) {
				$selectedSections = array_filter($sectionID, function($id) { return $id != '0'; });
				if(!empty($selectedSections)) {
					// We'll filter students by multiple sections later
				}
			}
			
			if(!empty($villageID) && is_array($villageID)) {
				$selectedVillages = array_filter($villageID, function($id) { return $id != '0'; });
				if(!empty($selectedVillages)) {
					// We'll filter students by multiple villages later
				}
			}
			
			$studentArray['srschoolyearID'] = $schoolyearID;
			$students = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');

			// Process each student to get class totals
			foreach($students as $student) {
				// Apply multi-select filters
				$includeStudent = true;
				
				// Filter by selected classes
				if(!empty($selectedClasses)) {
					if(!in_array($student->srclassesID, $selectedClasses)) {
						$includeStudent = false;
					}
				}
				
				// Filter by selected sections
				if($includeStudent && !empty($selectedSections)) {
					if(!in_array($student->srsectionID, $selectedSections)) {
						$includeStudent = false;
					}
				}
				
				// Filter by selected villages
				if($includeStudent && !empty($selectedVillages)) {
					if(!in_array($student->villageID, $selectedVillages)) {
						$includeStudent = false;
					}
				}
				
				if($includeStudent && !empty($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
					$classID = $student->srclassesID;
					$className = isset($classes[$classID]) ? $classes[$classID] : 'Unknown';

					if(!isset($classTotals[$classID])) {
						$classTotals[$classID] = [
							'Classname' => $className,
							'TotalFee' => 0,
							'TotalPaid' => 0,
							'TotalDiscount' => 0,
							'TotalWeaver' => 0,
							'TotalBalance' => 0
						];
					}

					// Add amounts
					$classTotals[$classID]['TotalFee'] += $totalAmountAndDiscount[$student->srstudentID]['amount'];
					$classTotals[$classID]['TotalDiscount'] += $totalAmountAndDiscount[$student->srstudentID]['discount'];

					// Add payments
					if(isset($totalPayment[$student->srstudentID]['payment'])) {
						$classTotals[$classID]['TotalPaid'] += $totalPayment[$student->srstudentID]['payment'];
					}

					// Add weavers
					if(isset($totalweavar[$student->srstudentID]['weaver'])) {
						$classTotals[$classID]['TotalWeaver'] += $totalweavar[$student->srstudentID]['weaver'];
					}
				}
			}

			// Calculate balances and format result
			$result = [];
			foreach($classTotals as $classID => $classData) {
				$totalDiscountWithWeaver = $classData['TotalDiscount'] + $classData['TotalWeaver'];
				$totalBalance = $classData['TotalFee'] - $totalDiscountWithWeaver - $classData['TotalPaid'];

				$result[] = (object)[
					'Classname' => $classData['Classname'],
					'TotalFee' => $classData['TotalFee'],
					'TotalPaid' => $classData['TotalPaid'],
					'TotalDiscount' => $totalDiscountWithWeaver,
					'TotalBalance' => $totalBalance
				];
			}

			$this->data['result'] = $result;
		} else {
			// Default: show all classes without filters
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			
			// Get all data without filters
			$postArray = array('schoolyearID' => $schoolyearID);
			$invoiceData = $this->invoice_m->get_all_balancefees_for_report_multi($postArray);
			$paymentData = $this->payment_m->get_order_by_payment_new_multi($schoolyearID, null, "");
			$weaverData = $this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID' => $schoolyearID));

			$totalAmountAndDiscount = $this->totalAmountAndDiscustomCompute($invoiceData);
			$totalPayment = $this->totalPaymentAndWeaver($paymentData);
			$totalweavar = $this->totalWeaver($weaverData);

			$classTotals = [];
			$classes = pluck($this->classes_m->general_get_classes(),'classes','classesID');

			$studentArray = array('srschoolyearID' => $schoolyearID);
			$students = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');

			foreach($students as $student) {
				if(!empty($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
					$classID = $student->srclassesID;
					$className = isset($classes[$classID]) ? $classes[$classID] : 'Unknown';

					if(!isset($classTotals[$classID])) {
						$classTotals[$classID] = [
							'Classname' => $className,
							'TotalFee' => 0,
							'TotalPaid' => 0,
							'TotalDiscount' => 0,
							'TotalWeaver' => 0,
							'TotalBalance' => 0
						];
					}

					$classTotals[$classID]['TotalFee'] += $totalAmountAndDiscount[$student->srstudentID]['amount'];
					$classTotals[$classID]['TotalDiscount'] += $totalAmountAndDiscount[$student->srstudentID]['discount'];

					if(isset($totalPayment[$student->srstudentID]['payment'])) {
						$classTotals[$classID]['TotalPaid'] += $totalPayment[$student->srstudentID]['payment'];
					}

					if(isset($totalweavar[$student->srstudentID]['weaver'])) {
						$classTotals[$classID]['TotalWeaver'] += $totalweavar[$student->srstudentID]['weaver'];
					}
				}
			}

			$result = [];
			foreach($classTotals as $classID => $classData) {
				$totalDiscountWithWeaver = $classData['TotalDiscount'] + $classData['TotalWeaver'];
				$totalBalance = $classData['TotalFee'] - $totalDiscountWithWeaver - $classData['TotalPaid'];

				$result[] = (object)[
					'Classname' => $classData['Classname'],
					'TotalFee' => $classData['TotalFee'],
					'TotalPaid' => $classData['TotalPaid'],
					'TotalDiscount' => $totalDiscountWithWeaver,
					'TotalBalance' => $totalBalance
				];
			}

			$this->data['result'] = $result;
		}

		$this->data["subview"] = "report/balancefees/all_classes_wise_report";
		$this->load->view('_layout_main', $this->data);
	}

	public function getClassWiseReport() {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';

		if(permissionChecker('balancefeesreport')) {
			if($_POST) {
				// Use EXACTLY the same logic as getBalanceFeesReport - just group results by class
				$schoolyearID = $this->session->userdata('defaultschoolyearID');
				$_POST['schoolyearID'] = $schoolyearID;
				$villageID    = $this->input->post('villageID'); 
				$classesID    = $this->input->post('classesID'); 
				$sectionID    = $this->input->post('sectionID'); 
				$studentID    = $this->input->post('studentID'); 

				$feetypeIDs = $this->input->post('feetypeID'); // Same as getBalanceFeesReport

				// Handle array input for classesID - convert to single value for compatibility
				if(is_array($classesID)) {
					// For the main query, use the first selected class or 0 if none selected
					$classesIDSingle = (!empty($classesID) && $classesID[0] != '0') ? $classesID[0] : 0;
				} else {
					$classesIDSingle = $classesID;
				}

				// Use the exact same student filtering logic as getBalanceFeesReport
				$studentArray = [];
				if((int)$classesIDSingle) {
					$studentArray['srclassesID'] = $classesIDSingle;
				}
				if((int)$sectionID) {
					$studentArray['srsectionID'] = $sectionID;
				}
				if((int)$studentID) {
					$studentArray['srstudentID'] = $studentID;
				}
				if((int)$villageID) {
					$studentArray['villageID'] = $villageID;
				}
				$studentArray['srschoolyearID'] = $schoolyearID;

				$this->db->order_by('srclassesID','ASC');
				$students = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');

				$classes = pluck($this->classes_m->general_get_classes(),'classes','classesID');

				// Update POST data with single value for the model methods
				$_POST['classesID'] = $classesIDSingle;

				// Get data using EXACTLY the same methods and parameters as getBalanceFeesReport
				$totalAmountAndDiscount = $this->totalAmountAndDiscustomCompute($this->invoice_m->get_all_balancefees_for_report_multi($this->input->post()));
				
				$totalPayment = $this->totalPaymentAndWeaver($this->payment_m->get_order_by_payment_new_multi($schoolyearID, $feetypeIDs, $studentID));
				
				$totalweavar = $this->totalWeaver($this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID' => $schoolyearID)));

				// Now group the results by class - this is the only difference from getBalanceFeesReport
				$classTotals = [];

				// Get the original array of selected classes for filtering results
				$selectedClasses = [];
				if(is_array($this->input->post('classesID'))) {
					$selectedClasses = array_filter($this->input->post('classesID'), function($id) { return $id != '0'; });
				}

				foreach($students as $student) {
					if(!empty($totalAmountAndDiscount[$student->srstudentID]['amount'])) {
						$classID = $student->srclassesID;
						
						// If specific classes were selected, only include those classes
						if(!empty($selectedClasses) && !in_array($classID, $selectedClasses)) {
							continue;
						}
						
						$className = isset($classes[$classID]) ? $classes[$classID] : 'Unknown';

						if(!isset($classTotals[$classID])) {
							$classTotals[$classID] = [
								'Classname' => $className,
								'TotalFee' => 0,
								'TotalPaid' => 0,
								'TotalDiscount' => 0,
								'TotalWeaver' => 0,
								'TotalBalance' => 0
							];
						}

						// Add amounts - same logic as getBalanceFeesReport
						$classTotals[$classID]['TotalFee'] += $totalAmountAndDiscount[$student->srstudentID]['amount'];
						$classTotals[$classID]['TotalDiscount'] += $totalAmountAndDiscount[$student->srstudentID]['discount'];

						// Add payments - same logic as getBalanceFeesReport
						if(isset($totalPayment[$student->srstudentID]['payment'])) {
							$classTotals[$classID]['TotalPaid'] += $totalPayment[$student->srstudentID]['payment'];
						}

						// Add weavers - same logic as getBalanceFeesReport
						if(isset($totalweavar[$student->srstudentID]['weaver'])) {
							$classTotals[$classID]['TotalWeaver'] += $totalweavar[$student->srstudentID]['weaver'];
						}
					}
				}

				// Calculate balances and format result - sort by class name
				$result = [];
				ksort($classTotals); // Sort by class ID
				
				foreach($classTotals as $classID => $classData) {
					$totalDiscountWithWeaver = $classData['TotalDiscount'] + $classData['TotalWeaver'];
					$totalBalance = $classData['TotalFee'] - $totalDiscountWithWeaver - $classData['TotalPaid'];

					$result[] = (object)[
						'Classname' => $classData['Classname'],
						'TotalFee' => $classData['TotalFee'],
						'TotalPaid' => $classData['TotalPaid'],
						'TotalDiscount' => $totalDiscountWithWeaver,
						'TotalBalance' => $totalBalance
					];
				}

				$this->data['result'] = $result;
				$retArray['render'] = $this->load->view('report/balancefees/class_wise_results', $this->data, true);
				$retArray['status'] = TRUE;
				echo json_encode($retArray);
				exit;
			} else {
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['render'] =  $this->load->view('report/reporterror', $this->data, true);
			$retArray['status'] = TRUE;
			echo json_encode($retArray);
			exit;
		}
	}

	public function getClassWiseSummaryReport() {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';

		if(permissionChecker('balancefeesreport')) {
			if($_POST) {
				// Use EXACTLY the same logic as getBalanceFeesReport
				$schoolyearID = $this->session->userdata('defaultschoolyearID');
				$_POST['schoolyearID'] = $schoolyearID;
				$villageID = $this->input->post('villageID_multi'); 
				$classesID_multi = $this->input->post('classesID_multi'); // Array of class IDs
				$feetypeIDs = $this->input->post('feetypeID_multi');

				$this->data['villageID'] = $villageID;
				$this->data['classesID_multi'] = $classesID_multi;
				$this->data['schoolyearID'] = $schoolyearID; 
				$this->data['feetypeIDs'] = $feetypeIDs;

				// Handle multi-class filtering: get students from all selected classes or all classes if none selected
				$studentArray = [];
				if((int)$villageID) {
					$studentArray['villageID'] = $villageID;
				}
				$studentArray['srschoolyearID'] = $schoolyearID;

				$this->db->order_by('srclassesID','ASC');
				$allStudents = pluck($this->studentrelation_m->get_studentrelation_join_no_student_deletion_data($studentArray),'obj','srstudentID');
				
				// Filter students by selected classes if any are selected
				if(!empty($classesID_multi) && is_array($classesID_multi)) {
					$filteredStudents = [];
					foreach($allStudents as $studentID => $student) {
						if(in_array($student->srclassesID, $classesID_multi)) {
							$filteredStudents[$studentID] = $student;
						}
					}
					$this->data['students'] = $filteredStudents;
				} else {
					$this->data['students'] = $allStudents;
				}
				
				$this->data['classes'] = pluck($this->classes_m->general_get_classes(),'classes','classesID');
				$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');
				$this->data['feetypes'] = ($this->feetypes_m->general_get_fee_multi($feetypeIDs));

				// Prepare POST data for models to get all data (not filtered by class) 
				$postData = array('schoolyearID' => $schoolyearID);
				if((int)$villageID) {
					$postData['villageID'] = $villageID;
				}

				// SAME data retrieval logic as getBalanceFeesReport
				$this->data['totalAmountAndDiscount'] = $this->totalAmountAndDiscustomCompute($this->invoice_m->get_all_balancefees_for_report_multi($postData));
				$this->data['totalPayment'] = $this->totalPaymentAndWeaver($this->payment_m->get_order_by_payment_new_multi($schoolyearID,$feetypeIDs,0));
				$this->data['totalPayment_split'] = $this->totalPaymentAndWeaver_split($this->payment_m->get_order_by_payment_new_multi($schoolyearID,$feetypeIDs,0));
				$this->data['totalweavar'] = $this->totalWeaver($this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID'=>$schoolyearID)));

				$retArray['render'] = $this->load->view('report/balancefees/ClassWiseSummaryReport', $this->data, true);
				$retArray['status'] = TRUE;
				echo json_encode($retArray);
				exit;
			} else {
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['render'] =  $this->load->view('report/reporterror', $this->data, true);
			echo json_encode($retArray);
			exit;
		}
	}
	

}

?>