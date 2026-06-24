<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Progresscardreport extends Admin_Controller {
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
		$this->load->model("Whatsapp_m");

		
        $this->load->model('mailandsmstemplate_m');
		$this->load->model('mailandsmstemplatetag_m');

		$language = $this->session->userdata('lang');
		$this->lang->load('progresscardreport', $language);
	}

	protected function rules() {
		$rules = array(
			array(
				'field' => 'classesID',
				'label' => $this->lang->line("progresscardreport_class"),
				'rules' => 'trim|required|xss_clean|callback_unique_data'
			),
			array(
				'field' => 'sectionID',
				'label' => $this->lang->line("progresscardreport_section"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'studentID',
				'label' => $this->lang->line("progresscardreport_student"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'examID',
				'label' => $this->lang->line("progresscardreport_class"),
				'rules' => 'trim|required|xss_clean|callback_unique_data'
			),
		);
		return $rules;
	} 

	protected function send_pdf_to_mail_rules() {
		$rules = array(
			array(
				'field' => 'classesID',
				'label' => $this->lang->line("progresscardreport_class"),
				'rules' => 'trim|required|xss_clean|callback_unique_data'
			),
			array(
				'field' => 'sectionID',
				'label' => $this->lang->line("progresscardreport_section"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'studentID',
				'label' => $this->lang->line("progresscardreport_student"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'to',
				'label' => $this->lang->line("progresscardreport_to"),
				'rules' => 'trim|required|xss_clean|valid_email'
			),
			array(
				'field' => 'subject',
				'label' => $this->lang->line("progresscardreport_subject"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'message',
				'label' => $this->lang->line("progresscardreport_message"),
				'rules' => 'trim|xss_clean'
			),
		);
		return $rules;
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
		$this->data["subview"] = "report/progresscard/ProgresscardReportView";
		$this->load->view('_layout_main', $this->data);
	}

	public function getProgresscardreport () {
		$retArray['status'] = FALSE;
		$retArray['render'] = '';
		if(permissionChecker('progresscardreport')) {
			if($_POST) {
				$classesID    = $this->input->post('classesID');
				$sectionID    = $this->input->post('sectionID');
				$studentID    = $this->input->post('studentID');
				$examID    = $this->input->post('examID');
				$schoolyearID = $this->session->userdata('defaultschoolyearID');
				$rules = $this->rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
				    echo json_encode($retArray);
				    exit;
				} else {
					$this->data['classesID'] = $classesID;
					$this->data['sectionID'] = $sectionID;
					$this->data['studentID'] = $studentID;
					$this->data['examID'] = $examID;

					$mArray       = [];
					$queryArray   = [];
					$mArray['schoolyearID']        = $schoolyearID;
					$queryArray['srschoolyearID']  = $schoolyearID;
					if((int)$classesID > 0) {
						$mArray['classesID']       = $classesID;
						$queryArray['srclassesID'] = $classesID;
					}
					if((int)$sectionID > 0) {
						$mArray['sectionID']       = $sectionID;
						$queryArray['srsectionID'] = $sectionID;
					}
					if((int)$studentID > 0) {
						$mArray['studentID']       = $studentID;
						$queryArray['srstudentID'] = $studentID;
					}

					$this->data['classes']  = pluck($this->classes_m->general_get_classes(),'classes','classesID');
					$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');
					$this->data['groups']   = pluck($this->studentgroup_m->get_studentgroup(),'group','studentgroupID');

					$students               = $this->studentrelation_m->general_get_order_by_student($queryArray);
					$marks                  = $this->mark_m->student_all_mark_array($mArray);
					// file_put_contents('test_marks_log.txt', print_r($marks, true));
				//$mandatorySubjects      = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID, 'type' => 1));
				     $mandatorySubjects      = $this->subject_m->general_get_order_by_subject_left_examschedule($classesID,$type = 1,$examID,$sectionID);
					//  echo "<pre>";print_r($mandatorySubjects);die;
					$optionalSubjects       = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID, 'type' => 0));

					$settingmarktypeID      = $this->data['siteinfos']->marktypeID;
					$markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages();
					$markpercentagesclassArr= isset($markpercentagesmainArr[$classesID]) ? $markpercentagesmainArr[$classesID] : [];
					$settingExam            = array_keys($markpercentagesclassArr);
					$percentageArr          = pluck($this->markpercentage_m->get_markpercentage(), 'obj', 'markpercentageID');
					
					$this->data['markpercentagesclassArr'] = $markpercentagesclassArr;
					$this->data['settingmarktypeID']       = $settingmarktypeID;

					$retMark = [];
					if(customCompute($marks)) {
						foreach ($marks as $mark) {
							$retMark[$mark->examID][$mark->studentID][$mark->subjectID][$mark->markpercentageID] = $mark->mark;
							$retMark[$mark->examID][$mark->studentID][$mark->subjectID]['default'] = $mark->mark;
						}
					}

					$markArray      = [];
					$studentChecker = [];
					$validExam      = [];
					if(customCompute($settingExam)) {
						foreach($settingExam as $examID) {
							if(customCompute($students)) {
								foreach ($students as $student) {
									$opuniquepercentageArr = [];
									if($student->sroptionalsubjectID > 0) {
										$opuniquepercentageArr = isset($markpercentagesclassArr[$examID][$student->sroptionalsubjectID]) ? $markpercentagesclassArr[$examID][$student->sroptionalsubjectID] : [];
									}
									$oppercentageMark = 0;
									if(customCompute($mandatorySubjects)) {
										foreach ($mandatorySubjects as $mandatorySubject) {
											$uniquepercentageArr = isset($markpercentagesclassArr[$examID][$mandatorySubject->subjectID]) ? $markpercentagesclassArr[$examID][$mandatorySubject->subjectID] : [];
											$markpercentages     = [];
											if(customCompute($uniquepercentageArr)) {
												$markpercentages = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
											}

											if(customCompute($markpercentages)) {
												foreach ($markpercentages as $markpercentageID) {
													$f = false;
		                                            if(isset($uniquepercentageArr['own']) && in_array($markpercentageID, $uniquepercentageArr['own'])) {
		                                                $f = true;
		                                            }

													if(isset($retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID][$markpercentageID]) && $f) {
														$markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID][$markpercentageID];
													} elseif(isset($retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID]['default']) && $f) {
														$markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID]['default'];
													}



													$f = false;
													if(customCompute($opuniquepercentageArr)) {
			                                            if(isset($opuniquepercentageArr['own']) && in_array($markpercentageID, $opuniquepercentageArr['own'])) {
			                                                $f = true;
			                                            }
													}
													if(!isset($studentChecker['subject'][$examID][$student->srstudentID][$markpercentageID]) && $f) {
														$oppercentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
														if($student->sroptionalsubjectID > 0) {

															if(isset($retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID][$markpercentageID])) {
																$markArray[$examID][$student->srstudentID]['markpercentageMark'][$student->sroptionalsubjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID][$markpercentageID];
															} elseif(isset($retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID]['default'])) {
																$markArray[$examID][$student->srstudentID]['markpercentageMark'][$student->sroptionalsubjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID]['default'];
															}
														}
														$studentChecker['subject'][$examID][$student->srstudentID][$markpercentageID] = TRUE;
													}
												}
											}
										}
									}


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
									$this->db->where('studentID',$student->srstudentID);
									$attendace = $this->db->get('attendance')->result_array();
									// echo $this->db->last_query();die;
									$absent = 0;
									$present = 0;
									
									// echo "<pre>";print_r($attendace);
									// dd($attendace);
								// 	if(!empty($attendace)){
										
								// 	for($j=0;$j<count($attendace);$j++){

								// 		// echo "<pre>";print_r($attendace[$j]);
										
								// 		if(!empty($attendace[$j])){
								// 	foreach($attendace[$j] as $k => $v){
									
										
								// 		for ($i=1; $i <= 31; $i++) { 
								// 			 $acolumnname = 'a'.$i;
								// 			if($k == $acolumnname){

								// 				// echo $v[$k]."aaaaaa<br/>"; 
								// 				// echo $k;die;
								// 				// print_r($v);die;

								// 				// if(!empty($v[$k])){
								// 					// if (is_array($v)) {
								// 					if($v[$k] == 'P'){ 
								// 						 $present += 1;
								// 					}else{ 
								// 						$present += 0;
								// 					}
													
								// 					if($v[$k] == 'A' ){ 
								// 						 $absent += 1;
								// 					}else{ 
								// 						$absent += 0;
								// 					}
								// 				// }
								// 				// }

										 

								// 			} 
								// 		}
								// 	}

								// 	}

								// }
								// }


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
									 $this->data['attendance'][$mkey][$student->srstudentID] =$temp;
									}
									//end code for attendance
									//  echo "<pre>";print_r($this->data['attendance']);die;

								}

								}
							}
						}
					}

					$this->data['percentageArr']     = $percentageArr;
					$this->data['grades']            = $this->grade_m->get_grade();
					$this->data['optionalSubjects']  = pluck($optionalSubjects,'obj','subjectID');
					$this->data['mandatorySubjects'] = $mandatorySubjects;
					$this->data['totalSubject']      = customCompute($mandatorySubjects);
					$this->data['validExams']        = $validExam;
					$this->data['exams']             = pluck($this->exam_m->get_exam(),'exam','examID');;
					$this->data['students']          = $students;
					$this->data['markArray']         = $markArray;
					$this->data['settingExam']       = $settingExam;
				$this->data['getHolidays'] = explode('","', $this->getHolidaysSession());
				$this->data['getWeekendDays'] = $this->getWeekendDaysSession();
  					$retArray['render'] = $this->load->view('report/progresscard/ProgresscardReport',$this->data,true);
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
		if(permissionChecker('progresscardreport')) {
			$classesID    = htmlentities(escapeString($this->uri->segment(3)));
			$sectionID    = htmlentities(escapeString($this->uri->segment(4)));
			$examID    = htmlentities(escapeString($this->uri->segment(5)));
// 			$examID    = htmlentities(escapeString($this->uri->segment(6)));
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			if((int)$classesID && ((int)$sectionID || $sectionID >= 0)) {
				$this->data['classesID'] = $classesID;
				$this->data['sectionID'] = $sectionID;
				// $this->data['studentID'] = $studentID;
                $this->data['examID'] = $examID;
				$mArray       = [];
				$queryArray   = [];
				$mArray['schoolyearID']        = $schoolyearID;
				$queryArray['srschoolyearID']  = $schoolyearID;
				if((int)$classesID > 0) {
					$mArray['classesID']       = $classesID;
					$queryArray['srclassesID'] = $classesID;
				}
				if((int)$sectionID > 0) {
					$mArray['sectionID']       = $sectionID;
					$queryArray['srsectionID'] = $sectionID;
				}
				// if((int)$studentID > 0) {
				// 	$mArray['studentID']       = $studentID;
				// 	$queryArray['srstudentID'] = $studentID;
				// }
				if((int)$examID > 0) {
					$mArray['examID']       = $examID;
					$queryArray['examID'] = $examID;
				}
				$this->data['classes']  = pluck($this->classes_m->general_get_classes(),'classes','classesID');
				$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');
				$this->data['groups']   = pluck($this->studentgroup_m->get_studentgroup(),'group','studentgroupID');

				$students               = $this->studentrelation_m->general_get_order_by_student($queryArray);
				$marks                  = $this->mark_m->student_all_mark_array($mArray);
				// $mandatorySubjects      = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID, 'type' => 1));
				$mandatorySubjects      = $this->subject_m->general_get_order_by_subject_left_examschedule($classesID,$type = 1,$examID,$sectionID);
				$optionalSubjects       = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID, 'type' => 0));

				$settingmarktypeID      = $this->data['siteinfos']->marktypeID;
				$markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages();
				$markpercentagesclassArr= isset($markpercentagesmainArr[$classesID]) ? $markpercentagesmainArr[$classesID] : [];
				$settingExam            = array_keys($markpercentagesclassArr);
				$percentageArr          = pluck($this->markpercentage_m->get_markpercentage(), 'obj', 'markpercentageID');
				
				$this->data['markpercentagesclassArr'] = $markpercentagesclassArr;
				$this->data['settingmarktypeID']       = $settingmarktypeID;

				$retMark = [];
				if(customCompute($marks)) {
					foreach ($marks as $mark) {
						$retMark[$mark->examID][$mark->studentID][$mark->subjectID][$mark->markpercentageID] = $mark->mark;
							$retMark[$mark->examID][$mark->studentID][$mark->subjectID]['default'] = $mark->mark;
					}
				}

				$markArray      = [];
				$studentChecker = [];
				$validExam      = [];
				if(customCompute($settingExam)) {
					foreach($settingExam as $examID) {
						if(customCompute($students)) {
							foreach ($students as $student) {
								$opuniquepercentageArr = [];
								if($student->sroptionalsubjectID > 0) {
									$opuniquepercentageArr = isset($markpercentagesclassArr[$examID][$student->sroptionalsubjectID]) ? $markpercentagesclassArr[$examID][$student->sroptionalsubjectID] : [];
								}
								$oppercentageMark = 0;
								if(customCompute($mandatorySubjects)) {
									foreach ($mandatorySubjects as $mandatorySubject) {
										$uniquepercentageArr = isset($markpercentagesclassArr[$examID][$mandatorySubject->subjectID]) ? $markpercentagesclassArr[$examID][$mandatorySubject->subjectID] : [];
										$markpercentages     = [];
										if(customCompute($uniquepercentageArr)) {
											$markpercentages = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
										}

										if(customCompute($markpercentages)) {
											foreach ($markpercentages as $markpercentageID) {
												$f = false;
	                                            if(isset($uniquepercentageArr['own']) && in_array($markpercentageID, $uniquepercentageArr['own'])) {
	                                                $f = true;
	                                            }

												if(isset($retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID][$markpercentageID]) && $f) {
													$markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID][$markpercentageID];
												}



												$f = false;
												if(customCompute($opuniquepercentageArr)) {
		                                            if(isset($opuniquepercentageArr['own']) && in_array($markpercentageID, $opuniquepercentageArr['own'])) {
		                                                $f = true;
		                                            }
												}
												if(!isset($studentChecker['subject'][$examID][$student->srstudentID][$markpercentageID]) && $f) {
													$oppercentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
													if($student->sroptionalsubjectID > 0) {

														if(isset($retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID][$markpercentageID])) {
															$markArray[$examID][$student->srstudentID]['markpercentageMark'][$student->sroptionalsubjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID][$markpercentageID];
														}
													}
													$studentChecker['subject'][$examID][$student->srstudentID][$markpercentageID] = TRUE;
												}
											}
										}
									}
								}
							}
						}
					}
				}

				$this->data['percentageArr']     = $percentageArr;
				$this->data['grades']            = $this->grade_m->get_grade();
				$this->data['optionalSubjects']  = pluck($optionalSubjects,'obj','subjectID');
				$this->data['mandatorySubjects'] = $mandatorySubjects;
				$this->data['totalSubject']      = customCompute($mandatorySubjects);
				$this->data['validExams']        = $validExam;
				$this->data['exams']             = pluck($this->exam_m->get_exam(),'exam','examID');;
				$this->data['students']          = $students;
				$this->data['markArray']         = $markArray;
				$this->data['settingExam']       = $settingExam;

				$this->reportPDF('progresscardreport.css', $this->data, 'report/progresscard/ProgresscardReportPDF');

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
		// echo "<pre>";print_r($_POST);die;

		$retArray['status']  = FALSE;
		$retArray['message'] = '';
		if(permissionChecker('progresscardreport')) {
			if($_POST) {
				$to           = $this->input->post('to');
				$subject      = $this->input->post('subject');
				$message      = $this->input->post('message');
				$classesID    = $this->input->post('classesID');
				$examID    = $this->input->post('examID');
				$sectionID    = $this->input->post('sectionID');
				$studentID    = $this->input->post('studentID');
				$schoolyearID = $this->session->userdata('defaultschoolyearID');

				$rules = $this->send_pdf_to_mail_rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
				    echo json_encode($retArray);
				    exit;
				} else {
					$this->data['classesID'] = $classesID;
					$this->data['sectionID'] = $sectionID;
					$this->data['studentID'] = $studentID;
					$this->data['examID'] = $examID;

					$mArray       = [];
					$queryArray   = [];
					$mArray['schoolyearID']        = $schoolyearID;
					$queryArray['srschoolyearID']  = $schoolyearID;
					if((int)$classesID > 0) {
						$mArray['classesID']       = $classesID;
						$queryArray['srclassesID'] = $classesID;
					}
					
					if((int)$sectionID > 0) {
						$mArray['sectionID']       = $sectionID;
						$queryArray['srsectionID'] = $sectionID;
					}
					if((int)$studentID > 0) {
						$mArray['studentID']       = $studentID;
						$queryArray['srstudentID'] = $studentID;
					}

					$this->data['classes']  = pluck($this->classes_m->general_get_classes(),'classes','classesID');
					$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');
					$this->data['groups']   = pluck($this->studentgroup_m->get_studentgroup(),'group','studentgroupID');

					$students               = $this->studentrelation_m->general_get_order_by_student($queryArray);
					$marks                  = $this->mark_m->student_all_mark_array($mArray);
					// $mandatorySubjects      = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID, 'type' => 1));
					$mandatorySubjects      = $this->subject_m->general_get_order_by_subject_left_examschedule($classesID,$type = 1,$examID,$sectionID);
					$optionalSubjects       = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID, 'type' => 0));

					$settingmarktypeID      = $this->data['siteinfos']->marktypeID;
					$markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages();
					$markpercentagesclassArr= isset($markpercentagesmainArr[$classesID]) ? $markpercentagesmainArr[$classesID] : [];
					$settingExam            = array_keys($markpercentagesclassArr);
					$percentageArr          = pluck($this->markpercentage_m->get_markpercentage(), 'obj', 'markpercentageID');
					
					$this->data['markpercentagesclassArr'] = $markpercentagesclassArr;
					$this->data['settingmarktypeID']       = $settingmarktypeID;

					$retMark = [];
					if(customCompute($marks)) {
						foreach ($marks as $mark) {
							$retMark[$mark->examID][$mark->studentID][$mark->subjectID][$mark->markpercentageID] = $mark->mark;
							$retMark[$mark->examID][$mark->studentID][$mark->subjectID]['default'] = $mark->mark;
						}
					}

					$markArray      = [];
					$studentChecker = [];
					$validExam      = [];
					if(customCompute($settingExam)) {
						foreach($settingExam as $examID) {
							if(customCompute($students)) {
								foreach ($students as $student) {
									$opuniquepercentageArr = [];
									if($student->sroptionalsubjectID > 0) {
										$opuniquepercentageArr = isset($markpercentagesclassArr[$examID][$student->sroptionalsubjectID]) ? $markpercentagesclassArr[$examID][$student->sroptionalsubjectID] : [];
									}
									$oppercentageMark = 0;
									if(customCompute($mandatorySubjects)) {
										foreach ($mandatorySubjects as $mandatorySubject) {
											$uniquepercentageArr = isset($markpercentagesclassArr[$examID][$mandatorySubject->subjectID]) ? $markpercentagesclassArr[$examID][$mandatorySubject->subjectID] : [];
											$markpercentages     = [];
											if(customCompute($uniquepercentageArr)) {
												$markpercentages = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
											}

											if(customCompute($markpercentages)) {
												foreach ($markpercentages as $markpercentageID) {
													$f = false;
		                                            if(isset($uniquepercentageArr['own']) && in_array($markpercentageID, $uniquepercentageArr['own'])) {
		                                                $f = true;
		                                            }

													if(isset($retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID][$markpercentageID]) && $f) {
														$markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID][$markpercentageID];
													} elseif(isset($retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID]['default']) && $f) {
														$markArray[$examID][$student->srstudentID]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$mandatorySubject->subjectID]['default'];
													}



													$f = false;
													if(customCompute($opuniquepercentageArr)) {
			                                            if(isset($opuniquepercentageArr['own']) && in_array($markpercentageID, $opuniquepercentageArr['own'])) {
			                                                $f = true;
			                                            }
													}
													if(!isset($studentChecker['subject'][$examID][$student->srstudentID][$markpercentageID]) && $f) {
														$oppercentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
														if($student->sroptionalsubjectID > 0) {

															if(isset($retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID][$markpercentageID])) {
																$markArray[$examID][$student->srstudentID]['markpercentageMark'][$student->sroptionalsubjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID][$markpercentageID];
															} elseif(isset($retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID]['default'])) {
																$markArray[$examID][$student->srstudentID]['markpercentageMark'][$student->sroptionalsubjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID]['default'];
															}
														}
														$studentChecker['subject'][$examID][$student->srstudentID][$markpercentageID] = TRUE;
													}
												}
											}
										}
									}
								}
							}
						}
					}

					$this->data['percentageArr']     = $percentageArr;
					$this->data['grades']            = $this->grade_m->get_grade();
					$this->data['optionalSubjects']  = pluck($optionalSubjects,'obj','subjectID');
					$this->data['mandatorySubjects'] = $mandatorySubjects;
					$this->data['totalSubject']      = customCompute($mandatorySubjects);
					$this->data['validExams']        = $validExam;
					$this->data['exams']             = pluck($this->exam_m->get_exam(),'exam','examID');;
					$this->data['students']          = $students;
					$this->data['markArray']         = $markArray;
					$this->data['settingExam']       = $settingExam;

					$this->reportSendToMail('progresscardreport.css', $this->data, 'report/progresscard/ProgresscardReportPDF',$to, $subject,$message);
					$retArray['status'] = TRUE;
					echo json_encode($retArray);
    				exit;
				}
			} else {
				$retArray['message'] = $this->lang->line('progresscardreport_permissionmethod');
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['message'] = $this->lang->line('progresscardreport_permission');
			echo json_encode($retArray);
			exit;
		}
	}

	public function send_pdf_to_whatsapp() {

		// echo "<pre>";print_r($_POST);die;


		$retArray['status']  = FALSE;
		$retArray['message'] = '';
		if(permissionChecker('progresscardreport')) {
			if($_POST) { 
				 
				$classesID    = $this->input->post('classesID');
				$sectionID    = $this->input->post('sectionID');
				$studentID    = $this->input->post('studentID');				
				$examID    = $this->input->post('examID');
				$schoolyearID = $this->session->userdata('defaultschoolyearID');

				
				 
					$this->data['classesID'] = $classesID;
					$this->data['sectionID'] = $sectionID;
					$this->data['studentID'] = $studentID;
					$this->data['examID'] = $examID;

					$mArray       = [];
					$queryArray   = [];
					$mArray['schoolyearID']        = $schoolyearID;
					$queryArray['srschoolyearID']  = $schoolyearID;
					if((int)$classesID > 0) {
						$mArray['classesID']       = $classesID;
						$queryArray['srclassesID'] = $classesID;
					}
					if((int)$sectionID > 0) {
						$mArray['sectionID']       = $sectionID;
						$queryArray['srsectionID'] = $sectionID;
					}
					// if((int)$studentID > 0) {
					// 	$mArray['studentID']       = $studentID;
					// 	$queryArray['srstudentID'] = $studentID;
					// }
					//print_r($studentID);die;
				for($i=0;$i<count($studentID);$i++){
						if((int)$studentID[$i] > 0) {
							$mArray['studentID']       = $studentID[$i];
							$queryArray['srstudentID'] = $studentID[$i];
						}
					$this->data['classes']  = pluck($this->classes_m->general_get_classes(),'classes','classesID');
					$this->data['sections'] = pluck($this->section_m->general_get_section(),'section','sectionID');
					$this->data['groups']   = pluck($this->studentgroup_m->get_studentgroup(),'group','studentgroupID');

					 $students               = $this->studentrelation_m->general_get_order_by_student($queryArray); 
					$marks                  = $this->mark_m->student_all_mark_array($mArray);
					$mandatorySubjects      = $this->subject_m->general_get_order_by_subject_left_examschedule($classesID,$type = 1,$examID,$sectionID);
					$optionalSubjects       = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID, 'type' => 0));

					$settingmarktypeID      = $this->data['siteinfos']->marktypeID;
					$markpercentagesmainArr = $this->marksetting_m->get_marksetting_markpercentages();
					$markpercentagesclassArr= isset($markpercentagesmainArr[$classesID]) ? $markpercentagesmainArr[$classesID] : [];
					$settingExam            = array_keys($markpercentagesclassArr);
					$percentageArr          = pluck($this->markpercentage_m->get_markpercentage(), 'obj', 'markpercentageID');
					
					$this->data['markpercentagesclassArr'] = $markpercentagesclassArr;
					$this->data['settingmarktypeID']       = $settingmarktypeID;

					$retMark = [];
					if(customCompute($marks)) {
						foreach ($marks as $mark) {
							$retMark[$mark->examID][$mark->studentID][$mark->subjectID][$mark->markpercentageID] = $mark->mark;
							$retMark[$mark->examID][$mark->studentID][$mark->subjectID]['default'] = $mark->mark;
						}
					}

					$markArray      = [];
					$studentChecker = [];
					$validExam      = [];
					if(customCompute($settingExam)) {
						foreach($settingExam as $examID) {
							if(customCompute($studentID[$i])) {
								// foreach ($students as $student) {
									// $opuniquepercentageArr = [];
									// if($student->sroptionalsubjectID > 0) {
									// 	$opuniquepercentageArr = isset($markpercentagesclassArr[$examID][$student->sroptionalsubjectID]) ? $markpercentagesclassArr[$examID][$student->sroptionalsubjectID] : [];
									// }
									$oppercentageMark = 0;
									if(customCompute($mandatorySubjects)) {
										foreach ($mandatorySubjects as $mandatorySubject) {
											$uniquepercentageArr = isset($markpercentagesclassArr[$examID][$mandatorySubject->subjectID]) ? $markpercentagesclassArr[$examID][$mandatorySubject->subjectID] : [];
											$markpercentages     = [];
											if(customCompute($uniquepercentageArr)) {
												$markpercentages = $uniquepercentageArr[(($settingmarktypeID==4) || ($settingmarktypeID==6)) ? 'unique' : 'own'];
											}

											if(customCompute($markpercentages)) {
												foreach ($markpercentages as $markpercentageID) {
													$f = false;
		                                            if(isset($uniquepercentageArr['own']) && in_array($markpercentageID, $uniquepercentageArr['own'])) {
		                                                $f = true;
		                                            }

													if(isset($retMark[$examID][$studentID[$i]][$mandatorySubject->subjectID][$markpercentageID]) && $f) {
														$markArray[$examID][$studentID[$i]]['markpercentageMark'][$mandatorySubject->subjectID][$markpercentageID] = $retMark[$examID][$studentID[$i]][$mandatorySubject->subjectID][$markpercentageID];
													}



													$f = false;
													if(customCompute($opuniquepercentageArr)) {
			                                            if(isset($opuniquepercentageArr['own']) && in_array($markpercentageID, $opuniquepercentageArr['own'])) {
			                                                $f = true;
			                                            }
													}
													if(!isset($studentChecker['subject'][$examID][$studentID[$i]][$markpercentageID]) && $f) {
														$oppercentageMark   += isset($percentageArr[$markpercentageID]) ? $percentageArr[$markpercentageID]->percentage : 0;
														// if($student->sroptionalsubjectID > 0) {

														// 	if(isset($retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID][$markpercentageID])) {
														// 		$markArray[$examID][$student->srstudentID]['markpercentageMark'][$student->sroptionalsubjectID][$markpercentageID] = $retMark[$examID][$student->srstudentID][$student->sroptionalsubjectID][$markpercentageID];
														// 	}
														// }
														$studentChecker['subject'][$examID][$studentID[$i]][$markpercentageID] = TRUE;
													}
												}
											}
										}
									//}
								}


								// code for student attendance in progress report
$this->data['months'] = $months_array = array(
    '6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec',
    '1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May',
);

$this->db->where('schoolyearID', $schoolyearID);
$this->data['schoolyear'] = $schoolyear = $this->db->get('schoolyear')->row()->schoolyear;
$schoolyear_exp = explode("-", $schoolyear);

foreach($months_array as $mkey => $v) {

    // Handle month-year formatting
    $d_m = str_pad($mkey, 2, "0", STR_PAD_LEFT);
    $year = ($d_m <= 5) ? $schoolyear_exp[1] : $schoolyear_exp[0];
    $monthyear = $d_m."-".$year;

    // Get attendance for this month and student
    $this->db->where('monthyear', $monthyear);
    $this->db->where('studentID', $studentID[$i]);
    $attendance = $this->db->get('attendance')->result_array();

    $absent = 0;
    $present = 0;

    // Loop through each attendance record for the month
    foreach($attendance as $row) {
        // Check each day a1..a31
        for ($d = 1; $d <= 31; $d++) {
            $acolumnname = 'a'.$d;

            if (isset($row[$acolumnname])) {
                $status = trim($row[$acolumnname]);

                if ($status === 'P') {
                    $present++;
                } elseif ($status === 'A') {
                    $absent++;
                }
            }
        }
    }

    $temp = array(
        'absent' => $absent,
        'present' => $present
    );
    $this->data['attendance'][$mkey][$studentID[$i]] = $temp;
}
// end code for attendance



							}
						}
					}
					// echo "<pre>";print_r($this->data['attendance']);die;

					$this->data['percentageArr']     = $percentageArr;
					$this->data['grades']            = $this->grade_m->get_grade();
					$this->data['optionalSubjects']  = pluck($optionalSubjects,'obj','subjectID');
					$this->data['mandatorySubjects'] = $mandatorySubjects;
					$this->data['totalSubject']      = customCompute($mandatorySubjects);
					$this->data['validExams']        = $validExam;
					$this->data['exams']             = pluck($this->exam_m->get_exam(),'exam','examID');;
					$this->data['students']          = $students;

					

					$this->data['markArray']         = $markArray;
					$this->data['settingExam']       = $settingExam;

					// $this->reportSendToMail('progresscardreport.css', $this->data, 'report/progresscard/ProgresscardReportPDF',$to, $subject,$message);

					// $attachment = $this->generateAttachment('progresscardreport.css', $this->data, 'report/progresscard/ProgresscardReportPDF'); 

					$attachment = $this->generateAttachment(
						'<style>' . file_get_contents(FCPATH.'assets/css/progresscardreport.css') . '</style>',
						$this->data,
						'report/progresscard/ProgresscardReportPDF'
					);


					$media_path = base_url().$attachment;

					// prepare bulk arrays (initialized on first iteration)
					if(!isset($bulkMessages)) {
						$bulkMessages = array();
						$attachmentsToUnlink = array();
						$phonesMap = array();
					}

					$phone = isset($students[0]->phone) ? $students[0]->phone : '';
					$student_name = isset($students[0]->srname) ? $students[0]->srname : (isset($students[0]->name) ? $students[0]->name : '');
					$exam_name = isset($this->data['exams'][$examID]) ? $this->data['exams'][$examID] : '';

					$params = "{$student_name},{$exam_name}";

					$bulkMessages[] = array(
						'phone' => $phone,
						'message' => $params,
						'url' => $media_path,
						'htype' => 'document',
						'fname' => 'Progress_Card_'.$student_name.'.pdf'
					);

					$attachmentsToUnlink[] = $attachment;
					$phonesMap[$phone] = array('attachment' => $attachment, 'student_name' => $student_name, 'exam_name' => $exam_name);

				}
				// echo "<pre>";print_r($bulkMessages);die;	

				// after loop - send all prepared messages in one batch
				if(isset($bulkMessages) && customCompute($bulkMessages)) {
					$template_sql = "select params,template_name from whatapp_templates where short_name like 'PROGRESS_CARD' ";
					$template = $this->db->query($template_sql)->row_array();
					// echo "<pre>";print_r($template);die;
					if($template && !empty($template['template_name'])) {
						$this->load->model('Whatsapp_m');
						$sentCount = $this->Whatsapp_m->sendWhatsapp_bulk_batch_with_media_progresscard($bulkMessages, $template['template_name']);
					} else {
						$sentCount = 0;
					}

					// unlink attachments and prepare response per phone
					foreach($attachmentsToUnlink as $att) {
						// if (file_exists($att)) {
						// 	if (unlink($att)) {
						// 		$unlinkMsg = "File deleted successfully.";
						// 	} else {
						// 		$unlinkMsg = "Error deleting the file.";
						// 	}
						// } else {
						// 	$unlinkMsg = "File does not exist.";
						// }
						foreach($phonesMap as $p => $info) {
							if($info['attachment'] === $att) {
								$retArray[$p]['unlink'] = $unlinkMsg;
								$retArray[$p]['attachment'] = $att;
								$retArray[$p]['status'] = TRUE;
							}
						}
					}

					$retArray['status'] = TRUE;
					$retArray['sent'] = isset($sentCount) ? $sentCount : 0;
					echo json_encode($retArray);
				}
			} else {
				$retArray['message'] = $this->lang->line('progresscardreport_permissionmethod');
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['message'] = $this->lang->line('progresscardreport_permission');
			echo json_encode($retArray);
			exit;
		}
	}

	public function getSection() {
		$classesID = $this->input->post('classesID');
		if((int)$classesID) {
			$sections = $this->section_m->general_get_order_by_section(array('classesID' => $classesID));
			echo "<option value='0'>", $this->lang->line("progresscardreport_please_select"),"</option>";
			if(customCompute($sections)) {
				foreach ($sections as $section) {
					echo "<option value=\"$section->sectionID\">".$section->section."</option>";
				}
			}
		}
	}

	public function getStudent() {
		$classesID = $this->input->post('classesID');
		$sectionID = $this->input->post('sectionID');
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		if((int)$classesID && (int)$sectionID) {
			$students = $this->studentrelation_m->general_get_order_by_student(array('srclassesID'=>$classesID,'srsectionID'=>$sectionID,'srschoolyearID'=>$schoolyearID));
			if(customCompute($students)) {
				echo "<option value='0'>". $this->lang->line("progresscardreport_please_select") ."</option>";
				foreach($students as $student) {
					echo "<option value=\"$student->srstudentID\">".$student->srname.' (Roll No: ' . $student->roll. ")</option>";
				}
			}
		}
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
	
	public function send_marks_to_sms() {
		if (!notification_enabled('exam_marks', 'sms')) {
			echo json_encode(['status' => false, 'message' => 'SMS is disabled for Exam Marks in Notification Config']); return;
		}
		// echo "<pre>";print_r($_POST);die;
	    $this->load->model('mailandsms_m');
		$marks_grade = $this->input->post('marks_grade');

		$st_ids = $this->input->post('st_ids');
		$mobile_no = $this->input->post('mobile_no');
		$marks_template = $this->input->post('marks_template');
		$st_names = $this->input->post('st_names');
		$total_marks = $this->input->post('total_marks');
		$exam_name = $this->input->post('exam_name');
		$total = 0;
		$this->data['setting'] = $this->setting_m->get_setting();
		$school_name = (isset($this->data['setting']->sname)) ? $this->data['setting']->sname : "";

		$sql = "select * from smssettings where types='msg91' and field_names='msg91_senderID'";
		$senderid = $this->db->query($sql)->row()->field_values;

		$sql1 = "select * from smssettings where types='msg91' and field_names='msg91_register_school_name'";
		$registered_school_name = $this->db->query($sql1)->row()->field_values;
			// echo $senderid;die;
		foreach($st_ids as $key => $student)
		{
		    if(isset($mobile_no[$key]) && $mobile_no[$key]!='')
		    { 
					$grade_str = (strpos($_SERVER['HTTP_HOST'], 'skc.collegehour') === false) ? ' Grade:'.$marks_grade[$key] : '';
					if( $senderid=='VGNSSP'){
		        	$template1 = rtrim($marks_template[$key], ',');
					
					
		        	$template = 'Dear parent, your children '.$st_names[$key].' '.$exam_name[$key].' marks are '.$template1.'. Total: '.$total_marks[$key].$grade_str.', From '.$school_name.'. VGNSSP';

					 // Dear parent, your children vasu FA - 1 marks are TELUGU=11/100,HINDI=16/50,ENGLISH=17/60,MATH=18/60,EVS=0/100,computer=0/100. Total: 62/470, From OURSCHOOL ERP. VGNSSP
				}
				// else if($senderid=='VIDYNI'){
				// 	$template1 = substr($marks_template[$key],0,-1);

				// 	$subs = explode(',',$template1);
				// 	// echo "<pre>";print_r($subs);die;

				// 	if($subs[6]){
				//     	$a = explode('=',$subs[6]);
			    // 		$seventh_sub = ",".substr($a[0],0,4) . "=" .$a[1] ;
				// 	}
			
				// 	$var1 = ($subs[0]?$subs[0]:'-').','.($subs[1]?$subs[1]:'-');
				//     $var2 = ($subs[2]?$subs[2]:'-').','.($subs[3]?$subs[3]:'-');
				// 	$var3 = ($subs[4]?$subs[4]:'-').','.($subs[5]?$subs[5]:'-').$seventh_sub;

					 					
				// 	$template = 'Dear parent, your children '.$st_names[$key].' '.$exam_name[$key].' marks are '.$var1.' and '.$var2.' and '.$var3.' . Total: '.$total_marks[$key].', From '.$registered_school_name.' . '.$senderid;

				// 	//Dear parent, your children srinivas fa- 1 marks are telugu - 25/25,hindhi - 25/25 and english -25/25,maths - 25/25 and evs-25/25, social25/25 . Total: tptal -150/150, From Sri vidyaniketan school . VIDYNI

				// }
				else if($senderid=='GGSDRS'){ //ggs darshi
					$template1 = rtrim($marks_template[$key], ',');

					$subs = explode(',',$template1);
					// echo "<pre>";print_r($subs);die;
					$var1 = ($subs[0]?$subs[0]:'-').','.($subs[1]?$subs[1]:'-');
				    $var2 = ($subs[2]?$subs[2]:'-').','.($subs[3]?$subs[3]:'-');
					$var3 = ($subs[4]?$subs[4]:'-').','.($subs[5]?$subs[5]:'-');
					$var4 = ($subs[6]?$subs[6]:'-').','.($subs[7]?$subs[7]:'-');
					
					$template = 'Dear parent, your children '.$st_names[$key].' Exam name '.$exam_name[$key].' marks are '.$var1.' and '.$var2.' and '.$var3.' and '.$var4.'. Total: '.$total_marks[$key].$grade_str.', From '.$registered_school_name.' . '.$senderid;

					
					}else if($senderid=='SDHEDU'){  //siddardha school
						$template1 = rtrim($marks_template[$key], ',');
	
						$subs = explode(',',$template1);
						// echo "<pre>";print_r($subs);die;
						$var1 = ($subs[0]?$subs[0]:'-').','.($subs[1]?$subs[1]:'-');
						$var2 = ($subs[2]?$subs[2]:'-').','.($subs[3]?$subs[3]:'-');
						$var3 = ($subs[4]?$subs[4]:'-').','.($subs[5]?$subs[5]:'-');
						$var4 = ($subs[6]?$subs[6]:'-').','.($subs[7]?$subs[7]:'-');
						
						$template = 'Dear parent, your children '.$st_names[$key].' Exam name '.$exam_name[$key].' marks are '.$var1.' and '.$var2.' and '.$var3.' and '.$var4.'. Total: '.$total_marks[$key].$grade_str.', From '.$registered_school_name.' . '.$senderid;
	
						
	
						//$template = 'Dear parent, your children '.$st_names[$key].' '.$exam_name[$key].' marks are '.$var1.' and '.$var2.' and'.$var3.'. Total: '.$total_marks[$key].', From '.$registered_school_name.' '.$senderid;
	
						//Dear parent, your children vasu marks are 34 and 45 and56. Total: 100, From Principal Gowtham Institutions Cumbum. SRIGEI
	
						// Dear parent, your children {#var#} marks are {#var#} and {#var#} and {#var#}. Total: {#var#}, From Sri Sadana Juniour College Markapur . SSEMRK
	
						
					} else if($senderid=='SVJCPM'){ //srivenkateswara school - sender id is different 
		        	$template1 = rtrim($marks_template[$key], ',');
					$sndid = 'SVES';

					$subs = explode(',',$template1);
					// echo "<pre>";print_r($subs);die;
					$var1 = ($subs[0]?$subs[0]:'-').','.($subs[1]?$subs[1]:'-');
				    $var2 = ($subs[2]?$subs[2]:'-').','.($subs[3]?$subs[3]:'-');
					$var3 = ($subs[4]?$subs[4]:'-').','.($subs[5]?$subs[5]:'-');
					$var4 = ($subs[6]?$subs[6]:'-').','.($subs[7]?$subs[7]:'-');
					
					$template = 'Dear parent, your children '.$st_names[$key].' Exam name '.$exam_name[$key].' marks are '.$var1.' and '.$var2.' and '.$var3.' and '.$var4.' . Total: '.$total_marks[$key].$grade_str.', From '.$registered_school_name.'.'.$senderid;
 
				} else if($senderid=='SSEMRK'){ //sadana
		        	$template1 = rtrim($marks_template[$key], ',');

					$subs = explode(',',$template1);
 					$var1 = ($subs[0]?$subs[0]:'-').','.($subs[1]?$subs[1]:'-');
				    $var2 = ($subs[2]?$subs[2]:'-').','.($subs[3]?$subs[3]:'-');
					$var3 = ($subs[4]?$subs[4]:'-').','.($subs[5]?$subs[5]:'-');
					$var4 = ($subs[6]?$subs[6]:'-').','.($subs[7]?$subs[7]:'-');
					
					$template = 'Dear parent, your children '.$st_names[$key].' marks are '.$var1.' and '.$var2.' and '.$var3.' and '.$var4.' . Total: '.$total_marks[$key].' Exam name '.$exam_name[$key].', From '.$registered_school_name.' . '.$senderid;

					 

					// Dear parent, your children {#var#} marks are {#var#} and {#var#} and {#var#}. Total: {#var#}, From Sri Sadana Juniour College Markapur . SSEMRK

					
				} else{
 		        	$template1 = rtrim($marks_template[$key], ',');
					// echo $template1;die;

					$subs = explode(',',$template1);
					// echo "<pre>";print_r($subs);die;
					$var1 = ($subs[0]?$subs[0]:'-').','.($subs[1]?$subs[1]:'-');
				    $var2 = ($subs[2]?$subs[2]:'-').','.($subs[3]?$subs[3]:'-');
					$var3 = ($subs[4]?$subs[4]:'-').','.($subs[5]?$subs[5]:'-');
					$var4 = ($subs[6]?$subs[6]:'-').','.($subs[7]?$subs[7]:'-');
					
					$template = 'Dear parent, your children '.$st_names[$key].' Exam name '.$exam_name[$key].' marks are '.$var1.' and '.$var2.' and '.$var3.' and '.$var4.' . Total: '.$total_marks[$key].$grade_str.', From '.$registered_school_name.' . '.$senderid;

					

					//$template = 'Dear parent, your children '.$st_names[$key].' '.$exam_name[$key].' marks are '.$var1.' and '.$var2.' and'.$var3.'. Total: '.$total_marks[$key].', From '.$registered_school_name.' '.$senderid;

					//Dear parent, your children vasu marks are 34 and 45 and56. Total: 100, From Principal Gowtham Institutions Cumbum. SRIGEI

					// Dear parent, your children {#var#} marks are {#var#} and {#var#} and {#var#}. Total: {#var#}, From Sri Sadana Juniour College Markapur . SSEMRK

					
				}
 		        $campid = $res = $this->userConfigSMS($template,$mobile_no[$key]);
		        // if($res==TRUE)
		        if((int)$campid)
		        {
		            $total++;
		            
        		    $array = array(
        				'campid' => $campid,
        				'usertypeID' => 3,
        				'users' => $st_names[$key],
        				'type' => 'SMS',
        				'message' => $template,
        				'year' => date('Y'),
        				'senderusertypeID' => $this->session->userdata('usertypeID'),
        				'senderID' => $this->session->userdata('loginuserID')
        			);
        			$this->mailandsms_m->insert_mailandsms($array);
		        }
		    }
		}
		if($total>0)
		{
		    $this->session->set_flashdata('success', 'Marks Sent successfully.');
		}
		echo json_encode($total);
	}

	public function send_marks_to_whatsapp() {
		if (!notification_enabled('exam_marks', 'whatsapp')) {
			echo json_encode(['status' => false, 'message' => 'WhatsApp is disabled for Exam Marks in Notification Config']); return;
		}
		// error_reporting(E_ALL);
		// ini_set('display_errors', 1);
		// echo "<pre>";print_r($_POST);die;
		$marks_grade = $this->input->post('marks_grade');
		$st_ids = $this->input->post('st_ids');
		$mobile_no = $this->input->post('mobile_no');
		$marks_template = $this->input->post('marks_template');
		$st_names = $this->input->post('st_names');
		$total_marks = $this->input->post('total_marks');
		$exam_name = $this->input->post('exam_name');
		$exam_date = $this->input->post('exam_date');
		$this->data['setting'] = $this->setting_m->get_setting();
		$school_name = (isset($this->data['setting']->sname)) ? $this->data['setting']->sname : "";

		$total = 0; 

		$template_sql = "select params,template_name from whatapp_templates where short_name like '%EXAM_MARKS%' ";
		$whatsapp_params = $this->db->query($template_sql)->row_array(); 
        $template_name = $whatsapp_params['template_name'];

		foreach($st_ids as $key => $student)
		{
		    if(isset($mobile_no[$key]) && $mobile_no[$key]!='')
		    { 
		        	$template1 = rtrim($marks_template[$key], ',');
					$grade_str = (strpos($_SERVER['HTTP_HOST'], 'skc.collegehour') === false) ? ' Grade:'.$marks_grade[$key] : '';
					 
					// Explode exam name with 'held on' to separate exam name and date
					$exam_parts = explode(' held on ', $exam_name[$key]);
					$exam_name_only = $exam_parts[0];
					$exam_date_only = isset($exam_parts[1]) ? $exam_parts[1] : '';
					
					// $params = $st_names[$key].','.$exam_name[$key].','.$exam_date[$key];
					$params = $st_names[$key].','.$exam_name_only.','.$exam_date_only;
					

					if (strpos($school_name, 'VIVEKA') !== false || strpos($_SERVER['HTTP_HOST'], 'skc.collegehour') !== false ) {
						$final_messege = $template1. '. Total: '.$total_marks[$key]; //no need grade & rank;
						$final_messege = str_replace(',', '.', $final_messege);

					}else{
						$final_messege = $template1. '. Total: '.$total_marks[$key] . ' Grade:'.$marks_grade[$key];
						$final_messege = str_replace(',', '.', $final_messege);
					}

					// echo $params;die;


					if (strpos($school_name, 'HANUMANTHARAO') !== false 
					|| strpos($school_name, 'SRI LITTLE') !== false
					|| strpos($school_name, 'Navami Global school') !== false 
					|| strpos($school_name, 'Infant Jesus E.M. High School') !== false 
					)
					{
					$message = $params.','.$final_messege;	//don't need school name in template

					}else{
					$message = $params.','.$final_messege.','.$school_name;

					}
					// echo $params;die;
					// echo $message;die;
					$res = $this->Whatsapp_m->sendWhatsapp($mobile_no[$key],$message,$template_name);
		        
		    }
		}
		if($total>0)
		{
		    $this->session->set_flashdata('success', 'Marks Sent successfully.');
		}
		echo json_encode($total);
	}
	

	public function send_balance_sms() {
		// echo "<pre>";print_r($_POST);
	    $this->load->model('mailandsms_m');
		$st_ids = $this->input->post('st_ids');
		$mobile_no = $this->input->post('mobile_no');
		$balance = $this->input->post('balance');
		$date = $this->input->post('date');
		$dynamic_term = $this->input->post('dynamic_term') != 'Please Select' ?  $this->input->post('dynamic_term') : '';
		// $marks_template = $this->input->post('marks_template');
		$st_names = $this->input->post('st_names');
		$total = 0;

		$this->data['setting'] = $this->setting_m->get_setting();
		$school_name = (isset($this->data['setting']->sname)) ? $this->data['setting']->sname : "";

		$sql = "select * from smssettings where types='msg91' and field_names='msg91_senderID'";
		$senderid = $this->db->query($sql)->row()->field_values;

		$sql1 = "select * from smssettings where types='msg91' and field_names='msg91_register_school_name'";
		$registered_school_name = $this->db->query($sql1)->row()->field_values;




		$template_id = 0;
        $template = $this->mailandsmstemplate_m->get_order_by_mailandsmstemplate(array('name'=>'balance_sms'));
        $message = $template[0]->template;  
        $templ_id = $template[0]->templ_id;  

		$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 3));
			 

		// print_r($st_ids);die;
		$j=0;
		foreach($st_names as $key => $student)
		{

			$j++;
			$user = array();
		 
			 $decrypt_data1 =  decrypt_data($balance[$key]);
			$decrypt_data = explode("^",$decrypt_data1);


			 $user['fee_amount'] = 'Rs '.$decrypt_data[0].'.00';
			 $user['paid_amount'] = 'Rs '.$decrypt_data[1].'.00';
			$user['balance_amount'] = 'Rs '.$decrypt_data[2].'.00';
			$user['date'] = $date;
			$user['dynnamic_term'] = $dynamic_term;
			$user['srname'] = $student;
			// print_r((object)($user));die;
			echo $template = $this->tagConvertor($userTags, (object)$user, $message, 'SMS');
 

		    if(isset($mobile_no[$key]) && $mobile_no[$key]!='')
		    { 	
		        	 
				 //$template = "Dear parent your children ".$st_names[$key]." please pay Rs ".$balance[$key]."/- on or before this month -Geethanjali School Vnk";
			

		       $campid = $res = $this->userConfigSMS_balance_sms($template,$mobile_no[$key],$templ_id);
		        // if($res==TRUE)
		        if((int)$campid)
		        {
		            $total++;
		            
        		    $array = array(
        				'campid' => $campid,
        				'usertypeID' => 3,
        				'users' => $st_names[$key],
        				'type' => 'SMS',
        				'message' => $template,
        				'year' => date('Y'),
        				'senderusertypeID' => $this->session->userdata('usertypeID'),
        				'senderID' => $this->session->userdata('loginuserID')
        			);
        			$this->mailandsms_m->insert_mailandsms($array);
		        }
		    }
		}
		// echo $j;die;
		if($total>=0)
		{
		    $this->session->set_flashdata('success', 'Balance SMS Sent successfully.');
		}
		echo json_encode($total);
	}


	private function userConfigSMS($message, $user)
	{
		//echo $message;die;
	    $this->load->model('mailandsmstemplate_m');
	    $this->load->library('msg91');
	    $template_id = 0;
        $template = $this->mailandsmstemplate_m->get_mailandsmstemplate(9);
        $template_id = $template->templ_id;
		if($user) {

			$obj = $this->msg91->send($user, $message, $template_id); 
				$campid = explode(":",$obj); 
				 $campid = rtrim($campid[1],'}"');
				 $campid = trim($campid,"'");
				
				if ($campid) {
    		//if($this->msg91->send($user, $message, $template_id) == TRUE){
    			// return TRUE;
				return $campid;
    		}
		}
	}

	private function userConfigSMS_balance_sms($message, $user,$templ_id)
	{
	    $this->load->model('mailandsmstemplate_m');
	    $this->load->library('msg91');
	    $template_id = 0;
        // $templ_id = $this->mailandsmstemplate_m->get_mailandsmstemplate(9);
        $template_id = $templ_id;
		if($user) {

			$obj = $this->msg91->send($user, $message, $template_id); 
				$campid = explode(":",$obj); 
				 $campid = rtrim($campid[1],'}"');
				 $campid = trim($campid,"'");
				
				if ($campid) {
    		//if($this->msg91->send($user, $message, $template_id) == TRUE){
    			// return TRUE;
				return $campid;
    		}
		}
	}

	private function tagConvertor($userTags, $user, $message, $sendType) { 
		if(customCompute($userTags)) {
			// echo "<pre>";print_r($user);
			// print_r($userTags);
		    $this->load->model('setting_m');
		    $this->data['setting'] = $this->setting_m->get_setting();
		    $school_name = (isset($this->data['setting']->sname)) ? $this->data['setting']->sname : "";
			foreach ($userTags as $key => $userTag) {
				if($userTag->tagname == '{{paid_amount}}') {
					if($user->paid_amount) {
						$message = str_replace('{{paid_amount}}', $user->paid_amount, $message);
					} else {
						$message = str_replace('{{paid_amount}}', ' ', $message);
					}
				} elseif($userTag->tagname == '{{category}}') {
					if($user->category) {
						$message = str_replace('{{category}}', $user->category, $message);
					} else {
						$message = str_replace('{{category}}', ' ', $message);
					}
				}
				elseif($userTag->tagname == '{{school_name}}') {
					$message = str_replace("{{school_name}}", $school_name, $message);
				}
                elseif($userTag->tagname == '{{student_name}}') {
					 $message = str_replace("{{student_name}}",$user->srname, $message);
				}elseif($userTag->tagname == '{{class_name}}' || $userTag->tagname == '[class]') {
					 $message = str_replace("{{class}}",$user->class_name, $message);
				}
				elseif($userTag->tagname == '{{balance_amount}}') {
					$message = str_replace("{{balance_amount}}",$user->dynnamic_term .' '.$user->balance_amount, $message);
				}
				elseif($userTag->tagname == '{{fee_amount}}') {
					$message = str_replace("{{fee_amount}}",$user->fee_amount, $message);
				}elseif($userTag->tagname == '{{date}}') {
					// Ensure we always return in d-m-Y format
					$formattedDate = '';
					if(!empty($user->date)) {
						$formattedDate = date('d-m-Y', strtotime($user->date));
					}
					$message = str_replace("{{date}}", $formattedDate, $message);
				}
			}
		}
		return $message;
	}

	// public function send_marks_to_whatsapp() {
	// 	 echo "<pre>";print_r($_POST);die;
	   
	// }

