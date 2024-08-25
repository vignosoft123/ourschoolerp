<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tmember extends Admin_Controller {
/*
| -----------------------------------------------------
| PRODUCT NAME: 	INILABS SCHOOL MANAGEMENT SYSTEMtransport_m
| -----------------------------------------------------
| AUTHOR:			INILABS TEAM
| -----------------------------------------------------
| EMAIL:			info@inilabs.net
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY INILABS IT
| -----------------------------------------------------
| WEBSITE:			http://inilabs.net
| -----------------------------------------------------
*/
	function __construct() {
		parent::__construct();
		$this->load->model("tmember_m");
		$this->load->model("transport_m");
		$this->load->model("student_m");
		$this->load->model("studentrelation_m");
		$this->load->model("section_m");
        $this->load->model('studentgroup_m');
        $this->load->model('subject_m');
        $this->load->model('hmember_m');
		
		$this->load->model("setting_m");
		$this->load->model('feetypes_m');
        $this->load->model("maininvoice_m"); 
		$this->load->model('invoice_m');
		$this->load->model('payment_m');
		$language = $this->session->userdata('lang');
		$this->lang->load('tmember', $language);	
	}

	public function send_mail_rules() {
		$rules = array(
			array(
				'field' => 'to',
				'label' => $this->lang->line("tmember_to"),
				'rules' => 'trim|required|max_length[60]|valid_email|xss_clean'
			),
			array(
				'field' => 'subject',
				'label' => $this->lang->line("tmember_subject"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'message',
				'label' => $this->lang->line("tmember_message"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'studentID',
				'label' => $this->lang->line("tmember_studentID"),
				'rules' => 'trim|required|max_length[10]|xss_clean|callback_unique_data'
			),
			array(
				'field' => 'classesID',
				'label' => $this->lang->line("tmember_classesID"),
				'rules' => 'trim|required|max_length[10]|xss_clean|callback_unique_data'
			)
		);
		return $rules;
	}

	public function unique_data($data) {
		if($data != '') {
			if($data == '0') {
				$this->form_validation->set_message('unique_data', 'The %s field is required.');
				return FALSE;
			}
			return TRUE;
		}
		return TRUE;
	}

	protected function rules() {
		$rules = array(
			array(
				'field' => 'transportID', 
				'label' => $this->lang->line("tmember_route_name"), 
				'rules' => 'trim|required|max_length[11]|xss_clean|callback_unique_transportID'
			),
			array(
				'field' => 'tbalance', 
				'label' => $this->lang->line("tmember_tfee"), 
				'rules' => 'trim|required|max_length[20]|xss_clean|numeric|callback_valid_number'
			)
		);
		return $rules;
	}

	public function index() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/select2/select2.js'
			)
		);

		$myProfile = false;
		if($this->session->userdata('usertypeID') == 3) {
			$id = $this->data['myclass'];
			if(!permissionChecker('tmember_view')) {
				$myProfile = true;
			}
		} else {
			$id = htmlentities(escapeString($this->uri->segment(3)));
		}

		if($this->session->userdata('usertypeID') == 3 && $myProfile) {
			$url = $id;
			$id = $this->session->userdata('loginuserID');
			$this->view($id, $url);
		} else {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			if((int)$id) {
				$this->data['set'] = $id;
				$this->data['classes'] = $this->classes_m->get_classes();
				$fetchClass = pluck($this->data['classes'], 'classesID', 'classesID');
				if(isset($fetchClass[$id])) {
					$this->data['students'] = $this->studentrelation_m->get_order_by_student(array('srclassesID' => $id, 'srschoolyearID' => $schoolyearID));

					if(customCompute($this->data['students'])) {
						$sections = $this->section_m->general_get_order_by_section(array("classesID" => $id));
						$this->data['sections'] = $sections;
						if(customCompute($sections)) {
							foreach ($sections as $key => $section) {
								$this->data['allsection'][$section->sectionID] = $this->studentrelation_m->get_order_by_student(array('srclassesID' => $id, "srsectionID" => $section->sectionID, 'srschoolyearID' => $schoolyearID));
							}
						}
					} else {
						$this->data['students'] = [];
					}
				} else {
					$this->data['students'] = [];
				}

				$this->data["subview"] = "tmember/index";
				$this->load->view('_layout_main', $this->data);
			} else {
				$this->data['set'] = $id;
				$this->data['students'] = [];
				$this->data['classes'] = $this->classes_m->get_classes();
				$this->data["subview"] = "tmember/index";
				$this->load->view('_layout_main', $this->data);
			}
		}
	}

	public function add() {
		if(($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
			$this->data['headerassets'] = array(
				'css' => array(
					'assets/select2/css/select2.css',
					'assets/select2/css/select2-bootstrap.css'
				),
				'js' => array(
					'assets/select2/select2.js'
				)
			);

			$id = htmlentities(escapeString($this->uri->segment(3)));
			$url = htmlentities(escapeString($this->uri->segment(4)));
			$this->data['transports'] = $this->transport_m->get_transport();
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			
			if((int)$id && (int)$url) {
				$student = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srschoolyearID' => $schoolyearID));
				// echo "<pre>";print_r($student);die;
				if(customCompute($student)) {
					$this->data['set'] = $url;
					if($student->transport == 0) {
						if($_POST) {
							$rules = $this->rules();
							$this->form_validation->set_rules($rules);
							if ($this->form_validation->run() == FALSE) {
								$this->data["subview"] = "tmember/add";
								$this->load->view('_layout_main', $this->data);			
							} else {
								$array = array(
									"studentID" => $student->srstudentID,
									"transportID" => $this->input->post("transportID"),
									"name" => $student->srname,
									"email" => $student->email,
									"phone" => $student->phone,
									"tbalance" => $this->input->post("tbalance"),
									"tjoindate" => date("Y-m-d")
								);
								$this->tmember_m->insert_tmember($array);
								$this->student_m->update_student(array("transport" => 1,"studentType" =>  1,'hostel' => 0), $id);
								$this->data["hmember"] = $this->hmember_m->get_single_hmember(array("studentID" => $student->srstudentID));
								if ($this->data["hmember"]) {
									$this->hmember_m->delete_hmember($this->data['hmember']->hmemberID);
								}


					//code for auto invoice generation 
						$studentID = $student->srstudentID;
						$is_auto_invoice = $this->setting_m->get_setting_where('is_student_auto_invoice');

					// if(!empty($is_auto_invoice) && ($is_auto_invoice['value'] == 1 || $is_auto_invoice['value'] == 2 ) ){
						$class_id = $student->classesID;
						$section_id = $student->sectionID;
						$year_id = $this->session->userdata('defaultschoolyearID');


 						// $pickup_id = $this->input->post("pickup_id");
						// $this->db->where('id',$pickup_id);
						// $p_res = $this->db->get('pickup_points')->row_array();
						$p_amount = $this->input->post("tbalance");

 						$fee_type_trasport = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%TRANSPORT FEE%' ")->row_array();
					 
					 

						 
						$fee_types = [
							array(
							'feetypeID' => $fee_type_trasport['feetypesID'],
							'amount' => $p_amount,
							'discount' => "",
							'subtotal' => $p_amount,
							'paidamount' => "",
							)
						]; 
						//[feetypeitems] => [{"feetypeID":"3","amount":"1","discount":"","subtotal":"1","paidamount":""},{"feetypeID":"52","amount":"2","discount":"","subtotal":"2","paidamount":""}]
					


						$json_fee_types = json_encode($fee_types);
						// echo "<pre>";print_r($json_fee_types);die;
						$invoice_data = array(
							'classesID' => $class_id,
							'sectionID' =>$section_id ,
							'studentID' => $studentID,
							'date' => date('d-m-Y'),
							'statusID' => 0,
							'payment_method' => 0,
							// 'feetypeitems' => '['.$json_fee_types.']',
							'feetypeitems' => $json_fee_types,
							'totalsubtotal' => $p_amount,
							'totalpaidamount' => 0,
							'editID' => 0,
						);

						$invoice_error = $this->saveinvoice($invoice_data);
						$this->db->update('student',array('invoice_error'=>$invoice_error),array('studentID'=>$studentID));

					// }
				
								
								$this->session->set_flashdata('success', $this->lang->line('menu_success'));
								redirect(base_url("tmember/index/$url"));
							}
						} else {
							$this->data["subview"] = "tmember/add";
							$this->load->view('_layout_main', $this->data);
						}
					} else {
						$this->data["subview"] = "error";
						$this->load->view('_layout_main', $this->data);
					}
				} else {
					$this->data["subview"] = "error";
					$this->load->view('_layout_main', $this->data);
				}
			} else {
				$this->data["subview"] = "error";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function edit() {
		if(($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
			$this->data['headerassets'] = array(
				'css' => array(
					'assets/select2/css/select2.css',
					'assets/select2/css/select2-bootstrap.css'
				),
				'js' => array(
					'assets/select2/select2.js'
				)
			);
			$id = htmlentities(escapeString($this->uri->segment(3)));
			$url = htmlentities(escapeString($this->uri->segment(4)));
			$schoolyearID = $this->session->userdata('defaultschoolyearID');

			if((int)$id && (int)$url) {
				$fetchClass = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
				if(isset($fetchClass[$url])) {
					$student = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srschoolyearID' => $schoolyearID));
					if(customCompute($student)) {
						$this->data['tmember'] = $this->tmember_m->get_single_tmember(array("studentID" =>$id));
						if(customCompute($this->data['tmember'])) {
							$this->data['transports'] = $this->transport_m->get_transport();
							$this->data['set'] = $url;
							if($student->transport == 1) {
								if($_POST) {
									$rules = $this->rules();
									$this->form_validation->set_rules($rules);
									if ($this->form_validation->run() == FALSE) { 
										$this->data["subview"] = "tmember/edit";
										$this->load->view('_layout_main', $this->data);
									} else {
										$array = array(
											"transportID" => $this->input->post("transportID"),
											"tbalance" => $this->input->post("tbalance")
										);
										$this->tmember_m->update_tmember($array, $this->data['tmember']->tmemberID);
										$this->session->set_flashdata('success', $this->lang->line('menu_success'));
										redirect(base_url("tmember/index/$url"));	
									}
								} else {
									$this->data["subview"] = "tmember/edit";
									$this->load->view('_layout_main', $this->data);
								}
							} else {
								$this->data["subview"] = "error";
								$this->load->view('_layout_main', $this->data);
							}
						} else {
							$this->data["subview"] = "error";
							$this->load->view('_layout_main', $this->data);
						}
					} else {
						$this->data["subview"] = "error";
						$this->load->view('_layout_main', $this->data);
					}
				} else {
					$this->data["subview"] = "error";
					$this->load->view('_layout_main', $this->data);
				}
			} else {
				$this->data["subview"] = "error";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function delete() {
		if(($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
			$id = htmlentities(escapeString($this->uri->segment(3)));
			$url = htmlentities(escapeString($this->uri->segment(4)));
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			if((int)$id && (int)$url) {
				$fetchClass = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
				if(isset($fetchClass[$url])) {
					$student = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srschoolyearID' => $schoolyearID));
					if($student) {
						$this->tmember_m->delete_tmember_sID($id);
						$this->student_m->update_student(array("transport" => 0), $id);
						$this->session->set_flashdata('success', $this->lang->line('menu_success'));
						redirect(base_url("tmember/index/$url"));
					} else {
						redirect(base_url("tmember/index"));
					}
				} else {
					redirect(base_url("tmember/index"));
				}
			} else {
				redirect(base_url("tmember/index"));
			}
		} else {
			redirect(base_url("tmember/index"));
		}
	}

	public function view($id = null, $url = null) {
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		if((int)$id && (int)$url) {
			$fetchClass = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
			if(isset($fetchClass[$url])) {
				$this->data['set'] = $url;
				$this->data['student'] = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srschoolyearID' => $schoolyearID), true);
				$this->data['usertypes'] = pluck($this->usertype_m->get_usertype(),'usertype','usertypeID');
				if(customCompute($this->data['student'])) {
					$this->data["classes"] = $this->classes_m->get_classes($this->data['student']->srclassesID);
					$this->data['tmember'] = $this->tmember_m->get_single_tmember(array('studentID' => $id));
					$this->data["section"] = $this->section_m->general_get_section($this->data['student']->srsectionID);
					if(customCompute($this->data['tmember'])) {
						$this->data['transport'] = $this->transport_m->get_transport($this->data['tmember']->transportID);
						$this->data["subview"] = "tmember/getView";
						$this->load->view('_layout_main', $this->data);
					} else {
						$this->data['transport'] = [];
						$this->data["subview"] = "tmember/getView";
						$this->load->view('_layout_main', $this->data);
					}
				} else {
					$this->data["subview"] = "error";
					$this->load->view('_layout_main', $this->data);
				}
			} else {
				$this->data["subview"] = "error";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function print_preview() {
		if(permissionChecker('tmember_view') || (($this->session->userdata('usertypeID') == 3) && permissionChecker('tmember') && ($this->session->userdata('loginuserID') == htmlentities(escapeString($this->uri->segment(3)))))) {
			$id = htmlentities(escapeString($this->uri->segment(3)));
			$url = htmlentities(escapeString($this->uri->segment(4)));
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			if((int)$id && (int)$url) {
				$fetchClass = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
				if(isset($fetchClass[$url])) {
					$this->data['set'] = $url;
					$this->data['student'] = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srschoolyearID' => $schoolyearID));
					$this->data['usertypes'] = pluck($this->usertype_m->get_usertype(),'usertype','usertypeID');
					if(customCompute($this->data['student'])) {
						$this->data["classes"] = $this->classes_m->get_classes($this->data['student']->srclassesID);
						$this->data["section"] = $this->section_m->general_get_section($this->data['student']->srsectionID);
						$this->data['tmember'] = $this->tmember_m->get_single_tmember(array('studentID' => $id));
						if(customCompute($this->data['tmember'])) {
							$this->data['transport'] = $this->transport_m->get_transport($this->data['tmember']->transportID);
							$this->reportPDF('tmembermodule.css',$this->data, 'tmember/print_preview');
						} else {
							$this->data['transport'] = [];
							$this->reportPDF('tmembermodule.css',$this->data, 'tmember/print_preview');
						}
					} else {
						$this->data["subview"] = "error";
						$this->load->view('_layout_main', $this->data);
					}
				} else {
					$this->data["subview"] = "error";
					$this->load->view('_layout_main', $this->data);
				}
			} else {
				$this->data["subview"] = "error";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "errorpermission";
			$this->load->view('_layout_main', $this->data);
		}
	}
	
	public function send_mail() {
		$retArray['status'] = FALSE;
		$retArray['message'] = '';
		if(permissionChecker('tmember_view') || (($this->session->userdata('usertypeID') == 3) && permissionChecker('tmember') && ($this->session->userdata('loginuserID') == $this->input->post('studentID')))) {
			if($_POST) {
				$rules = $this->send_mail_rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
				    echo json_encode($retArray);
				    exit;
				} else {
					$id = $this->input->post('studentID');
					$url = $this->input->post('classesID');
					if ((int)$id && (int)$url) {
						$fetchClass = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
						if(isset($fetchClass[$url])) {
							$schoolyearID = $this->session->userdata('defaultschoolyearID');
							$this->data['student'] = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srschoolyearID' => $schoolyearID));
							$this->data['usertypes'] = pluck($this->usertype_m->get_usertype(),'usertype','usertypeID');
							if(customCompute($this->data["student"])) {
								$this->data["classes"] = $this->classes_m->get_classes($this->data['student']->srclassesID);
								$this->data["section"] = $this->section_m->general_get_section($this->data['student']->srsectionID);
								$this->data['tmember'] = $this->tmember_m->get_single_tmember(array('studentID' => $id));
								if(customCompute($this->data['tmember'])) {
									$this->data['transport'] = $this->transport_m->get_transport($this->data['tmember']->transportID);
									$email = $this->input->post('to');
									$subject = $this->input->post('subject');
									$message = $this->input->post('message');
									$this->reportSendToMail('tmembermodule.css',$this->data, 'tmember/print_preview', $email, $subject, $message);
									$retArray['message'] = "Success";
									$retArray['status'] = TRUE;
									echo json_encode($retArray);
								} else {
									$this->data['transport'] = [];
									$email = $this->input->post('to');
									$subject = $this->input->post('subject');
									$message = $this->input->post('message');
									$this->reportSendToMail('tmembermodule.css',$this->data, 'tmember/print_preview', $email, $subject, $message);
									$retArray['message'] = "Success";
									$retArray['status'] = TRUE;
									echo json_encode($retArray);
								}
							} else {
								$retArray['message'] = $this->lang->line('student_data_not_found');
								echo json_encode($retArray);
								exit;
							}
						} else {
							$retArray['message'] = $this->lang->line('student_data_not_found');
							echo json_encode($retArray);
							exit;
						}
					} else {
						$retArray['message'] = $this->lang->line('student_data_not_found');
						echo json_encode($retArray);
						exit;
					}
				}
			} else {
				$retArray['message'] = $this->lang->line('tmember_permissionmethod');
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['message'] = $this->lang->line('tmember_permission');
			echo json_encode($retArray);
			exit;
		}
	}

	public function student_list() {
		$classID = $this->input->post('id');
		if((int)$classID) {
			$string = base_url("tmember/index/$classID");
			echo $string;
		} else {
			redirect(base_url("tmember/index"));
		}
	}

	public function transport_fare() {
		$pickup_id = $this->input->post('id');
		if((int)$pickup_id) {
			// $string = $this->transport_m->get_transport($transportID);
			// $string = $this->db->query('SELECT *FROM `transport`WHERE `transportID` ='.$transportID)->row();
			$string = $this->db->query('SELECT fare FROM `pickup_points`WHERE `id` ='.$pickup_id)->row();
			// echo $this->db->last_query();die;
			echo $string->fare;
		} else {
			echo '';
		}
	}

	public function unique_transportID() {
		if($this->input->post('transportID') == 0) {
			$this->form_validation->set_message("unique_transportID", "The %s field is required");
	     	return FALSE;
		}
		return TRUE;
	}

	public function valid_number() {
		if($this->input->post('tbalance') && $this->input->post('tbalance') < 0) {
			$this->form_validation->set_message("valid_number", "%s is invalid number");
			return FALSE;
		}
		return TRUE;
	}
public function pickup_points(){
	$route_id = $_POST['id'];
	$result = $this->db->query("select * from pickup_points where route_id = '".$route_id."'")->result_array();
	$html = "<option value=''>Select</option>";
	foreach($result as $res){
		$html .=  '<option value="'.$res["id"].'" >'.$res["pickupPoint"].'</option>';

	}
	echo $html;
}

public function saveinvoice($inv_data)
{
	$_POST = $inv_data;
	// echo "<pre>";print_r($_POST);die;
	$maininvoiceID      = 0;
	$retArray['status'] = FALSE;
	if(($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1) || ($this->session->userdata('defaultschoolyearID') == 5)) {
		 	if($_POST) {
				//$rules = $this->rules($this->input->post('statusID'));
				// $this->form_validation->set_rules($rules);
				// if($this->form_validation->run() == FALSE) {
				// 	$retArray['error']  = $this->form_validation->error_array();
				// 	$retArray['status'] = FALSE;
				// 	return json_encode($retArray);
				// 	// exit;
				// } else {
					$invoiceMainArray     = [];
					$globalPaymentArray   = [];
					$invoiceArray         = [];
					$paymentArray         = [];
					$paymentHistoryArray  = [];
					$studentArray         = [];
					$globalPaymentIDArray = [];
					$feetype              = pluck($this->feetypes_m->get_feetypes(), 'feetypes', 'feetypesID');
					$feetypeitems         = json_decode($this->input->post('feetypeitems'));
					// echo "<pre>";print_r($feetypeitems );die;
					$schoolyearID         = $this->session->userdata('defaultschoolyearID');

					$studentID = $this->input->post('studentID');
					$classesID = $this->input->post('classesID');
					$sectionID = $this->input->post('sectionID');
					if(((int)$studentID || $studentID == 0) && (int)($classesID)) {
						if($studentID == 0) {
							$getstudents = $this->studentrelation_m->get_order_by_student([
								"srclassesID"    => $classesID,
								'srschoolyearID' => $schoolyearID,
								"srsectionID" => $sectionID,
							]);
						  
						} else {
							$getstudents = $this->studentrelation_m->get_order_by_student([
								"srclassesID"    => $classesID,
								'srstudentID'    => $studentID,
								'srschoolyearID' => $schoolyearID,
								"srsectionID" => $sectionID,
							]);
						   
						}

						if(customCompute($getstudents)) {
							$paymentStatus = 0;
							if($this->input->post('statusID') !== '0') {
								if((float)$this->input->post('totalsubtotal') == (float)0) {
									$paymentStatus = 2;
								} else {
									if((float)$this->input->post('totalpaidamount') > (float)0) {
										if((float)$this->input->post('totalsubtotal') == (float)$this->input->post('totalpaidamount')) {
											$paymentStatus = 2;
										} else {
											$paymentStatus = 1;
										}
									}
								}
							}

							$clearancetype = 'unpaid';
							if($paymentStatus == 0) {
								$clearancetype = 'unpaid';
							} elseif($paymentStatus == 1) {
								$clearancetype = 'partial';
							} elseif($paymentStatus == 2) {
								$clearancetype = 'paid';
							}

							foreach($getstudents as $key => $getstudent) {
								$invoiceMainArray[] = [
									'maininvoiceschoolyearID' => $schoolyearID,
									'maininvoiceclassesID'    => $this->input->post('classesID'),
									'maininvoicesectionID'    => $this->input->post('sectionID'),
									'maininvoicestudentID'    => $getstudent->srstudentID,
									'maininvoicestatus'       => (($this->input->post('statusID') !== '0') ? (((float)$this->input->post('totalsubtotal') == (float)0) ? 2 : (((float)$this->input->post('totalpaidamount') > (float)0) ? ((float)$this->input->post('totalsubtotal') == (float)$this->input->post('totalpaidamount') ? 2 : 1) : 0)) : 0),
									'maininvoiceuserID'       => $this->session->userdata('loginuserID'),
									'maininvoiceusertypeID'   => $this->session->userdata('usertypeID'),
									'maininvoiceuname'        => $this->session->userdata('name'),
									'maininvoicedate'         => date("Y-m-d", strtotime($this->input->post("date"))),
									'maininvoicecreate_date'  => date('Y-m-d'),
									'maininvoiceday'          => date('d'),
									'maininvoicemonth'        => date('m'),
									'maininvoiceyear'         => date('Y'),
									'maininvoicedeleted_at'   => 1
								];

								$globalPaymentArray[] = [
									'classesID'          => $getstudent->srclassesID,
									'sectionID'          => $getstudent->srsectionID,
									'studentID'          => $getstudent->srstudentID,
									'clearancetype'      => $clearancetype,
									'invoicename'        => $getstudent->srregisterNO . '-' . $getstudent->srname,
									'invoicedescription' => '',
									'paymentyear'        => date('Y'),
									'schoolyearID'       => $schoolyearID,
								];

								$studentArray[] = $getstudent->srstudentID;
							}

							if(customCompute($invoiceMainArray)) {
								$count   = customCompute($invoiceMainArray);
								$firstID = $this->maininvoice_m->insert_batch_maininvoice($invoiceMainArray);

								$lastID = $firstID + ($count - 1);

								if($lastID >= $firstID) {
									$j = 0;
									for($i = $firstID; $i <= $lastID; $i++) {
										if(customCompute($feetypeitems)) {
											foreach($feetypeitems as $feetypeitem) {
												$invoiceArray[] = [
													'schoolyearID'  => $invoiceMainArray[$j]['maininvoiceschoolyearID'],
													'classesID'     => $invoiceMainArray[$j]['maininvoiceclassesID'],
													'studentID'     => $invoiceMainArray[$j]['maininvoicestudentID'],
													'feetypeID'     => isset($feetypeitem->feetypeID) ? $feetypeitem->feetypeID : 0,
													'feetype'       => isset($feetype[$feetypeitem->feetypeID]) ? $feetype[$feetypeitem->feetypeID] : '',
													'amount'        => isset($feetypeitem->amount) ? $feetypeitem->amount : 0,
													'discount'      => (isset($feetypeitem->discount) ? (($feetypeitem->discount == '') ? 0 : $feetypeitem->discount) : 0),
													'paidstatus'    => ($this->input->post('statusID') !== '0') ? (((float)$feetypeitem->paidamount > (float)0) ? (((float)$feetypeitem->subtotal == (float)$feetypeitem->paidamount) ? 2 : 1) : 0) : 0,
													'userID'        => $invoiceMainArray[$j]['maininvoiceuserID'],
													'usertypeID'    => $invoiceMainArray[$j]['maininvoiceusertypeID'],
													'uname'         => $invoiceMainArray[$j]['maininvoiceuname'],
													'date'          => $invoiceMainArray[$j]['maininvoicedate'],
													'create_date'   => $invoiceMainArray[$j]['maininvoicecreate_date'],
													'day'           => $invoiceMainArray[$j]['maininvoiceday'],
													'month'         => $invoiceMainArray[$j]['maininvoicemonth'],
													'year'          => $invoiceMainArray[$j]['maininvoiceyear'],
													'deleted_at'    => $invoiceMainArray[$j]['maininvoicedeleted_at'],
													'maininvoiceID' => $i
												];
										
												$paymentHistoryArray[] = [
													'paymenttype'   => ucfirst($this->input->post('payment_method')),
													'paymentamount' => $feetypeitem->paidamount
												];
											}
										}
										$j++;
									}
								}
							}

							$paymentInserStatus = 0;
							if($this->input->post('statusID') == !'0') {
								if($this->input->post('totalpaidamount') > 0) {
									if((float)$this->input->post('totalsubtotal') == (float)$this->input->post('totalpaidamount')) {
										$paymentInserStatus = 2;
									} else {
										$paymentInserStatus = 1;
									}
								} else {
									$paymentInserStatus = 0;
								}
							}

							$invoicefirstID = $this->invoice_m->insert_batch_invoice($invoiceArray);

							$invoiceSubtotalStatus = 1;
							if((float)$this->input->post('totalsubtotal') == (float)0) {
								$invoiceSubtotalStatus = 0;
							}

							if($paymentInserStatus && $invoiceSubtotalStatus) {
								if(customCompute($invoiceArray)) {
									$invoicecount   = customCompute($invoiceArray);
									$invoicefirstID = $invoicefirstID;
									$invoicelastID  = $invoicefirstID + ($invoicecount - 1);

									$globalcount   = customCompute($globalPaymentArray);
									$globalfirstID = $this->globalpayment_m->insert_batch_globalpayment($globalPaymentArray);
									$globallastID  = $globalfirstID + ($globalcount - 1);

									if(customCompute($studentArray)) {
										$studentcount = customCompute($getstudents);
										for($n = 0; $n <= ($studentcount - 1); $n++) {
											$globalPaymentIDArray[$studentArray[$n]] = $globalfirstID;
											$globalfirstID++;
										}
									}

									if($invoicelastID >= $invoicefirstID) {
										$k = 0;
										for($i = $invoicefirstID; $i <= $invoicelastID; $i++) {
											$paymentArray[] = [
												'schoolyearID'    => $invoiceArray[$k]['schoolyearID'],
												'invoiceID'       => $i,
												'studentID'       => $invoiceArray[$k]['studentID'],
												'paymentamount'   => isset($paymentHistoryArray[$k]['paymentamount']) ? (($paymentHistoryArray[$k]['paymentamount'] == "") ? NULL : $paymentHistoryArray[$k]['paymentamount']) : 0,
												'paymenttype'     => ucfirst($this->input->post('payment_method')),
												'paymentdate'     => date('Y-m-d'),
												'paymentday'      => date('d'),
												'paymentmonth'    => date('m'),
												'paymentyear'     => date('Y'),
												'userID'          => $invoiceArray[$k]['userID'],
												'usertypeID'      => $invoiceArray[$k]['usertypeID'],
												'uname'           => $invoiceArray[$k]['uname'],
												'transactionID'   => 'CASHANDCHEQUE' . random19(),
												'globalpaymentID' => isset($globalPaymentIDArray[$invoiceArray[$k]['studentID']]) ? $globalPaymentIDArray[$invoiceArray[$k]['studentID']] : 0
											];
											$k++;
										}
									}

									if(customCompute($paymentArray)) {
										$this->payment_m->insert_batch_payment($paymentArray);
									}
								}
							}

							$this->session->set_flashdata('success', $this->lang->line('menu_success'));
							$retArray['status']  = TRUE;
							$retArray['message'] = 'Success';
							return json_encode($retArray);
							// exit;
						} else {
							$retArray['error'] = ['student' => 'Student not found.'];
							return json_encode($retArray);
							// exit;
						}
					} else {
						$retArray['error'] = ['classstudent' => 'Class and Student not found.'];
						return json_encode($retArray);
						// exit;
					}
				//}
			} else {
				$retArray['error'] = ['posttype' => 'Post type is required.'];
				return json_encode($retArray);
				
			}
		 
	} else {
		$retArray['error'] = ['permission' => 'Permission Denied.'];
		echo json_encode($retArray);
		exit;
	}
}

}