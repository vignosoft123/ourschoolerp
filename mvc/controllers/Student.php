<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Student extends Admin_Controller
{
	/*
| -----------------------------------------------------
| PRODUCT NAME: 	INILABS SCHOOL MANAGEMENT SYSTEM
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
	public $upload_data = [];
	public $mailandsmstemplate_m;
	public $mailandsmstemplatetag_m;
	public $mailandsms_m;
	public $Whatsapp_m;

	function __construct()
	{
		// PHP 8.x outputs E_DEPRECATED/E_NOTICE/E_WARNING as HTML before redirect() fires.
		// Suppress soft errors so CI's error handler stays silent; fatal errors still reported.
		error_reporting(error_reporting() & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
		parent::__construct();
		$this->load->model("student_m");
		$this->load->model("parents_m");
		$this->load->model("section_m");
		$this->load->model("classes_m");
		$this->load->model("setting_m");
		$this->load->model('studentrelation_m');
		$this->load->model('studentgroup_m');
		$this->load->model('studentextend_m');
		$this->load->model('subject_m');
		$this->load->model('routine_m');
		$this->load->model('teacher_m');
		$this->load->model('subjectattendance_m');
		$this->load->model('sattendance_m');
		$this->load->model('invoice_m');
		$this->load->model('payment_m');
		$this->load->model('weaverandfine_m');
		$this->load->model('feetypes_m');
		$this->load->model('exam_m');
		$this->load->model('grade_m');
		$this->load->model('markpercentage_m');
		$this->load->model('markrelation_m');
		$this->load->model('mark_m');
		$this->load->model('document_m');
		$this->load->model('leaveapplication_m');
		$this->load->model('marksetting_m');
		$this->load->model('village_m');
		$this->load->model('Setting_m');
		$this->load->model('transport_m');
		$this->load->model("hostel_m");
		$this->load->model("tmember_m");
		$this->load->model("category_m");
		$this->load->model("hmember_m");
 
        $this->load->model("globalpayment_m");
        $this->load->model("maininvoice_m"); 
        $this->load->model('user_m'); 
        $this->load->model("payment_settings_m");  
        $this->load->model('payment_gateway_m');
        $this->load->model('payment_gateway_option_m');
        $this->load->model('studentsiblings_m');
        $this->load->model('mailandsmstemplate_m');
        $this->load->model('mailandsmstemplatetag_m');
        $this->load->model('mailandsms_m');
        $this->load->model('Whatsapp_m');


		$language = $this->session->userdata('lang');
		$this->lang->load('student', $language);
		$this->lang->load('tmember', $language);
		$this->lang->load('hmember', $language);

		
		$this->load->library('msg91');
	}

	public function send_mail_rules()
	{
		$rules = array(
			array(
				'field' => 'to',
				'label' => $this->lang->line("student_to"),
				'rules' => 'trim|required|max_length[60]|valid_email|xss_clean'
			),
			array(
				'field' => 'subject',
				'label' => $this->lang->line("student_subject"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'message',
				'label' => $this->lang->line("student_message"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'studentID',
				'label' => $this->lang->line("student_studentID"),
				'rules' => 'trim|required|max_length[10]|xss_clean|callback_unique_data'
			),
			array(
				'field' => 'classesID',
				'label' => $this->lang->line("student_classesID"),
				'rules' => 'trim|required|max_length[10]|xss_clean|callback_unique_data'
			)
		);
		return $rules;
	}

	private function getView($id, $url)
	{
		$schoolyearID = $this->session->userdata('defaultschoolyearID');

		$fetchClasses = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
		if (isset($fetchClasses[$url])) {
			if ((int)$id && (int)$url) {

				// $studentInfo = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srclassesID' => $url, 'srschoolyearID' => $schoolyearID), TRUE);

				$studentInfo = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srschoolyearID' => $schoolyearID), TRUE);

				$this->data['all_classes'] = pluck($this->classes_m->get_classes(), 'classes', 'classesID');

				$this->pluckInfo();
				$this->basicInfo($studentInfo);
				$this->typeInfo($studentInfo);
				$this->parentInfo($studentInfo);
				$this->routineInfo($studentInfo);
				$this->attendanceInfo($studentInfo);
				$this->markInfo($studentInfo);
				$this->invoiceInfo($studentInfo);
				$this->paymentInfo($studentInfo);
				$this->documentInfo($studentInfo);



				if (customCompute($studentInfo)) {
					$this->data['set']     = $url;
					$this->data['leaveapplications'] = $this->leave_applications_date_list_by_user_and_schoolyear($id, $schoolyearID, $studentInfo->usertypeID);
					
					$this->data['refered_by_name'] = '';
					if ($studentInfo->refered_by) {
						$refered_by = explode('-', $studentInfo->refered_by, 2);
						if(count($refered_by) == 2) {
							$type = $refered_by[0];
							$typeID = $refered_by[1];
							if($type == 'teacher') {
								$teacher = $this->teacher_m->get_single_teacher(array('teacherID' => $typeID));
								$this->data['refered_by_name'] = customCompute($teacher) ? $teacher->name . " [Teacher]" : '';
							} elseif($type == 'user') {
								$user = $this->user_m->get_single_user(array('userID' => $typeID));
								$this->data['refered_by_name'] = customCompute($user) ? $user->name . " [User]" : '';
							} elseif($type == 'others') {
								$this->data['refered_by_name'] = htmlspecialchars($typeID) . ' [Other]';
							}
						}
					}
					$this->data['siblings'] = $this->studentsiblings_m->get_siblings_by_student($id);

					$this->data["subview"] = "student/getView";
					$this->load->view('_layout_main', $this->data);
				} else {
					$this->data["subview"] = "error";
					$this->load->view('_layout_main', $this->data);
				}
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	private function allPaymentByInvoice($payments)
	{
		$retPaymentArr = [];
		if ($payments) {
			foreach ($payments as $payment) {
				if (isset($retPaymentArr[$payment->invoiceID])) {
					$retPaymentArr[$payment->invoiceID] += $payment->paymentamount;
				} else {
					$retPaymentArr[$payment->invoiceID] = $payment->paymentamount;
				}
			}
		}
		return $retPaymentArr;
	}

	private function allWeaverAndFineByInvoice($weaverandfines)
	{
		$retWeaverAndFineArr = [];
		if ($weaverandfines) {
			foreach ($weaverandfines as $weaverandfine) {
				if (isset($retWeaverAndFineArr[$weaverandfine->invoiceID]['weaver'])) {
					$retWeaverAndFineArr[$weaverandfine->invoiceID]['weaver'] += $weaverandfine->weaver;
				} else {
					$retWeaverAndFineArr[$weaverandfine->invoiceID]['weaver'] = $weaverandfine->weaver;
				}

				if (isset($retWeaverAndFineArr[$weaverandfine->invoiceID]['fine'])) {
					$retWeaverAndFineArr[$weaverandfine->invoiceID]['fine'] += $weaverandfine->fine;
				} else {
					$retWeaverAndFineArr[$weaverandfine->invoiceID]['fine'] = $weaverandfine->fine;
				}
			}
		}
		return $retWeaverAndFineArr;
	}


	private function getMark($studentID, $classesID)
	{
		if ((int)$studentID && (int)$classesID) {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$student      = $this->studentrelation_m->get_single_student(array('srstudentID' => $studentID, 'srclassesID' => $classesID, 'srschoolyearID' => $schoolyearID));
			$classes      = $this->classes_m->get_single_classes(array('classesID' => $classesID));

			if (customCompute($student) && customCompute($classes)) {
				$queryArray = [
					'classesID'    => $student->srclassesID,
					'sectionID'    => $student->srsectionID,
					'studentID'    => $student->srstudentID,
					'schoolyearID' => $schoolyearID,
				];

				$exams             = pluck($this->exam_m->get_exam(), 'exam', 'examID');
				// echo "<pre>";print_r($subjects);die;
				$grades            = $this->grade_m->get_grade();
				$marks             = $this->mark_m->student_all_mark_array($queryArray);
				$markpercentages   = $this->markpercentage_m->get_markpercentage();

				$subjects          = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID));
				//  echo "<pre>";print_r($subjects);die;

				$subjectArr        = [];
				$optionalsubjectArr = [];
				if (customCompute($subjects)) {
					foreach ($subjects as $subject) {
						if ($subject->type == 0) {
							$optionalsubjectArr[$subject->subjectID] = $subject->subjectID;
						}
						$subjectArr[$subject->subjectID] = $subject;
					}
				}

				$retMark = [];
				if (customCompute($marks)) {
					foreach ($marks as $mark) {
						$retMark[$mark->examID][$mark->subjectID][$mark->markpercentageID] = $mark->mark;
					}
				}

				$allStudentMarks = $this->mark_m->student_all_mark_array(array('classesID' => $classesID, 'schoolyearID' => $schoolyearID));
				$highestMarks    = [];
				foreach ($allStudentMarks as $value) {
					if (!isset($highestMarks[$value->examID][$value->subjectID][$value->markpercentageID])) {
						$highestMarks[$value->examID][$value->subjectID][$value->markpercentageID] = -1;
					}
					$highestMarks[$value->examID][$value->subjectID][$value->markpercentageID] = max($value->mark, $highestMarks[$value->examID][$value->subjectID][$value->markpercentageID]);
				}
				$marksettings  = $this->marksetting_m->get_marksetting_markpercentages();

				$this->data['settingmarktypeID'] = $this->data['siteinfos']->marktypeID;
				$this->data['subjects']          = $subjectArr;
				$this->data['all_subjects']          = $subjects;
				$this->data['exams']             = $exams;
				$this->data['grades']            = $grades;
				$this->data['markpercentages']   = pluck($markpercentages, 'obj', 'markpercentageID');
				$this->data['optionalsubjectArr'] = $optionalsubjectArr;
				$this->data['marks']             = $retMark;
				$this->data['highestmarks']      = $highestMarks;
				$this->data['marksettings']      = isset($marksettings[$classesID]) ? $marksettings[$classesID] : [];
			} else {
				$this->data['settingmarktypeID'] = 0;
				$this->data['subjects']          = [];
				$this->data['exams']             = [];
				$this->data['grades']            = [];
				$this->data['markpercentages']   = [];
				$this->data['optionalsubjectArr'] = [];
				$this->data['marks']             = [];
				$this->data['highestmarks']      = [];
				$this->data['marksettings']      = [];
			}
		} else {
			$this->data['settingmarktypeID'] = 0;
			$this->data['subjects']          = [];
			$this->data['exams']             = [];
			$this->data['grades']            = [];
			$this->data['markpercentages']   = [];
			$this->data['optionalsubjectArr'] = [];
			$this->data['marks']             = [];
			$this->data['highestmarks']      = [];
			$this->data['marksettings']      = [];
		}
	}

	private function pluckInfo()
	{
		$this->data['subjects'] = pluck($this->subject_m->general_get_subject(), 'subject', 'subjectID');
		$this->data['teachers'] = pluck($this->teacher_m->get_teacher(), 'name', 'teacherID');
		$this->data['feetypes'] = pluck($this->feetypes_m->get_feetypes(), 'feetypes', 'feetypesID');
	}

	private function basicInfo($studentInfo)
	{
		// echo "<pre>";print_r($studentInfo);die;
		if (customCompute($studentInfo)) {
			$this->data['profile'] = $studentInfo;
			$this->data['usertype'] = $this->usertype_m->get_single_usertype(array('usertypeID' => 3));
			$this->data['class'] = $this->classes_m->get_single_classes(array('classesID' => $studentInfo->srclassesID));
			$this->data['section'] = $this->section_m->general_get_single_section(array('sectionID' => $studentInfo->srsectionID));
			$this->data['group'] = $this->studentgroup_m->get_single_studentgroup(array('studentgroupID' => $studentInfo->srstudentgroupID));
			$this->data['optionalsubject'] = $this->subject_m->general_get_single_subject(array('subjectID' => $studentInfo->sroptionalsubjectID));
		} else {
			$this->data['profile'] = [];
		}
	}

	private function typeInfo($studentInfo)
	{
		if (customCompute($studentInfo)) {
			$this->data['studntTransportDetails'] =  $transPrortDetails = $this->tmember_m->get_single_tmember(array('studentID' => $studentInfo->srstudentID));
			$this->data['studntHostelDetails'] = $hostelDetails = $this->hmember_m->get_single_hmember(array('studentID' => $studentInfo->srstudentID));
			if ($this->data['studntTransportDetails']) {
				$this->data['transports'] = $this->transport_m->get_transport($transPrortDetails->transportID);
			}
			if ($this->data['studntHostelDetails']) {
				$this->data["hostels"] = $this->hostel_m->get_hostel($hostelDetails->hostelID);
			}
		} else {
			$this->data['studntTransportDetails'] = [];
			$this->data['studntHostelDetails'] = [];
		}
	}

	private function parentInfo($studentInfo)
	{
		if (customCompute($studentInfo)) {
			$this->data['parents'] = $this->parents_m->get_single_parents(array('parentsID' => $studentInfo->parentID));
		} else {
			$this->data['parents'] = [];
		}
	}

	private function routineInfo($studentInfo)
	{
		$settingWeekends = [];
		if ($this->data['siteinfos']->weekends != '') {
			$settingWeekends = explode(',', $this->data['siteinfos']->weekends);
		}
		$this->data['routineweekends'] = $settingWeekends;

		$this->data['routines'] = [];
		if (customCompute($studentInfo)) {
			$schoolyearID           = $this->session->userdata('defaultschoolyearID');
			$this->data['routines'] = pluck_multi_array($this->routine_m->get_order_by_routine(array('classesID' => $studentInfo->srclassesID, 'sectionID' => $studentInfo->srsectionID, 'schoolyearID' => $schoolyearID)), 'obj', 'day');
		}
	}

	private function attendanceInfo($studentInfo)
	{
		$this->data['holidays'] =  $this->getHolidaysSession();
		$this->data['getWeekendDays'] =  $this->getWeekendDaysSession();
		if (customCompute($studentInfo)) {
			$this->data['setting'] = $this->setting_m->get_setting();
			if ($this->data['setting']->attendance == "subject") {
				$this->data["attendancesubjects"] = $this->subject_m->general_get_order_by_subject(array("classesID" => $studentInfo->srclassesID));
			}

			if ($this->data['setting']->attendance == "subject") {
				$attendances = $this->subjectattendance_m->get_order_by_sub_attendance(array("studentID" => $studentInfo->srstudentID, "classesID" => $studentInfo->srclassesID));
				$this->data['attendances_subjectwisess'] = pluck_multi_array_key($attendances, 'obj', 'subjectID', 'monthyear');
			} else {
				$attendances = $this->sattendance_m->get_order_by_attendance(array("studentID" => $studentInfo->srstudentID, "classesID" => $studentInfo->srclassesID));
				$this->data['attendancesArray'] = pluck($attendances, 'obj', 'monthyear');
			}
		} else {
			$this->data['setting'] = [];
			$this->data['attendancesubjects'] = [];
			$this->data['attendances_subjectwisess'] = [];
			$this->data['attendancesArray'] = [];
		}
	}

	private function markInfo($studentInfo)
	{
		if (customCompute($studentInfo)) {
			$this->getMark($studentInfo->srstudentID, $studentInfo->srclassesID);
		} else {
			$this->data['set'] 				= [];
			$this->data["exams"] 			= [];
			$this->data["grades"] 			= [];
			$this->data['markpercentages']	= [];
			$this->data['validExam'] 		= [];
			$this->data['separatedMarks'] 	= [];
			$this->data["highestMarks"] 	= [];
			$this->data["section"] 			= [];
		}
	}

	private function invoiceInfo($studentInfo)
	{
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		if (customCompute($studentInfo)) {
			// $this->data['invoices'] = $this->invoice_m->get_order_by_invoice(array('schoolyearID' => $schoolyearID, 'studentID' => $studentInfo->srstudentID, 'deleted_at' => 1));

			$this->data['invoices'] = $this->invoice_m->get_order_by_invoice_join_maininvoice( $studentInfo->srstudentID, $schoolyearID, 1);

			$payments = $this->payment_m->get_order_by_payment(array('schoolyearID' => $schoolyearID, 'studentID' => $studentInfo->srstudentID));
			$weaverandfines = $this->weaverandfine_m->get_order_by_weaverandfine(array('schoolyearID' => $schoolyearID, 'studentID' => $studentInfo->srstudentID));

			$this->data['allpaymentbyinvoice'] = $this->allPaymentByInvoice($payments);
			$this->data['allweaverandpaymentbyinvoice'] = $this->allWeaverAndFineByInvoice($weaverandfines);
		} else {
			$this->data['invoices'] = [];
			$this->data['allpaymentbyinvoice'] = [];
			$this->data['allweaverandpaymentbyinvoice'] = [];
		}
	}

	private function paymentInfo($studentInfo)
	{
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		if (customCompute($studentInfo)) {
			$this->data['payments'] = $this->payment_m->get_payment_with_studentrelation_by_studentID_and_schoolyearID($studentInfo->srstudentID, $schoolyearID);
		} else {
			$this->data['payments'] = [];
		}
	}

	protected function rules_documentupload()
	{
		$rules = array(
			array(
				'field' => 'title',
				'label' => $this->lang->line("student_name"),
				'rules' => 'trim|required|xss_clean|max_length[128]'
			),
			array(
				'field' => 'file',
				'label' => $this->lang->line("student_file"),
				'rules' => 'trim|xss_clean|max_length[200]|callback_unique_document_upload'
			)
		);

		return $rules;
	}

	public function unique_document_upload()
	{
		$new_file = '';
		if ($_FILES["file"]['name'] != "") {
			$file_name = $_FILES["file"]['name'];
			$random = random19();
			$makeRandom = hash('sha512', $random . (strtotime(date('Y-m-d H:i:s'))) . config_item("encryption_key"));
			$file_name_rename = $makeRandom;
			$explode = explode('.', $file_name);
			if (customCompute($explode) >= 2) {
				$new_file = $file_name_rename . '.' . end($explode);
				$config['upload_path'] = "./uploads/documents";
				$config['allowed_types'] = "gif|jpg|png|jpeg|pdf|doc|xml|docx|GIF|JPG|PNG|JPEG|PDF|DOC|XML|DOCX|xls|xlsx|txt|ppt|csv";
				$config['file_name'] = $new_file;
				$config['max_size'] = '5120';
				$config['max_width'] = '10000';
				$config['max_height'] = '10000';
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload("file")) {
					$this->form_validation->set_message("unique_document_upload", $this->upload->display_errors());
					return FALSE;
				} else {
					$this->upload_data['file'] =  $this->upload->data();
					return TRUE;
				}
			} else {
				$this->form_validation->set_message("unique_document_upload", "Invalid file");
				return FALSE;
			}
		} else {
			$this->form_validation->set_message("unique_document_upload", "The file is required.");
			return FALSE;
		}
	}

	public function documentUpload()
	{
		$retArray['status'] = FALSE;
		$retArray['render'] = '';

		if (permissionChecker('student_add')) {
			if ($_POST) {
				$rules = $this->rules_documentupload();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray['errors'] = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
					echo json_encode($retArray);
					exit;
				} else {
					$title = $this->input->post('title');
					$file = $this->upload_data['file']['file_name'];
					$userID = $this->input->post('studentID');

					$array = array(
						'title' => $title,
						'file' => $file,
						'userID' => $userID,
						'usertypeID' => 3,
						"create_date" => date("Y-m-d H:i:s"),
						"create_userID" => $this->session->userdata('loginuserID'),
						"create_usertypeID" => $this->session->userdata('usertypeID')
					);

					$this->document_m->insert_document($array);
					$this->session->set_flashdata('success', $this->lang->line('menu_success'));

					$retArray['status'] = TRUE;
					$retArray['render'] = 'Success';
					echo json_encode($retArray);
					exit;
				}
			} else {
				$retArray['status'] = FALSE;
				$retArray['render'] = 'Error';
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['status'] = FALSE;
			$retArray['render'] = 'Permission Denay.';
			echo json_encode($retArray);
			exit;
		}
	}

	private function documentInfo($studentInfo)
	{
		if (customCompute($studentInfo)) {
			$this->data['documents'] = $this->document_m->get_order_by_document(array('usertypeID' => 3, 'userID' => $studentInfo->srstudentID));
		} else {
			$this->data['documents'] = [];
		}
	}

	public function download_document()
	{
		$id 		= htmlentities(escapeString($this->uri->segment(3)));
		$studentID 	= htmlentities(escapeString($this->uri->segment(4)));
		$classesID 	= htmlentities(escapeString($this->uri->segment(5)));
		if ((int)$id && (int)$studentID && (int)$classesID) {
			if ((permissionChecker('student_add') && permissionChecker('student_delete')) || ($this->session->userdata('usertypeID') == 3 && $this->session->userdata('loginuserID') == $studentID)) {
				$document = $this->document_m->get_single_document(array('documentID' => $id));
				if (customCompute($document)) {
					$file = realpath('uploads/documents/' . $document->file);
					if (file_exists($file)) {
						$expFileName = explode('.', $file);
						$originalname = ($document->title) . '.' . end($expFileName);
						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename="' . basename($originalname) . '"');
						header('Expires: 0');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');
						header('Content-Length: ' . filesize($file));
						readfile($file);
						exit;
					} else {
						redirect(base_url('student/view/' . $studentID . '/' . $classesID));
					}
				} else {
					redirect(base_url('student/view/' . $studentID . '/' . $classesID));
				}
			} else {
				redirect(base_url('student/view/' . $studentID . '/' . $classesID));
			}
		} else {
			redirect(base_url('student/index'));
		}
	}

	public function delete_document()
	{
		$id 		= htmlentities(escapeString($this->uri->segment(3)));
		$studentID 	= htmlentities(escapeString($this->uri->segment(4)));
		$classesID 	= htmlentities(escapeString($this->uri->segment(5)));
		if ((int)$id && (int)$studentID && (int)$classesID) {
			if (permissionChecker('student_add') && permissionChecker('student_delete')) {
				$document = $this->document_m->get_single_document(array('documentID' => $id));
				if (customCompute($document)) {
					if (config_item('demo') == FALSE) {
						if (file_exists(FCPATH . 'uploads/document/' . $document->file)) {
							unlink(FCPATH . 'uploads/document/' . $document->file);
						}
					}

					$this->document_m->delete_document($id);
					$this->session->set_flashdata('success', $this->lang->line('menu_success'));
					redirect(base_url('student/view/' . $studentID . '/' . $classesID));
				} else {
					redirect(base_url('student/view/' . $studentID . '/' . $classesID));
				}
			} else {
				redirect(base_url('student/view/' . $studentID . '/' . $classesID));
			}
		} else {
			redirect(base_url('student/index'));
		}
	}

	protected function rules()
	{
		$rules = array(
			array(
				'field' => 'name',
				'label' => $this->lang->line("student_name"),
				'rules' => 'trim|required|xss_clean|max_length[60]'
			),
			array(
				'field' => 'father_name',
				'label' => $this->lang->line("father_name"),
				'rules' => 'trim|required|xss_clean|max_length[60]'
			),
		 
			array(
				'field' => 'dob',
				'label' => $this->lang->line("student_dob"),
				'rules' => 'trim|max_length[10]|callback_date_valid|xss_clean'
			),
			// array(
			// 	'field' => 'sex',
			// 	'label' => $this->lang->line("student_sex"),
			// 	'rules' => 'trim|required|max_length[10]|xss_clean'
			// ),
			array(
				'field' => 'bloodgroup',
				'label' => $this->lang->line("student_bloodgroup"),
				'rules' => 'trim|max_length[5]|xss_clean'
			),
			array(
				'field' => 'religion',
				'label' => $this->lang->line("student_religion"),
				'rules' => 'trim|max_length[25]|xss_clean'
			),
			array(
				'field' => 'email',
				'label' => $this->lang->line("student_email"),
				'rules' => 'trim|max_length[40]|valid_email|xss_clean'
			),
			array(
				'field' => 'phone',
				'label' => $this->lang->line("student_phone"),
				'rules' => 'trim|required|max_length[25]|min_length[5]|xss_clean'
			),
			array(
				'field' => 'address',
				'label' => $this->lang->line("student_address"),
				'rules' => 'trim|max_length[200]|xss_clean'
			),
			array(
				'field' => 'state',
				'label' => $this->lang->line("student_state"),
				'rules' => 'trim|max_length[128]|xss_clean'
			),
			array(
				'field' => 'country',
				'label' => $this->lang->line("student_country"),
				'rules' => 'trim|max_length[128]|xss_clean'
			),
			array(
				'field' => 'classesID',
				'label' => $this->lang->line("student_classes"),
				'rules' => 'trim|required|numeric|max_length[11]|xss_clean|callback_unique_classesID'
			),
			array(
				'field' => 'joined_class',
				'label' => "joined_class",
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'sectionID',
				'label' => $this->lang->line("student_section"),
				'rules' => 'trim|required|numeric|max_length[11]|xss_clean|callback_unique_sectionID|callback_unique_capacity'
			),
			array(
				'field' => 'registerNO',
				'label' => $this->lang->line("student_registerNO"),
				'rules' => 'trim|required|max_length[40]|callback_unique_registerNO|xss_clean'
			),
			array(
				'field' => 'roll',
				'label' => $this->lang->line("student_roll"),
				// 'rules' => 'trim|required|max_length[11]|numeric|callback_unique_roll|xss_clean'
				'rules' => 'trim|required|max_length[11]|numeric|xss_clean'
			),
			array(
				'field' => 'guargianID',
				'label' => $this->lang->line("student_guargian"),
				'rules' => 'trim|max_length[11]|xss_clean|numeric'
			),
			array(
				'field' => 'photo',
				'label' => $this->lang->line("student_photo"),
				'rules' => 'trim|max_length[200]|xss_clean|callback_photoupload'
			),

			array(
				'field' => 'studentGroupID',
				'label' => $this->lang->line("student_studentgroup"),
				'rules' => 'trim|max_length[11]|xss_clean|numeric'
			),

			array(
				'field' => 'optionalSubjectID',
				'label' => $this->lang->line("student_optionalsubject"),
				'rules' => 'trim|max_length[11]|xss_clean|numeric'
			),

			array(
				'field' => 'extraCurricularActivities',
				'label' => $this->lang->line("student_extracurricularactivities"),
				'rules' => 'trim|max_length[128]|xss_clean'
			),

			array(
				'field' => 'remarks',
				'label' => $this->lang->line("student_remarks"),
				'rules' => 'trim|max_length[128]|xss_clean'
			),
			array(
				'field' => 'admission_date',
				'label' => $this->lang->line("student_admission_date"),
				'rules' => 'trim|max_length[10]|callback_date_valid|xss_clean'
			),
			// array(
			// 	'field' => 'villageID',
			// 	'label' => $this->lang->line("student_village"),
			// 	'rules' => 'trim|required|numeric'
			// ),
			array(
				'field' => 'mole1',
				'label' => $this->lang->line("mole1"),
				'rules' => 'trim|max_length[250]|xss_clean'
			),
			array(
				'field' => 'mole2',
				'label' => $this->lang->line("mole2"),
				'rules' => 'trim|max_length[250]|xss_clean'
			),
			array(
				'field' => 'aadharCardNumber',
				'label' => $this->lang->line("aadharCardNumber"),
				'rules' => 'trim|min_length[12]|max_length[12]|xss_clean'
			),
			// 			array(
			// 				'field' => 'username',
			// 				'label' => $this->lang->line("student_username"),
			// 				'rules' => 'trim|required|min_length[4]|max_length[40]|xss_clean|callback_lol_username'
			// 			),
			// 			array(
			// 				'field' => 'password',
			// 				'label' => $this->lang->line("student_password"),
			// 				'rules' => 'trim|required|min_length[4]|max_length[40]|xss_clean'
			// 			),
			// array(
			// 	'field' => 'father_name',
			// 	'label' => "Father Name",
			// 	'rules' => 'trim|required|max_length[100]|xss_clean'
			// )
			array(
				'field' => 'refered_by',
				'label' => "Refered By",
				'rules' => 'trim|xss_clean'
			),
		);
		return $rules;
	}

	protected function transportRules()
	{
		$rules = array(

			array(
				'field' => 'transportID',
				'label' => $this->lang->line("transportID"),
				'rules' => 'trim|required|greater_than[0]',
				"errors" => [
					'greater_than' => 'This field is required.',
				],
			)
		);
		return $rules;
	}

	protected function hostelRules()
	{
		$rules = array(

			array(
				'field' => 'categoryID',
				'label' => $this->lang->line("categoryID"),
				'rules' => 'trim|required|greater_than[0]',
				"errors" => [
					'greater_than' => 'This field is required.',
				],
			),
			array(
				'field' => 'hostelID',
				'label' => $this->lang->line("hostelID"),
				'rules' => 'trim|required|greater_than[0]',
				"errors" => [
					'greater_than' => 'This field is required.',
				],
			)
		);
		return $rules;
	}

	public function photoupload()
	{
		$id = htmlentities(escapeString($this->uri->segment(3)));
		$student = array();
		if ((int)$id) {
			$student = $this->student_m->general_get_single_student(array('studentID' => $id));
		}

		$new_file = "default.png";
		if ($_FILES["photo"]['name'] != "") {
			$file_name = $_FILES["photo"]['name'];
			$random = random19();
			$makeRandom = hash('sha512', $random . $this->input->post('username') . config_item("encryption_key"));
			$file_name_rename = $makeRandom;
			$explode = explode('.', $file_name);
			if (customCompute($explode) >= 2) {
				$new_file = $file_name_rename . '.' . end($explode);
				$config['upload_path'] = "./uploads/images";
				$config['allowed_types'] = "gif|jpg|png";
				$config['file_name'] = $new_file;
				$config['max_size'] = '1024';
				$config['max_width'] = '3000';
				$config['max_height'] = '3000';
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload("photo")) {
					$this->form_validation->set_message("photoupload", $this->upload->display_errors());
					return FALSE;
				} else {
					$this->upload_data['file'] =  $this->upload->data();
					return TRUE;
				}
			} else {
				$this->form_validation->set_message("photoupload", "Invalid file");
				return FALSE;
			}
		} else {
			if (customCompute($student)) {
				$this->upload_data['file'] = array('file_name' => $student->photo);
				return TRUE;
			} else {
				$this->upload_data['file'] = array('file_name' => $new_file);
				return TRUE;
			}
		}
	}

	public function index()
	{
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css',
				'assets/datepicker/datepicker.css'
			),
			'js' => array(
				'assets/datepicker/datepicker.js',
					'assets/select2/select2.js'
			)
		);

		$this->addRemarksColumn();



		$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$this->data['classes'] = $this->classes_m->get_classes();
			$this->data['sections'] = $this->section_m->general_get_section();
			$this->data['parents'] = $this->parents_m->get_parents();
			$this->data['studentgroups'] = $this->studentgroup_m->get_studentgroup();
			$this->data['villages'] = $this->village_m->get_active_villages();
			$settings = $this->Setting_m->get_setting();
			$this->data['randomAdmissionCode'] = $this->getAdmissonNumber($settings);
			$this->data['transports'] = $this->transport_m->get_transport();
			$this->data["hostels"] = $this->hostel_m->get_hostel();
			$this->data['teachers'] = pluck($this->teacher_m->get_teacher(), 'name', 'teacherID');
			// print_r($this->data['teachers'] );die;

			

			if ($this->input->post("hostelID") > 0) {
				$this->data['categorys'] = $this->category_m->get_order_by_category(array("hostelID" => $this->input->post("hostelID")));
			} else {
				$this->data['categorys'] = [];
			}

			$classesID = $this->input->post("classesID");

			if ($classesID > 0) {
				$this->data['sections'] = $this->section_m->general_get_order_by_section(array("classesID" => $classesID));
				$this->data['optionalSubjects'] = $this->subject_m->general_get_order_by_subject(array("classesID" => $classesID, 'type' => 0));
			} else {
				$this->data['sections'] = [];
				$this->data['optionalSubjects'] = [];
			}

			$this->data['sectionID'] = $this->input->post("sectionID");
			$this->data['optionalSubjectID'] = 0;

			if ($_POST) {
				if(!empty($this->input->post("village_name"))){
					$village_name = $this->db->query('select villageName from villages where villageID='.$this->input->post("village_name"))->row()->villageName;
				}
				
				$rules = $this->rules();
				$this->form_validation->set_rules($rules);
				if ($this->input->post('studentType') == 1) {
					$transportRules = $this->transportRules();
					$this->form_validation->set_rules($transportRules);
				}

				if ($this->input->post('studentType') == 2) {
					$hostelRules = $this->hostelRules();
					$this->form_validation->set_rules($hostelRules);
				}


				if ($this->form_validation->run() == FALSE) {
					$this->data["subview"] = "student/add";
					$this->load->view('_layout_main', $this->data);
				} else {

					$sectionID = $this->input->post("sectionID");
					if ($sectionID == 0) {
						$this->data['sectionID'] = 0;
					} else {
						$this->data['sections'] = $this->section_m->general_get_order_by_section(array('classesID' => $classesID));
						$this->data['sectionID'] = $this->input->post("sectionID");
					}

					if ($this->input->post('optionalSubjectID')) {
						$this->data['optionalSubjectID'] = $this->input->post('optionalSubjectID');
					} else {
						$this->data['optionalSubjectID'] = 0;
					}

					$array["remarks"] = $this->input->post("remarks");
					$array["first_name"] = $this->input->post("first_name");
					$array["last_name"] = $this->input->post("last_name");
					$array["name"] = $this->input->post("name");
					$array["sex"] = $this->input->post("sex");
					$array["religion"] = $this->input->post("religion");
					$array["email"] = $this->input->post("email");
					$array["phone"] = $this->input->post("phone");
					$array["address"] = $this->input->post("address");
					$array["classesID"] = $this->input->post("classesID");
					$array["sectionID"] = $this->input->post("sectionID");
					$array["roll"] = $this->input->post("roll");
					$array["bloodgroup"] = $this->input->post("bloodgroup");
					$array["state"] = $this->input->post("state");
					$array["country"] = $this->input->post("country");
					$array["registerNO"] = $this->input->post("registerNO");
					// $array["username"] = "stud" . rand(100000, 999999); //$this->input->post("username");
					// $array['password'] = $this->student_m->hash("1234567890"); //$this->input->post("password")
					$array['username'] = $this->input->post("registerNO");
					$array['password'] =  $this->student_m->hash($this->input->post("phone"));
					$array['usertypeID'] = 3;
					$array['parentID'] = $this->input->post('guargianID');
					$array['library'] = 0;
					$array['hostel'] = 0;
					$array['transport'] = 0;
					$array['createschoolyearID'] = $schoolyearID;
					$array['schoolyearID'] = $schoolyearID;
					$array["create_date"] = date("Y-m-d H:i:s");
					$array["modify_date"] = date("Y-m-d H:i:s");
					$array["create_userID"] = $this->session->userdata('loginuserID');
					$array["create_username"] = $this->session->userdata('username');
					$array["create_usertype"] = $this->session->userdata('usertype');
					$array["active"] = 1;
					$array["villageID"] = $this->input->post('village_name');
					$array["village_name"] = $village_name;
					$array["aadharCardNumber"] = $this->input->post('aadharCardNumber');

					$array["ration_card"] = $this->input->post('ration_card');
					$array["bank_name"] = $this->input->post('bank_name');
					$array["account_no"] = $this->input->post('account_no');
					$array["ifsc_code"] = $this->input->post('ifsc_code');
					$array["branch_name"] = $this->input->post('branch_name');
					$array["joined_class"] = $this->input->post('joined_class');
					$array["rf_id"] = $this->input->post('rf_id');
					$array["alternative_phone1"] = $this->input->post('alternative_phone1');
					$array["alternative_phone2"] = $this->input->post('alternative_phone2');
					$array["caste"] = $this->input->post('cast');
					$array["sub_caste"] = $this->input->post('sub_caste');
					$array["pen_number"] = $this->input->post('pen_number');
					$array["child_id"] = $this->input->post('child_id');
					$array["medium"] = $this->input->post('medium')?? 'English';

					$array["mole1"] = $this->input->post('mole1');
					$array["mole2"] = $this->input->post('mole2');
					$array["studentType"] = $this->input->post('studentType');

					if ($this->input->post('studentType') == 1) {
						if ($this->input->post("transportID") == 0) {
							$this->data["subview"] = "error";
							$this->load->view('_layout_main', $this->data);
						}
					}


					if ($this->input->post('dob')) {
						$array["dob"] = date("Y-m-d", strtotime($this->input->post("dob")));
					}
					if ($this->input->post('admission_date')) {
						$array["admission_date"] = date("Y-m-d", strtotime($this->input->post("admission_date")));
					}
					$array['photo'] = $this->upload_data['file']['file_name'];
					// 	@$this->usercreatemail($this->input->post('email'), $this->input->post('username'), $this->input->post('password'));
					//echo print_r($array);die;
					$this->student_m->insert_student($array);
					// echo $this->db->last_query();die;
					$studentID = $this->db->insert_id();

					if ($studentID && $array["studentType"] == 1) {
						$transPortArray = array(
							"studentID" => $studentID,
							"transportID" => $this->input->post("transportID"),
							"name" => $this->input->post("name"),
							"email" => $this->input->post("email"),
							"phone" => $this->input->post("phone"),
							"tbalance" => $this->input->post("tbalance"),
							"tjoindate" => date("Y-m-d")
						);

						$this->tmember_m->insert_tmember($transPortArray);
						$this->student_m->update_student(array("transport" => 1), $studentID);
					} else if ($studentID && $array["studentType"] == 2) {
						$category_main_id = $this->category_m->get_single_category(array("hostelID" => $this->input->post("hostelID"), "categoryID" =>  $this->input->post("categoryID")));
						$hostelArray = array(
							"hostelID" => $this->input->post("hostelID"),
							"categoryID" => $this->input->post("categoryID"),
							"studentID" => $studentID,
							"hbalance" => $category_main_id->hbalance,
							"hjoindate" => date("Y-m-d")
						);
						$this->hmember_m->insert_hmember($hostelArray);
						$this->student_m->update_student(array("hostel" => 1), $studentID);
					}


					//Edited by Naveen
					if ($studentID > 0) {
						$parent_array = array();
						$parent_array['name'] = $this->input->post("father_name");
						$parent_array['father_name'] = $this->input->post("father_name");
						$parent_array['father_aadhar'] = $this->input->post("father_aadhar");
						$parent_array['mother_aadhar'] = $this->input->post("mother_aadhar");
						$parent_array['mother_name'] =  $this->input->post("mother_name") ? $this->input->post("mother_name") : '-';
						$parent_array["phone"] = $this->input->post("phone");
						$parent_array['photo'] = "default.png";
						$parent_array['usertypeID'] = 4;
						$parent_array['active'] = 1;
						$parent_array['create_date'] = date("Y-m-d H:i:s");
						$parent_array['modify_date'] = date("Y-m-d H:i:s");

						$parent_id = $this->student_m->insert_parent($parent_array);
						if ($parent_id > 0) {
							$this->student_m->update_student(array("parentID" => $parent_id), $studentID);
						}
					}

					$section = $this->section_m->general_get_section($this->input->post("sectionID"));
					$classes = $this->classes_m->get_classes($this->input->post("classesID"));

					if (customCompute($classes)) {
						$setClasses = $classes->classes;
					} else {
						$setClasses = NULL;
					}

					if (customCompute($section)) {
						$setSection = $section->section;
					} else {
						$setSection = NULL;
					}

					$arrayStudentRelation = array(
						'srstudentID' => $studentID,
						'srname' => $this->input->post("name"),
						'srclassesID' => $this->input->post("classesID"),
						'srclasses' => $setClasses,
						'srroll' => $this->input->post("roll"),
						'srregisterNO' => $this->input->post("registerNO"),
						'srsectionID' => $this->input->post("sectionID"),
						'srsection' => $setSection,
						'srstudentgroupID' => $this->input->post('studentGroupID'),
						'sroptionalsubjectID' => $this->input->post('optionalSubjectID'),
						'srschoolyearID' => $schoolyearID,
					);

					$studentExtendArray = array(
						'studentID' => $studentID,
						'studentgroupID' => $this->input->post('studentGroupID'),
						'optionalsubjectID' => $this->input->post('optionalSubjectID'),
						'extracurricularactivities' => $this->input->post('extraCurricularActivities'),
						'remarks' => $this->input->post('remarks')
					);

					$this->studentextend_m->insert_studentextend($studentExtendArray);
					$this->studentrelation_m->insert_studentrelation($arrayStudentRelation);



					$smsSent = false;
					$waSent  = null;
					try {
						$template      = $this->mailandsmstemplate_m->get_mailandsmstemplate(3); // login credentials
						$singlestudent = $this->studentrelation_m->general_get_single_student(array('srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID), TRUE);
						if ($template && !empty($template->template) && customCompute($singlestudent)) {
							$status  = $this->userConfigSMS($template->template, $singlestudent, $usertypeID=3, $getway='msg91');
							$smsSent = !empty($status['check']);
						}

						// WhatsApp: send login credentials to student/parent
						$waTemplate = $this->db->get_where('whatapp_templates', array('short_name' => 'STUDENT_REGISTRATION'))->row();
						if ($waTemplate && customCompute($singlestudent)) {
							$setting     = $this->Setting_m->get_setting();
							$school_name = !empty($setting->sname)   ? $setting->sname   : '';
							$website     = !empty($setting->website) ? $setting->website : '';
							$waPhone     = !empty($singlestudent->alternative_phone1) ? $singlestudent->alternative_phone1 : $singlestudent->phone;
							$params      = implode(',', [
								$singlestudent->name,
								$school_name,
								$singlestudent->username,
								$singlestudent->phone,
								$website,
							]);
							$waResult = $this->Whatsapp_m->sendWhatsapp($waPhone, $params, $waTemplate->template_name);
							$waSent   = ($waResult !== false);
						}
					} catch (Throwable $e) {
						log_message('error', 'Student registration SMS/WA error: ' . $e->getMessage());
					}

					//code for auto invoice generation
					$is_auto_invoice = $this->Setting_m->get_setting_where('is_student_auto_invoice');

					// echo $is_auto_invoice['value'] ; die;
				if(!empty($is_auto_invoice) && $is_auto_invoice['value'] == 1 ){ //school fee invoice
					$class_id = $this->input->post("classesID");
					$section_id = $this->input->post("sectionID");
					$year_id = $this->session->userdata('defaultschoolyearID');


					if($this->input->post('studentType') == 1){//transport
						$pickup_id = $this->input->post("pickup_id");
						$this->db->where('id',$pickup_id);
						$p_res = $this->db->get('pickup_points')->row_array();
						$p_amount = $p_res['fare'];
					}else if($this->input->post('studentType') == 2){ //hostel
						$hostelID = $this->input->post("hostelID");
						$categoryID = $this->input->post("categoryID");
						$this->db->where('categoryID',$categoryID);
						$this->db->where('hostelID',$hostelID);
						$p_res = $this->db->get('category')->row_array();
						$h_amount = $p_res['hbalance'];
					}

					$fee_type = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%SCHOOL FEE%' OR `feetypes` LIKE '%COLLEGE FEE%' ")->row_array();
					$fee_type_trasport = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%TRANSPORT FEE%' ")->row_array();
					$fee_type_hostel = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%Hostel Fee%' ")->row_array();
					$admission_fee_type = $this->db->query("SELECT feetypesID,fee_amount FROM `feetypes` WHERE `feetypes` LIKE '%Admission%' ")->row_array();

					$amount = $this->db->query("SELECT fee_amount FROM `school_fees` WHERE `class_id` = '".$class_id."' AND `section_id` = '".$section_id."' AND `year_id` = '".$year_id."' ")->row_array();

					$subtotal_amount =$amount;

					// print_r($admission_fee_type);die;
					//admission fee added to invoice start
					if( $this->input->post("add_admission_fee_invoice") == 1 && !empty($admission_fee_type)  ){
						if($this->input->post('studentType') == 3){ //dayscolar
							$fee_types = [array(
								'feetypeID' => $fee_type['feetypesID'],
								'amount' => $amount['fee_amount'],
								'discount' => "",
								'subtotal' => $subtotal_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
							
						}else if($this->input->post('studentType') == 1){ //trasport
							$fee_types = [array(
								'feetypeID' => $fee_type['feetypesID'],
								'amount' => $amount['fee_amount'],
								'discount' => "",
								'subtotal' => $subtotal_amount,
								'paidamount' => "",
							),array(
								'feetypeID' => $fee_type_trasport['feetypesID'],
								'amount' => $p_amount,
								'discount' => "",
								'subtotal' => $p_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
							
						}if($this->input->post('studentType') == 2){ //hostel
							$fee_types = [array(
								'feetypeID' => $fee_type['feetypesID'],
								'amount' => $amount['fee_amount'],
								'discount' => "",
								'subtotal' => $subtotal_amount,
								'paidamount' => "",
							),array(
								'feetypeID' => $fee_type_hostel['feetypesID'],
								'amount' => $h_amount,
								'discount' => "",
								'subtotal' => $h_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
						}
					}else{ 	//admission fee added to invoice end
					

					if($this->input->post('studentType') == 3){ //dayscolar
						$fee_types = [array(
							'feetypeID' => $fee_type['feetypesID'],
							'amount' => $amount['fee_amount'],
							'discount' => "",
							'subtotal' => $subtotal_amount,
							'paidamount' => "",
						)];
						
					}else if($this->input->post('studentType') == 1){ //trasport
						$fee_types = [array(
							'feetypeID' => $fee_type['feetypesID'],
							'amount' => $amount['fee_amount'],
							'discount' => "",
							'subtotal' => $subtotal_amount,
							'paidamount' => "",
						),array(
							'feetypeID' => $fee_type_trasport['feetypesID'],
							'amount' => $p_amount,
							'discount' => "",
							'subtotal' => $p_amount,
							'paidamount' => "",
						)];
						
					}if($this->input->post('studentType') == 2){ //hostel
						$fee_types = [array(
							'feetypeID' => $fee_type['feetypesID'],
							'amount' => $amount['fee_amount'],
							'discount' => "",
							'subtotal' => $subtotal_amount,
							'paidamount' => "",
						),array(
							'feetypeID' => $fee_type_hostel['feetypesID'],
							'amount' => $h_amount,
							'discount' => "",
							'subtotal' => $h_amount,
							'paidamount' => "",
						)];
					}
 					
					}
					//[feetypeitems] => [{"feetypeID":"3","amount":"1","discount":"","subtotal":"1","paidamount":""},{"feetypeID":"52","amount":"2","discount":"","subtotal":"2","paidamount":""}]
					


					$json_fee_types = json_encode($fee_types);
					// echo "<pre>";print_r($json_fee_types);die;
					$invoice_data = array(
						'classesID' => $this->input->post("classesID"),
						'sectionID' =>$this->input->post("sectionID"),
						'studentID' => $studentID,
						'date' => date('d-m-Y'),
						'statusID' => 0,
						'payment_method' => 0,
						// 'feetypeitems' => '['.$json_fee_types.']',
						'feetypeitems' => $json_fee_types,
						'totalsubtotal' => $subtotal_amount,
						'totalpaidamount' => 0,
						'editID' => 0,
					);

					$invoice_error = $this->saveinvoice($invoice_data);
					$this->db->update('student',array('invoice_error'=>$invoice_error),array('studentID'=>$studentID));

				}else if(!empty($is_auto_invoice) && $is_auto_invoice['value'] == 2 ){ //term fee invoice
					$class_id = $this->input->post("classesID");
					$section_id = $this->input->post("sectionID");
					$year_id = $this->session->userdata('defaultschoolyearID');


					if($this->input->post('studentType') == 1){//transport
						$pickup_id = $this->input->post("pickup_id");
						$this->db->where('id',$pickup_id);
						$p_res = $this->db->get('pickup_points')->row_array();
						$p_amount = $p_res['fare'];
					}else if($this->input->post('studentType') == 2){ //hostel
						$hostelID = $this->input->post("hostelID");
						$categoryID = $this->input->post("categoryID");
						$this->db->where('categoryID',$categoryID);
						$this->db->where('hostelID',$hostelID);
						$p_res = $this->db->get('category')->row_array();
						$h_amount = $p_res['hbalance'];
					}

					$term1_fee_type = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE 'Term1 Fee' ")->row_array();
					$term2_fee_type = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE 'Term2 Fee' ")->row_array();
					$term3_fee_type = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE 'Term3 Fee' ")->row_array();

					$fee_type_trasport = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%TRANSPORT FEE%' ")->row_array();
					$fee_type_hostel = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%Hostel Fee%' ")->row_array();

					$term_res = $this->db->query("SELECT * FROM `term_fees` WHERE `class_id` = '".$class_id."' AND `section_id` = '".$section_id."' AND `year_id` = '".$year_id."' ")->row_array();
					

					$admission_fee_type = $this->db->query("SELECT feetypesID,fee_amount FROM `feetypes` WHERE `feetypes` LIKE '%Admission%' ")->row_array();

					$term1 = $term_res['term1_fee'];
					$term2 = $term_res['term2_fee'];
					$term3 = $term_res['term3_fee'];

					$subtotal_amount =$term1+$term2+$term3;

					if( $this->input->post("add_admission_fee_invoice") == 1 && !empty($admission_fee_type)  ){

						if($this->input->post('studentType') == 3){ //dayscolar
						

							$fee_types = [
								array(	//term1
								'feetypeID' => $term1_fee_type['feetypesID'],
								'amount' => $term1,
								'discount' => "",
								'subtotal' => $term1,
								'paidamount' => "",
							),array(	//term2
								'feetypeID' => $term2_fee_type['feetypesID'],
								'amount' => $term2,
								'discount' => "",
								'subtotal' => $term2,
								'paidamount' => "",
							),array(	//term3
								'feetypeID' => $term3_fee_type['feetypesID'],
								'amount' => $term3,
								'discount' => "",
								'subtotal' => $term3,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
							
						}else if($this->input->post('studentType') == 1){ //trasport
							$fee_types = [
								array(	//term1
									'feetypeID' => $term1_fee_type['feetypesID'],
									'amount' => $term1,
									'discount' => "",
									'subtotal' => $term1,
									'paidamount' => "",
								),array(	//term2
									'feetypeID' => $term2_fee_type['feetypesID'],
									'amount' => $term2,
									'discount' => "",
									'subtotal' => $term2,
									'paidamount' => "",
								),array(	//term3
									'feetypeID' => $term3_fee_type['feetypesID'],
									'amount' => $term3,
									'discount' => "",
									'subtotal' => $term3,
									'paidamount' => "",
								),array(	//transport
								'feetypeID' => $fee_type_trasport['feetypesID'],
								'amount' => $p_amount,
								'discount' => "",
								'subtotal' => $p_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
							
						}if($this->input->post('studentType') == 2){ //hostel
							$fee_types = [
								array(	//term1
									'feetypeID' => $term1_fee_type['feetypesID'],
									'amount' => $term1,
									'discount' => "",
									'subtotal' => $term1,
									'paidamount' => "",
								),array(	//term2
									'feetypeID' => $term2_fee_type['feetypesID'],
									'amount' => $term2,
									'discount' => "",
									'subtotal' => $term2,
									'paidamount' => "",
								),array(	//term3
									'feetypeID' => $term3_fee_type['feetypesID'],
									'amount' => $term3,
									'discount' => "",
									'subtotal' => $term3,
									'paidamount' => "",
								),array(	//hostel
								'feetypeID' => $fee_type_hostel['feetypesID'],
								'amount' => $h_amount,
								'discount' => "",
								'subtotal' => $h_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
						}

					}else{

						if($this->input->post('studentType') == 3){ //dayscolar
							

							$fee_types = [
								array(	//term1
								'feetypeID' => $term1_fee_type['feetypesID'],
								'amount' => $term1,
								'discount' => "",
								'subtotal' => $term1,
								'paidamount' => "",
							),array(	//term2
								'feetypeID' => $term2_fee_type['feetypesID'],
								'amount' => $term2,
								'discount' => "",
								'subtotal' => $term2,
								'paidamount' => "",
							),array(	//term3
								'feetypeID' => $term3_fee_type['feetypesID'],
								'amount' => $term3,
								'discount' => "",
								'subtotal' => $term3,
								'paidamount' => "",
							)
						];
							
						}else if($this->input->post('studentType') == 1){ //trasport
							$fee_types = [
								array(	//term1
									'feetypeID' => $term1_fee_type['feetypesID'],
									'amount' => $term1,
									'discount' => "",
									'subtotal' => $term1,
									'paidamount' => "",
								),array(	//term2
									'feetypeID' => $term2_fee_type['feetypesID'],
									'amount' => $term2,
									'discount' => "",
									'subtotal' => $term2,
									'paidamount' => "",
								),array(	//term3
									'feetypeID' => $term3_fee_type['feetypesID'],
									'amount' => $term3,
									'discount' => "",
									'subtotal' => $term3,
									'paidamount' => "",
								),array(	//transport
								'feetypeID' => $fee_type_trasport['feetypesID'],
								'amount' => $p_amount,
								'discount' => "",
								'subtotal' => $p_amount,
								'paidamount' => "",
							)
						];
							
						}if($this->input->post('studentType') == 2){ //hostel
							$fee_types = [
								array(	//term1
									'feetypeID' => $term1_fee_type['feetypesID'],
									'amount' => $term1,
									'discount' => "",
									'subtotal' => $term1,
									'paidamount' => "",
								),array(	//term2
									'feetypeID' => $term2_fee_type['feetypesID'],
									'amount' => $term2,
									'discount' => "",
									'subtotal' => $term2,
									'paidamount' => "",
								),array(	//term3
									'feetypeID' => $term3_fee_type['feetypesID'],
									'amount' => $term3,
									'discount' => "",
									'subtotal' => $term3,
									'paidamount' => "",
								),array(	//hostel
								'feetypeID' => $fee_type_hostel['feetypesID'],
								'amount' => $h_amount,
								'discount' => "",
								'subtotal' => $h_amount,
								'paidamount' => "",
							)
						];
						}
					}
 					
					//[feetypeitems] => [{"feetypeID":"3","amount":"1","discount":"","subtotal":"1","paidamount":""},{"feetypeID":"52","amount":"2","discount":"","subtotal":"2","paidamount":""}]
					


					$json_fee_types = json_encode($fee_types);
					// echo "<pre>";print_r($json_fee_types);die;
					$invoice_data = array(
						'classesID' => $this->input->post("classesID"),
						'sectionID' =>$this->input->post("sectionID"),
						'studentID' => $studentID,
						'date' => date('d-m-Y'),
						'statusID' => 0,
						'payment_method' => 0,
						// 'feetypeitems' => '['.$json_fee_types.']',
						'feetypeitems' => $json_fee_types,
						'totalsubtotal' => $subtotal_amount,
						'totalpaidamount' => 0,
						'editID' => 0,
					);
					// echo "<pre>";print_r($invoice_data);die;

					$invoice_error = $this->saveinvoice($invoice_data);
					$this->db->update('student',array('invoice_error'=>$invoice_error),array('studentID'=>$studentID));

				}
					
					$flashMsg  = 'Student Registered Successfully.';
					$flashMsg .= $smsSent         ? ' SMS Sent Successfully.'      : ' SMS Sending Failed.';
					if ($waSent === true)          { $flashMsg .= ' WhatsApp Sent Successfully.'; }
					elseif ($waSent === false)     { $flashMsg .= ' WhatsApp Sending Failed.'; }
					$this->session->set_flashdata('success', $flashMsg);
					redirect(base_url("student/index"));
				}
			}


			

		$myProfile = false;
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		if ($this->session->userdata('usertypeID') == 3) {
			$id = $this->data['myclass'];
			if (!permissionChecker('student_view')) {
				$myProfile = true;
			}
		} else {
			$id = htmlentities(escapeString($this->uri->segment(3)));
		}

		if ($this->session->userdata('usertypeID') == 3 && $myProfile) {
			$url = $id;
			$id = $this->session->userdata('loginuserID');
			$this->getView($id, $url);
		} else {
			$this->data['set'] = $id;
			$this->data['classes'] = $this->classes_m->get_classes();

			if ((int)$id) {
				$this->data['students'] = $this->studentrelation_m->get_order_by_student(array('srclassesID' => $id, 'srschoolyearID' => $schoolyearID),$studentExtend = FALSE,1);

				
				if (customCompute($this->data['students'])) {
					$sections = $this->section_m->general_get_order_by_section(array("classesID" => $id));
					$this->data['sections'] = $sections;
					foreach ($sections as $key => $section) {
						$this->data['allsection'][$section->sectionID] = $this->studentrelation_m->get_order_by_student(array('srclassesID' => $id, "srsectionID" => $section->sectionID, 'srschoolyearID' => $schoolyearID));
					}
				} else {
					$this->data['students'] = [];
				}
			} else {
				$this->data['students'] = [];
			}
			// echo "<pre>";print_r($this->data['students']);
			$this->data["subview"] = "student/index";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function add()
	{

		// echo "<pre>";print_r($_POST);die;
		if (($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
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

			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$this->data['classes'] = $this->classes_m->get_classes();
			$this->data['sections'] = $this->section_m->general_get_section();
			$this->data['parents'] = $this->parents_m->get_parents();
			$this->data['studentgroups'] = $this->studentgroup_m->get_studentgroup();
			$this->data['villages'] = $this->village_m->get_active_villages();
			$settings = $this->Setting_m->get_setting();
			$this->data['randomAdmissionCode'] = $this->getAdmissonNumber($settings);
			$this->data['transports'] = $this->transport_m->get_transport();
			$this->data["hostels"] = $this->hostel_m->get_hostel();
			$teachers = $this->teacher_m->get_teacher();
			$users = $this->user_m->get_user();
			$combined_teachers = [];
			if(customCompute($teachers)) {
				foreach ($teachers as $teacher) {
					$combined_teachers['teacher-' . $teacher->teacherID] = $teacher->name . " [Teacher]";
				}
			}
			if(customCompute($users)) {
				foreach ($users as $user) {
					$combined_teachers['user-' . $user->userID] = $user->name . " [User]";
				}
			}
			$this->data['teachers'] = $combined_teachers;

			

			if ($this->input->post("hostelID") > 0) {
				$this->data['categorys'] = $this->category_m->get_order_by_category(array("hostelID" => $this->input->post("hostelID")));
			} else {
				$this->data['categorys'] = [];
			}

			$classesID = $this->input->post("classesID");

			if ($classesID > 0) {
				$this->data['sections'] = $this->section_m->general_get_order_by_section(array("classesID" => $classesID));
				$this->data['optionalSubjects'] = $this->subject_m->general_get_order_by_subject(array("classesID" => $classesID, 'type' => 0));
			} else {
				$this->data['sections'] = [];
				$this->data['optionalSubjects'] = [];
			}

			$this->data['sectionID'] = $this->input->post("sectionID");
			$this->data['optionalSubjectID'] = 0;

			if ($_POST) {
					// ini_set('display_errors', 1);
					// error_reporting(E_ALL);
				if(!empty($this->input->post("village_name"))){
					$village_name = $this->db->query('select villageName from villages where villageID='.$this->input->post("village_name"))->row()->villageName;
				}
				
				$rules = $this->rules();
				$this->form_validation->set_rules($rules);
				if ($this->input->post('studentType') == 1) {
					$transportRules = $this->transportRules();
					$this->form_validation->set_rules($transportRules);
				}

				if ($this->input->post('studentType') == 2) {
					$hostelRules = $this->hostelRules();
					$this->form_validation->set_rules($hostelRules);
				}


				if ($this->form_validation->run() == FALSE) {
					$this->data["subview"] = "student/add";
					$this->load->view('_layout_main', $this->data);
				} else {

					$sectionID = $this->input->post("sectionID");
					if ($sectionID == 0) {
						$this->data['sectionID'] = 0;
					} else {
						$this->data['sections'] = $this->section_m->general_get_order_by_section(array('classesID' => $classesID));
						$this->data['sectionID'] = $this->input->post("sectionID");
					}

					if ($this->input->post('optionalSubjectID')) {
						$this->data['optionalSubjectID'] = $this->input->post('optionalSubjectID');
					} else {
						$this->data['optionalSubjectID'] = 0;
					}

					$array["remarks"] = $this->input->post("remarks");
					$array["first_name"] = $this->input->post("first_name");
					$array["last_name"] = $this->input->post("last_name");
					$array["name"] = $this->input->post("name");
					$array["sex"] = $this->input->post("sex");
					$array["religion"] = $this->input->post("religion");
					$array["email"] = $this->input->post("email");
					$array["phone"] = $this->input->post("phone");
					$array["address"] = $this->input->post("address");
					$array["classesID"] = $this->input->post("classesID");
					$array["sectionID"] = $this->input->post("sectionID");
					$array["roll"] = $this->input->post("roll");
					$array["bloodgroup"] = $this->input->post("bloodgroup");
					$array["state"] = $this->input->post("state");
					$array["country"] = $this->input->post("country");
					$array["registerNO"] = $this->input->post("registerNO");
					// $array["username"] = "stud" . rand(100000, 999999); //$this->input->post("username");
					// $array['password'] = $this->student_m->hash("1234567890"); //$this->input->post("password")
					$array['username'] = $this->input->post("registerNO");
					$array['password'] =  $this->student_m->hash($this->input->post("phone"));
					$array['usertypeID'] = 3;
					$array['parentID'] = $this->input->post('guargianID');
					$array['library'] = 0;
					$array['hostel'] = 0;
					$array['transport'] = 0;
					$array['createschoolyearID'] = $schoolyearID;
					$array['schoolyearID'] = $schoolyearID;
					$array["create_date"] = date("Y-m-d H:i:s");
					$array["modify_date"] = date("Y-m-d H:i:s");
					$array["create_userID"] = $this->session->userdata('loginuserID');
					$array["create_username"] = $this->session->userdata('username');
					$array["create_usertype"] = $this->session->userdata('usertype');
					$array["active"] = 1;
					$array["villageID"] = $this->input->post('village_name');
					$array["village_name"] = $village_name;
					$array["aadharCardNumber"] = $this->input->post('aadharCardNumber');

					$array["ration_card"] = $this->input->post('ration_card');
					$array["bank_name"] = $this->input->post('bank_name');
					$array["account_no"] = $this->input->post('account_no');
					$array["ifsc_code"] = $this->input->post('ifsc_code');
					$array["branch_name"] = $this->input->post('branch_name');
					$array["joined_class"] = $this->input->post('joined_class');
					$array["rf_id"] = $this->input->post('rf_id');
					$array["alternative_phone1"] = $this->input->post('alternative_phone1');
					$array["alternative_phone2"] = $this->input->post('alternative_phone2');
					$array["caste"] = $this->input->post('cast');
					$array["sub_caste"] = $this->input->post('sub_caste');
					$array["pen_number"] = $this->input->post('pen_number');
					$array["child_id"] = $this->input->post('child_id');
					$array["medium"] = $this->input->post('medium') ?? 'Enlish';

					$array["mole1"] = $this->input->post('mole1');
					$array["mole2"] = $this->input->post('mole2');
					$array["studentType"] = $this->input->post('studentType');
					$refered_by_val = $this->input->post('refered_by');
					if ($refered_by_val === 'others') {
						$array["refered_by"] = 'others-' . $this->input->post('refered_by_other');
					} else {
						$array["refered_by"] = $refered_by_val;
					}

					if ($this->input->post('studentType') == 1) {
						if ($this->input->post("transportID") == 0) {
							$this->data["subview"] = "error";
							$this->load->view('_layout_main', $this->data);
						}
					}


					if ($this->input->post('dob')) {
						$array["dob"] = date("Y-m-d", strtotime($this->input->post("dob")));
					}
					if ($this->input->post('admission_date')) {
						$array["admission_date"] = date("Y-m-d", strtotime($this->input->post("admission_date")));
					}
					$array['photo'] = $this->upload_data['file']['file_name'];
					// 	@$this->usercreatemail($this->input->post('email'), $this->input->post('username'), $this->input->post('password'));
					//echo print_r($array);die;
					$this->student_m->insert_student($array);
					// echo $this->db->last_query();die;
					$studentID = $this->db->insert_id();

					if ($studentID && $array["studentType"] == 1) {
						$transPortArray = array(
							"studentID" => $studentID,
							"transportID" => $this->input->post("transportID"),
							"name" => $this->input->post("name"),
							"email" => $this->input->post("email"),
							"phone" => $this->input->post("phone"),
							"tbalance" => $this->input->post("tbalance"),
							"tjoindate" => date("Y-m-d")
						);

						$this->tmember_m->insert_tmember($transPortArray);
						$this->student_m->update_student(array("transport" => 1), $studentID);
					} else if ($studentID && $array["studentType"] == 2) {
						$category_main_id = $this->category_m->get_single_category(array("hostelID" => $this->input->post("hostelID"), "categoryID" =>  $this->input->post("categoryID")));
						$hostelArray = array(
							"hostelID" => $this->input->post("hostelID"),
							"categoryID" => $this->input->post("categoryID"),
							"studentID" => $studentID,
							"hbalance" => $category_main_id->hbalance,
							"hjoindate" => date("Y-m-d")
						);
						$this->hmember_m->insert_hmember($hostelArray);
						$this->student_m->update_student(array("hostel" => 1), $studentID);
					}


					//Edited by Naveen
					if ($studentID > 0) {
						$parent_array = array();
						$parent_array['name'] = $this->input->post("father_name") ?? '';
						$parent_array['father_name'] = $this->input->post("father_name") ?? '';
						$parent_array['father_aadhar'] = $this->input->post("father_aadhar") ?? '';
						$parent_array['mother_aadhar'] = $this->input->post("mother_aadhar") ?? '';
						$parent_array['mother_name'] =  $this->input->post("mother_name") ? $this->input->post("mother_name") : '-';
						$parent_array["phone"] = $this->input->post("phone") ?? 0;
						$parent_array['photo'] = "default.png";
						$parent_array['usertypeID'] = 4;
						$parent_array['active'] = 1;
						$parent_array['create_date'] = date("Y-m-d H:i:s");
						$parent_array['modify_date'] = date("Y-m-d H:i:s");

						$parent_id = $this->student_m->insert_parent($parent_array);
						// echo $this->db->last_query
						if ($parent_id > 0) {
							$this->student_m->update_student(array("parentID" => $parent_id), $studentID);
						}
					}

					$section = $this->section_m->general_get_section($this->input->post("sectionID"));
					$classes = $this->classes_m->get_classes($this->input->post("classesID"));

					if (customCompute($classes)) {
						$setClasses = $classes->classes;
					} else {
						$setClasses = NULL;
					}

					if (customCompute($section)) {
						$setSection = $section->section;
					} else {
						$setSection = NULL;
					}

					$arrayStudentRelation = array(
						'srstudentID' => $studentID,
						'srname' => $this->input->post("name") ?? 0,
						'srclassesID' => $this->input->post("classesID") ?? 0,
						'srclasses' => $setClasses,
						'srroll' => $this->input->post("roll") ?? 0,
						'srregisterNO' => $this->input->post("registerNO") ?? 0,
						'srsectionID' => $this->input->post("sectionID") ?? 0,
						'srsection' => $setSection,
						'srstudentgroupID' => $this->input->post('studentGroupID') ? $this->input->post('studentGroupID') : 0,
						'sroptionalsubjectID' => $this->input->post('optionalSubjectID') ?? 0,
						'srschoolyearID' => $schoolyearID,
					);

					$studentExtendArray = array(
						'studentID' => $studentID,
						'studentgroupID' => $this->input->post('studentGroupID') ?? 0,
						'optionalsubjectID' => $this->input->post('optionalSubjectID') ?? 0,
						'extracurricularactivities' => $this->input->post('extraCurricularActivities') ?? 0,
						'remarks' => $this->input->post('remarks') ?? '-'
					);

					$this->studentextend_m->insert_studentextend($studentExtendArray);
					$this->studentrelation_m->insert_studentrelation($arrayStudentRelation);

					$sibling_ids = array_filter((array)$this->input->post('sibling_studentID'));
					foreach ($sibling_ids as $sibID) {
						$sibID = (int)$sibID;
						if ($sibID > 0 && $sibID != $studentID) {
							$this->studentsiblings_m->insert_sibling(array('studentID' => $studentID, 'sibling_studentID' => $sibID));
							$this->studentsiblings_m->insert_sibling(array('studentID' => $sibID, 'sibling_studentID' => $studentID));
						}
					}
					// echo $this->db->last_query();die;



					$smsSent = false;
					$waSent  = null;
					try {
						$template      = $this->mailandsmstemplate_m->get_mailandsmstemplate(3); // login credentials
						$singlestudent = $this->studentrelation_m->general_get_single_student(array('srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID), TRUE);
						if ($template && !empty($template->template) && customCompute($singlestudent)) {
							$status  = $this->userConfigSMS($template->template, $singlestudent, $usertypeID=3, $getway='msg91');
							$smsSent = !empty($status['check']);
						}

						// WhatsApp: send login credentials to student/parent
						$waTemplate = $this->db->get_where('whatapp_templates', array('short_name' => 'STUDENT_REGISTRATION'))->row();
						if ($waTemplate && customCompute($singlestudent)) {
							$setting     = $this->Setting_m->get_setting();
							$school_name = !empty($setting->sname)   ? $setting->sname   : '';
							$website     = !empty($setting->website) ? $setting->website : '';
							$waPhone     = !empty($singlestudent->alternative_phone1) ? $singlestudent->alternative_phone1 : $singlestudent->phone;
							$params      = implode(',', [
								$singlestudent->name,
								$school_name,
								$singlestudent->username,
								$singlestudent->phone,
								$website,
							]);
							$waResult = $this->Whatsapp_m->sendWhatsapp($waPhone, $params, $waTemplate->template_name);
							$waSent   = ($waResult !== false);
						}
					} catch (Throwable $e) {
						log_message('error', 'Student registration SMS/WA error: ' . $e->getMessage());
					}

					//code for auto invoice generation
					$is_auto_invoice = $this->Setting_m->get_setting_where('is_student_auto_invoice');

					// echo $is_auto_invoice['value'] ; die;
				if(!empty($is_auto_invoice) && $is_auto_invoice['value'] == 1 ){ //school fee invoice
					$class_id = $this->input->post("classesID");
					$section_id = $this->input->post("sectionID");
					$year_id = $this->session->userdata('defaultschoolyearID');


					if($this->input->post('studentType') == 1){//transport
						$pickup_id = $this->input->post("pickup_id");
						$this->db->where('id',$pickup_id);
						$p_res = $this->db->get('pickup_points')->row_array();
						$p_amount = $p_res['fare'];
					}else if($this->input->post('studentType') == 2){ //hostel
						$hostelID = $this->input->post("hostelID");
						$categoryID = $this->input->post("categoryID");
						$this->db->where('categoryID',$categoryID);
						$this->db->where('hostelID',$hostelID);
						$p_res = $this->db->get('category')->row_array();
						$h_amount = $p_res['hbalance'];
					}

					$fee_type = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%SCHOOL FEE%' OR `feetypes` LIKE '%COLLEGE FEE%' ")->row_array();
					$fee_type_trasport = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%TRANSPORT FEE%' ")->row_array();
					$fee_type_hostel = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%Hostel Fee%' ")->row_array();
					$admission_fee_type = $this->db->query("SELECT feetypesID,fee_amount FROM `feetypes` WHERE `feetypes` LIKE '%Admission%' ")->row_array();

					$amount = $this->db->query("SELECT fee_amount FROM `school_fees` WHERE `class_id` = '".$class_id."' AND `section_id` = '".$section_id."' AND `year_id` = '".$year_id."' ")->row_array();

					$subtotal_amount =$amount;

					// print_r($admission_fee_type);die;
					//admission fee added to invoice start
					if( $this->input->post("add_admission_fee_invoice") == 1 && !empty($admission_fee_type)  ){
						if($this->input->post('studentType') == 3){ //dayscolar
							$fee_types = [array(
								'feetypeID' => $fee_type['feetypesID'],
								'amount' => $amount['fee_amount'],
								'discount' => "",
								'subtotal' => $subtotal_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
							
						}else if($this->input->post('studentType') == 1){ //trasport
							$fee_types = [array(
								'feetypeID' => $fee_type['feetypesID'],
								'amount' => $amount['fee_amount'],
								'discount' => "",
								'subtotal' => $subtotal_amount,
								'paidamount' => "",
							),array(
								'feetypeID' => $fee_type_trasport['feetypesID'],
								'amount' => $p_amount,
								'discount' => "",
								'subtotal' => $p_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
							
						}if($this->input->post('studentType') == 2){ //hostel
							$fee_types = [array(
								'feetypeID' => $fee_type['feetypesID'],
								'amount' => $amount['fee_amount'],
								'discount' => "",
								'subtotal' => $subtotal_amount,
								'paidamount' => "",
							),array(
								'feetypeID' => $fee_type_hostel['feetypesID'],
								'amount' => $h_amount,
								'discount' => "",
								'subtotal' => $h_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
						}
					}else{ 	//admission fee added to invoice end
					

					if($this->input->post('studentType') == 3){ //dayscolar
						$fee_types = [array(
							'feetypeID' => $fee_type['feetypesID'],
							'amount' => $amount['fee_amount'],
							'discount' => "",
							'subtotal' => $subtotal_amount,
							'paidamount' => "",
						)];
						
					}else if($this->input->post('studentType') == 1){ //trasport
						$fee_types = [array(
							'feetypeID' => $fee_type['feetypesID'],
							'amount' => $amount['fee_amount'],
							'discount' => "",
							'subtotal' => $subtotal_amount,
							'paidamount' => "",
						),array(
							'feetypeID' => $fee_type_trasport['feetypesID'],
							'amount' => $p_amount,
							'discount' => "",
							'subtotal' => $p_amount,
							'paidamount' => "",
						)];
						
					}if($this->input->post('studentType') == 2){ //hostel
						$fee_types = [array(
							'feetypeID' => $fee_type['feetypesID'],
							'amount' => $amount['fee_amount'],
							'discount' => "",
							'subtotal' => $subtotal_amount,
							'paidamount' => "",
						),array(
							'feetypeID' => $fee_type_hostel['feetypesID'],
							'amount' => $h_amount,
							'discount' => "",
							'subtotal' => $h_amount,
							'paidamount' => "",
						)];
					}
 					
					}
					//[feetypeitems] => [{"feetypeID":"3","amount":"1","discount":"","subtotal":"1","paidamount":""},{"feetypeID":"52","amount":"2","discount":"","subtotal":"2","paidamount":""}]
					


					$json_fee_types = json_encode($fee_types);
					// echo "<pre>";print_r($json_fee_types);die;
					$invoice_data = array(
						'classesID' => $this->input->post("classesID"),
						'sectionID' =>$this->input->post("sectionID"),
						'studentID' => $studentID,
						'date' => date('d-m-Y'),
						'statusID' => 0,
						'payment_method' => 0,
						// 'feetypeitems' => '['.$json_fee_types.']',
						'feetypeitems' => $json_fee_types,
						'totalsubtotal' => $subtotal_amount,
						'totalpaidamount' => 0,
						'editID' => 0,
					);

					$invoice_error = $this->saveinvoice($invoice_data);
					$this->db->update('student',array('invoice_error'=>$invoice_error),array('studentID'=>$studentID));

				}else if(!empty($is_auto_invoice) && $is_auto_invoice['value'] == 2 ){ //term fee invoice
					$class_id = $this->input->post("classesID");
					$section_id = $this->input->post("sectionID");
					$year_id = $this->session->userdata('defaultschoolyearID');


					if($this->input->post('studentType') == 1){//transport
						$pickup_id = $this->input->post("pickup_id");
						$this->db->where('id',$pickup_id);
						$p_res = $this->db->get('pickup_points')->row_array();
						$p_amount = $p_res['fare'];
					}else if($this->input->post('studentType') == 2){ //hostel
						$hostelID = $this->input->post("hostelID");
						$categoryID = $this->input->post("categoryID");
						$this->db->where('categoryID',$categoryID);
						$this->db->where('hostelID',$hostelID);
						$p_res = $this->db->get('category')->row_array();
						$h_amount = $p_res['hbalance'];
					}

					$term1_fee_type = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE 'Term1 Fee' ")->row_array();
					$term2_fee_type = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE 'Term2 Fee' ")->row_array();
					$term3_fee_type = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE 'Term3 Fee' ")->row_array();

					$fee_type_trasport = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%TRANSPORT FEE%' ")->row_array();
					$fee_type_hostel = $this->db->query("SELECT feetypesID FROM `feetypes` WHERE `feetypes` LIKE '%Hostel Fee%' ")->row_array();

					$term_res = $this->db->query("SELECT * FROM `term_fees` WHERE `class_id` = '".$class_id."' AND `section_id` = '".$section_id."' AND `year_id` = '".$year_id."' ")->row_array();
					

					$admission_fee_type = $this->db->query("SELECT feetypesID,fee_amount FROM `feetypes` WHERE `feetypes` LIKE '%Admission%' ")->row_array();

					$term1 = $term_res['term1_fee'];
					$term2 = $term_res['term2_fee'];
					$term3 = $term_res['term3_fee'];

					$subtotal_amount =$term1+$term2+$term3;

					if( $this->input->post("add_admission_fee_invoice") == 1 && !empty($admission_fee_type)  ){

						if($this->input->post('studentType') == 3){ //dayscolar
						

							$fee_types = [
								array(	//term1
								'feetypeID' => $term1_fee_type['feetypesID'],
								'amount' => $term1,
								'discount' => "",
								'subtotal' => $term1,
								'paidamount' => "",
							),array(	//term2
								'feetypeID' => $term2_fee_type['feetypesID'],
								'amount' => $term2,
								'discount' => "",
								'subtotal' => $term2,
								'paidamount' => "",
							),array(	//term3
								'feetypeID' => $term3_fee_type['feetypesID'],
								'amount' => $term3,
								'discount' => "",
								'subtotal' => $term3,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
							
						}else if($this->input->post('studentType') == 1){ //trasport
							$fee_types = [
								array(	//term1
									'feetypeID' => $term1_fee_type['feetypesID'],
									'amount' => $term1,
									'discount' => "",
									'subtotal' => $term1,
									'paidamount' => "",
								),array(	//term2
									'feetypeID' => $term2_fee_type['feetypesID'],
									'amount' => $term2,
									'discount' => "",
									'subtotal' => $term2,
									'paidamount' => "",
								),array(	//term3
									'feetypeID' => $term3_fee_type['feetypesID'],
									'amount' => $term3,
									'discount' => "",
									'subtotal' => $term3,
									'paidamount' => "",
								),array(	//transport
								'feetypeID' => $fee_type_trasport['feetypesID'],
								'amount' => $p_amount,
								'discount' => "",
								'subtotal' => $p_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
							
						}if($this->input->post('studentType') == 2){ //hostel
							$fee_types = [
								array(	//term1
									'feetypeID' => $term1_fee_type['feetypesID'],
									'amount' => $term1,
									'discount' => "",
									'subtotal' => $term1,
									'paidamount' => "",
								),array(	//term2
									'feetypeID' => $term2_fee_type['feetypesID'],
									'amount' => $term2,
									'discount' => "",
									'subtotal' => $term2,
									'paidamount' => "",
								),array(	//term3
									'feetypeID' => $term3_fee_type['feetypesID'],
									'amount' => $term3,
									'discount' => "",
									'subtotal' => $term3,
									'paidamount' => "",
								),array(	//hostel
								'feetypeID' => $fee_type_hostel['feetypesID'],
								'amount' => $h_amount,
								'discount' => "",
								'subtotal' => $h_amount,
								'paidamount' => "",
							),
							array(
								'feetypeID' => $admission_fee_type['feetypesID'],
								'amount' => $admission_fee_type['fee_amount'],
								'discount' => "",
								'subtotal' => $admission_fee_type['fee_amount'],
								'paidamount' => "",
							)
						];
						}

					}else{

						if($this->input->post('studentType') == 3){ //dayscolar
							

							$fee_types = [
								array(	//term1
								'feetypeID' => $term1_fee_type['feetypesID'],
								'amount' => $term1,
								'discount' => "",
								'subtotal' => $term1,
								'paidamount' => "",
							),array(	//term2
								'feetypeID' => $term2_fee_type['feetypesID'],
								'amount' => $term2,
								'discount' => "",
								'subtotal' => $term2,
								'paidamount' => "",
							),array(	//term3
								'feetypeID' => $term3_fee_type['feetypesID'],
								'amount' => $term3,
								'discount' => "",
								'subtotal' => $term3,
								'paidamount' => "",
							)
						];
							
						}else if($this->input->post('studentType') == 1){ //trasport
							$fee_types = [
								array(	//term1
									'feetypeID' => $term1_fee_type['feetypesID'],
									'amount' => $term1,
									'discount' => "",
									'subtotal' => $term1,
									'paidamount' => "",
								),array(	//term2
									'feetypeID' => $term2_fee_type['feetypesID'],
									'amount' => $term2,
									'discount' => "",
									'subtotal' => $term2,
									'paidamount' => "",
								),array(	//term3
									'feetypeID' => $term3_fee_type['feetypesID'],
									'amount' => $term3,
									'discount' => "",
									'subtotal' => $term3,
									'paidamount' => "",
								),array(	//transport
								'feetypeID' => $fee_type_trasport['feetypesID'],
								'amount' => $p_amount,
								'discount' => "",
								'subtotal' => $p_amount,
								'paidamount' => "",
							)
						];
							
						}if($this->input->post('studentType') == 2){ //hostel
							$fee_types = [
								array(	//term1
									'feetypeID' => $term1_fee_type['feetypesID'],
									'amount' => $term1,
									'discount' => "",
									'subtotal' => $term1,
									'paidamount' => "",
								),array(	//term2
									'feetypeID' => $term2_fee_type['feetypesID'],
									'amount' => $term2,
									'discount' => "",
									'subtotal' => $term2,
									'paidamount' => "",
								),array(	//term3
									'feetypeID' => $term3_fee_type['feetypesID'],
									'amount' => $term3,
									'discount' => "",
									'subtotal' => $term3,
									'paidamount' => "",
								),array(	//hostel
								'feetypeID' => $fee_type_hostel['feetypesID'],
								'amount' => $h_amount,
								'discount' => "",
								'subtotal' => $h_amount,
								'paidamount' => "",
							)
						];
						}
					}
 					
					//[feetypeitems] => [{"feetypeID":"3","amount":"1","discount":"","subtotal":"1","paidamount":""},{"feetypeID":"52","amount":"2","discount":"","subtotal":"2","paidamount":""}]
					


					$json_fee_types = json_encode($fee_types);
					// echo "<pre>";print_r($json_fee_types);die;
					$invoice_data = array(
						'classesID' => $this->input->post("classesID"),
						'sectionID' =>$this->input->post("sectionID"),
						'studentID' => $studentID,
						'date' => date('d-m-Y'),
						'statusID' => 0,
						'payment_method' => 0,
						// 'feetypeitems' => '['.$json_fee_types.']',
						'feetypeitems' => $json_fee_types,
						'totalsubtotal' => $subtotal_amount,
						'totalpaidamount' => 0,
						'editID' => 0,
					);
					// echo "<pre>";print_r($invoice_data);die;

					$invoice_error = $this->saveinvoice($invoice_data);
					$this->db->update('student',array('invoice_error'=>$invoice_error),array('studentID'=>$studentID));

				}
					
					$flashMsg  = 'Student Registered Successfully.';
					$flashMsg .= $smsSent         ? ' SMS Sent Successfully.'      : ' SMS Sending Failed.';
					if ($waSent === true)          { $flashMsg .= ' WhatsApp Sent Successfully.'; }
					elseif ($waSent === false)     { $flashMsg .= ' WhatsApp Sending Failed.'; }
					$this->session->set_flashdata('success', $flashMsg);
					redirect(base_url("student/admission_slip/$studentID"));
				}
			} else {
				$this->data["subview"] = "student/add";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function admission_slip()
	{
		$studentID = (int) htmlentities(escapeString($this->uri->segment(3)));
		if (!$studentID) {
			redirect(base_url('student/index'));
			return;
		}
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		$student = $this->studentrelation_m->general_get_single_student(
			['srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID], TRUE
		);
		if (!customCompute($student)) {
			redirect(base_url('student/index'));
			return;
		}
		$this->data['profile'] = $student;
		$this->data['parents'] = ($student->parentID > 0)
			? $this->parents_m->get_single_parents(['parentsID' => $student->parentID])
			: null;

		// Lookup arrays
		$this->data['all_classes']   = pluck($this->classes_m->get_classes(), 'classes', 'classesID');
		$this->data['student_groups']= pluck($this->studentgroup_m->get_studentgroup(), 'group', 'studentgroupID');
		$this->data['subjects']      = pluck($this->subject_m->general_get_order_by_subject(), 'subject', 'subjectID');

		// Transport (type=1)
		$transport_details = $this->tmember_m->get_single_tmember(['studentID' => $studentID]);
		$this->data['transport_details'] = $transport_details;
		$this->data['transport_route']   = customCompute($transport_details)
			? $this->transport_m->get_transport($transport_details->transportID)
			: null;

		// Hostel (type=2)
		$this->data['hostel_details'] = $this->hmember_m->get_single_hmember(['studentID' => $studentID]);

		// Siblings
		$this->data['siblings'] = $this->studentsiblings_m->get_siblings_by_student($studentID);

		// Referred By — decode stored value to display label
		$refered_raw = $student->refered_by ?? '';
		if (strpos($refered_raw, 'teacher-') === 0) {
			$t = $this->teacher_m->get_single_teacher(['teacherID' => (int) substr($refered_raw, 8)]);
			$this->data['refered_by_label'] = customCompute($t) ? $t->name . ' [Teacher]' : $refered_raw;
		} elseif (strpos($refered_raw, 'user-') === 0) {
			$u = $this->user_m->get_single_user(['userID' => (int) substr($refered_raw, 5)]);
			$this->data['refered_by_label'] = customCompute($u) ? $u->name . ' [User]' : $refered_raw;
		} elseif (strpos($refered_raw, 'others-') === 0) {
			$this->data['refered_by_label'] = substr($refered_raw, 7);
		} else {
			$this->data['refered_by_label'] = $refered_raw;
		}

		$this->load->view('student/AdmissionSlip', $this->data);
	}

	public function edit()
	{
		if (($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
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
			$usertype = $this->session->userdata("usertype");
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$studentID = htmlentities(escapeString($this->uri->segment(3)));
			$url = htmlentities(escapeString($this->uri->segment(4)));
			if ((int)$studentID && (int)$url) {
				$this->data['classes'] = $this->classes_m->get_classes();
				$this->data['student'] = $objStudent = $this->studentrelation_m->get_single_student(array('srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID), TRUE);
				// echo "<pre>";print_r($this->data['student']);die;

				// echo "<pre>@@@@@@@@";print_r($objStudent);
				 
				$this->db->where('route_id',$objStudent->villageID);
				$this->data['pickup_points'] = $this->db->get('pickup_points')->result_array();

				$this->data['parents']  = $this->parents_m->get_parents();
				$this->data['studentgroups'] = $this->studentgroup_m->get_studentgroup();
				$this->data['villages'] = $this->village_m->get_active_villages();

				$teachers = $this->teacher_m->get_teacher();
				$users = $this->user_m->get_user();
				$combined_teachers = [];
				if(customCompute($teachers)) {
					foreach ($teachers as $teacher) {
						$combined_teachers['teacher-' . $teacher->teacherID] = $teacher->name . " [Teacher]";
					}
				}
				if(customCompute($users)) {
					foreach ($users as $user) {
						$combined_teachers['user-' . $user->userID] = $user->name . " [User]";
					}
				}
				$this->data['teachers'] = $combined_teachers;

				if (customCompute($this->data['student'])) {
					$classesID = $this->data['student']->srclassesID;
					$this->data['sections'] = $this->section_m->general_get_order_by_section(array('classesID' => $classesID));
					$this->data['optionalSubjects'] = $this->subject_m->general_get_order_by_subject(array("classesID" => $classesID, 'type' => 0));
					if ($this->input->post('optionalSubjectID')) {
						$this->data['optionalSubjectID'] = $this->input->post('optionalSubjectID');
					} else {
						$this->data['optionalSubjectID'] = 0;
					}
				}
				$this->data['transports'] = $this->transport_m->get_transport();
				$this->data["hostels"] = $this->hostel_m->get_hostel();
				$this->data['studntTransportDetails'] = $this->tmember_m->get_single_tmember(array('studentID' => $studentID), TRUE);
				$this->data['studntHostelDetails'] = $this->hmember_m->get_single_hmember(array('studentID' => $studentID), TRUE);

				if ($objStudent->studentType == 2) {
					$this->data['categorys'] = $this->category_m->get_order_by_category(array("hostelID" => $this->data['studntHostelDetails']->hostelID));
				} else {
					$this->data['categorys'] = [];
				}

				$this->data['set'] = $url;
				$this->data['existing_siblings'] = $this->studentsiblings_m->get_siblings_by_student($studentID);
				if (customCompute($this->data['student'])) {
					if ($_POST) {
						if(!empty($this->input->post("village_name"))){
							$village_name = $this->db->query('select villageName from villages where villageID='.$this->input->post("village_name"))->row()->villageName;
						}
						
						$rules = $this->rules();
						unset($rules[22]);
						unset($rules[13]);
						//unset($rules[21]);
						$this->form_validation->set_rules($rules);
						if ($this->form_validation->run() == FALSE) {
							$this->data["subview"] = "student/edit";
							$this->load->view('_layout_main', $this->data);
						} else {
							$array = array();
							$array["pickup_id"] = $this->input->post("pickup_id");
							$array["first_name"] = $this->input->post("first_name");
							$array["last_name"] = $this->input->post("last_name");
							$array["name"] = $this->input->post("name");
							$array["sex"] = $this->input->post("sex");
							$array["religion"] = $this->input->post("religion");
							$array["email"] = $this->input->post("email");
							$array["phone"] = $this->input->post("phone");
							$array["address"] = $this->input->post("address");
							$array["classesID"] = $this->input->post("classesID");
							$array["sectionID"] = $this->input->post("sectionID");
							// $array["roll"] = $this->input->post("roll");
							$array["bloodgroup"] = $this->input->post("bloodgroup");
							$array["state"] = $this->input->post("state");
							$array["country"] = $this->input->post("country");
							$array["registerNO"] = $this->input->post("registerNO");
							$array["parentID"] = $this->input->post("guargianID");
							//$array["username"] = $this->input->post("username");
							$array["modify_date"] = date("Y-m-d H:i:s");
							$array['photo'] = $this->upload_data['file']['file_name'];
							$array["villageID"] = $this->input->post('village_name');
							$array["village_name"] = $village_name;
							$array["aadharCardNumber"] = $this->input->post('aadharCardNumber');

							$array["ration_card"] = $this->input->post('ration_card');
							$array["bank_name"] = $this->input->post('bank_name');
							$array["account_no"] = $this->input->post('account_no');
							$array["ifsc_code"] = $this->input->post('ifsc_code');
							$array["branch_name"] = $this->input->post('branch_name');
							$array["joined_class"] = $this->input->post('joined_class');
							$array["rf_id"] = $this->input->post('rf_id');
							$array["alternative_phone1"] = $this->input->post('alternative_phone1');
							$array["alternative_phone2"] = $this->input->post('alternative_phone2');
							$array["caste"] = $this->input->post('cast');
							$array["sub_caste"] = $this->input->post('sub_caste'); 
							$array["pen_number"] = $this->input->post('pen_number');
							$array["child_id"] = $this->input->post('child_id');
							$array["medium"] = $this->input->post('medium')?? 'medium';
							$array["remarks"] = $this->input->post('remarks');

							$array["mole1"] = $this->input->post('mole1');
							$array["mole2"] = $this->input->post('mole2');
							$array["studentType"] = $this->input->post('studentType');
							$refered_by_val = $this->input->post('refered_by');
							if ($refered_by_val === 'others') {
								$array["refered_by"] = 'others-' . $this->input->post('refered_by_other');
							} else {
								$array["refered_by"] = $refered_by_val;
							}

							if ($this->input->post('dob')) {
								$array["dob"] 	= date("Y-m-d", strtotime($this->input->post("dob")));
							} else {
								$array["dob"] = NULL;
							}

							if ($this->input->post('admission_date')) {
								$array["admission_date"] 	= date("Y-m-d", strtotime($this->input->post("admission_date")));
							} else {
								$array["admission_date"] = NULL;
							}

							$parent_id = @$this->input->post("guargianID");
							if (isset($parent_id) && $parent_id > 0) {
								$array["parentID"] = $parent_id;
							}

							if($_POST['sectionID'] != $_POST['old_section'] || $_POST['classesID'] != $_POST['old_class']){
								 
								$this->db->where('classesID',$_POST['classesID']);
								$this->db->where('sectionID',$_POST['sectionID']);
								$cnt = $this->db->get('student')->num_rows();
								// echo $this->db->last_query();
								
								 $array["roll"] = $cnt+1;
							}

							$studentReletion = $this->studentrelation_m->general_get_order_by_student(array('srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID));
							$section = $this->section_m->general_get_section($this->input->post("sectionID"));
							$classes = $this->classes_m->get_classes($this->input->post("classesID"));

							if (customCompute($classes)) {
								$setClasses = $classes->classes;
							} else {
								$setClasses = NULL;
							}

							if (customCompute($section)) {
								$setSection = $section->section;
							} else {
								$setSection = NULL;
							}

							if ($studentID && $array["studentType"] == 1) {
								$transPortArray = array(
									"studentID" => $studentID,
									"transportID" => $this->input->post("transportID"),
									"name" => $this->input->post("name"),
									"email" => $this->input->post("email"),
									"phone" => $this->input->post("phone"),
									"tbalance" => $this->input->post("tbalance"),
								);
								$this->data["hmember"] = $this->hmember_m->get_single_hmember(array("studentID" => $studentID));

								if ($this->data["hmember"]) {
									$this->hmember_m->delete_hmember($this->data['hmember']->hmemberID);
								}
								$this->data['studntTransportDetails'] = $this->tmember_m->get_single_tmember(array('studentID' => $studentID), TRUE);
								if ($this->data['studntTransportDetails']) {
									$this->tmember_m->update_tmember($transPortArray, $this->data['studntTransportDetails']->tmemberID);
								} else {
									$transPortArray["tjoindate"] =  date("Y-m-d");
									$this->tmember_m->insert_tmember($transPortArray);
								}


								$this->student_m->update_student(array("transport" => 1, "hostel" => 0), $studentID);
							} else if ($studentID && $array["studentType"] == 2) {
								$category_main_id = $this->category_m->get_single_category(array("hostelID" => $this->input->post("hostelID"), "categoryID" =>  $this->input->post("categoryID")));
								$hostelArray = array(
									"hostelID" => $this->input->post("hostelID"),
									"categoryID" => $this->input->post("categoryID"),
									"studentID" => $studentID,
									"hbalance" => $category_main_id->hbalance,
								);
								$this->data['studntTransportDetails'] = $this->tmember_m->get_single_tmember(array('studentID' => $studentID), TRUE);
								if ($this->data['studntTransportDetails']) {
									$this->tmember_m->delete_tmember_sID($studentID);
								}

								$this->data['studntHostelDetails'] = $this->hmember_m->get_single_hmember(array('studentID' => $studentID), TRUE);
								if ($this->data['studntHostelDetails']) {
									$this->hmember_m->update_hmember($hostelArray, $this->data['studntHostelDetails']->hmemberID);
								} else {
									$hostelArray["hjoindate"] =  date("Y-m-d");
									$this->hmember_m->insert_hmember($hostelArray);
								}


								$this->student_m->update_student(array("hostel" => 1, "transport" => 0), $studentID);
							} else {
								$this->data["hmember"] = $this->hmember_m->get_single_hmember(array("studentID" => $studentID));

								if ($this->data["hmember"]) {
									$this->hmember_m->delete_hmember($this->data['hmember']->hmemberID);
								}
								$this->data['studntTransportDetails'] = $this->tmember_m->get_single_tmember(array('studentID' => $studentID), TRUE);
								if ($this->data['studntTransportDetails']) {
									$this->tmember_m->delete_tmember_sID($studentID);
								}

								$this->student_m->update_student(array("hostel" => 0, "transport" => 0), $studentID);
							}

							if (!customCompute($studentReletion)) {
								$arrayStudentRelation = array(
									'srstudentID' => $studentID,
									'srname' => $this->input->post("name"),
									'srclassesID' => $this->input->post("classesID"),
									'srclasses' => $setClasses,
									// 'srroll' => $this->input->post("roll"),
									'srregisterNO' => $this->input->post("registerNO"),
									'srsectionID' => $this->input->post("sectionID"),
									'srsection' => $setSection,
									'srstudentgroupID' => $this->input->post("studentGroupID"),
									'sroptionalsubjectID' => $this->input->post("optionalSubjectID"),
									'srschoolyearID' => $schoolyearID
								);

								if($_POST['sectionID'] != $_POST['old_section'] || $_POST['classesID'] != $_POST['old_class']){
								 
									$this->db->where('classesID',$_POST['classesID']);
									$this->db->where('sectionID',$_POST['sectionID']);
									$cnt = $this->db->get('student')->num_rows();
									// echo $this->db->last_query();
									
									 $arrayStudentRelation["srroll"] = $cnt+1;
								}

								$this->studentrelation_m->insert_studentrelation($arrayStudentRelation);
							} else {
								$arrayStudentRelation = array(
									'srname' => $this->input->post("name"),
									'srclassesID' => $this->input->post("classesID"),
									'srclasses' => $setClasses,
									// 'srroll' => $this->input->post("roll"),
									'srregisterNO' => $this->input->post("registerNO"),
									'srsectionID' => $this->input->post("sectionID"),
									'srsection' => $setSection,
									'srstudentgroupID' => $this->input->post("studentGroupID"),
									'sroptionalsubjectID' => $this->input->post("optionalSubjectID"),
								);
								if($_POST['sectionID'] != $_POST['old_section'] || $_POST['classesID'] != $_POST['old_class']){
								 
									$this->db->where('classesID',$_POST['classesID']);
									$this->db->where('sectionID',$_POST['sectionID']);
									$cnt = $this->db->get('student')->num_rows();
									// echo $this->db->last_query();
									
									 $arrayStudentRelation["srroll"] = $cnt+1;
								}
								$this->studentrelation_m->update_studentrelation_with_multicondition($arrayStudentRelation, array('srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID));
							}

							$studentExtendArray = array(
								'studentgroupID' => $this->input->post('studentGroupID'),
								'optionalsubjectID' => $this->input->post('optionalSubjectID'),
								'extracurricularactivities' => $this->input->post('extraCurricularActivities'),
								'remarks' => $this->input->post('remarks')
							);

							$arrParentData =  array(
								'phone' => $this->input->post("phone"),
								'father_name' => $this->input->post("father_name"),
								'mother_name' =>  $this->input->post("mother_name") ? $this->input->post("mother_name") : '-',
								'father_aadhar' => $this->input->post("father_aadhar"),
								'mother_aadhar' => $this->input->post("mother_aadhar"),
							);



							$this->studentextend_m->update_studentextend_by_studentID($studentExtendArray, $studentID);


								// echo $objStudent->parentID;die;
							$this->student_m->update_student($array, $studentID);

							if($objStudent->parentID == 0 || $objStudent->parentID == null){
								if ($studentID > 0) {
									$parent_array = array();
									$parent_array['name'] = $this->input->post("father_name") ?? '';
									$parent_array['father_name'] = $this->input->post("father_name") ?? '';
									$parent_array['father_aadhar'] = $this->input->post("father_aadhar") ?? '';
									$parent_array['mother_aadhar'] = $this->input->post("mother_aadhar") ?? '';
									$parent_array['mother_name'] =  $this->input->post("mother_name") ? $this->input->post("mother_name") : '-';
									$parent_array["phone"] = $this->input->post("phone") ?? 0;
									$parent_array['photo'] = "default.png";
									$parent_array['usertypeID'] = 4;
									$parent_array['active'] = 1;
									$parent_array['create_date'] = date("Y-m-d H:i:s");
									$parent_array['modify_date'] = date("Y-m-d H:i:s");

									$parent_id = $this->student_m->insert_parent($parent_array);
									// echo $this->db->last_query
									if ($parent_id > 0) {
										$this->student_m->update_student(array("parentID" => $parent_id), $studentID);
									}
								}
							}else{
								$this->student_m->update_parent($arrParentData, $objStudent->parentID);
							}


							//Update invoice and payment tables when class or section changes
							if($_POST['sectionID'] != $_POST['old_section'] || $_POST['classesID'] != $_POST['old_class']){
								$schoolyearID = $this->session->userdata('defaultschoolyearID');
								$newClassesID = $this->input->post("classesID");
								$newSectionID = $this->input->post("sectionID");
								
								//Update maininvoice table
								$this->db->where('maininvoicestudentID', $studentID);
								$this->db->where('maininvoiceschoolyearID', $schoolyearID);
								$this->db->update('maininvoice', array(
									'maininvoiceclassesID' => $newClassesID,
									'maininvoicesectionID' => $newSectionID
								));
								
								//Update globalpayment table
								$this->db->where('studentID', $studentID);
								$this->db->where('schoolyearID', $schoolyearID);
								$this->db->update('globalpayment', array(
									'classesID' => $newClassesID,
									'sectionID' => $newSectionID
								));
								
								//Update invoice table
								$this->db->where('studentID', $studentID);
								$this->db->where('schoolyearID', $schoolyearID);
								$this->db->update('invoice', array(
									'classesID' => $newClassesID 
								));
							}

							$existing_sibs = $this->studentsiblings_m->get_siblings_by_student($studentID);
							$existing_sib_ids = array_map(function($s){ return (int)$s->sibling_studentID; }, $existing_sibs ?: array());
							$new_sib_ids = array_filter(array_map('intval', (array)$this->input->post('sibling_studentID')));
							foreach (array_diff($existing_sib_ids, $new_sib_ids) as $removed) {
								$this->studentsiblings_m->delete_pair($studentID, $removed);
								$this->studentsiblings_m->delete_pair($removed, $studentID);
							}
							foreach (array_diff($new_sib_ids, $existing_sib_ids) as $added) {
								if ($added != $studentID) {
									$this->studentsiblings_m->insert_sibling(array('studentID' => $studentID, 'sibling_studentID' => $added));
									$this->studentsiblings_m->insert_sibling(array('studentID' => $added, 'sibling_studentID' => $studentID));
								}
							}

							$this->session->set_flashdata('success', $this->lang->line('menu_success'));
							redirect(base_url("student/index/$url"));
						}
					} else {
						$this->data["subview"] = "student/edit";
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

	public function get_students_by_class_section()
	{
		$classesID = (int)$this->input->get('classesID');
		$sectionID = (int)$this->input->get('sectionID');
		$schoolyearID = $this->session->userdata('defaultschoolyearID');

		$this->db->select('student.studentID, student.name, studentrelation.srroll');
		$this->db->from('studentrelation');
		$this->db->join('student', 'student.studentID = studentrelation.srstudentID', 'INNER');
		$this->db->where('studentrelation.srclassesID', $classesID);
		$this->db->where('studentrelation.srschoolyearID', $schoolyearID);
		$this->db->where('student.name IS NOT NULL', NULL, FALSE);
		$this->db->where('student.name !=', '');
		if ($sectionID > 0) {
			$this->db->where('studentrelation.srsectionID', $sectionID);
		}
		$this->db->order_by('studentrelation.srroll', 'ASC');
		$students = $this->db->get()->result();

		$result = array();
		if ($students) {
			foreach ($students as $s) {
				$result[] = array('studentID' => $s->studentID, 'name' => $s->name, 'roll' => $s->srroll);
			}
		}
		header('Content-Type: application/json');
		echo json_encode($result);
		exit;
	}

	public function view()
	{
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/custom-scrollbar/jquery.mCustomScrollbar.css'
			),
			'js' => array(
				'assets/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js'
			)
		);

		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		$id = htmlentities(escapeString($this->uri->segment(3)));
		$url = htmlentities(escapeString($this->uri->segment(4)));
		$this->getView($id, $url);
	}

	public function print_preview()
	{
		if (permissionChecker('student_view') || (($this->session->userdata('usertypeID') == 3) && permissionChecker('student') && ($this->session->userdata('loginuserID') == htmlentities(escapeString($this->uri->segment(3)))))) {
			$usertypeID = $this->session->userdata('usertypeID');
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$this->data['studentgroups'] = pluck($this->studentgroup_m->get_studentgroup(), 'group', 'studentgroupID');
			$this->data['optionalSubjects'] = pluck($this->subject_m->general_get_order_by_subject(array('type' => 0)), 'subject', 'subjectID');
			$id = htmlentities(escapeString($this->uri->segment(3)));
			$url = htmlentities(escapeString($this->uri->segment(4)));
			if ((int)$id && (int)$url) {
				$this->data["student"] = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srclassesID' => $url, 'srschoolyearID' => $schoolyearID), TRUE);
				if (customCompute($this->data["student"])) {
					$this->data['usertype'] = $this->usertype_m->get_single_usertype(array('usertypeID' => $this->data['student']->usertypeID));
					$this->data["class"] = $this->classes_m->general_get_classes($this->data['student']->srclassesID);
					$this->data["section"] = $this->section_m->general_get_section($this->data['student']->srsectionID);
					$this->reportPDF('studentmodule.css', $this->data, 'student/print_preview');
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

	public function send_mail()
	{
		$retArray['status'] = FALSE;
		$retArray['message'] = '';
		if (permissionChecker('student_view') || (($this->session->userdata('usertypeID') == 3) && permissionChecker('student') && ($this->session->userdata('loginuserID') == $this->input->post('studentID')))) {
			if ($_POST) {
				$rules = $this->send_mail_rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
					echo json_encode($retArray);
					exit;
				} else {
					$this->data['studentgroups'] = pluck($this->studentgroup_m->get_studentgroup(), 'group', 'studentgroupID');
					$this->data['optionalSubjects'] = pluck($this->subject_m->general_get_order_by_subject(array('type' => 0)), 'subject', 'subjectID');
					$id = $this->input->post('studentID');
					$url = $this->input->post('classesID');
					if ((int)$id && (int)$url) {
						$schoolyearID = $this->session->userdata('defaultschoolyearID');
						$this->data["student"] = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srclassesID' => $url, 'srschoolyearID' => $schoolyearID), TRUE);
						if (customCompute($this->data["student"])) {
							$this->data['usertype'] = $this->usertype_m->get_single_usertype(array('usertypeID' => $this->data['student']->usertypeID));
							$this->data["class"] = $this->classes_m->general_get_single_classes(array('classesID' => $this->data['student']->srclassesID));
							$this->data["section"] = $this->section_m->general_get_single_section(array('sectionID' => $this->data['student']->srsectionID));
							$email = $this->input->post('to');
							$subject = $this->input->post('subject');
							$message = $this->input->post('message');
							$this->reportSendToMail('studentmodule.css', $this->data, 'student/print_preview', $email, $subject, $message);
							$retArray['message'] = "Message";
							$retArray['status'] = TRUE;
							echo json_encode($retArray);
							exit;
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
				$retArray['message'] = $this->lang->line('student_permissionmethod');
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['message'] = $this->lang->line('student_permission');
			echo json_encode($retArray);
			exit;
		}
	}

	public function delete()
	{
		if (($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$id = htmlentities(escapeString($this->uri->segment(3)));
			$url = htmlentities(escapeString($this->uri->segment(4)));
			if ((int)$id && (int)$url) {
				$this->data['student'] = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srschoolyearID' => $schoolyearID));
				if (customCompute($this->data['student'])) {
					if (config_item('demo') == FALSE) {
						if ($this->data['student']->photo != 'default.png' && $this->data['student']->photo != 'defualt.png') {
							if (file_exists(FCPATH . 'uploads/images/' . $this->data['student']->photo)) {
								unlink(FCPATH . 'uploads/images/' . $this->data['student']->photo);
							}
						}
					}
					$this->student_m->delete_student($id);
					$this->studentextend_m->delete_studentextend_by_studentID($id);
					$this->session->set_flashdata('success', $this->lang->line('menu_success'));
					redirect(base_url("student/index/$url"));
				} else {
					redirect(base_url("student/index"));
				}
			} else {
				redirect(base_url("student/index/$url"));
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function multi_delete()
	{
		if (($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
			$ids = $this->input->post('ids');
			$url = $this->input->post('url');
			if ($ids) {
				if (is_string($ids)) {
					$ids = explode(',', $ids);
				}
				$ids = array_filter(array_map('intval', (array)$ids));
				if (!customCompute($ids)) {
					if ($url) redirect(base_url("student/index/$url"));
					else redirect(base_url("student/index"));
				}

				// fetch students to remove photos
				$this->db->where_in('studentID', $ids);
				$students = $this->db->get('student')->result();
				if (customCompute($students)) {
					if (config_item('demo') == FALSE) {
						foreach ($students as $stu) {
							if (isset($stu->photo) && $stu->photo != 'default.png' && $stu->photo != 'defualt.png') {
								if (file_exists(FCPATH . 'uploads/images/' . $stu->photo)) {
									@unlink(FCPATH . 'uploads/images/' . $stu->photo);
								}
							}
						}
					}
				}

				// delete students and related extended rows
				$this->student_m->delete_multiple_student($ids);

				$this->session->set_flashdata('success', $this->lang->line('menu_success'));
				if ($url) redirect(base_url("student/index/$url"));
				else redirect(base_url("student/index"));
			} else {
				if ($url) redirect(base_url("student/index/$url"));
				else redirect(base_url("student/index"));
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function update_login_details()
	{
		header('Content-Type: application/json');
		if (!permissionChecker('student_edit')) {
			echo json_encode(['status' => false, 'message' => 'Permission denied']); return;
		}
		$studentID = (int)$this->input->post('studentID');
		$username  = trim($this->input->post('username'));
		$password  = trim($this->input->post('password'));

		if (!$studentID || !$username) {
			echo json_encode(['status' => false, 'message' => 'Username is required']); return;
		}

		// Check username not already taken by another student
		$existing = $this->db->where('username', $username)->where('studentID !=', $studentID)->get('student')->row();
		if ($existing) {
			echo json_encode(['status' => false, 'message' => 'Username already in use by another student']); return;
		}

		$update = ['username' => $username];
		if (!empty($password)) {
			$update['password'] = $this->student_m->hash($password);
		}

		$this->student_m->update_student($update, $studentID);
		echo json_encode(['status' => true, 'message' => 'Login details updated successfully']);
	}

	public function send_login_sms()
	{
		header('Content-Type: application/json');
		if (!permissionChecker('student_edit')) {
			echo json_encode(['status' => false, 'message' => 'Permission denied']); return;
		}
		$id = (int)$this->input->post('id');
		if (!$id) {
			echo json_encode(['status' => false, 'message' => 'Invalid student ID']); return;
		}
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		$student = $this->studentrelation_m->general_get_single_student(['srstudentID' => $id, 'srschoolyearID' => $schoolyearID], TRUE);
		if (!customCompute($student)) {
			echo json_encode(['status' => false, 'message' => 'Student not found']); return;
		}
		$this->load->model('mailandsmstemplate_m');
		$template = $this->mailandsmstemplate_m->get_mailandsmstemplate(3);
		$status = ($template && !empty($template->template)) ? $this->userConfigSMS($template->template, $student, 3, 'msg91') : array();
		if (!empty($status['check'])) {
			echo json_encode(['status' => true,  'message' => 'SMS sent to ' . $student->name]);
		} else {
			echo json_encode(['status' => false, 'message' => 'SMS failed for ' . $student->name]);
		}
	}

	public function send_login_whatsapp()
	{
		header('Content-Type: application/json');
		if (!permissionChecker('student_edit')) {
			echo json_encode(['status' => false, 'message' => 'Permission denied']); return;
		}
		$id = (int)$this->input->post('id');
		if (!$id) {
			echo json_encode(['status' => false, 'message' => 'Invalid student ID']); return;
		}
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		$student = $this->studentrelation_m->general_get_single_student(['srstudentID' => $id, 'srschoolyearID' => $schoolyearID], TRUE);
		if (!customCompute($student)) {
			echo json_encode(['status' => false, 'message' => 'Student not found']); return;
		}
		$waTemplate = $this->db->get_where('whatapp_templates', ['short_name' => 'STUDENT_REGISTRATION'])->row();
		if (!$waTemplate) {
			echo json_encode(['status' => false, 'message' => 'WhatsApp template not configured']); return;
		}
		$this->load->model('Whatsapp_m');
		$setting     = $this->Setting_m->get_setting();
		$school_name = !empty($setting->sname)   ? $setting->sname   : '';
		$website     = !empty($setting->website) ? $setting->website : '';
		$waPhone     = !empty($student->alternative_phone1) ? $student->alternative_phone1 : $student->phone;
		$params      = implode(',', [$student->name, $school_name, $student->username, $student->phone, $website]);
		$waResult    = $this->Whatsapp_m->sendWhatsapp($waPhone, $params, $waTemplate->template_name);
		if ($waResult !== false) {
			echo json_encode(['status' => true,  'message' => 'WhatsApp sent to ' . $student->name]);
		} else {
			echo json_encode(['status' => false, 'message' => 'WhatsApp failed for ' . $student->name]);
		}
	}

	public function send_bulk_login_sms()
	{
		header('Content-Type: application/json');
		if (!permissionChecker('student_edit')) {
			echo json_encode(['status' => false, 'message' => 'Permission denied']); return;
		}
		$ids = array_filter(array_map('intval', explode(',', $this->input->post('ids'))));
		if (!$ids) {
			echo json_encode(['status' => false, 'message' => 'No students selected']); return;
		}
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		$this->load->model('mailandsmstemplate_m');
		$template = $this->mailandsmstemplate_m->get_mailandsmstemplate(3);
		$sent = 0; $failed = 0;
		foreach ($ids as $id) {
			$student = $this->studentrelation_m->general_get_single_student(['srstudentID' => $id, 'srschoolyearID' => $schoolyearID], TRUE);
			if (!customCompute($student)) { $failed++; continue; }
			$status = ($template && !empty($template->template)) ? $this->userConfigSMS($template->template, $student, 3, 'msg91') : array();
			!empty($status['check']) ? $sent++ : $failed++;
		}
		$msg = 'SMS sent: ' . $sent;
		if ($failed) $msg .= ', Failed: ' . $failed;
		echo json_encode(['status' => true, 'message' => $msg]);
	}

	public function send_bulk_login_whatsapp()
	{
		header('Content-Type: application/json');
		if (!permissionChecker('student_edit')) {
			echo json_encode(['status' => false, 'message' => 'Permission denied']); return;
		}
		$ids = array_filter(array_map('intval', explode(',', $this->input->post('ids'))));
		if (!$ids) {
			echo json_encode(['status' => false, 'message' => 'No students selected']); return;
		}
		$waTemplate = $this->db->get_where('whatapp_templates', ['short_name' => 'STUDENT_REGISTRATION'])->row();
		if (!$waTemplate) {
			echo json_encode(['status' => false, 'message' => 'WhatsApp template not configured']); return;
		}
		$this->load->model('Whatsapp_m');
		$setting     = $this->Setting_m->get_setting();
		$school_name = !empty($setting->sname)   ? $setting->sname   : '';
		$website     = !empty($setting->website) ? $setting->website : '';
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		$sent = 0; $failed = 0;
		foreach ($ids as $id) {
			$student = $this->studentrelation_m->general_get_single_student(['srstudentID' => $id, 'srschoolyearID' => $schoolyearID], TRUE);
			if (!customCompute($student)) { $failed++; continue; }
			$waPhone = !empty($student->alternative_phone1) ? $student->alternative_phone1 : $student->phone;
			$params  = implode(',', [$student->name, $school_name, $student->username, $student->phone, $website]);
			$result  = $this->Whatsapp_m->sendWhatsapp($waPhone, $params, $waTemplate->template_name);
			$result !== false ? $sent++ : $failed++;
		}
		$msg = 'WhatsApp sent: ' . $sent;
		if ($failed) $msg .= ', Failed: ' . $failed;
		echo json_encode(['status' => true, 'message' => $msg]);
	}

	public function unique_roll()
	{
		$id = htmlentities(escapeString($this->uri->segment(3)));
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		if ((int)$id) {
			$student = $this->studentrelation_m->general_get_order_by_student(array("srroll" => $this->input->post("roll"), "srstudentID !=" => $id, "srclassesID" => $this->input->post('classesID'), "srsectionID" => $this->input->post('sectionID'), 'srschoolyearID' => $schoolyearID));
			if (customCompute($student)) {
				$this->form_validation->set_message("unique_roll", "The %s is already exists.");
				return FALSE;
			}
			return TRUE;
		} else {
			$student = $this->studentrelation_m->general_get_order_by_student(array("srroll" => $this->input->post("roll"), "srclassesID" => $this->input->post('classesID'), 'srschoolyearID' => $schoolyearID));

			if (customCompute($student)) {
				$this->form_validation->set_message("unique_roll", "The %s is already exists.");
				return FALSE;
			}
			return TRUE;
		}
	}

	public function lol_username()
	{
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if ((int)$id) {
			$student = $this->student_m->general_get_single_student(array('studentID' => $id));
			$tables = array('student' => 'student', 'parents' => 'parents', 'teacher' => 'teacher', 'user' => 'user', 'systemadmin' => 'systemadmin');
			$array = array();
			$i = 0;
			foreach ($tables as $table) {
				$user = $this->student_m->get_username($table, array("username" => $this->input->post('username'), "username !=" => $student->username));
				if (customCompute($user)) {
					$this->form_validation->set_message("lol_username", "%s already exists");
					$array['permition'][$i] = 'no';
				} else {
					$array['permition'][$i] = 'yes';
				}
				$i++;
			}
			if (in_array('no', $array['permition'])) {
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			$tables = array('student' => 'student', 'parents' => 'parents', 'teacher' => 'teacher', 'user' => 'user', 'systemadmin' => 'systemadmin');
			$array = array();
			$i = 0;
			foreach ($tables as $table) {
				$user = $this->student_m->get_username($table, array("username" => $this->input->post('username')));
				if (customCompute($user)) {
					$this->form_validation->set_message("lol_username", "%s already exists");
					$array['permition'][$i] = 'no';
				} else {
					$array['permition'][$i] = 'yes';
				}
				$i++;
			}

			if (in_array('no', $array['permition'])) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	public function date_valid($date)
	{
		if ($date) {
			if (strlen($date) < 10) {
				$this->form_validation->set_message("date_valid", "%s is not valid dd-mm-yyyy");
				return FALSE;
			} else {
				$arr = explode("-", $date);
				$dd = $arr[0];
				$mm = $arr[1];
				$yyyy = (int)$arr[2];
				if (checkdate($mm, $dd, $yyyy)) {
					return TRUE;
				} else {
					$this->form_validation->set_message("date_valid", "%s is not valid dd-mm-yyyy");
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	public function unique_classesID()
	{
		if ($this->input->post('classesID') == 0) {
			$this->form_validation->set_message("unique_classesID", "The %s field is required");
			return FALSE;
		}
		return TRUE;
	}

	public function unique_sectionID()
	{
		if ($this->input->post('sectionID') == 0) {
			$this->form_validation->set_message("unique_sectionID", "The %s field is required");
			return FALSE;
		}
		return TRUE;
	}

	public function student_list()
	{
		$classID = $this->input->post('id');
		if ((int)$classID) {
			$string = base_url("student/index/$classID");
			echo $string;
		} else {
			redirect(base_url("student/index"));
		}
	}

	public function unique_email()
	{
		if ($this->input->post('email')) {
			$id = htmlentities(escapeString($this->uri->segment(3)));
			if ((int)$id) {
				$student_info = $this->student_m->general_get_single_student(array('studentID' => $id));
				$tables = array('student' => 'student', 'parents' => 'parents', 'teacher' => 'teacher', 'user' => 'user', 'systemadmin' => 'systemadmin');
				$array = array();
				$i = 0;
				foreach ($tables as $table) {
					$user = $this->student_m->get_username($table, array("email" => $this->input->post('email'), 'username !=' => $student_info->username));
					if (customCompute($user)) {
						$this->form_validation->set_message("unique_email", "%s already exists");
						$array['permition'][$i] = 'no';
					} else {
						$array['permition'][$i] = 'yes';
					}
					$i++;
				}
				if (in_array('no', $array['permition'])) {
					return FALSE;
				} else {
					return TRUE;
				}
			} else {
				$tables = array('student' => 'student', 'parents' => 'parents', 'teacher' => 'teacher', 'user' => 'user', 'systemadmin' => 'systemadmin');
				$array = array();
				$i = 0;
				foreach ($tables as $table) {
					$user = $this->student_m->get_username($table, array("email" => $this->input->post('email')));
					if (customCompute($user)) {
						$this->form_validation->set_message("unique_email", "%s already exists");
						$array['permition'][$i] = 'no';
					} else {
						$array['permition'][$i] = 'yes';
					}
					$i++;
				}

				if (in_array('no', $array['permition'])) {
					return FALSE;
				} else {
					return TRUE;
				}
			}
		}
		return TRUE;
	}

	public function sectioncall()
	{
		$classesID = $this->input->post('id');
		if ((int)$classesID) {
			$allsection = $this->section_m->general_get_order_by_section(array('classesID' => $classesID));
			echo "<option value='0'>", $this->lang->line("student_select_section"), "</option>";
			foreach ($allsection as $value) {
				echo "<option value=\"$value->sectionID\">", $value->section, "</option>";
			}
		}
	}

	public function optionalsubjectcall()
	{
		$classesID = $this->input->post('id');
		if ((int)$classesID) {
			$allOptionalSubjects = $this->subject_m->general_get_order_by_subject(array("classesID" => $classesID, 'type' => 0));
			echo "<option value='0'>", $this->lang->line("student_select_optionalsubject"), "</option>";
			foreach ($allOptionalSubjects as $value) {
				echo "<option value=\"$value->subjectID\">", $value->subject, "</option>";
			}
		}
	}

	public function unique_capacity()
	{
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if ((int)$id) {
			if ($this->input->post('sectionID')) {
				$sectionID = $this->input->post('sectionID');
				$classesID = $this->input->post('classesID');
				$schoolyearID = $this->data['siteinfos']->school_year;
				$section = $this->section_m->general_get_section($this->input->post('sectionID'));
				$student = $this->studentrelation_m->general_get_order_by_student(array('srclassesID' => $classesID, 'srsectionID' => $sectionID, 'srschoolyearID' => $schoolyearID, 'srstudentID !=' => $id));
				if (customCompute($student) >= $section->capacity) {
					$this->form_validation->set_message("unique_capacity", "The %s capacity is full.");
					return FALSE;
				}
				return TRUE;
			} else {
				$this->form_validation->set_message("unique_capacity", "The %s field is required.");
				return FALSE;
			}
		} else {
			if ($this->input->post('sectionID')) {
				$sectionID = $this->input->post('sectionID');
				$classesID = $this->input->post('classesID');
				$schoolyearID = $this->data['siteinfos']->school_year;
				$section = $this->section_m->general_get_section($this->input->post('sectionID'));
				$student = $this->studentrelation_m->general_get_order_by_student(array('srclassesID' => $classesID, 'srsectionID' => $sectionID, 'srschoolyearID' => $schoolyearID));
				if (customCompute($student) >= $section->capacity) {
					$this->form_validation->set_message("unique_capacity", "The %s capacity is full.");
					return FALSE;
				}
				return TRUE;
			} else {
				$this->form_validation->set_message("unique_capacity", "The %s field is required.");
				return FALSE;
			}
		}
	}

	public function unique_registerNO()
	{
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if ((int)$id) {
			$student = $this->studentrelation_m->general_get_single_student(array("srregisterNO" => $this->input->post("registerNO"), "srstudentID !=" => $id));
			if (customCompute($student)) {
				$this->form_validation->set_message("unique_registerNO", "The %s is already exists.");
				return FALSE;
			}
			return TRUE;
		} else {
			$student = $this->studentrelation_m->general_get_single_student(array("srregisterNO" => $this->input->post("registerNO")));
			if (customCompute($student)) {
				$this->form_validation->set_message("unique_registerNO", "The %s is already exists.");
				return FALSE;
			}
			return TRUE;
		}
	}

	public function active()
	{
		if (permissionChecker('student_edit')) {
			$id     = $this->input->post('id');
			$status = $this->input->post('status');
			if ($id != '' && $status != '') {
				if ((int)$id) {
					$schoolyearID = $this->session->userdata('defaultschoolyearID');
					$student = $this->studentrelation_m->get_single_studentrelation(array('srstudentID' => $id, 'srschoolyearID' => $schoolyearID));
					if (customCompute($student)) {
						if ($status == 'chacked') {
							$this->student_m->update_student(array('active' => 1), $id);
							echo 'Success';
						} elseif ($status == 'unchacked') {
							$this->student_m->update_student(array('active' => 0), $id);
							echo 'Success';
						} else {
							echo "Error";
						}
					} else {
						echo 'Error';
					}
				} else {
					echo "Error";
				}
			} else {
				echo "Error";
			}
		} else {
			echo "Error";
		}
	}

	private function leave_applications_date_list_by_user_and_schoolyear($userID, $schoolyearID, $usertypeID)
	{
		$leaveapplications = $this->leaveapplication_m->get_order_by_leaveapplication(array('create_userID' => $userID, 'create_usertypeID' => $usertypeID, 'schoolyearID' => $schoolyearID, 'status' => 1));

		$retArray = [];
		if (customCompute($leaveapplications)) {
			$oneday    = 60 * 60 * 24;
			foreach ($leaveapplications as $leaveapplication) {
				for ($i = strtotime($leaveapplication->from_date); $i <= strtotime($leaveapplication->to_date); $i = $i + $oneday) {
					$retArray[] = date('d-m-Y', $i);
				}
			}
		}
		return $retArray;
	}

	public function unique_data($data)
	{
		if ($data != '') {
			if ($data == '0') {
				$this->form_validation->set_message('unique_data', 'The %s field is required.');
				return FALSE;
			}
		}
		return TRUE;
	}

	private function userConfigSMS($message, $user, $usertypeID, $getway)
	{
		// print_r($user).'====';echo $usertypeID;
		$this->load->model('mailandsmstemplate_m');
		$this->load->model('mailandsmstemplatetag_m');
		$template_id = 0;
		$template = $this->mailandsmstemplate_m->get_mailandsmstemplate(3); //login details
		$template_id = ($template && isset($template->templ_id)) ? (int)$template->templ_id : 0;
		if ($user && $usertypeID) {
			$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => $usertypeID));

			if ($usertypeID == 2) {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 2));
			} elseif ($usertypeID == 3) {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 3));
			} elseif ($usertypeID == 4) {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 4));
			} else {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 1));
			}
			// echo $userTags;
			 $message = $this->tagConvertor($userTags, $user, $message, 'SMS');
			// echo "phone==".$user->phone;
			// echo "message==".$message;
			// echo "template_id==".$template_id;
			// echo "getway==".$getway;die;
			if ($user->phone) {
				$send = $this->allgetway_send_message($getway, $user->phone, $message, $template_id);
				return $send;
			} else {
				$send = array('check' => TRUE);
				return $send;
			}
		}
	}

	private function allgetway_send_message($getway, $to, $message, $template_id = 0)
	{
		// echo 'aaaaa'.$getway . $to . $message . $template_id;die;
		$result = [];
		if ($getway == "clickatell") {
			if ($to) {
				$this->clickatell->send_message($to, $message);
				$result['check'] = TRUE;
				return $result;
			}
		} elseif ($getway == 'twilio') {
			$get = $this->twilio->get_twilio();
			$from = $get['number'];
			if ($to) {
				$response = $this->twilio->sms($from, $to, $message);
				if ($response->IsError) {
					$result['check'] = FALSE;
					$result['message'] = $response->ErrorMessage;
					return $result;
				} else {
					$result['check'] = TRUE;
					return $result;
				}
			}
		} elseif ($getway == 'bulk') {
			if ($to) {
				if ($this->bulk->send($to, $message) == TRUE) {
					$result['check'] = TRUE;
					return $result;
				} else {
					$result['check'] = FALSE;
					$result['message'] = "Check your bulk account";
					return $result;
				}
			}
		} elseif ($getway == 'msg91') {
			if ($to) {
				 
				$obj = $this->msg91->send($to, $message, $template_id);
				$campid = explode(":",(string)$obj);
				 $campid = rtrim($campid[1] ?? '','}"');
				 $campid = trim($campid,"'");
				
				if ($campid) {

				// if ($this->msg91->send($to, $message, $template_id) == TRUE) {
					$this->load->model('mailandsms_m');
					$array = array(
						'campid'=> $campid,
						'usertypeID' => 3,
						'users' =>  $to,
						'type' => ucfirst('Sms'),
						'message' => $message,
						'year' => date('Y'),
						'senderusertypeID' => $this->session->userdata('usertypeID'),
						'senderID' => $this->session->userdata('loginuserID')
					);
					$this->mailandsms_m->insert_mailandsms($array);
					$result['check'] = TRUE;
					return $result;
				} else {
					$result['check'] = FALSE;
					$result['message'] = "Check your msg91 account";
					return $result;
				}
			}
		}
	}

	private function tagConvertor($userTags, $user, $message, $sendType)
	{
		 
		$this->data['setting'] = $this->Setting_m->get_setting();
		$school_name = (isset($this->data['setting']->sname)) ? $this->data['setting']->sname : "";
		$website = (isset($this->data['setting']->website)) ? $this->data['setting']->website : "";
		
		if (customCompute($userTags)) {
			foreach ($userTags as $key => $userTag) {
				if ($userTag->tagname == '[name]') {
					if ($user->name) {
						$message = str_replace('[name]', $user->name, $message);
					} else {
						$message = str_replace('[name]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[designation]') {
					if ($user->designation) {
						$message = str_replace('[designation]', $user->designation, $message);
					} else {
						$message = str_replace('[designation]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[dob]') {
					if ($user->dob) {
						$dob =  date("d M Y", strtotime($user->dob));
						$message = str_replace('[dob]', $dob, $message);
					} else {
						$message = str_replace('[dob]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[gender]') {
					if ($user->sex) {
						$message = str_replace('[gender]', $user->sex, $message);
					} else {
						$message = str_replace('[gender]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[religion]') {
					if ($user->religion) {
						$message = str_replace('[religion]', $user->religion, $message);
					} else {
						$message = str_replace('[religion]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[email]') {
					if ($user->email) {
						$message = str_replace('[email]', $user->email, $message);
					} else {
						$message = str_replace('[email]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[phone]') {
					if ($user->phone) {
						$message = str_replace('[phone]', $user->phone, $message);
					} else {
						$message = str_replace('[phone]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[address]') {
					if ($user->address) {
						$message = str_replace('[address]', $user->address, $message);
					} else {
						$message = str_replace('[address]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[jod]') {
					if ($user->jod) {
						$jod =  date("d M Y", strtotime($user->jod));
						$message = str_replace('[jod]', $jod, $message);
					} else {
						$message = str_replace('[jod]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[username]') {
					if ($user->username) {
						$message = str_replace('[username]', $user->username, $message);
					} else {
						$message = str_replace('[username]', ' ', $message);
					}
				} elseif ($userTag->tagname == "[father's_name]") {
					if ($user->father_name) {
						$message = str_replace("[father's_name]", $user->father_name, $message);
					} else {
						$message = str_replace("[father's_name]", ' ', $message);
					}
				} elseif ($userTag->tagname == "[mother's_name]") {
					if ($user->mother_name) {
						$message = str_replace("[mother's_name]", $user->mother_name, $message);
					} else {
						$message = str_replace("[mother's_name]", ' ', $message);
					}
				} elseif ($userTag->tagname == "[father's_profession]") {
					if ($user->father_profession) {
						$message = str_replace("[father's_profession]", $user->father_profession, $message);
					} else {
						$message = str_replace("[father's_profession]", ' ', $message);
					}
				} elseif ($userTag->tagname == "[mother's_profession]") {
					if ($user->mother_profession) {
						$message = str_replace("[mother's_profession]", $user->mother_profession, $message);
					} else {
						$message = str_replace("[mother's_profession]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[class]') {
					$classes = $this->classes_m->general_get_classes($user->srclassesID);
					if (customCompute($classes)) {
						$message = str_replace('[class]', $classes->classes, $message);
					} else {
						$message = str_replace('[class]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[roll]') {
					if ($user->srroll) {
						$message = str_replace("[roll]", $user->srroll, $message);
					} else {
						$message = str_replace("[roll]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[state]') {
					if ($user->state) {
						$message = str_replace("[state]", $user->state, $message);
					} else {
						$message = str_replace("[state]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[register_no]') {
					if ($user->srregisterNO) {
						$message = str_replace("[register_no]", $user->srregisterNO, $message);
					} else {
						$message = str_replace("[register_no]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[section]') {
					if ($user->srsectionID) {
						$section = $this->section_m->general_get_section($user->srsectionID);
						if (customCompute($section)) {
							$message = str_replace('[section]', $section->section, $message);
						} else {
							$message = str_replace('[section]', ' ', $message);
						}
					} else {
						$message = str_replace("[section]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[blood_group]') {
					if ($user->bloodgroup && $user->bloodgroup != '0') {
						$message = str_replace("[blood_group]", $user->bloodgroup, $message);
					} else {
						$message = str_replace("[blood_group]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[group]') {
					if ($user->srstudentgroupID && $user->srstudentgroupID != 0) {
						$group = $this->studentgroup_m->get_studentgroup($user->srstudentgroupID);
						if (customCompute($group)) {
							$message = str_replace('[group]', $group->group, $message);
						} else {
							$message = str_replace('[group]', ' ', $message);
						}
					} else {
						$message = str_replace('[group]', ' ', $message);
					}
				} elseif ($userTag->tagname == '{{student_name}}') {
					if ($user->name) {
						$message = str_replace("{{student_name}}", $user->name, $message);
					} else {
						$message = str_replace("{{student_name}}", ' ', $message);
					}
				} elseif ($userTag->tagname == '{{roll_no}}') {
					if ($user->roll) {
						$message = str_replace("{{roll_no}}", $user->srroll, $message);
					} else {
						$message = str_replace("{{roll_no}}", ' ', $message);
					}
				} elseif ($userTag->tagname == '{{absent_date}}') {
					$message = str_replace("{{absent_date}}", date("Y-m-d"), $message);
				} elseif ($userTag->tagname == '{{school_name}}') {
					$message = str_replace("{{school_name}}", $school_name, $message);
				} elseif ($userTag->tagname == '{{url}}') {
					$message = str_replace("{{url}}", $website, $message);
				} elseif ($userTag->tagname == '{{username}}') {
					if ($user->username) {
						$message = str_replace("{{username}}", $user->username, $message);
					} else {
						$message = str_replace("{{username}}", ' ', $message);
					}
				} elseif ($userTag->tagname == '{{password}}') {
					$pass = !empty($user->phone) ? $user->phone : '';
					$message = str_replace("{{password}}", $pass, $message);
				}
			}
		}
		return $message;
	}

	private function getAdmissonNumber($objSettings)
	{
		if ($objSettings->isRandomAdmissionNumber == 1) {
			$result = $this->student_m->get_max_student();
			$num = $result->studentID + 1;
			return $objSettings->schoolCode . sprintf("%04d", $num);
		}
		return null;
	}

	public function studentTransportAndHostelDetail()
	{
		$studentID = $this->input->post('studentID');
		$data =  array();
		$data['studntTransportDetails'] = $this->tmember_m->get_single_tmember(array('studentID' => $studentID), TRUE);
		$data['studntHostelDetails'] = $this->hmember_m->get_single_hmember(array('studentID' => $studentID), TRUE);

		echo json_encode($data);
	}

	public function get_auto_roll_no(){
	    $this->db->where('schoolyearID',$this->session->userdata('defaultschoolyearID'));
		$this->db->where('classesID',$_POST['classesID']);
		$this->db->where('sectionID',$_POST['sectionID']);
		 $cnt = $this->db->get('student')->num_rows();
		 echo $cnt+1;
		//echo $this->db->last_query();die;
	}
	
	public function global_student_search($s=""){
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css',
				'assets/datepicker/datepicker.css'
			),
			'js' => array(
				'assets/select2/select2.js',
				'assets/datepicker/datepicker.js',
			)
		);

		$schoolyearID = $this->session->userdata('defaultschoolyearID');
			  $s = $_POST['global_search'];
			$this->data['students'] = $this->studentrelation_m->global_student_search($s,array( 'srschoolyearID' => $schoolyearID));

			// echo "<pre>";print_r($this->data['students']);die;
			$this->data['classes'] = $this->classes_m->get_classes();
			if (customCompute($this->data['students'])) {
				$sections = $this->section_m->general_get_order_by_section(array("classesID" => $id));
				$this->data['sections'] = $sections;
				foreach ($sections as $key => $section) {
					$this->data['allsection'][$section->sectionID] = $this->studentrelation_m->get_order_by_student(array('srclassesID' => $id, "srsectionID" => $section->sectionID, 'srschoolyearID' => $schoolyearID));
				}
			} else {
				$this->data['students'] = [];
			}
		 

		$this->data["subview"] = "student/index";
		$this->load->view('_layout_main', $this->data);
	
	}
public function study_certificate($sid=""){ 
	// error_reporting(E_ALL);
	// ini_set('display_errors', 1);

	$this->data['headerassets'] = array(
		'css' => array(
			'assets/select2/css/select2.css',
			'assets/select2/css/select2-bootstrap.css'
		),
		'js' => array(
			'assets/select2/select2.js'
		)
	);
	
	$usertype = $this->session->userdata("usertype");
	$schoolyearID = $this->session->userdata('defaultschoolyearID');
	$studentID = htmlentities(escapeString($this->uri->segment(3)));
	$url = htmlentities(escapeString($this->uri->segment(4)));
	if ((int)$studentID  ) {
		$this->data['classes'] = $this->classes_m->get_classes();
		$this->data['student'] = $objStudent = $this->studentrelation_m->get_single_student(array('srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID), TRUE);
	}
	// echo "<pre>";print_r($this->data['student'] );die;
	// $this->data["subview"] = "student/study_certificate";
	$this->data["subview"] = "studycertificate_new";
	$this->load->view('_layout_main', $this->data);


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
public function get_pickup_points(){
	$transport_id = $_POST['id'];
	$this->db->where('route_id',$transport_id);
	$p_res = $this->db->get('pickup_points')->result_array();
	$html = "<option value='0'>Select Pikcup point</option>";
	foreach($p_res as $p){
		$html .= "<option value='".$p['id']."'> ".$p['pickupPoint']." </option>";
	}
	echo $html;
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

public function checkRoll(){
	$html = "";
	$schoolyearID = $this->session->userdata('defaultschoolyearID');

	$classesID  = $_POST['classesID'];
	$sectionID  = $_POST['sectionID'];
	$rollNo  = $_POST['rollNo'];
	if(!empty($classesID) && !empty($sectionID)  ){
		$this->db->where('sectionID',$sectionID);
		$this->db->where('classesID',$classesID);
		$this->db->where('roll',$rollNo);
		$this->db->where('schoolyearID',$schoolyearID);
		$res = $this->db->get('student')->result_array();
		
		if(!empty($res)){
			$html .= "<b style='color:red' >Roll Number already allocated to ";
			foreach($res as $r){				
				$html .= $r['name']. ', ';
			}
			$html .= "</b>";
		}else{

			$this->db->where('sectionID',$sectionID);
			$this->db->where('classesID',$classesID); 
			$this->db->where('schoolyearID',$schoolyearID);
			$res = $this->db->get('student')->num_rows();

			$roll = $res + 1;
			$html .= "<b style='color:green'>Suggested Roll No : ".$roll . "</b>";
		}

	}
	echo $html;
}

public function checkRoll_update(){
	$html = "";
	$schoolyearID = $this->session->userdata('defaultschoolyearID');

	$classesID  = $_POST['classesID'];
	$sectionID  = $_POST['sectionID'];
	$rollNo  = $_POST['rollNo'];
	$studentID  = $_POST['studentID'];
	/*if(!empty($classesID) && !empty($sectionID)  ){
		$this->db->where('sectionID',$sectionID);
		$this->db->where('classesID',$classesID);
		$this->db->where('roll',$rollNo);
		$this->db->where('schoolyearID',$schoolyearID);
		$res = $this->db->get('student')->result_array();
		
		if(!empty($res)){
			$html .= "<b style='color:red' >Roll Number already allocated to ";
			foreach($res as $r){				
				$html .= $r['name']. ', ';
			}
			$html .= "</b>";
		}else{

			$this->db->where('sectionID',$sectionID);
			$this->db->where('classesID',$classesID); 
			$this->db->where('schoolyearID',$schoolyearID);
			$res = $this->db->get('student')->num_rows();

			$roll = $res + 1;
			$html .= "<b style='color:green'> enter Roll No : ".$roll . "</b>";
		}*/

		$data = array('roll'=>$rollNo);
		$data1 = array('srroll'=>$rollNo);

		$this->db->where('studentID',$studentID);
		$this->db->update('student',$data);

		$this->db->where('srstudentID',$studentID);
		$this->db->update('studentrelation',$data1);

	// }

	$update = "<b style='color:green'>Roll No updated successfully </b>";
	if(!empty($html)){
		$final = $update.'but system suggests '.$html;
	}
	echo $update = "Roll Number updated successfully!";
	// echo $final;
}