public function send_balance_whatsapp()
{
    if (!notification_enabled('fee_reminder', 'whatsapp')) {
        echo json_encode(['status' => false, 'message' => 'WhatsApp is disabled for Fee Reminder in Notification Config']); return;
    }
    $retArray = ['status' => false, 'message' => ''];

    $st_ids       = $this->input->post('st_ids');
    $mobile_no    = $this->input->post('mobile_no');
    $balance      = $this->input->post('balance');
    $date         = $this->input->post('date');
    $dynamic_term = $this->input->post('dynamic_term') != 'Please Select' ? $this->input->post('dynamic_term') : '';
    $st_names     = $this->input->post('st_names');
    $class_name   = $this->input->post('class_name');
 

	 $template_sql = "select params,template_name from whatapp_templates where short_name like '%FEE_REMINDER%' ";
		$template = $this->db->query($template_sql)->row_array();

    if (!$template) {
        $retArray['message'] = "Invalid template selected.";
        echo json_encode($retArray);
        return;
    }

    if (empty($st_ids) || empty($mobile_no) || empty($balance) || empty($template['template_name'])) {
        $retArray['message'] = "Missing required fields.";
        echo json_encode($retArray);
        return;
    }

    // ✅ Get registered school name
    $this->load->model('setting_m');
    $this->data['setting'] = $this->setting_m->get_setting();
    $school_name = isset($this->data['setting']->sname) ? $this->data['setting']->sname : '';

    $query = $this->db->select('field_values')
        ->where(['types' => 'msg91', 'field_names' => 'msg91_register_school_name'])
        ->get('smssettings')
        ->row();
    $registered_school_name = $query ? $query->field_values : $school_name;
 
	

    // ✅ Prepare bulk message data
    $bulkMessages = [];
    foreach ($st_names as $key => $student_name) {
        if (empty($mobile_no[$key])) continue;


		 $decrypt_data1 =  decrypt_data($balance[$key]);
		$decrypt_data = explode("^",$decrypt_data1);


		// $user['fee_amount'] = 'Rs '.$decrypt_data[0].'.00';
		// $user['paid_amount'] = 'Rs '.$decrypt_data[1].'.00';
		// $user['balance_amount'] = 'Rs '.$decrypt_data[2].'.00';

		//   $param1 = $student_name;
        // $param2 = 'Rs '.str_replace(',', '', $decrypt_data[2]);
        // $param3 = $date;
        // // $param4 = $registered_school_name;

        // $params = "{$param1},{$param2},{$param3}";
		// echo $params;die;
		$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 3));

        $user['balance_amount'] = str_replace(',', '', $decrypt_data[2]);
        $user['date'] = $date; 
        $user['srname'] = $student_name;
        $user['class_name'] = $class_name;
        print_r($template['params']);
        echo $params = $this->tagConvertor($userTags, (object)$user, $template['params'], 'SMS');

        $bulkMessages[] = [
            'phone'   => $mobile_no[$key],
            'message' => $params
        ];
    }

    if (empty($bulkMessages)) {
        $retArray['message'] = "No valid phone numbers found.";
        echo json_encode($retArray);
        return;
    }

    // ✅ Send messages via model
    $this->load->model('Whatsapp_m');
    $sentCount = $this->Whatsapp_m->sendWhatsapp_bulk_batch($bulkMessages, $template['template_name']);

    $retArray['status'] = true;
    $retArray['message'] = "WhatsApp balance messages sent successfully to {$sentCount} recipients.";
    echo json_encode($retArray);
}


    /**
     * Send progress card as WhatsApp document using template PROGRESS_CARD
     * Uses sendWhatsapp_bulk_batch_with_media to handle document attachments
     */
    private function send_progresscard_whatsapp($phone, $student_name, $exam_name, $media_path)
    {
        $retArray = ['status' => false, 'message' => '', 'sent' => 0];

        if (empty($phone) || empty($media_path)) {
            $retArray['message'] = 'Missing phone or media path';
            return $retArray;
        }

        // Get template with short_name like PROGRESS_CARD
        $template_sql = "select params,template_name from whatapp_templates where short_name like 'PROGRESS_CARD' ";
        $template = $this->db->query($template_sql)->row_array();

        if (!$template || empty($template['template_name'])) {
            $retArray['message'] = 'Template PROGRESS_CARD not configured';
            return $retArray;
        }

        // Params: student_name,exam_name (as stated in requirements)
        $params = "{$student_name},{$exam_name}";

        $bulkMessages = [];
        $bulkMessages[] = [
            'phone'   => $phone,
            'message' => $params,
            'url'     => $media_path,
            'htype'   => 'document'
        ];

        $this->load->model('Whatsapp_m');
        // Use sendWhatsapp_bulk_batch_with_media to handle htype and url parameters
        // $sentCount = $this->Whatsapp_m->sendWhatsapp_bulk_batch_with_media($bulkMessages, $template['template_name']);
        $sentCount = $this->Whatsapp_m->sendWhatsapp_bulk_batch_with_media_progresscard($bulkMessages, $template['template_name']);

		

        $retArray['status'] = true;
        $retArray['message'] = "Sent to {$sentCount} recipient(s)";
        $retArray['sent'] = $sentCount;
        return $retArray;
    }
}
