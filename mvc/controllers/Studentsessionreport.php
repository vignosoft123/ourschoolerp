<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Studentsessionreport extends Admin_Controller {
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
	function __construct() {
		parent::__construct();
		$this->load->model("classes_m");
		$this->load->model('section_m');
		$this->load->model("studentrelation_m");
		$this->load->model("exam_m");
		$this->load->model("markpercentage_m");
		$this->load->model("subject_m");
		$this->load->model("setting_m");
		$this->load->model("mark_m");
		$this->load->model("grade_m");
		$this->load->model("studentgroup_m");
		$this->load->model("marksetting_m");

		$language = $this->session->userdata('lang');
		$this->lang->load('studentsessionreport', $language);

// 		error_reporting(E_ALL);
// ini_set('display_errors', 1);
	}
	
 	public function index() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css',
				'assets/custom-scrollbar/jquery.mCustomScrollbar.css',
			),
			'js' => array(
				'assets/select2/select2.js',
				'assets/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js',
			)
		);
		$this->data['classes'] = $this->classes_m->general_get_classes();
		$schoolyearID            = $this->session->userdata('defaultschoolyearID');
		// $this->data['students']  = $this->studentrelation_m->general_get_order_by_student(['srschoolyearID'=> $schoolyearID]);
		$this->data["subview"]   = "report/studentsession/StudentsessionReportView";
		$this->load->view('_layout_main', $this->data);
	}

	public function getstudentsessionreport_bkp () {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';
		if(permissionChecker('studentsessionreport')) {
			if($_POST) {
				$studentID  = $this->input->post('studentID');
				$classID  = $this->input->post('classID');
				$sectionID  = $this->input->post('sectionID');
				$rules      = $this->rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
				    echo json_encode($retArray);
				    exit;
				} else {

					$markArray    = [];
					$queryArray   = [];
					$schoolyearID = $this->session->userdata('defaultschoolyearID');
					$queryArray['srschoolyearID'] = $schoolyearID;
					$markArray['schoolyearID']    = $schoolyearID;

					if((int)$studentID > 0) {
						$markArray['studentID']    = $studentID;
						$queryArray['srstudentID'] = $studentID;
					}

					$students               = pluck($this->studentrelation_m->general_get_order_by_student($queryArray), 'obj', 'srschoolyearID');
					if (customCompute($students)) {
						foreach ($students as $s) {
							$queryArray['srclassesID'] = $s->srclassesID;
							$queryArray['srsectionID'] = $s->srsectionID;
							$markArray['classesID']    = $s->srclassesID;
							$markArray['sectionID']    = $s->srsectionID;
							break;
						}
					}
					$marks                  = $this->mark_m->student_all_mark_array($markArray);
					$mandatorySubjects      = pluck_multi_array_key($this->subject_m->general_get_order_by_subject(array('type' => 1)), 'obj', 'classesID', 'subjectID');
					$optionalSubjects       = pluck_multi_array_key($this->subject_m->general_get_order_by_subject(array('type' => 0)), 'obj', 'classesID', 'subjectID');

					$settingmarktypeID      = $this->data['siteinfos']->marktypeID;
					// $markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages();
					$markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages_new($classID,$sectionID);
					$percentageArr          = pluck($this->markpercentage_m->get_markpercentage(), 'obj', 'markpercentageID');
					


					$retMark = [];
					if(customCompute($marks)) {
						foreach ($marks as $mark) {
							$value = isset($mark->mark) ? $mark->mark : 0;
							if (isset($mark->eattendance) && strtolower(trim($mark->eattendance)) === 'absent') {
								$value = 'A';
							}
							$retMark[$mark->schoolyearID][$mark->classesID][$mark->examID][$mark->subjectID][$mark->markpercentageID] = $value;
						}
					}

					$this->data['classes']           = pluck($this->classes_m->general_get_classes(),'classes','classesID');
					$this->data['sections']          = pluck($this->section_m->general_get_section(),'section','sectionID');
					$this->data['groups']            = pluck($this->studentgroup_m->get_studentgroup(),'group','studentgroupID');
					$this->data['exams']             = pluck($this->exam_m->get_exam(),'exam','examID');
					$this->data['grades']            = $this->grade_m->get_grade();
					$this->data['schoolyears']       = pluck($this->schoolyear_m->get_schoolyear(),'schoolyear','schoolyearID');

					$this->data['studentID']         = $studentID;
					$this->data['retMark']           = $retMark;
					$this->data['percentageArr']     = $percentageArr;
					$this->data['mandatorySubjects'] = $mandatorySubjects;
					$this->data['optionalSubjects']  = $optionalSubjects;					
					$this->data['students']          = $students;
					$this->data['settingmarktypeID']       = $settingmarktypeID;
					$this->data['markpercentagesmainArr']  = $markpercentagesmainArr;

					$retArray['render'] = $this->load->view('report/studentsession/StudentsessionReport',$this->data,true);
					$retArray['status'] = TRUE;
					echo json_encode($retArray);
					exit();
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

	public function getstudentsessionreport () {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';
		if(permissionChecker('studentsessionreport')) {
			if($_POST) {
				$studentID  = $this->input->post('studentID');
				$classID  = $this->input->post('classID');
				$sectionID  = $this->input->post('sectionID');
				$rules      = $this->rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
				    echo json_encode($retArray);
				    exit;
				} else {

					$markArray    = [];
					$queryArray   = [];
					$schoolyearID = $this->session->userdata('defaultschoolyearID');
					$queryArray['srschoolyearID'] = $schoolyearID;
					$markArray['schoolyearID']    = $schoolyearID;

					if((int)$studentID > 0) {
						$markArray['studentID']    = $studentID;
						$queryArray['srstudentID'] = $studentID;
					}

					if((int)$classID > 0) {
						$queryArray['srclassesID'] = $classID;
						$markArray['classesID']    = $classID;
					}
					if((int)$sectionID > 0) {
						$queryArray['srsectionID'] = $sectionID;
						$markArray['sectionID']    = $sectionID;
					}

					$students               = pluck($this->studentrelation_m->general_get_order_by_student($queryArray), 'obj', 'srschoolyearID');
					$marks                  = $this->mark_m->student_all_mark_array($markArray);
					$mandatorySubjects      = pluck_multi_array_key($this->subject_m->general_get_order_by_subject(array('type' => 1)), 'obj', 'classesID', 'subjectID');
					$optionalSubjects       = pluck_multi_array_key($this->subject_m->general_get_order_by_subject(array('type' => 0)), 'obj', 'classesID', 'subjectID');

					$settingmarktypeID      = $this->data['siteinfos']->marktypeID;
					// $markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages();
					$markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages_new($classID,$sectionID);
					$percentageArr          = pluck($this->markpercentage_m->get_markpercentage(), 'obj', 'markpercentageID');
					
					 
					$query = $this->db->query("SELECT e.*, es.max_mark FROM exam e LEFT JOIN examschedule es ON es.examID = e.examID where es.classesID=$classID and es.sectionID=$sectionID  GROUP by examID");
					$result = $query->result_array();					
					$examMarks = [];
					foreach ($result as $row) {
						$examMarks[$row['examID']] = $row['max_mark'];
					}
					$this->data['exam_max_marks'] = $examMarks;

					// Build exam -> subjects mapping from examschedule
					$examSubjects = [];
					$es = $this->db->select('examID,subjectID')->from('examschedule')->where('classesID', $classID)->where('sectionID', $sectionID)->get()->result();
					if(customCompute($es)){
						foreach($es as $row) {
							$examSubjects[$row->examID][] = $row->subjectID;
						}
					}
					$this->data['examSubjects'] = $examSubjects;

					// Build scheduled subject IDs and objects (subjects scheduled for any exam for this class/section)
					$scheduledSubjectIDs = [];
					if (customCompute($examSubjects)) {
						foreach ($examSubjects as $e => $sarr) {
							foreach ($sarr as $sid) {
								$scheduledSubjectIDs[] = $sid;
							}
						}
					}
					$scheduledSubjectIDs = array_unique($scheduledSubjectIDs);
					$scheduledSubjects = [];
					if (customCompute($scheduledSubjectIDs)) {
						foreach ($scheduledSubjectIDs as $sid) {
							$sub = $this->subject_m->get_subject($sid, TRUE);
							if ($sub) {
								$scheduledSubjects[$sid] = $sub;
							}
						}
					}
					$this->data['scheduledSubjectIDs'] = $scheduledSubjectIDs;
					$this->data['scheduledSubjects'] = $scheduledSubjects;
					// echo "<pre>";print_r($this->data['exam_max_marks']);die;

					$retMark = [];
					if(customCompute($marks)) {
						foreach ($marks as $mark) {
							$value = isset($mark->mark) ? $mark->mark : 0;
							if (isset($mark->eattendance) && strtolower(trim($mark->eattendance)) === 'absent') {
								$value = 'A';
							}
							$retMark[$mark->schoolyearID][$mark->classesID][$mark->examID][$mark->subjectID][$mark->markpercentageID] = $value;
						}
					}

					$this->data['classes']           = pluck($this->classes_m->general_get_classes(),'classes','classesID');
					$this->data['sections']          = pluck($this->section_m->general_get_section(),'section','sectionID');
					$this->data['groups']            = pluck($this->studentgroup_m->get_studentgroup(),'group','studentgroupID');
					$this->data['exams']             = pluck($this->exam_m->get_exam(),'exam','examID');
					// echo "<pre>";print_r($this->data['exams']);die;
					$this->data['grades']            = $this->grade_m->get_grade();
					$this->data['schoolyears']       = pluck($this->schoolyear_m->get_schoolyear(),'schoolyear','schoolyearID');

					$this->data['studentID']         = $studentID;
					$this->data['retMark']           = $retMark;
					$this->data['percentageArr']     = $percentageArr;
					$this->data['mandatorySubjects'] = $mandatorySubjects;
					$this->data['optionalSubjects']  = $optionalSubjects;					
					$this->data['students']          = $students;
					$this->data['settingmarktypeID']       = $settingmarktypeID;
					$this->data['markpercentagesmainArr']  = $markpercentagesmainArr;
					$this->data['getHolidays'] = explode('","', $this->getHolidaysSession());
				$this->data['getWeekendDays'] = $this->getWeekendDaysSession();

				$schoolyearID = $this->session->userdata('defaultschoolyearID');

						 $is_display_attendance_res = $this->setting_m->get_setting_where('is_display_attendance_on_progresscard');
									 $this->data['is_display_attendance'] =$is_display_attendance = $is_display_attendance_res['value'];

									if($is_display_attendance > 0){
									//code for student attendance in progress report

									$this->data['months'] = $months_array = array('6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec','1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May',);
									
									$this->db->where('schoolyearID',$schoolyearID);
									$this->data['schoolyear'] =  $schoolyear = $this->db->get('schoolyear')->row()->schoolyear;
									 $schoolyear_exp = explode("-",$schoolyear);

									// for($m=6;$m<count($months_array)+6;$m++){
										foreach($months_array as $mkey=>$v){

											if ($mkey < 10) {
												$d_m = str_pad($mkey, 2, "0", STR_PAD_LEFT);
											}else{$d_m = $mkey;}

											if($d_m <= 5){
												$year = $schoolyear_exp[1];
											}else{
												$year = $schoolyear_exp[0];
											}
										
										$monthyear = $d_m."-".$year;
									$this->db->where('monthyear',$monthyear);
									$this->db->where('studentID',$studentID);
									$attendace = $this->db->get('attendance')->result_array();
									// echo $this->db->last_query();die;
									$absent = 0;
									$present = 0; 


								if(!empty($attendace)){
									for($j = 0; $j < count($attendace); $j++) {
										if(!empty($attendace[$j])){
											foreach($attendace[$j] as $k => $v){
												for ($i = 1; $i <= 31; $i++) { 
													$acolumnname = 'a'.$i;
													if($k == $acolumnname){
								
														// Corrected here: checking $v directly
														if($v == 'P'){ 
															$present += 1;
														} else { 
															$present += 0;
														}
								
														if($v == 'A'){ 
															$absent += 1;
														} else { 
															$absent += 0;
														}
													}
												}
											}
										}
									}
								}
								
									$temp = array(
										'absent' => $absent,
										'present' => $present
									 );
									 $this->data['attendance'][$mkey][$studentID] =$temp;
									}
									//end code for attendance
									 
								}



					$retArray['render'] = $this->load->view('report/studentsession/StudentsessionReport',$this->data,true);
					$retArray['status'] = TRUE;
					echo json_encode($retArray);
					exit();
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

	
	public function pdf() {
		if(permissionChecker('studentsessionreport')) {
			$studentID    = htmlentities(escapeString($this->uri->segment(3)));
			if((int)$studentID) {

				$markArray    = [];
				$queryArray   = [];
				$schoolyearID = $this->session->userdata('defaultschoolyearID');
				$queryArray['srschoolyearID'] = $schoolyearID;
				$markArray['schoolyearID']    = $schoolyearID;

				if((int)$studentID > 0) {
					$markArray['studentID']    = $studentID;
					$queryArray['srstudentID'] = $studentID;
				}

				$students               = pluck($this->studentrelation_m->general_get_order_by_student($queryArray), 'obj', 'srschoolyearID');
				if (customCompute($students)) {
					foreach ($students as $s) {
						$queryArray['srclassesID'] = $s->srclassesID;
						$queryArray['srsectionID'] = $s->srsectionID;
						$markArray['classesID']    = $s->srclassesID;
						$markArray['sectionID']    = $s->srsectionID;
						break;
					}
				}
				$marks                  = $this->mark_m->student_all_mark_array($markArray);
				$mandatorySubjects      = pluck_multi_array_key($this->subject_m->general_get_order_by_subject(array('type' => 1)), 'obj', 'classesID', 'subjectID');
				$optionalSubjects       = pluck_multi_array_key($this->subject_m->general_get_order_by_subject(array('type' => 0)), 'obj', 'classesID', 'subjectID');

				$settingmarktypeID      = $this->data['siteinfos']->marktypeID;
				$markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages();
				$percentageArr          = pluck($this->markpercentage_m->get_markpercentage(), 'obj', 'markpercentageID');
				


				$retMark = [];
				if(customCompute($marks)) {
					foreach ($marks as $mark) {
						$value = isset($mark->mark) ? $mark->mark : 0;
						if (isset($mark->eattendance) && strtolower(trim($mark->eattendance)) === 'absent') {
							$value = 'A';
						}
						$retMark[$mark->schoolyearID][$mark->classesID][$mark->examID][$mark->subjectID][$mark->markpercentageID] = $value;
					}
				}

				$this->data['classes']           = pluck($this->classes_m->general_get_classes(),'classes','classesID');
				$this->data['sections']          = pluck($this->section_m->general_get_section(),'section','sectionID');
				$this->data['groups']            = pluck($this->studentgroup_m->get_studentgroup(),'group','studentgroupID');
				$this->data['exams']             = pluck($this->exam_m->get_exam(),'exam','examID');
				$this->data['grades']            = $this->grade_m->get_grade();
				$this->data['schoolyears']       = pluck($this->schoolyear_m->get_schoolyear(),'schoolyear','schoolyearID');

				$this->data['studentID']         = $studentID;
				$this->data['retMark']           = $retMark;
				$this->data['percentageArr']     = $percentageArr;
				$this->data['mandatorySubjects'] = $mandatorySubjects;
				$this->data['optionalSubjects']  = $optionalSubjects;					
				$this->data['students']          = $students;
				$this->data['settingmarktypeID']       = $settingmarktypeID;
				$this->data['markpercentagesmainArr']  = $markpercentagesmainArr;

				// Build exam -> subjects mapping and scheduled subjects for PDF
				$classID = 0; $sectionID = 0;
				if (customCompute($students)) {
					foreach ($students as $s) { $classID = isset($s->srclassesID) ? $s->srclassesID : 0; $sectionID = isset($s->srsectionID) ? $s->srsectionID : 0; break; }
				}
				$examSubjects = [];
				if ($classID && $sectionID) {
					$es = $this->db->select('examID,subjectID')->from('examschedule')->where('classesID', $classID)->where('sectionID', $sectionID)->get()->result();
					if(customCompute($es)){
						foreach($es as $row) {
							$examSubjects[$row->examID][] = $row->subjectID;
						}
					}
				}
				$this->data['examSubjects'] = $examSubjects;

				$scheduledSubjectIDs = [];
				if (customCompute($examSubjects)) {
					foreach ($examSubjects as $e => $sarr) {
						foreach ($sarr as $sid) {
							$scheduledSubjectIDs[] = $sid;
						}
					}
				}
				$scheduledSubjectIDs = array_unique($scheduledSubjectIDs);
				$scheduledSubjects = [];
				if (customCompute($scheduledSubjectIDs)) {
					foreach ($scheduledSubjectIDs as $sid) {
						$sub = $this->subject_m->get_subject($sid, TRUE);
						if ($sub) {
							$scheduledSubjects[$sid] = $sub;
						}
					}
				}
				$this->data['scheduledSubjectIDs'] = $scheduledSubjectIDs;
				$this->data['scheduledSubjects'] = $scheduledSubjects;

				$this->reportPDF('studentsessionreport.css', $this->data, 'report/studentsession/StudentsessionReportPDF');

			} else {
				$this->data["subview"] = "error";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "errorpermission";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function send_pdf_to_mail() {
		$retArray['status']  = FALSE;
		$retArray['message'] = '';
		if(permissionChecker('studentsessionreport')) {
			if($_POST) {
				$to           = $this->input->post('to');
				$subject      = $this->input->post('subject');
				$message      = $this->input->post('message');
				$studentID    = $this->input->post('studentID');

				$rules = $this->send_pdf_to_mail_rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
				    echo json_encode($retArray);
				    exit;
				} else {
					$markArray    = [];
					$queryArray   = [];
					$schoolyearID = $this->session->userdata('defaultschoolyearID');
					$queryArray['srschoolyearID'] = $schoolyearID;
					$markArray['schoolyearID']    = $schoolyearID;

					if((int)$studentID > 0) {
						$markArray['studentID']    = $studentID;
						$queryArray['srstudentID'] = $studentID;
					}

					$students               = pluck($this->studentrelation_m->general_get_order_by_student($queryArray), 'obj', 'srschoolyearID');
					if (customCompute($students)) {
						foreach ($students as $s) {
							$queryArray['srclassesID'] = $s->srclassesID;
							$queryArray['srsectionID'] = $s->srsectionID;
							$markArray['classesID']    = $s->srclassesID;
							$markArray['sectionID']    = $s->srsectionID;
							break;
						}
					}
					$marks                  = $this->mark_m->student_all_mark_array($markArray);
					$mandatorySubjects      = pluck_multi_array_key($this->subject_m->general_get_order_by_subject(array('type' => 1)), 'obj', 'classesID', 'subjectID');
					$optionalSubjects       = pluck_multi_array_key($this->subject_m->general_get_order_by_subject(array('type' => 0)), 'obj', 'classesID', 'subjectID');

					$settingmarktypeID      = $this->data['siteinfos']->marktypeID;
					$markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages();
					$percentageArr          = pluck($this->markpercentage_m->get_markpercentage(), 'obj', 'markpercentageID');
					


					$retMark = [];
					if(customCompute($marks)) {
						foreach ($marks as $mark) {
							$value = isset($mark->mark) ? $mark->mark : 0;
							if (isset($mark->eattendance) && strtolower(trim($mark->eattendance)) === 'absent') {
								$value = 'A';
							}
							$retMark[$mark->schoolyearID][$mark->classesID][$mark->examID][$mark->subjectID][$mark->markpercentageID] = $value;
						}
					}

					$this->data['classes']           = pluck($this->classes_m->general_get_classes(),'classes','classesID');
					$this->data['sections']          = pluck($this->section_m->general_get_section(),'section','sectionID');
					$this->data['groups']            = pluck($this->studentgroup_m->get_studentgroup(),'group','studentgroupID');
					$this->data['exams']             = pluck($this->exam_m->get_exam(),'exam','examID');
					$this->data['grades']            = $this->grade_m->get_grade();
					$this->data['schoolyears']       = pluck($this->schoolyear_m->get_schoolyear(),'schoolyear','schoolyearID');

					$this->data['studentID']         = $studentID;
					$this->data['retMark']           = $retMark;
					$this->data['percentageArr']     = $percentageArr;
					$this->data['mandatorySubjects'] = $mandatorySubjects;
					$this->data['optionalSubjects']  = $optionalSubjects;					
					$this->data['students']          = $students;
					$this->data['settingmarktypeID']       = $settingmarktypeID;
					$this->data['markpercentagesmainArr']  = $markpercentagesmainArr;

					$this->reportSendToMail('studentsessionreport.css', $this->data, 'report/studentsession/StudentsessionReportPDF',$to, $subject,$message);
					$retArray['status'] = TRUE;
					echo json_encode($retArray);
    				exit;
				}
			} else {
				$retArray['message'] = $this->lang->line('studentsessionreport_permissionmethod');
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['message'] = $this->lang->line('studentsessionreport_permission');
			echo json_encode($retArray);
			exit;
		}
	}

	protected function rules() {
		$rules = array(
			array(
				'field' => 'studentID',
				'label' => $this->lang->line("studentsessionreport_student"),
				'rules' => 'trim|required|xss_clean|callback_unique_data'
			)
		);
		return $rules;
	} 

	protected function send_pdf_to_mail_rules() {
		$rules = array(
			array(
				'field' => 'studentID',
				'label' => $this->lang->line("studentsessionreport_student"),
				'rules' => 'trim|required|xss_clean|callback_unique_data'
			),
			array(
				'field' => 'to',
				'label' => $this->lang->line("studentsessionreport_to"),
				'rules' => 'trim|required|xss_clean|valid_email'
			),
			array(
				'field' => 'subject',
				'label' => $this->lang->line("studentsessionreport_subject"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'message',
				'label' => $this->lang->line("studentsessionreport_message"),
				'rules' => 'trim|xss_clean'
			),
		);
		return $rules;
	}

	public function unique_data($data) {
		if($data != "") {
			if($data === "0") {
				$this->form_validation->set_message('unique_data', 'The %s field is required.');
				return FALSE;
			}
		} 
		return TRUE;
	}
}
