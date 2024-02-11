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
									
									// echo "<pre>";print_r($this->session->userdata());die;
									// dd($attendace);
									if(!empty($attendace)){
										// echo count($attendace);die;
									for($j=0;$j<count($attendace);$j++){
									foreach($attendace[$j] as $k => $v){
										// echo $k."<br/>";
										for ($i=1; $i <= 31; $i++) { 
											 $acolumnname = 'a'.$i;
											if($k == $acolumnname){
												if(!empty($v[$k])){
													if($v[$k] == 'P'){
														 $present += 1;
													}else if($v[$k] == 'A' ){
														 $absent += 1;
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
									//  echo "<pre>";print_r($this->data['attendance']);
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
								$this->db->where('studentID',$studentID[$i]);
								$attendace = $this->db->get('attendance')->result_array();
								$absent = 0;
								$present = 0;
								
								
								for($j=0;$j<count($attendace);$j++){
								foreach($attendace[$j] as $k => $v){
									for ($d=1; $d <= 31; $d++) { 
										$acolumnname = 'a'.$d;
										if($k == $acolumnname){
											 if(!empty($v[$k])){
												if($v[$k] == 'P'){
													$present += 1;
												}else if($v[$k] == 'A'){
													$absent += 1;
												}
											 }
										}
									}
								}}
								$temp = array(
									'absent' => $absent,
									'present' => $present
								 );
								 $this->data['attendance'][$mkey][$studentID[$i]] =$temp;
								}
								//end code for attendance


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

					$attachment = $this->generateAttachment('progresscardreport.css', $this->data, 'report/progresscard/ProgresscardReportPDF');
					$media_path = base_url().$attachment;
					// echo "<pre>";print_r($students);die;
					
					$params = array('whatsapp_number'=> $students[0]->phone,'media_path'=>$media_path,'whatsapp_msg'=>'Progress Report');
					$api_response = $this->send_whatsapp_attachment($params);


					$retArray[$students[0]->phone]['status'] = TRUE;
					$retArray[$students[0]->phone]['api_response'] = $api_response;
					echo json_encode($retArray);
    				//exit;
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
					echo "<option value=\"$student->srstudentID\">".$student->srname."</option>";
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
		// echo "<pre>";print_r($_POST);die;
	    $this->load->model('mailandsms_m');
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

		foreach($st_ids as $key => $student)
		{
		    if(isset($mobile_no[$key]) && $mobile_no[$key]!='')
		    {
				if( $senderid=='VGNSSP'){
		        	$template1 = substr($marks_template[$key],0,-1);
					
		        	$template = 'Dear parent, your children '.$st_names[$key].' '.$exam_name[$key].' marks are '.$template1.'. Total: '.$total_marks[$key].', From '.$school_name.'. VGNSSP';

					 // Dear parent, your children vasu FA - 1 marks are TELUGU=11/100,HINDI=16/50,ENGLISH=17/60,MATH=18/60,EVS=0/100,computer=0/100. Total: 62/470, From OURSCHOOL ERP. VGNSSP
				}else if($senderid=='VIDYNI'){
					$template1 = substr($marks_template[$key],0,-1);

					$subs = explode(',',$template1);
					// echo "<pre>";print_r($subs);die;
					$var1 = ($subs[0]?$subs[0]:'-').','.($subs[1]?$subs[1]:'-');
				    $var2 = ($subs[2]?$subs[2]:'-').','.($subs[3]?$subs[3]:'-');
					$var3 = ($subs[4]?$subs[4]:'-').','.($subs[5]?$subs[5]:'-');
					
					$template = 'Dear parent, your children '.$st_names[$key].' '.$exam_name[$key].' marks are '.$var1.' and '.$var2.' and '.$var3.' . Total: '.$total_marks[$key].', From '.$registered_school_name.' . '.$senderid;

					//Dear parent, your children srinivas fa- 1 marks are telugu - 25/25,hindhi - 25/25 and english -25/25,maths - 25/25 and evs-25/25, social25/25 . Total: tptal -150/150, From Sri vidyaniketan school . VIDYNI

				}else{
		        	$template1 = substr($marks_template[$key],0,-1);

					$subs = explode(',',$template1);
					// echo "<pre>";print_r($subs);die;
					$var1 = ($subs[0]?$subs[0]:'-').','.($subs[1]?$subs[1]:'-');
				    $var2 = ($subs[2]?$subs[2]:'-').','.($subs[3]?$subs[3]:'-');
					$var3 = ($subs[4]?$subs[4]:'-').','.($subs[5]?$subs[5]:'-');
					
					$template = 'Dear parent, your children '.$st_names[$key].' '.$exam_name[$key].' marks are '.$var1.' and '.$var2.' and '.$var3.'. Total: '.$total_marks[$key].', From '.$registered_school_name.' . '.$senderid;

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
	
	private function userConfigSMS($message, $user)
	{
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

	public function send_marks_to_whatsapp() {
		 echo "<pre>";print_r($_POST);die;
	   
	}

}