public function phone_update(){
	$html = "";
	$schoolyearID = $this->session->userdata('defaultschoolyearID');

 
	$phone  = $_POST['phone'];
	$studentID  = $_POST['studentID'];
	$parentID  = $_POST['parentID'];
	 
		$data = array('phone'=>$phone); 

		$this->db->where('studentID',$studentID);
		$this->db->update('student',$data); 
		
		$this->db->where('parentsID',$parentID);
		$this->db->update('parents',$data); 

	echo $update = "Phone Number updated successfully!";
	// echo $final;
}

public function uploadPhoto(){
	// print_r($_POST);die;
	$id = $_POST['studentID'];
	if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        
        // Directory where the file will be uploaded
        $uploadDir = 'uploads/images/';
        
        // Get the original file name and its extension
        $originalFileName = pathinfo($file['name'], PATHINFO_FILENAME); // File name without extension
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);   // File extension
        
        // Create a new file name using a timestamp and original extension
        $timestamp = time();
        $newFileName = $originalFileName . '_' . $timestamp . '.' . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;

        // Move the uploaded file to the destination folder with the new name
        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            echo "File successfully uploaded as: " . $newFileName;

			$this->db->where('studentID',$id);
			$this->db->update('student',array('photo'=>$newFileName) );
        } else {
            echo "Failed to upload file.";
        }
    }
}


