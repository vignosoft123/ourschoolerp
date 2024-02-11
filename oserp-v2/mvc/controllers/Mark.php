<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mark extends Admin_Controller
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
	function __construct()
	{
		parent::__construct();
		$this->load->model("mark_m");
		$this->load->model("grade_m");
		$this->load->model("classes_m");
		$this->load->model("exam_m");
		$this->load->model("subject_m");
		$this->load->model("section_m");
		$this->load->model("student_m");
		$this->load->model("markrelation_m");
		$this->load->model("markpercentage_m");
		$this->load->model('studentrelation_m');
		$this->load->model('marksetting_m');
		$this->load->library('csvimport');

		$language = $this->session->userdata('lang');
		$this->lang->load('mark', $language);
	}

	protected function rules()
	{
		$rules = array(
			array(
				'field' => 'examID',
				'label' => $this->lang->line("mark_exam"),
				'rules' => 'trim|required|xss_clean|max_length[11]|callback_unique_examID'
			),
			array(
				'field' => 'classesID',
				'label' => $this->lang->line("mark_classes"),
				'rules' => 'trim|required|xss_clean|max_length[11]|callback_unique_classesID'
			),
			array(
				'field' => 'sectionID',
				'label' => $this->lang->line("mark_section"),
				'rules' => 'trim|required|xss_clean|max_length[11]|callback_unique_sectionID'
			),
			// array(
			// 	'field' => 'subjectID',
			// 	'label' => $this->lang->line("mark_subject"),
			// 	'rules' => 'trim|xss_clean|max_length[11]|callback_unique_subjectID'
			// )
		);
		return $rules;
	}

	protected function markRules()
	{
		$rules = array(
			array(
				'field' => 'examID',
				'label' => $this->lang->line("mark_exam"),
				'rules' => 'trim|required|xss_clean|max_length[11]|callback_unique_examID'
			),
			array(
				'field' => 'classesID',
				'label' => $this->lang->line("mark_classes"),
				'rules' => 'trim|required|xss_clean|max_length[11]|callback_unique_classesID'
			),
			array(
				'field' => 'subjectID',
				'label' => $this->lang->line("mark_subject"),
				'rules' => 'trim|required|xss_clean|max_length[11]|callback_unique_subjectID'
			),
			// array(
			// 	'field' => 'inputs',
			// 	'label' => $this->lang->line("mark_subject"),
			// 	'rules' => 'trim|xss_clean|max_length[11]|callback_unique_inputs'
			// )
		);
		return $rules;
	}

	public function send_mail_rules()
	{
		$rules = array(
			array(
				'field' => 'to',
				'label' => $this->lang->line("mark_to"),
				'rules' => 'trim|required|max_length[60]|valid_email|xss_clean'
			),
			array(
				'field' => 'subject',
				'label' => $this->lang->line("mark_subject"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'message',
				'label' => $this->lang->line("mark_message"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'id',
				'label' => $this->lang->line("mark_studentID"),
				'rules' => 'trim|required|max_length[10]|xss_clean|callback_unique_data'
			),
			array(
				'field' => 'set',
				'label' => $this->lang->line("mark_classesID"),
				'rules' => 'trim|required|max_length[10]|xss_clean|callback_unique_data'
			)
		);
		return $rules;
	}

	public function index()
	{
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
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		if ($this->session->userdata('usertypeID') == 3) {
			$id = $this->data['myclass'];
			if (!permissionChecker('mark_view')) {
				$myProfile = true;
			}
		} else {
			$id = htmlentities(escapeString($this->uri->segment(3)));
		}

		if ($this->session->userdata('usertypeID') == 3 && $myProfile) {
			$url = $id;
			$id = $this->session->userdata('loginuserID');
			$this->view($id, $url);
		} else {
			$this->data['set'] = $id;
			$this->data['classes'] = $this->classes_m->get_classes();
        				// echo "aaaa<pre>";print_r($this->session->userdata());die;

			if ((int)$id) {
				$fetchClass = pluck($this->data['classes'], 'classesID', 'classesID');
				if (isset($fetchClass[$id])) {
					
					if($this->session->userdata('usertypeID') == 3){
						$this->db->where('username',$this->session->userdata('username'));
						$query = $this->db->get('student');
						// echo $this->db->last_query();die;
						$student_id = $query->row()->studentID; 
						$this->data['students'] = $this->studentrelation_m->get_order_by_student(array('srclassesID' => $id, 'srschoolyearID' => $schoolyearID, 'studentID' => $student_id));
			
					}else{
						$this->data['students'] = $this->studentrelation_m->get_order_by_student(array('srclassesID' => $id, 'srschoolyearID' => $schoolyearID));
					}

					


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
			} else {
				$this->data['students'] = [];
			}

			$this->data["subview"] = "mark/index";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function add()
	{
	    
	   // error_reporting(E_ALL);
		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
    
		if (($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) {
			$this->data['headerassets'] = array(
				'css' => array(
					'assets/select2/css/select2.css',
					'assets/select2/css/select2-bootstrap.css'
				),
				'js' => array(
					'assets/select2/select2.js'
				)
			);
			$this->data['students']           = [];
			$this->data['settingmarktypeID']  = $this->data['siteinfos']->marktypeID;
			$graduateclass                    = ''; //$this->data['siteinfos']->ex_class;

			$this->data['set_exam']    = 0;
			$this->data['set_classes'] = 0;
			$this->data['set_section'] = 0;
			$this->data['set_subject'] = 0;

			$this->data['sendExam']    = [];
			$this->data['sendSubject'] = [];
			$this->data['sendClasses'] = [];
			$this->data['sendSection'] = [];
			$this->data['exams']       = [];
			$this->data['classes']  = $this->classes_m->get_order_by_classes(['classesID !=' => $graduateclass]);

			if ($_POST) {
				$rules = $this->rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data["subview"] = "mark/add";
					$this->load->view('_layout_main', $this->data);
				} else {
					$examID          = $this->input->post('examID');
					$classesID       = $this->input->post('classesID');
					$sectionID       = $this->input->post('sectionID');
					$subjectID       = $this->input->post('subjectID');
					$downloadFile       = $this->input->post('downloadFile');

					if ((int)$classesID) {
        				$this->data['exams']    = $this->marksetting_m->get_exam($this->data['siteinfos']->marktypeID, $classesID);
        				// $subjectsss = $this->data['subjects'] = $this->subject_m->get_order_by_subject(array('classesID' => $classesID,'examID' => $examID,'sectionID' => $sectionID));
        				
        				$subjectsss = $this->data['subjects'] = $this->subject_m->get_order_by_subject(array('classesID' => $classesID),$examID,$sectionID);
        				// echo "aaaa<pre>";print_r($subjectsss);die;
        				$this->data['sections'] = $this->section_m->get_order_by_section(array('classesID' => $classesID));
        			} else {
        				$this->data['subjects'] = [];
        				$this->data['sections'] = [];
        			}
					
					$this->data['set_exam']    = $examID;
					$this->data['set_classes'] = $classesID;
					$this->data['set_section'] = $sectionID;
					$this->data['set_subject'] = $subjectID;

					$exam            = $this->exam_m->get_single_exam(array('examID' => $examID));
					$subject         = $this->subject_m->get_single_subject(array('subjectID' => $subjectID));
					$classes         = $this->classes_m->get_single_classes(array('classesID' => $classesID));
					$section         = $this->section_m->get_single_section(array('sectionID' => $sectionID));
					$markpercentages = $this->markpercentage_m->get_markpercentage();


					$markpercentageArr['marktypeID'] = $this->data['siteinfos']->marktypeID;
					$markpercentageArr['classesID']  = $classesID;
					$markpercentageArr['examID']     = $examID;
					$markpercentageArr['subjectID']  = $subjectID;
					$markpercentageArr['subject']    = $subject;

					$this->data['sendExam']     = $exam;
					$this->data['sendSubject']  = $subject;
					$this->data['sendClasses']  = $classes;
					$this->data['sendSection']  = $section;

					$schoolyearID       = $this->session->userdata('defaultschoolyearID');
					$studentArray = [
						'srclassesID'   => $classesID,
						'srsectionID'   => $sectionID,
						'srschoolyearID' => $schoolyearID,
					];

					$students  = [];
					if (customCompute($subject)) {
						if ($subject->type == 1) {
							// $students = $this->studentrelation_m->get_order_by_student([
							$students = $this->studentrelation_m->get_order_by_student_limit([
								"srclassesID"    	=> $classesID,
								'srschoolyearID' 	=> $schoolyearID
							]);
						} else {
							$students = $this->studentrelation_m->get_order_by_student_limit(array(
								"srclassesID" => $classesID,
								'srschoolyearID' => $schoolyearID,
								'sroptionalsubjectID' => $subject->subjectID
							));

							$studentArray['sroptionalsubjectID'] = $subject->subjectID;
						}
					}

					$sendStudent = $this->studentrelation_m->get_order_by_student($studentArray);
					foreach ($subjectsss as $subj) { 

					$markPluck   = pluck($this->mark_m->get_order_by_mark(array("examID" => $examID, "classesID" => $classesID, "	subjectID" => $subj->subjectID, 'schoolyearID' => $schoolyearID)), 'obj', 'studentID');

					$array = [];
					if (customCompute($students)) {

						// echo "<pre>";print_r($subjectsss);die;

						
						foreach ($students as $student) {
							if (!isset($markPluck[$student->studentID])) {
								//echo 123; die;
								$array[] = array(
									"examID"       => $examID,
									"schoolyearID" => $schoolyearID,
									"exam"         => $exam->exam,
									"studentID"    => $student->studentID,
									"classesID"    => $classesID,
									"subjectID"    => $subj->subjectID, //$subjectID,
									"subject"      => $subj->subject,
									"year"         => date('Y'),
									"create_date"  => date("Y-m-d H:i:s"),
									'create_userID' => $this->session->userdata("loginuserID"),
									'create_usertypeID' => $this->session->userdata('usertypeID')
								);
							}
						}

						// echo "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@<pre>";print_r($array);

						if (customCompute($array)) {
							$count = customCompute($array);

					 
							$firstID = $this->mark_m->insert_batch_mark($array);
							$lastID = $firstID + ($count - 1);

							$markRelationArray = [];
							if ($lastID >= $firstID) {
								for ($i = $firstID; $i <= $lastID; $i++) {
									foreach ($markpercentages as $value) {
										$markRelationArray[] = [
											"markID" => $i,
											"markpercentageID" => $value->markpercentageID
										];
									}
								}
							}

							if (customCompute($markRelationArray)) {
								$this->markrelation_m->insert_batch_markrelation($markRelationArray);
							}
						}
					}

						$mark = $this->mark_m->get_order_by_mark(array('schoolyearID' => $schoolyearID, "examID" => $examID, "classesID" => $classesID));
						$this->data['marks'] = $mark;
						// echo "<pre>";print_r($this->data['marks']);die;
					}

					if (customCompute($students)) {
						$missingmMarkRelationArray = [];
						$allMarkWithRelation = $this->markrelation_m->get_all_mark_with_relation(array('schoolyearID' => $schoolyearID, 'examID' => $examID, 'classesID' => $classesID, 'subjectID' => $subjectID));


						$studentMarkPercentage = [];
						foreach ($allMarkWithRelation as $key => $value) {
							$studentMarkPercentage[$value->studentID][$value->examID][$value->subjectID]['markpercentage'][] = $value->markpercentageID;
							$studentMarkPercentage[$value->studentID][$value->examID]['markID'][$value->subjectID] = $value->markID;
						}

						$markpercentages = pluck($markpercentages, 'markpercentageID');
						foreach ($students as $student) {
							$studentPercentage = isset($studentMarkPercentage[$student->studentID][$examID][$subjectID]['markpercentage']) ? $studentMarkPercentage[$student->studentID][$examID][$subjectID]['markpercentage'] : [];

							if (customCompute($studentPercentage)) {
								$diffMarkPercentage = array_diff($markpercentages, $studentMarkPercentage[$student->studentID][$examID][$subjectID]['markpercentage']);
								foreach ($diffMarkPercentage as $item) {
									$missingmMarkRelationArray[] = [
										"markID" => $studentMarkPercentage[$student->studentID][$examID]['markID'][$subjectID],
										"markpercentageID" => $item
									];
								}
							}
						}

						if (customCompute($missingmMarkRelationArray)) {
							$this->markrelation_m->insert_batch_markrelation($missingmMarkRelationArray);
						}
					}

					$this->data['students']         = $sendStudent;
					$this->data['markpercentages']  = $this->marksetting_m->get_marksetting_markpercentages_add($markpercentageArr);

					$this->data['markRelations']    = $this->getMarkRelationArray($this->mark_m->student_all_mark_array(array('schoolyearID' => $schoolyearID, 'examID' => $examID, 'classesID' => $classesID, 'subjectID' => $subjectID)));

		// 					echo "<pre>";print_r($this->data['markRelations']);
		// die;
					if ($downloadFile == 1) {
						$this->download_mark_sheet($this->data);
					} else {
						$this->data["subview"] = "mark/add";
						$this->load->view('_layout_main', $this->data);
					}
				}
			} else {
				$this->data["subview"] = "mark/add";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function view($studentID = null, $classID = null)
	{
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/custom-scrollbar/jquery.mCustomScrollbar.css'
			),
			'js' => array(
				'assets/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js'
			)
		);

		if ((int) $studentID && (int) $classID) {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$student = $this->studentrelation_m->get_single_student(array('srstudentID' => $studentID, 'srclassesID' => $classID, 'srschoolyearID' => $schoolyearID));
			if (customCompute($student)) {
				$fetchClass = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
				if (isset($fetchClass[$classID])) {
					$this->getView($studentID, $classID);
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

	private function getMarkRelationArray($arrays = NULL)
	{
		$mark   = [];
		$markwr = [];
		if (customCompute($arrays)) {
			foreach ($arrays as $array) {
				$mark[$array->studentID][$array->markpercentageID]   = $array->mark;
				$markwr[$array->studentID][$array->markpercentageID] = $array->markrelationID;
			}
		}
		$this->data['markwr'] = $markwr;
		return $mark;
	}

	private function getView($id, $url)
	{
		if ((int)$id && (int)$url) {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$studentInfo = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srclassesID' => $url, 'srschoolyearID' => $schoolyearID));

			$this->pluckInfo();
			$this->basicInfo($studentInfo);
			$this->markInfo($studentInfo);

			if (customCompute($studentInfo)) {
				$this->data["subview"] = "mark/view";
				$this->load->view('_layout_main', $this->data);
			} else {
				$this->data["subview"] = "error";
				$this->load->view('_layout_main', $this->data);
			}
		}
	}

	private function pluckInfo()
	{
		$this->data['subjects'] = pluck($this->subject_m->general_get_subject(), 'subject', 'subjectID');
	}

	private function basicInfo($studentInfo)
	{
		if (customCompute($studentInfo)) {
			$this->data['profile']  = $studentInfo;
			$this->data['usertype'] = $this->usertype_m->get_single_usertype(array('usertypeID' => $studentInfo->usertypeID));
			$this->data['class']    = $this->classes_m->get_single_classes(array('classesID' => $studentInfo->srclassesID));
			$this->data['section']  = $this->section_m->general_get_single_section(array('sectionID' => $studentInfo->srsectionID));
		} else {
			$this->data['profile'] = [];
		}
	}

	private function markInfo($studentInfo)
	{
		if (customCompute($studentInfo)) {
			$this->getMark($studentInfo->studentID, $studentInfo->srclassesID);
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
				$grades            = $this->grade_m->get_grade();
				$marks             = $this->mark_m->student_all_mark_array($queryArray);
				$markpercentages   = $this->markpercentage_m->get_markpercentage();

				$subjects          = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID));
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

	public function mark_send()
	{
		$retArray['status'] = FALSE;
		$retArray['message'] = '';

		if ($_POST) {
			$rules = $this->markRules();
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE) {
				$retArray = $this->form_validation->error_array();
				$retArray['status'] = FALSE;
				echo json_encode($retArray);
				exit;
			} else {
				$examID 		= $this->input->post("examID");
				$classesID		= $this->input->post("classesID");
				$subjectID 		= $this->input->post("subjectID");
				$inputs 		= $this->input->post("inputs");
				$schoolyearID 	= $this->data['siteinfos']->school_year;

				$markRelationArray = [];
				if (customCompute($inputs)) {
					foreach ($inputs as $key => $value) {
						$data = explode('-', $value['mark']);
						if (!empty($value['value']) || $value['value'] != "") {

							$this->db->where('markpercentageID',$value['markpercentageid']);
							$this->db->where('markID',$data[1]);
							$this->db->update('markrelation',array('mark' => abs($value['value']) ));

							// $markRelationArray[] = [
							// 	'markrelationID' => $data[1],
							// 	'mark' => abs($value['value'])
							// ];
						} else {
							// $markRelationArray[] = [
							// 	'markrelationID' => $data[1],
							// 	'mark' => NULL
							// ];
							// $this->db->where('markpercentageID',$value['markpercentageid']);
							// $this->db->where('markID',$data[1]);
							// $this->db->update('markrelation',array('mark' => abs($value['value']) ));

						}
					}
				}

				// if (customCompute($markRelationArray)) {
					// $this->markrelation_m->update_batch_markrelation($markRelationArray, 'markrelationID');
					
				// }

				$retArray['status'] = TRUE;;
				$retArray['message'] = $this->lang->line('mark_success');
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['message'] = 'Something wrong';
			echo json_encode($retArray);
			exit;
		}
	}

	public function print_preview()
	{
		if (permissionChecker('mark_view') || (($this->session->userdata('usertypeID') == 3) && permissionChecker('mark') && ($this->session->userdata('loginuserID') == htmlentities(escapeString($this->uri->segment(3)))))) {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$studentID 	= htmlentities(escapeString($this->uri->segment(3)));
			$classID 	= htmlentities(escapeString($this->uri->segment(4)));

			if ((int)$studentID && (int)$classID) {
				$schoolyearID = $this->session->userdata('defaultschoolyearID');
				$student = $this->studentrelation_m->get_single_student(array('srstudentID' => $studentID, 'srclassesID' => $classID, 'srschoolyearID' => $schoolyearID));
				if (customCompute($student)) {
					$fetchClass = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
					if (isset($fetchClass[$classID])) {
						$this->getMarkPrintPDF($studentID, $classID);
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

	private function getMarkPrintPDF($studentID, $classesID)
	{
		if ((int)$studentID && (int)$classesID) {
			$schoolyearID = $this->session->userdata('defaultschoolyearID');
			$student      = $this->studentrelation_m->get_single_student(array('srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID));
			$classes      = $this->classes_m->get_single_classes(array('classesID' => $classesID));

			if (customCompute($student) && customCompute($classes)) {
				$queryArray = [
					'classesID'    => $student->srclassesID,
					'sectionID'    => $student->srsectionID,
					'studentID'    => $student->srstudentID,
					'schoolyearID' => $schoolyearID,
				];

				$exams             = pluck($this->exam_m->get_exam(), 'exam', 'examID');
				$grades            = $this->grade_m->get_grade();
				$marks             = $this->mark_m->student_all_mark_array($queryArray);
				$markpercentages   = $this->markpercentage_m->get_markpercentage();

				$subjects          = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID));
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
				$usertype        = $this->usertype_m->get_single_usertype(array('usertypeID' => $student->usertypeID));
				$section         = $this->section_m->general_get_single_section(array('sectionID' => $student->srsectionID));

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
				$this->data['exams']             = $exams;
				$this->data['grades']            = $grades;
				$this->data['markpercentages']   = pluck($markpercentages, 'obj', 'markpercentageID');
				$this->data['optionalsubjectArr'] = $optionalsubjectArr;
				$this->data['marks']             = $retMark;
				$this->data['highestmarks']      = $highestMarks;
				$this->data['marksettings']      = isset($marksettings[$classesID]) ? $marksettings[$classesID] : [];

				$this->data['student']           = $student;
				$this->data['classes']           = $classes;
				$this->data['section']           = $section;
				$this->data['usertype']          = $usertype;

				$this->reportPDF('markmodule.css', $this->data, 'mark/print_preview');
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

				$this->data['student']           = [];
				$this->data['classes']           = [];
				$this->data['section']           = [];
				$this->data['usertype']          = [];
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

			$this->data['student']           = [];
			$this->data['classes']           = [];
			$this->data['section']           = [];
			$this->data['usertype']          = [];
		}
	}

	public function send_mail()
	{
		$retArray['status'] = FALSE;
		$retArray['message'] = '';
		if (permissionChecker('mark_view') || (($this->session->userdata('usertypeID') == 3) && permissionChecker('mark') && ($this->session->userdata('loginuserID') == $this->input->post('id')))) {
			if ($_POST) {
				$rules = $this->send_mail_rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
					echo json_encode($retArray);
					exit;
				} else {
					$studentID = $this->input->post('id');
					$classesID = $this->input->post('set');

					if ((int)$studentID && (int)$classesID) {
						$schoolyearID = $this->session->userdata('defaultschoolyearID');
						$student = $this->studentrelation_m->get_single_student(array('srstudentID' => $studentID, 'srclassesID' => $classesID, 'srschoolyearID' => $schoolyearID));
						$classes = $this->classes_m->get_single_classes(array('classesID' => $classesID));
						if (customCompute($student) && customCompute($classes)) {
							$email        = $this->input->post('to');
							$inputsubject = $this->input->post('subject');
							$message      = $this->input->post('message');

							$queryArray = [
								'classesID' => $student->srclassesID,
								'sectionID' => $student->srsectionID,
								'studentID' => $student->srstudentID,
								'schoolyearID' => $schoolyearID,
							];

							$exams             = pluck($this->exam_m->get_exam(), 'exam', 'examID');
							$grades            = $this->grade_m->get_grade();
							$marks             = $this->mark_m->student_all_mark_array($queryArray);
							$markpercentages   = $this->markpercentage_m->get_markpercentage();

							$subjects          = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID));
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
							$usertype        = $this->usertype_m->get_single_usertype(array('usertypeID' => $student->usertypeID));
							$section         = $this->section_m->general_get_single_section(array('sectionID' => $student->srsectionID));

							$allStudentMarks = $this->mark_m->student_all_mark_array(array('classesID' => $classesID, 'schoolyearID' => $schoolyearID));
							$highestMarks = [];
							foreach ($allStudentMarks as $value) {
								if (!isset($highestMarks[$value->examID][$value->subjectID][$value->markpercentageID])) {
									$highestMarks[$value->examID][$value->subjectID][$value->markpercentageID] = -1;
								}
								$highestMarks[$value->examID][$value->subjectID][$value->markpercentageID] = max($value->mark, $highestMarks[$value->examID][$value->subjectID][$value->markpercentageID]);
							}
							$marksettings  = $this->marksetting_m->get_marksetting_markpercentages();

							$this->data['settingmarktypeID'] = $this->data['siteinfos']->marktypeID;
							$this->data['subjects']          = $subjectArr;
							$this->data['exams']             = $exams;
							$this->data['grades']            = $grades;
							$this->data['markpercentages']   = pluck($markpercentages, 'obj', 'markpercentageID');
							$this->data['optionalsubjectArr'] = $optionalsubjectArr;
							$this->data['marks']             = $retMark;
							$this->data['highestmarks']      = $highestMarks;
							$this->data['marksettings']      = isset($marksettings[$classesID]) ? $marksettings[$classesID] : [];

							$this->data['student']           = $student;
							$this->data['classes']           = $classes;
							$this->data['section']           = $section;
							$this->data['usertype']          = $usertype;

							$this->reportSendToMail('markmodule.css', $this->data, 'mark/print_preview', $email, $inputsubject, $message);
							$retArray['message'] = "Success";
							$retArray['status'] = TRUE;
							echo json_encode($retArray);
							exit;
						} else {
							$retArray['message'] = $this->lang->line('mark_data_not_found');
							echo json_encode($retArray);
							exit;
						}
					} else {
						$retArray['message'] = $this->lang->line('mark_data_not_found');
						echo json_encode($retArray);
						exit;
					}
				}
			} else {
				$retArray['message'] = $this->lang->line('mark_permissionmethod');
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['message'] = $this->lang->line('mark_permission');
			echo json_encode($retArray);
			exit;
		}
	}

	public function mark_list()
	{
		$classID = $this->input->post('id');
		if ((int)$classID) {
			$string = base_url("mark/index/$classID");
			echo $string;
		} else {
			redirect(base_url("mark/index"));
		}
	}

	public function examcall()
	{
		$classesID = $this->input->post('classesID');
		if ((int)$classesID) {
			$exams    = pluck($this->marksetting_m->get_exam($this->data['siteinfos']->marktypeID, $classesID), 'obj', 'examID');
			echo "<option value='0'>", $this->lang->line("mark_select_exam"), "</option>";
			if (customCompute($exams)) {
				foreach ($exams as $exam) {
					echo "<option value=" . $exam->examID . ">" . $exam->exam . "</option>";
				}
			}
		} else {
			echo "<option value='0'>", $this->lang->line("mark_select_exam"), "</option>";
		}
	}

	public function subjectcall()
	{
		$id = $this->input->post('id');
		if ((int)$id) {
			$allsubject = $this->subject_m->get_order_by_subject(array("classesID" => $id));
			echo "<option value='0'>", $this->lang->line("mark_select_subject"), "</option>";
			foreach ($allsubject as $value) {
				echo "<option value=\"$value->subjectID\">", $value->subject, "</option>";
			}
		} else {
			echo "<option value='0'>", $this->lang->line("mark_select_subject"), "</option>";
		}
	}

	public function sectioncall()
	{
		$id = $this->input->post('id');
		if ((int)$id) {
			$allsection = $this->section_m->get_order_by_section(array("classesID" => $id));
			echo "<option value='0'>", $this->lang->line("mark_select_section"), "</option>";
			foreach ($allsection as $value) {
				echo "<option value=\"$value->sectionID\">", $value->section, "</option>";
			}
		} else {
			echo "<option value='0'>", $this->lang->line("mark_select_section"), "</option>";
		}
	}

	public function unique_data($data)
	{
		if ($data != '') {
			if ($data == '0') {
				$this->form_validation->set_message('unique_data', 'The %s field is required.');
				return FALSE;
			}
			return TRUE;
		}
		return TRUE;
	}

	public function unique_examID()
	{
		if ($this->input->post('examID') == 0) {
			$this->form_validation->set_message("unique_examID", "The %s field is required");
			return FALSE;
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

	public function unique_subjectID()
	{
		if ($this->input->post('subjectID') == 0) {
			$this->form_validation->set_message("unique_subjectID", "The %s field is required");
			return FALSE;
		}
		return TRUE;
	}

	public function unique_inputs()
	{
		$inputs = $this->input->post('inputs');
		if (customCompute($inputs)) {
			$classesID       = $this->input->post('classesID');
			$examID          = $this->input->post('examID');
			$subjectID       = $this->input->post('subjectID');
			$subject         = $this->subject_m->get_single_subject(array('subjectID' => $subjectID));

			$markpercentageArr['marktypeID'] = $this->data['siteinfos']->marktypeID;
			$markpercentageArr['classesID']  = $classesID;
			$markpercentageArr['examID']     = $examID;
			$markpercentageArr['subjectID']  = $subjectID;
			$markpercentageArr['subject']    = $subject;

			$getMarkPercentage = $this->marksetting_m->get_marksetting_markpercentages_add($markpercentageArr);
			foreach ($inputs as $value) {
				$markpercentageID = $value['markpercentageid'];
				$markValue        = $value['value'];

				if (isset($getMarkPercentage[$markpercentageID])) {
					if (is_numeric($markValue)) {
						if (0 > $markValue || $markValue > $getMarkPercentage[$markpercentageID]->percentage) {
							$this->form_validation->set_message('unique_inputs', 'Mark can not cross max mark');
							return FALSE;
						}
					} else {
						if (is_string($markValue) && $markValue != '') {
							$this->form_validation->set_message('unique_inputs', 'String data is deniable');
							return FALSE;
						}
					}
				}
			}
		}
		return TRUE;
	}

	// download mark excel sheet
	public function download_mark_sheet($arrData)
	{

		$headings = array('slno', 'name', 'sid');
		foreach ($arrData['markpercentages'] as $data) {
			//$headings[] = $data->markpercentagetype . ($data->percentage);
			$headings[] = $data->markpercentagetype;
		}

		$studentData = [];
		foreach ($arrData['students'] as $nkey => $student) {
			$studentData[] = array($nkey + 1, $student->name,  $student->srstudentID);
		}

		$data = array_merge(array($headings), $studentData);

		$singleMarkRecord = $arrData['marks'][0];

		$className = '';
		$clsaaNameNumeric = '';
		foreach ($arrData['classes'] as $class) {
			if ($class->classesID == $singleMarkRecord->classesID) {
				$clsaaNameNumeric = $class->classes_numeric;
				$className = $class->classes;
			}
		}

		//$fileName = $className . '-' . $singleMarkRecord->subject . '-' . $singleMarkRecord->exam . ' ' . '(' . $clsaaNameNumeric . '-' . $singleMarkRecord->subjectID . '-' . $singleMarkRecord->examID . ')';

		$fileName = $className . '-' . $singleMarkRecord->subject . '-' . $singleMarkRecord->exam;

		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=\"$fileName" . ".csv\"");
		header("Pragma: no-cache");
		header("Expires: 0");

		$handle = fopen('php://output', 'w');

		foreach ($data as $data_array) {
			fputcsv($handle, $data_array);
		}
		fclose($handle);

		return;
	}


	public function mark_bulkimport()
	{
		if (isset($_FILES["csvMark"])) {
			$config['upload_path']   = "./uploads/csv/";
			$config['allowed_types'] = 'text/plain|text/csv|csv';
			$config['max_size']      = '2048';
			$config['file_name']     = $_FILES["csvMark"]['name'];
			$config['overwrite']     = TRUE;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload("csvMark")) {
				$this->session->set_flashdata('error', $this->lang->line('bulkimport_upload_fail'));
				redirect(base_url("mark/add"));
			} else {
				$file_data      = $this->upload->data();
				$file_path      = './uploads/csv/' . $file_data['file_name'];


				$examID          = $this->input->post('examId');
				$classesID       = $this->input->post('classId');
				$sectionID       = $this->input->post('sectionId');
				$subjectID       = $this->input->post('subjectId');
				$this->data['set_exam']    = $examID;
				$this->data['set_classes'] = $classesID;
				$this->data['set_section'] = $sectionID;
				$this->data['set_subject'] = $subjectID;

				$exam            = $this->exam_m->get_single_exam(array('examID' => $examID));
				$subject         = $this->subject_m->get_single_subject(array('subjectID' => $subjectID));
				$classes         = $this->classes_m->get_single_classes(array('classesID' => $classesID));
				$section         = $this->section_m->get_single_section(array('sectionID' => $sectionID));
				$markpercentages = $this->markpercentage_m->get_markpercentage();

				$markpercentageArr['marktypeID'] = $this->data['siteinfos']->marktypeID;
				$markpercentageArr['classesID']  = $classesID;
				$markpercentageArr['examID']     = $examID;
				$markpercentageArr['subjectID']  = $subjectID;
				$markpercentageArr['subject']    = $subject;

				$this->data['sendExam']     = $exam;
				$this->data['sendSubject']  = $subject;
				$this->data['sendClasses']  = $classes;
				$this->data['sendSection']  = $section;

				$this->data['markpercentages']  = $objMarkpercentages = $this->marksetting_m->get_marksetting_markpercentages_add($markpercentageArr);


				$column_headers = array('slno', 'name', 'sid');
				foreach ($objMarkpercentages as $data) {
					$column_headers[] = $data->markpercentagetype;
				}

				$schoolyearID       = $this->session->userdata('defaultschoolyearID');

				$MarksArray = [];
				$uploadMarkData =  [];
				if ($csv_array = @$this->csvimport->get_array($file_path, $column_headers)) {
					if (customCompute($csv_array)) {
						foreach ($csv_array as $row) {
							$MarksArray[$row['sid']] =  $row;
						}
					}

					$allMarkWithRelation = $this->markrelation_m->get_all_mark_with_relation(array('schoolyearID' => $schoolyearID, 'examID' => $examID, 'classesID' => $classesID, 'subjectID' => $subjectID));

					foreach ($allMarkWithRelation as $markWithRelation) {
						$uploadMarkData[] = array(
							//'sid' => $markWithRelation->studentID,
							//'markID' => $markWithRelation->markID,
							'markrelationID' => $markWithRelation->markrelationID,
							//'markpercentageID' => $markWithRelation->markpercentageID,
							//'markpercentagetype' => $markWithRelation->markpercentagetype,
							'mark' => isset($MarksArray[$markWithRelation->studentID][$markWithRelation->markpercentagetype]) ? $MarksArray[$markWithRelation->studentID][$markWithRelation->markpercentagetype] : null
						);
					}
					//print_r($uploadMarkData); die;
					if (customCompute($uploadMarkData)) {
						$this->markrelation_m->update_batch_markrelation($uploadMarkData, 'markrelationID');
					}

					$this->session->set_flashdata('success', "Uploaded Success!");
					redirect(base_url("mark/add"));
				} else {
					$this->session->set_flashdata('error', "Wrong csv file!");
					redirect(base_url("mark/add"));
				}
			}
		} else {
			$this->session->set_flashdata('error', $this->lang->line('bulkimport_select_file'));
			redirect(base_url("mark/add"));
		}
	}

	public function sendmarks_sms(){
		print_r($_POST);die;
	}
}
