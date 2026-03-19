<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expensereport extends Admin_Controller {
// class Productpurchasereport extends Admin_Controller {

	public function __construct() {
		parent::__construct();

        $this->load->model("productwarehouse_m");
        $this->load->model("productsupplier_m");
        $this->load->model("productpurchase_m");
        $this->load->model("productpurchaseitem_m");
        $this->load->model("productpurchasepaid_m");
        $this->load->model("expense_m");
        $language = $this->session->userdata('lang');
		$this->lang->load('productpurchasereport', $language);
	}

	public function rules() {
		$rules = array(
	        array(
	                'field' => 'expensetypesID',
	                'label' => 'Expense Category',
	                'rules' => 'trim|xss_clean'
	        ),
            array(
	                'field' => 'reference_no',
	                'label' => $this->lang->line('productpurchasereport_referenceNo'),
	                'rules' => 'trim|xss_clean|callback_unique_data'
	        ),
            array(
	                'field' => 'fromdate',
	                'label' => $this->lang->line('productpurchasereport_fromdate'),
	                'rules' => 'trim|xss_clean|callback_date_valid|callback_unique_date'
	        ),
	        array(
	                'field' => 'todate',
	                'label' => $this->lang->line('productpurchasereport_todate'),
	                'rules' => 'trim|xss_clean|callback_date_valid'
	        )
		);
		return $rules;
	}

	public function send_pdf_to_mail_rules() {
		$rules = array(
            array(
                'field' => 'productsupplierID',
                'label' => $this->lang->line('productpurchasereport_supplier'),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'productwarehouseID',
                'label' => $this->lang->line('productpurchasereport_warehouse'),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'reference_no',
                'label' => $this->lang->line('productpurchasereport_referenceNo'),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'statusID',
                'label' => $this->lang->line('productpurchasereport_status'),
                'rules' => 'trim|xss_clean'
            ),
		    array(
	                'field' => 'fromdate',
	                'label' => $this->lang->line('productpurchasereport_fromdate'),
                    'rules' => 'trim|xss_clean'
	        ),
	        array(
	                'field' => 'todate',
	                'label' => $this->lang->line('productpurchasereport_todate'),
                    'rules' => 'trim|xss_clean'
	        ),
	        array(
	                'field' => 'to',
	                'label' => $this->lang->line('productpurchasereport_to'),
	                'rules' => 'trim|required|xss_clean|valid_email'
	        ),
	        array(
	                'field' => 'subject',
	                'label' => $this->lang->line('productpurchasereport_subject'),
	                'rules' => 'trim|required|xss_clean'
	        ),
	        array(
	                'field' => 'message',
	                'label' => $this->lang->line('productpurchasereport_message'),
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
        $this->data['expensetypes'] = $this->db->get('expensetypes')->result();
        $this->data["subview"] = "report/expense/ExpenseReportView";
		$this->load->view('_layout_main', $this->data);
	}

    public function unique_data($data) {
        if($data != "") {
            if($data == "0") {
                $this->form_validation->set_message('unique_data', 'The %s field value invalid.');
                return FALSE;
            }
            return TRUE;
        } 
        return TRUE;
    }

	public function getExpensereport() {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';
		// if(permissionChecker('productpurchasereport')) {
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
                    $this->data['expensetypesID'] = $this->input->post('expensetypesID');
                     $this->data['reference_no'] = !empty($this->input->post('reference_no')) ? $this->input->post('reference_no') : '0';
                     $this->data['fromdate'] = $this->input->post('fromdate');
					$this->data['todate'] = $this->input->post('todate');


                    $this->data['expensetypes'] = $this->db->get('expensetypes')->result();
                   
					$expensesArray = $this->expense_m->get_all_expenses_for_report($this->input->post());
                    // echo "<pre>";print_r($expensesArray);die;


                    $this->data['expenses'] = $expensesArray;
					$retArray['render'] = $this->load->view('report/expense/ExpenseReport',$this->data,true);
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
		// 	$retArray['render'] =  $this->load->view('report/reporterror', $this->data, true);
		// 	$retArray['status'] = TRUE;
		// 	echo json_encode($retArray);
		// 	exit;
		// }
	}

	public function pdf() {
		$expensetypesID  = htmlentities(escapeString($this->uri->segment(3)));
		$reference_no    = htmlentities(escapeString($this->uri->segment(4)));
		$fromdate = htmlentities(escapeString($this->uri->segment(5)));
		$todate   = htmlentities(escapeString($this->uri->segment(6)));
		if((int)$expensetypesID >= 0) {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$this->data['expensetypesID'] = $expensetypesID;
			$this->data['reference_no'] = $reference_no;
			$this->data['fromdate'] = ($fromdate != '') ? date('d-m-Y', $fromdate) : '';
			$this->data['todate']   = ($todate != '') ? date('d-m-Y', $todate) : '';

			$postArray = [];
			$postArray['expensetypesID'] = $expensetypesID;
			$postArray['reference_no']    = $reference_no;
			if($fromdate !='' && $todate != '') {
				$postArray['fromdate'] = date('d-m-Y',$fromdate);
				$postArray['todate']   = date('d-m-Y',$todate);
			}

			$this->data['expensetypes'] = $this->db->get('expensetypes')->result();
			$this->data['expenses'] = $this->expense_m->get_all_expenses_for_report($postArray);

			$this->reportPDF('productpurchasereport.css', $this->data, 'report/expense/ExpenseReportPDF');
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function send_pdf_to_mail() {
		$retArray['status'] = FALSE;
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

				$schoolyearID = $this->session->userdata('defaultschoolyearID');
				$this->data['expensetypesID'] = $this->input->post('expensetypesID');
				$this->data['reference_no'] = $this->input->post('reference_no');
				$this->data['fromdate'] = strtotime($this->input->post('fromdate'));
				$this->data['todate']   = strtotime($this->input->post('todate'));

				$this->data['expensetypes'] = $this->db->get('expensetypes')->result();
				$this->data['expenses'] = $this->expense_m->get_all_expenses_for_report($this->input->post());

				$this->reportSendToMail('productpurchasereport.css', $this->data, 'report/expense/ExpenseReportPDF', $to, $subject, $message);
				$retArray['status'] = TRUE;
				echo json_encode($retArray);
			}
		} else {
			$retArray['message'] = $this->lang->line('productpurchasereport_permissionmethod');
			echo json_encode($retArray);
			exit;
		}
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
		header('Content-Disposition: attachment;filename="expensereport.xlsx"');
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
		$expensetypesID  = htmlentities(escapeString($this->uri->segment(3)));
		$reference_no    = htmlentities(escapeString($this->uri->segment(4)));
		$fromdate = htmlentities(escapeString($this->uri->segment(5)));
		$todate   = htmlentities(escapeString($this->uri->segment(6)));
		
		if((int)$expensetypesID >= 0) {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$this->data['expensetypesID'] = $expensetypesID;
			$this->data['reference_no'] = $reference_no;
			$this->data['fromdate'] = $fromdate;
			$this->data['todate']   = $todate;

			$postArray = [];
			$postArray['expensetypesID'] = $expensetypesID;
			$postArray['reference_no']    = $reference_no;
			if($fromdate !='' && $todate != '') {
				$postArray['fromdate'] = date('d-m-Y',$fromdate);
				$postArray['todate']   = date('d-m-Y',$todate);
			}

			$this->data['expensetypes'] = $this->db->get('expensetypes')->result();
			$this->data['expenses'] = $this->expense_m->get_all_expenses_for_report($postArray);
			
			return $this->generateXML($this->data);
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

    private function generateXML($arrays) {
        extract($arrays);
        if(customCompute($expenses)) {
            $sheet = $this->phpspreadsheet->spreadsheet->getActiveSheet();

            if($fromdate != '' && $todate != '' ) { 
                $fdate = "From Date : ";
                $fdate .= date('d M Y',$fromdate);

                $tdate = "To Date : ";
                $tdate .= date('d M Y',$todate);

                $sheet->setCellValue('A1',$fdate);
                $sheet->setCellValue('H1',$tdate);
            } elseif($expensetypesID != 0) {
                $category = "Category : ";
                foreach($expensetypes as $expensetype) {
                    if($expensetype->expensetypesID == $expensetypesID) {
                        $category .= $expensetype->expensetypes;
                    }
                }
                $sheet->setCellValue('A1',$category);
            } elseif($reference_no != '0') {
                $reference_no_text = $this->lang->line('productpurchasereport_referenceNo')." : ". $reference_no;
                $sheet->setCellValue('A1',$reference_no_text);
            } else {
                $sheet->getRowDimension('1')->setVisible(false);
            }

            $headers = array();
            $headers['slno'] = $this->lang->line('slno');
            $headers['referenceNo'] = $this->lang->line('productpurchasereport_referenceNo');
            $headers['category'] = "Expense Category";
            $headers['date'] = "Expense Date";
            $headers['created_by'] = "Created By";
            $headers['created_date'] = "Created Date";
            $headers['note'] = "Note";
            $headers['amount'] = "Amount";

            $i=0;
            $bodys = array();
            $total_amount = 0;
            foreach($expenses as $expense) {
                $bodys[$i][] = $i+1;
                $bodys[$i][] = $expense['expense_referenceno'];
                $bodys[$i][] = $expense['expensetypes'];
                $bodys[$i][] = date('d M Y', strtotime($expense['date']));
                $bodys[$i][] = $expense['uname'];
                $bodys[$i][] = date('d M Y', strtotime($expense['create_date']));
                $bodys[$i][] = $expense['note'];
                $bodys[$i][] = number_format($expense['amount'],2);
                $total_amount += $expense['amount'];
                $i++;
            }

            $bodys[$i][] = "Grand Total";
            $bodys[$i][] = "";
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
            $sheet->getStyle('A1:H2')->applyFromArray($styleArray);

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
            $sheet->getStyle('A3:H'.$styleColumn)->applyFromArray($styleArray);

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
            $sheet->getStyle('A'.$styleColumn.':H'.$styleColumn)->applyFromArray($styleArray);

            $startmerge = "A".$styleColumn;
            $endmerge = "G".$styleColumn;
            $sheet->mergeCells("$startmerge:$endmerge");
            $sheet->mergeCells("A1:G1");

        } else {
          redirect('expensereport');
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