private function addRemarksColumn() {
	if (!$this->db->field_exists('remarks', 'student')) {
		$sql = "ALTER TABLE `student` ADD `remarks` TEXT NULL DEFAULT NULL AFTER `pickup_id`";
		$this->db->query($sql);
	}
}

public function export_comprehensive_excel()
{
	if (!permissionChecker('student_view')) {
		$this->data["subview"] = "errorpermission";
		$this->load->view('_layout_main', $this->data);
		return;
	}

	$this->load->library('phpspreadsheet');
	$sheet = $this->phpspreadsheet->spreadsheet->getActiveSheet();
	
	// Set column widths
	$sheet->getDefaultColumnDimension()->setWidth(15);
	$sheet->getDefaultRowDimension()->setRowHeight(20);
	
	// Headers
	$headers = [
		'A1' => 'Sl No',
		'B1' => 'Register No',
		'C1' => 'Admission Date', 
		'D1' => 'First Name',
		'E1' => 'Last Name',
		'F1' => 'Full Name',
		'G1' => 'Roll Number',
		'H1' => 'Class',
		'I1' => 'Section',
		'J1' => 'Gender',
		'K1' => 'Date of Birth',
		'L1' => 'Religion',
		'M1' => 'Caste',
		'N1' => 'Sub Caste',
		'O1' => 'Blood Group',
		'P1' => 'Father Name',
		'Q1' => 'Father Aadhar',
		'R1' => 'Mother Name',
		'S1' => 'Mother Aadhar',
		'T1' => 'Phone',
		'U1' => 'Alternative Phone 1',
		'V1' => 'Alternative Phone 2',
		'W1' => 'Email',
		'X1' => 'Address',
		'Y1' => 'Village',
		'Z1' => 'State',
		'AA1' => 'Country',
		'AB1' => 'Aadhar Card Number',
		'AC1' => 'Ration Card',
		'AD1' => 'Bank Name',
		'AE1' => 'Account Number',
		'AF1' => 'IFSC Code',
		'AG1' => 'Branch Name',
		'AH1' => 'RFID',
		'AI1' => 'Student Type',
		'AJ1' => 'Transport Fee',
		'AK1' => 'PEN Number',
		'AL1' => 'Child ID',
		'AM1' => 'Medium',
		'AN1' => 'Mother Tongue',
		'AO1' => 'Mole 1',
		'AP1' => 'Mole 2',
		'AQ1' => 'Remarks',
		'AR1' => 'Active Status',
		'AS1' => 'Username',
		'AT1' => 'Referred By'
	];
	
	// Set headers
	foreach ($headers as $cell => $value) {
		$sheet->setCellValue($cell, $value);
	}
	
	// Style headers
	$headerStyle = [
		'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
		'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => '366092']],
		'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
		'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
	];
	$sheet->getStyle('A1:AT1')->applyFromArray($headerStyle);
	
	// Get comprehensive student data
	$schoolyearID = $this->session->userdata('defaultschoolyearID');
	$classesID = htmlentities(escapeString($this->uri->segment(3)));
	$sectionID = htmlentities(escapeString($this->uri->segment(4)));
	
	if ((int)$classesID) {
		if ((int)$sectionID) {
			// Filter by both class and section
			$students = $this->getComprehensiveStudentDataBySection($classesID, $sectionID, $schoolyearID);
		} else {
			// Filter by class only
			$students = $this->getComprehensiveStudentData($classesID, $schoolyearID);
		}
	} else {
		// Get all students
		$students = $this->getAllComprehensiveStudentData($schoolyearID);
	}
	
	// Populate data
	$row = 2;
	$i = 1;
	foreach ($students as $student) {
		$studentType = ['', 'TRANSPORT', 'HOSTEL', 'DAY SCHOLAR'];
		$genderMap = ['1' => 'Male', '2' => 'Female', '3' => 'Other'];
		
		$sheet->setCellValue('A' . $row, $i);
		$sheet->setCellValue('B' . $row, $student->srregisterNO ?? $student->registerNO);
		$sheet->setCellValue('C' . $row, $student->admission_date ? date('d-m-Y', strtotime($student->admission_date)) : '');
		$sheet->setCellValue('D' . $row, $student->first_name);
		$sheet->setCellValue('E' . $row, $student->last_name);
		$sheet->setCellValue('F' . $row, $student->srname ?? $student->name);
		$sheet->setCellValue('G' . $row, $student->srroll ?? $student->roll);
		$sheet->setCellValue('H' . $row, $student->srclasses ?? $student->class_name);
		$sheet->setCellValue('I' . $row, $student->srsection ?? $student->section_name);
		$sheet->setCellValue('J' . $row, $genderMap[$student->sex] ?? $student->sex);
		$sheet->setCellValue('K' . $row, $student->dob ? date('d-m-Y', strtotime($student->dob)) : '');
		$sheet->setCellValue('L' . $row, $student->religion);
		$sheet->setCellValue('M' . $row, $student->caste);
		$sheet->setCellValue('N' . $row, $student->sub_caste);
		$sheet->setCellValue('O' . $row, $student->bloodgroup);
		$sheet->setCellValue('P' . $row, $student->father_name);
		$sheet->setCellValue('Q' . $row, $student->father_aadhar);
		$sheet->setCellValue('R' . $row, $student->mother_name);
		$sheet->setCellValue('S' . $row, $student->mother_aadhar);
		$sheet->setCellValue('T' . $row, $student->phone);
		$sheet->setCellValue('U' . $row, $student->alternative_phone1);
		$sheet->setCellValue('V' . $row, $student->alternative_phone2);
		$sheet->setCellValue('W' . $row, $student->email);
		$sheet->setCellValue('X' . $row, $student->address);
		$sheet->setCellValue('Y' . $row, $student->village_name ?? $student->villageName);
		$sheet->setCellValue('Z' . $row, $student->state);
		$sheet->setCellValue('AA' . $row, $student->country);
		$sheet->setCellValue('AB' . $row, $student->aadharCardNumber);
		$sheet->setCellValue('AC' . $row, $student->ration_card);
		$sheet->setCellValue('AD' . $row, $student->bank_name);
		$sheet->setCellValue('AE' . $row, $student->account_no);
		$sheet->setCellValue('AF' . $row, $student->ifsc_code);
		$sheet->setCellValue('AG' . $row, $student->branch_name);
		$sheet->setCellValue('AH' . $row, $student->rf_id);
		$sheet->setCellValue('AI' . $row, $studentType[$student->studentType] ?? 'DAY SCHOLAR');
		$sheet->setCellValue('AJ' . $row, $student->tbalance ?? '');
		$sheet->setCellValue('AK' . $row, $student->pen_number);
		$sheet->setCellValue('AL' . $row, $student->child_id);
		$sheet->setCellValue('AM' . $row, $student->medium);
		$sheet->setCellValue('AN' . $row, $student->mother_toungue);
		$sheet->setCellValue('AO' . $row, $student->mole1);
		$sheet->setCellValue('AP' . $row, $student->mole2);
		$sheet->setCellValue('AQ' . $row, $student->remarks);
		$sheet->setCellValue('AR' . $row, $student->active ? 'Active' : 'Inactive');
		$sheet->setCellValue('AS' . $row, $student->username);
		$sheet->setCellValue('AT' . $row, $student->referred_teacher_name ?? '');
		
		$row++;
		$i++;
	}
	
	// Auto-size columns
	foreach (range('A', 'AT') as $column) {
		$sheet->getColumnDimension($column)->setAutoSize(true);
	}
	
	// Apply border to all data
	if ($row > 2) {
		$dataRange = 'A1:AT' . ($row - 1);
		$borderStyle = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];
		$sheet->getStyle($dataRange)->applyFromArray($borderStyle);
	}
	
	// Generate file
	$filename = 'comprehensive_student_data';
	if ((int)$classesID) {
		if ((int)$sectionID) {
			// Get section name for filename
			$this->db->select('classes.classes, section.section');
			$this->db->from('classes');
			$this->db->join('section', 'section.classesID = classes.classesID');
			$this->db->where('classes.classesID', $classesID);
			$this->db->where('section.sectionID', $sectionID);
			$sectionInfo = $this->db->get()->row();
			if ($sectionInfo) {
				$filename .= '_' . str_replace(' ', '_', $sectionInfo->classes) . '_' . str_replace(' ', '_', $sectionInfo->section);
			}
		} else {
			// Get class name for filename
			$this->db->select('classes');
			$this->db->from('classes');
			$this->db->where('classesID', $classesID);
			$classInfo = $this->db->get()->row();
			if ($classInfo) {
				$filename .= '_' . str_replace(' ', '_', $classInfo->classes);
			}
		}
	}
	$filename .= '_' . date('Y-m-d_H-i-s') . '.xlsx';
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="' . $filename . '"');
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1'); // IE 9
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0
	
	$this->phpspreadsheet->output($this->phpspreadsheet->spreadsheet);
}

private function getComprehensiveStudentData($classesID, $schoolyearID)
{
	$this->db->select('
		student.*,
		studentrelation.*,
		villages.villageName,
		parents.father_name,
		parents.mother_name,
		parents.father_aadhar,
		parents.mother_aadhar,
		classes.classes as class_name,
		section.section as section_name,
		tmember.tbalance
	');
	$this->db->from('studentrelation');
	$this->db->join('student', 'student.studentID = studentrelation.srstudentID', 'LEFT');
	$this->db->join('villages', 'villages.villageID = student.villageID', 'LEFT');
	$this->db->join('parents', 'parents.parentsID = student.parentID', 'LEFT');
	$this->db->join('classes', 'classes.classesID = studentrelation.srclassesID', 'LEFT');
	$this->db->join('section', 'section.sectionID = studentrelation.srsectionID', 'LEFT');
	$this->db->join('tmember', 'tmember.studentID = student.studentID', 'LEFT');
	$this->db->where('studentrelation.srclassesID', $classesID);
	$this->db->where('studentrelation.srschoolyearID', $schoolyearID);
	$this->db->where('student.active', 1);
	$this->db->order_by('studentrelation.srroll', 'ASC');
	
	$query = $this->db->get();
	return $query->result();
}

private function getAllComprehensiveStudentData($schoolyearID)
{
	$this->db->select('
		student.*,
		studentrelation.*,
		villages.villageName,
		parents.father_name,
		parents.mother_name,
		parents.father_aadhar,
		parents.mother_aadhar,
		classes.classes as class_name,
		section.section as section_name,
		tmember.tbalance
	');
	$this->db->from('studentrelation');
	$this->db->join('student', 'student.studentID = studentrelation.srstudentID', 'LEFT');
	$this->db->join('villages', 'villages.villageID = student.villageID', 'LEFT');
	$this->db->join('parents', 'parents.parentsID = student.parentID', 'LEFT');
	$this->db->join('classes', 'classes.classesID = studentrelation.srclassesID', 'LEFT');
	$this->db->join('section', 'section.sectionID = studentrelation.srsectionID', 'LEFT');
	$this->db->join('tmember', 'tmember.studentID = student.studentID', 'LEFT');
	$this->db->where('studentrelation.srschoolyearID', $schoolyearID);
	$this->db->where('student.active', 1);
	$this->db->order_by('studentrelation.srclassesID, studentrelation.srroll', 'ASC');
	
	$query = $this->db->get();
	return $query->result();
}

private function getComprehensiveStudentDataBySection($classesID, $sectionID, $schoolyearID)
{
	$this->db->select('
		student.*,
		studentrelation.*,
		villages.villageName,
		parents.father_name,
		parents.mother_name,
		parents.father_aadhar,
		parents.mother_aadhar,
		classes.classes as class_name,
		section.section as section_name,
		tmember.tbalance
	');
	$this->db->from('studentrelation');
	$this->db->join('student', 'student.studentID = studentrelation.srstudentID', 'LEFT');
	$this->db->join('villages', 'villages.villageID = student.villageID', 'LEFT');
	$this->db->join('parents', 'parents.parentsID = student.parentID', 'LEFT');
	$this->db->join('classes', 'classes.classesID = studentrelation.srclassesID', 'LEFT');
	$this->db->join('section', 'section.sectionID = studentrelation.srsectionID', 'LEFT');
	$this->db->join('tmember', 'tmember.studentID = student.studentID', 'LEFT');
	$this->db->where('studentrelation.srclassesID', $classesID);
	$this->db->where('studentrelation.srsectionID', $sectionID);
	$this->db->where('studentrelation.srschoolyearID', $schoolyearID);
	$this->db->where('student.active', 1);
	$this->db->order_by('studentrelation.srroll', 'ASC');
	
	$query = $this->db->get();
	return $query->result();
}

}
