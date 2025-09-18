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

	public function add_bkp($a=array())
	{
		if(!empty($a)){
			$_POST = $a;
			//print_r($_POST);

		}
	    
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

						$mark = $this->mark_m->get_order_by_mark_new(array('schoolyearID' => $schoolyearID, "examID" => $examID, "classesID" => $classesID));
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

 public function add($a=array())
{
    if(!empty($a)){
        $_POST = $a;
        //print_r($_POST);
    }

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

                    $mark = $this->mark_m->get_order_by_mark_new(array('schoolyearID' => $schoolyearID, "examID" => $examID, "classesID" => $classesID));
                    $this->data['marks'] = $mark;
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

                // NOTE: For markRelations we keep original behaviour (subject filtered),
                // but for rank we will fetch all marks for this class/exam/sy.
                $this->data['markRelations']    = $this->getMarkRelationArray($this->mark_m->student_all_mark_array(array('schoolyearID' => $schoolyearID, 'examID' => $examID, 'classesID' => $classesID, 'subjectID' => $subjectID)));

                // ----------------- RANK CALCULATION START -----------------
                // Get all mark rows (all subjects) so we can compute per-subject totals, absent, compare with min_mark
                $allMarks = $this->mark_m->student_all_mark_array(array(
                    'schoolyearID' => $schoolyearID,
                    'examID'       => $examID,
                    'classesID'    => $classesID
                    // intentionally not filtering by subjectID so we cover all subjects for rank
                ));

                $studentSubjectSum = [];    // sum per student per subject
                $studentSubjectAbsent = []; // absent flag per student per subject
                $subjectMin = [];          // subject-wise min_mark (from examschedule.min_mark)

                if (customCompute($allMarks)) {
                    foreach ($allMarks as $r) {
                        $sid = $r->studentID;
                        $subid = $r->subjectID;

                        $value = 0;
                        if (isset($r->mark) && $r->mark !== null && $r->mark !== '') {
                            // mark might be stored as text, cast to int
                            $value = (int)$r->mark;
                        }

                        if (!isset($studentSubjectSum[$sid])) {
                            $studentSubjectSum[$sid] = [];
                        }
                        if (!isset($studentSubjectSum[$sid][$subid])) {
                            $studentSubjectSum[$sid][$subid] = 0;
                        }
                        $studentSubjectSum[$sid][$subid] += $value;

                        // track absent (some code stores 'Absent' in eattendance)
                        if (isset($r->eattendance) && $r->eattendance !== null) {
                            $ea = strtolower(trim($r->eattendance));
                            if ($ea === 'absent' || $ea === 'a') {
                                if (!isset($studentSubjectAbsent[$sid])) $studentSubjectAbsent[$sid] = [];
                                $studentSubjectAbsent[$sid][$subid] = true;
                            }
                        }

                        // store subject min mark (examschedule.min_mark)
                        if (!isset($subjectMin[$subid])) {
                            $subjectMin[$subid] = isset($r->min_mark) ? (int)$r->min_mark : 0;
                        }
                    }
                }

                // compute totals and fail flag for each student
                $studentResults = []; // keyed by studentID
                $subjectsForCalc = isset($this->data['subjects']) ? $this->data['subjects'] : [];

                foreach ($sendStudent as $stu) {
                    $sid = $stu->studentID;
                    $total = 0;
                    $isFail = false;

                    // iterate through subjects visible in this page (subjectsForCalc)
                    if (customCompute($subjectsForCalc)) {
                        foreach ($subjectsForCalc as $s) {
                            $subid = $s->subjectID;
                            $subSum = isset($studentSubjectSum[$sid][$subid]) ? $studentSubjectSum[$sid][$subid] : 0;

                            // if any absent record for this student-subject => fail
                            if (isset($studentSubjectAbsent[$sid]) && isset($studentSubjectAbsent[$sid][$subid]) && $studentSubjectAbsent[$sid][$subid]) {
                                $isFail = true;
                            }

                            // get min mark for subject (from examschedule if present)
                            $min = isset($subjectMin[$subid]) ? $subjectMin[$subid] : 0;

                            // if obtained less than min mark => fail
                            if ($subSum < $min) {
                                $isFail = true;
                            }

                            $total += $subSum;
                        }
                    }

                    $studentResults[$sid] = [
                        'total' => $total,
                        'isFail' => $isFail
                    ];
                }

                // assign ranks only to passed students
                $passed = [];
                foreach ($studentResults as $sid => $res) {
                    if (!$res['isFail']) {
                        $passed[$sid] = $res['total'];
                    }
                }

                if (customCompute($passed)) {
                    // sort passed students by total descending, preserve keys (studentID)
                    arsort($passed);

                    $currentIndex = 0;
                    $prevTotal = null;
                    $lastRank = 0;
                    $studentRanks = [];

                    foreach ($passed as $sid => $totalVal) {
                        $currentIndex++;
                        if ($prevTotal !== null && $totalVal == $prevTotal) {
                            // same total => same rank as previous
                            $studentRanks[$sid] = $lastRank;
                        } else {
                            // new total => rank is currentIndex among passed students
                            $studentRanks[$sid] = $currentIndex;
                            $lastRank = $currentIndex;
                        }
                        $prevTotal = $totalVal;
                    }

                    // attach ranks to studentResults, fails get '-'
                    foreach ($studentResults as $sid => $res) {
                        $studentResults[$sid]['rank'] = isset($studentRanks[$sid]) ? $studentRanks[$sid] : '-';
                    }
                } else {
                    // no passed students, all '-' or fail
                    foreach ($studentResults as $sid => $res) {
                        $studentResults[$sid]['rank'] = '-';
                    }
                }

                // pass to view
                $this->data['studentResults'] = $studentResults;
                // ----------------- RANK CALCULATION END -----------------

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

// Main add page
 public function add_paginations($a=array())
{
    if(!empty($a)){
        $_POST = $a;
        //print_r($_POST);
    }

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

                    $mark = $this->mark_m->get_order_by_mark_new(array('schoolyearID' => $schoolyearID, "examID" => $examID, "classesID" => $classesID));
                    $this->data['marks'] = $mark;
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

                // NOTE: For markRelations we keep original behaviour (subject filtered),
                // but for rank we will fetch all marks for this class/exam/sy.
                $this->data['markRelations']    = $this->getMarkRelationArray($this->mark_m->student_all_mark_array(array('schoolyearID' => $schoolyearID, 'examID' => $examID, 'classesID' => $classesID, 'subjectID' => $subjectID)));

                // ----------------- RANK CALCULATION START -----------------
                // Get all mark rows (all subjects) so we can compute per-subject totals, absent, compare with min_mark
                $allMarks = $this->mark_m->student_all_mark_array(array(
                    'schoolyearID' => $schoolyearID,
                    'examID'       => $examID,
                    'classesID'    => $classesID
                    // intentionally not filtering by subjectID so we cover all subjects for rank
                ));

                $studentSubjectSum = [];    // sum per student per subject
                $studentSubjectAbsent = []; // absent flag per student per subject
                $subjectMin = [];          // subject-wise min_mark (from examschedule.min_mark)

                if (customCompute($allMarks)) {
                    foreach ($allMarks as $r) {
                        $sid = $r->studentID;
                        $subid = $r->subjectID;

                        $value = 0;
                        if (isset($r->mark) && $r->mark !== null && $r->mark !== '') {
                            // mark might be stored as text, cast to int
                            $value = (int)$r->mark;
                        }

                        if (!isset($studentSubjectSum[$sid])) {
                            $studentSubjectSum[$sid] = [];
                        }
                        if (!isset($studentSubjectSum[$sid][$subid])) {
                            $studentSubjectSum[$sid][$subid] = 0;
                        }
                        $studentSubjectSum[$sid][$subid] += $value;

                        // track absent (some code stores 'Absent' in eattendance)
                        if (isset($r->eattendance) && $r->eattendance !== null) {
                            $ea = strtolower(trim($r->eattendance));
                            if ($ea === 'absent' || $ea === 'a') {
                                if (!isset($studentSubjectAbsent[$sid])) $studentSubjectAbsent[$sid] = [];
                                $studentSubjectAbsent[$sid][$subid] = true;
                            }
                        }

                        // store subject min mark (examschedule.min_mark)
                        if (!isset($subjectMin[$subid])) {
                            $subjectMin[$subid] = isset($r->min_mark) ? (int)$r->min_mark : 0;
                        }
                    }
                }

                // compute totals and fail flag for each student
                $studentResults = []; // keyed by studentID
                $subjectsForCalc = isset($this->data['subjects']) ? $this->data['subjects'] : [];

                foreach ($sendStudent as $stu) {
                    $sid = $stu->studentID;
                    $total = 0;
                    $isFail = false;

                    // iterate through subjects visible in this page (subjectsForCalc)
                    if (customCompute($subjectsForCalc)) {
                        foreach ($subjectsForCalc as $s) {
                            $subid = $s->subjectID;
                            $subSum = isset($studentSubjectSum[$sid][$subid]) ? $studentSubjectSum[$sid][$subid] : 0;

                            // if any absent record for this student-subject => fail
                            if (isset($studentSubjectAbsent[$sid]) && isset($studentSubjectAbsent[$sid][$subid]) && $studentSubjectAbsent[$sid][$subid]) {
                                $isFail = true;
                            }

                            // get min mark for subject (from examschedule if present)
                            $min = isset($subjectMin[$subid]) ? $subjectMin[$subid] : 0;

                            // if obtained less than min mark => fail
                            if ($subSum < $min) {
                                $isFail = true;
                            }

                            $total += $subSum;
                        }
                    }

                    $studentResults[$sid] = [
                        'total' => $total,
                        'isFail' => $isFail
                    ];
                }

                // assign ranks only to passed students
                $passed = [];
                foreach ($studentResults as $sid => $res) {
                    if (!$res['isFail']) {
                        $passed[$sid] = $res['total'];
                    }
                }

                if (customCompute($passed)) {
                    // sort passed students by total descending, preserve keys (studentID)
                    arsort($passed);

                    $currentIndex = 0;
                    $prevTotal = null;
                    $lastRank = 0;
                    $studentRanks = [];

                    foreach ($passed as $sid => $totalVal) {
                        $currentIndex++;
                        if ($prevTotal !== null && $totalVal == $prevTotal) {
                            // same total => same rank as previous
                            $studentRanks[$sid] = $lastRank;
                        } else {
                            // new total => rank is currentIndex among passed students
                            $studentRanks[$sid] = $currentIndex;
                            $lastRank = $currentIndex;
                        }
                        $prevTotal = $totalVal;
                    }

                    // attach ranks to studentResults, fails get '-'
                    foreach ($studentResults as $sid => $res) {
                        $studentResults[$sid]['rank'] = isset($studentRanks[$sid]) ? $studentRanks[$sid] : '-';
                    }
                } else {
                    // no passed students, all '-' or fail
                    foreach ($studentResults as $sid => $res) {
                        $studentResults[$sid]['rank'] = '-';
                    }
                }

                // pass to view
                $this->data['studentResults'] = $studentResults;
                // ----------------- RANK CALCULATION END -----------------

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
public function get_students_page() {
    $classesID = $this->input->post('classesID');
    $sectionID = $this->input->post('sectionID');
    $examID    = $this->input->post('examID');
    $offset    = $this->input->post('offset') ?: 0;
    $limit     = 20;

    $schoolyearID = $this->session->userdata('defaultschoolyearID');

    if(!$classesID || !$sectionID || !$examID){
        echo "Missing params"; return;
    }

    $students = $this->studentrelation_m->get_students_batch([
        'srclassesID' => $classesID,
        'srsectionID' => $sectionID,
        'srschoolyearID' => $schoolyearID
    ], $limit, $offset);

    $subjects = $this->subject_m->get_order_by_subject(['classesID'=>$classesID], $examID, $sectionID);

    // fetch marks
    $marksAll = $this->mark_m->student_all_mark_array([
        'schoolyearID' => $schoolyearID,
        'examID'       => $examID,
        'classesID'    => $classesID
    ]);

    // convert marks to studentID->subjectID->mark
    $marksArr = [];
    foreach($marksAll as $m) {
        $marksArr[$m->studentID][$m->subjectID] = $m->mark;
    }

    // calculate total & fail
    $studentResults = [];
    foreach($students as $stu){
        $total = 0; $fail = false;
        foreach($subjects as $sub){
            $val = isset($marksArr[$stu->studentID][$sub->subjectID]) ? (int)$marksArr[$stu->studentID][$sub->subjectID] : 0;
            $total += $val;
            if($val < $sub->min_mark) $fail = true;
        }
        $studentResults[$stu->studentID] = ['total'=>$total, 'isFail'=>$fail];
    }

    // assign ranks (lowest rank number = top)
    $passed = [];
    foreach($studentResults as $sid=>$res){
        if(!$res['isFail']) $passed[$sid] = $res['total'];
    }

    if(customCompute($passed)){
        arsort($passed);
        $lastRank=0; $prevTotal=null; $studentRanks=[];
        $currentIndex=0;
        foreach($passed as $sid=>$totalVal){
            $currentIndex++;
            if($prevTotal!==null && $totalVal==$prevTotal){
                $studentRanks[$sid]=$lastRank;
            }else{
                $studentRanks[$sid]=$currentIndex;
                $lastRank=$currentIndex;
            }
            $prevTotal=$totalVal;
        }
    }

    foreach($studentResults as $sid=>$res){
        $studentResults[$sid]['rank'] = isset($studentRanks[$sid]) ? $studentRanks[$sid] : '-';
    }

    $data['students'] = $students;
    $data['subjects'] = $subjects;
    $data['marksArr'] = $marksArr;
    $data['studentResults'] = $studentResults;

    $this->load->view('mark/ajax_student_rows', $data);
}







	public function add_marks_excel($a=array())
	{
		if(!empty($a)){
			$_POST = $a;
			print_r($_POST);

		}
	    
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
					$this->data["subview"] = "mark/add_marks_excel";
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
						$this->data["subview"] = "mark/add_marks_excel";
						$this->load->view('_layout_main', $this->data);
					}
				}
			} else {
				$this->data["subview"] = "mark/add_marks_excel";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function add_modified($a=array())
	{
		if(!empty($a)){
			$_POST = $a;
			print_r($_POST);

		}
	    
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


					   // Form is submitted, now handle the bulk mark data
					   $marksInput = $this->input->post('add_mark');
        
					   // Check if marks data is available
					   if (!empty($marksInput)) {
						   // Call saveAllMarks method to process the data
						   $this->saveAllMarks();
					   } else {
						//    $this->session->set_flashdata('error', 'No marks data to save.');
						//    redirect(base_url('mark/add'));
					   }


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
						$marks = $this->data['marks'] = $mark;
						// echo "<pre>";print_r($this->data['marks']);die;
					}

					$marks_lookup = [];
					foreach ($marks as $mark) {
						$marks_lookup[$mark->studentID][$mark->subjectID][$mark->markpercentageID] = $mark->mark;
					}
					$this->data['marks_lookup'] = $marks_lookup;  


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
						$this->data["subview"] = "mark/add_modified";
						$this->load->view('_layout_main', $this->data);
					}
				}
			} else {
				$this->data["subview"] = "mark/add_modified";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}


	public function saveAllMarks()
	{
		$this->load->model('mark_m');
	
		$marksInput = $this->input->post('marks');
		$examID = $this->input->post('examID') ?? $this->data['set_exam'];
		$classesID = $this->input->post('classesID') ?? $this->data['set_classes'];
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
	
		if (!empty($marksInput)) {
			foreach ($marksInput as $studentID => $subjectArray) {
				foreach ($subjectArray as $subjectID => $percentageArray) {
					// Step 1: Ensure mark entry exists (1 per exam/student/subject/year)
					$mark = $this->mark_m->get_single_mark([
						'examID' => $examID,
						'classesID' => $classesID,
						'subjectID' => $subjectID,
						'studentID' => $studentID,
						'schoolyearID' => $schoolyearID
					]);
	
					if (!$mark) {
						// If no main mark record, insert one (basic data only)
						$markID = $this->mark_m->insert_mark([
							'examID' => $examID,
							'classesID' => $classesID,
							'subjectID' => $subjectID,
							'studentID' => $studentID,
							'schoolyearID' => $schoolyearID,
							'create_date' => date('Y-m-d H:i:s'),
							'create_userID' => $this->session->userdata('userID'),
							'create_usertypeID' => $this->session->userdata('usertypeID'),
							'year' => date('Y'),
							'exam' => '',     // optional
							'subject' => '',  // optional
							'eattendance' => NULL
						]);
					} else {
						$markID = $mark->markID;
					}
	
					// Step 2: Save marks per percentage
					foreach ($percentageArray as $markpercentageID => $markValue) {
						$existingRelation = $this->mark_m->get_single_markrelation([
							'examID' => $examID,
							'classesID' => $classesID,
							'subjectID' => $subjectID,
							'studentID' => $studentID,
							'schoolyearID' => $schoolyearID,
							'markpercentageID' => $markpercentageID
						]);
	
						$markValue = abs($markValue); // Ensure positive
	
						if ($existingRelation) {
							$this->mark_m->update_markrelation([
								'mark' => $markValue
							], $markID, $markpercentageID);
						} else {
							$this->mark_m->insert_markrelation([
								'markID' => $markID,
								'markpercentageID' => $markpercentageID,
								'mark' => $markValue
							]);
						}
						//echo $this->db->last_query();die;
					}
				}
			}
	
			$this->session->set_flashdata('success', 'Marks saved successfully.');
		} else {
			$this->session->set_flashdata('error', 'No marks data to save.');
		}
	
		redirect(base_url('mark/add'));
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
						// if (!empty($value['value']) || $value['value'] != "") {

							$this->db->where('markpercentageID',$value['markpercentageid']);
							$this->db->where('markID',$data[1]);
							$this->db->update('markrelation',array('mark' => abs($value['value']) ));
							// echo $this->db->last_query();die;
						// } else {
							
						// }
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
			
			// echo "<pre>";print_r($exams);die;
			
			echo "<option value='0'>  ", $this->lang->line("mark_select_exam"), "</option>";
			if (customCompute($exams)) {
				foreach ($exams as $exam) {
        echo "<option value='" . $exam->examID . "' data-examdate='" . $exam->date . "'>" . $exam->exam . "</option>";
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

	public function saveAttendance(){

		// print_r($_POST);die;
		$mark_id = $_POST['markID']; 
		$percentage_id = $_POST['percentage_id']; 
		if( $_POST['attendance'] == 'Absent'){
			 $sql = "select * from markrelation where markID=$mark_id and markpercentageID = $percentage_id and mark is not null and mark!=0";
			   $cnt = $this->db->query($sql)->num_rows();
			if(($cnt) > 0){
 				$this->session->set_flashdata('error', 'Already have marks, please remove the marks before adding attendance as absent!' );
				 redirect(base_url("mark/add"));
			}
		}
			// }else{
				// $this->add($_POST);

				$schoolyearID = $this->session->userdata('defaultschoolyearID');

				$examID = $_POST['examID'];
				$classesID = $_POST['classesID'];
				$sectionID = $_POST['sectionID'];
				$subjectID = $_POST['subjectID'];
				$studentID = $_POST['studentID'];
				$mark_id = $_POST['markID']; 


				$data = array('eattendance' => $_POST['attendance']);

				$this->db->where('schoolyearID', $schoolyearID);
				$this->db->where('examID', $examID);
				$this->db->where('classesID', $classesID);
				$this->db->where('sectionID', $sectionID);
				$this->db->where('subjectID', $subjectID);
				$this->db->where('studentID', $studentID);
				$this->db->update('eattendance' ,$data);

				$this->db->where('schoolyearID', $schoolyearID);
				// $this->db->where('examID', $examID);
				// $this->db->where('classesID', $classesID);
				// $this->db->where('sectionID', $sectionID);
				// $this->db->where('subjectID', $subjectID);
				// $this->db->where('studentID', $studentID);
				$this->db->where('markID', $mark_id);
				$this->db->update('mark' ,$data);

				$this->session->set_flashdata('success', 'Successfully added the attendance' );

				// redirect(base_url("mark/add"));
				$this->add($_POST);
			// }
		// }

		
		

	}


	

// 	public function marks_bulkimport()
// {
//     if ($_FILES['csvMarks']['error'] == 0) {
//         $filename = $_FILES['csvMarks']['name'];
//         $temp_path = "./uploads/csv/" . $filename;

//         if (!move_uploaded_file($_FILES['csvMarks']['tmp_name'], $temp_path)) {
//             echo "❌ File upload failed."; die;
//         }

//         $csv_array = $this->csvimport->get_array_flexible($temp_path, TRUE);

//         if (!customCompute($csv_array) || count($csv_array) < 3) {
//             echo "❌ CSV format is invalid."; die;
//         }

//         echo "<pre>";

//         // Extract metadata (first row)
//         $metaRow = $csv_array[0];
//         $metaString = implode("\t", $metaRow);
//         preg_match_all('/(\w+ID):\s*(\d+)/', $metaString, $matches);
//         $metaData = array_combine($matches[1], $matches[2]);
//         print_r($metaData);

//         // Extract headers (second row)
//         $headers = $csv_array[1];
//         echo "\nHEADERS:\n";
//         print_r($headers);

//         // Data rows start from 3rd row
//         $updateData = [];
//         for ($i = 2; $i < count($csv_array); $i++) {
//             $row = $csv_array[$i];
//             echo "\nRow #".($i-2)." studentID: ";
//             $studentID = isset($row[0]) ? trim($row[0]) : '';

//             echo $studentID . "\n";
//             if (!is_numeric($studentID)) {
//                 echo "⚠️ Skipping invalid studentID.\n";
//                 continue;
//             }

//             for ($j = 2; $j < count($row); $j++) {
//                 $subjectHeader = isset($headers[$j]) ? $headers[$j] : '';
//                 if (preg_match('/\^(\d+)/', $subjectHeader, $sm)) {
//                     $subjectID = $sm[1];
//                     $mark = trim($row[$j]);
//                     if ($mark !== '') {
//                         $updateData[] = [
//                             'studentID' => $studentID,
//                             'subjectID' => $subjectID,
//                             'mark'      => $mark,
//                             'classID'   => $metaData['classID'] ?? 0,
//                             'examID'    => $metaData['examID'] ?? 0,
//                             'sectionID' => $metaData['sectionID'] ?? 0,
//                         ];
//                     }
//                 }
//             }
//         }

//         echo "\n✅ FINAL UPDATE DATA:\n";
//         print_r($updateData);
//         die;

//     } else {
//         echo "❌ No file selected or upload error."; die;
//     }
// }


public function marks_bulkimport()
{
    if (isset($_FILES["csvMarks"])) {
        $config['upload_path']   = "./uploads/csv/";
        $config['allowed_types'] = 'csv|text/plain|text/csv';
        $config['max_size']      = '2048';
        $config['file_name']     = $_FILES["csvMarks"]['name'];
        $config['overwrite']     = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload("csvMarks")) {
            $this->session->set_flashdata('error', "File upload failed: " . $this->upload->display_errors());
            redirect(base_url("mark/add"));
        }

        $file_data = $this->upload->data();
        $file_path = './uploads/csv/' . $file_data['file_name'];


// 		$temp_path = './uploads/csv/temp_' . time() . '.csv';
// file_put_contents($temp_path, implode("\n", $data_lines));


        // Read the raw file contents to auto detect delimiter
        $raw = file_get_contents($file_path);
        $raw_lines = explode("\n", $raw);
        $meta_line = isset($raw_lines[0]) ? $raw_lines[0] : '';

        // Parse meta line (classID, examID, sectionID)
        preg_match('/classID:\s*(\d+)/i', $meta_line, $m1);
        preg_match('/examID:\s*(\d+)/i', $meta_line, $m2);
        preg_match('/sectionID:\s*(\d+)/i', $meta_line, $m3);
        $classID   = isset($m1[1]) ? (int) $m1[1] : 0;
        $examID    = isset($m2[1]) ? (int) $m2[1] : 0;
        $sectionID = isset($m3[1]) ? (int) $m3[1] : 0;

        // Detect delimiter: tab (\t) or comma
        $secondLine = $raw_lines[1] ?? '';
        $delimiter = (substr_count($secondLine, "\t") > substr_count($secondLine, ",")) ? "\t" : ",";

        // Parse CSV properly using detected delimiter
        $csv_array = [];
        $headers = [];
        $handle = fopen($file_path, 'r');
        if ($handle !== false) {
            // Skip meta line
            fgets($handle);

            // Get headers
            $headers = fgetcsv($handle, 0, $delimiter);

            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($data) != count($headers)) continue;
                $csv_array[] = array_combine($headers, $data);
            }
            fclose($handle);
        }

        // Debug outputs
        // echo "<pre>";
        // echo "META INFO:\n";
        // print_r(['classID' => $classID, 'examID' => $examID, 'sectionID' => $sectionID]);

        // echo "\nHEADERS:\n";
        // print_r($headers);

        // echo "\nCSV ROWS:\n";
        // print_r($csv_array);

        // Identify subject columns
        $subjectColumns = [];
        foreach ($headers as $header) {
            if (preg_match('/\^(\d+)$/', $header, $matches)) {
                $subjectID = $matches[1];
                $subjectColumns[$header] = $subjectID;
            }
        }

        // echo "\nIdentified Subject Columns:\n";
        // print_r($subjectColumns);

        // Fetch existing marks
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $marks = $this->mark_m->get_order_by_mark_new([
            'schoolyearID' => $schoolyearID,
            'examID'       => $examID,
            'classesID'    => $classID,
        ]);

        $existingMarks = [];
        foreach ($marks as $m) {
            $key = "{$m->studentID}_{$m->subjectID}_{$m->examID}";
            $existingMarks[$key] = $m->markID;
        }

        // Build update data
        $updateData = [];
        foreach ($csv_array as $index => $row) {
            $studentID = isset($row['studentID']) ? trim($row['studentID']) : null;
            echo "\nRow #$index studentID: $studentID";

            if (!$studentID || !is_numeric($studentID)) {
                echo " ⚠️ Skipping invalid studentID.";
                continue;
            }

            foreach ($subjectColumns as $columnName => $subjectID) {
                if (!isset($row[$columnName])) continue;

                $markValue = trim($row[$columnName]);
                if ($markValue === '') continue;

                $key = "{$studentID}_{$subjectID}_{$examID}";
                if (isset($existingMarks[$key])) {
                    $updateData[] = [
                        'markID' => $existingMarks[$key],
                        'mark'   => $markValue,
                    ];
                }
            }
        }

        // echo "\n✅ FINAL UPDATE DATA:\n";
        // print_r($updateData);
        // exit;

        // Final update
        if (!empty($updateData)) {
            $this->db->update_batch('markrelation', $updateData, 'markID');
            $this->session->set_flashdata('success', "Marks updated successfully.");
			// Clean up both files
			@unlink($temp_path);    // delete temp CSV used for parsing
			@unlink($file_path);    // delete uploaded original CSV
        } else {
            $this->session->set_flashdata('error', "No matching marks found to update.");
        }

        redirect(base_url("mark/add"));
    } else {
        $this->session->set_flashdata('error', "No file selected.");
        redirect(base_url("mark/add"));
    }
}


// public function marks_bulkimport()
// {
//     if (isset($_FILES["csvMarks"])) {
//         $config['upload_path']   = "./uploads/csv/";
//         $config['allowed_types'] = 'csv|text/plain|text/csv';
//         $config['max_size']      = '2048';
//         $config['file_name']     = $_FILES["csvMarks"]['name'];
//         $config['overwrite']     = TRUE;

//         $this->load->library('upload', $config);

//         if (!$this->upload->do_upload("csvMarks")) {
//             $this->session->set_flashdata('error', "File upload failed: " . $this->upload->display_errors());
//             redirect(base_url("mark/add"));
//         }

//         $file_data = $this->upload->data();
//         $file_path = './uploads/csv/' . $file_data['file_name'];

//         $raw_lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//         if (count($raw_lines) < 3) {
//             $this->session->set_flashdata('error', "CSV data is too short or invalid.");
//             redirect(base_url("mark/add"));
//         }

//         // Extract metadata
//         preg_match('/classID:\s*(\d+)/i', $raw_lines[0], $m1);
//         preg_match('/examID:\s*(\d+)/i', $raw_lines[0], $m2);
//         preg_match('/sectionID:\s*(\d+)/i', $raw_lines[0], $m3);
//         $classID = isset($m1[1]) ? (int) $m1[1] : 0;
//         $examID = isset($m2[1]) ? (int) $m2[1] : 0;
//         $sectionID = isset($m3[1]) ? (int) $m3[1] : 0;

//         // Extract only the data portion
//         $data_lines = array_slice($raw_lines, 2);
//         $temp_path = './uploads/csv/temp_' . time() . '.csv';
//         file_put_contents($temp_path, implode("\n", $data_lines));

//         // Load csvimport and parse using new method
//         $this->load->library('csvimport');
//         $csv_array = $this->csvimport->get_array_flexible($temp_path);

//         // Cleanup temp file
//         @unlink($temp_path);

//         if (!customCompute($csv_array)) {
//             $this->session->set_flashdata('error', "CSV data not found or invalid format.");
//             redirect(base_url("mark/add"));
//         }

//         // Extract subject columns
//         $firstRow = reset($csv_array);
//         $headers = array_keys($firstRow);
//         $subjectColumns = [];
//         foreach ($headers as $header) {
//             if (preg_match('/\^(\d+)$/', $header, $matches)) {
//                 $subjectID = $matches[1];
//                 $subjectColumns[$header] = $subjectID;
//             }
//         }

//         // Fetch existing marks
//         $schoolyearID = $this->session->userdata('defaultschoolyearID');
//         $marks = $this->mark_m->get_order_by_mark_new([
//             'schoolyearID' => $schoolyearID,
//             'examID'       => $examID,
//             'classesID'    => $classID
//         ]);

//         $existingMarks = [];
//         foreach ($marks as $m) {
//             $existingMarks["{$m->studentID}_{$m->subjectID}_{$m->examID}"] = $m->markID;
//         }

//         $updateData = [];

//         foreach ($csv_array as $row) {
//             $studentID = isset($row['studentID']) ? trim($row['studentID']) : null;
//             if (!$studentID || !is_numeric($studentID)) continue;

//             foreach ($subjectColumns as $columnName => $subjectID) {
//                 if (!isset($row[$columnName])) continue;

//                 $markValue = trim($row[$columnName]);
//                 if ($markValue === '') continue;

//                 $key = "{$studentID}_{$subjectID}_{$examID}";
//                 if (isset($existingMarks[$key])) {
//                     $markID = $existingMarks[$key];
//                     $updateData[] = [
//                         'markID' => $markID,
//                         'mark'   => $markValue,
//                     ];
//                 }
//             }
//         }

//         // Debug print and stop here
//         echo "<pre>";
//         print_r($updateData);
//         die;

//         // Actual update (if needed)
//         if (!empty($updateData)) {
//             $this->db->update_batch('markrelation', $updateData, 'markID');
//             $this->session->set_flashdata('success', "Marks updated successfully.");
//         } else {
//             $this->session->set_flashdata('error', "No matching marks found to update.");
//         }

//         redirect(base_url("mark/add"));
//     } else {
//         $this->session->set_flashdata('error', "No file selected.");
//         redirect(base_url("mark/add"));
//     }
// }




// public function marks_bulkimport()
// {
//     if (isset($_FILES["csvMarks"])) {
//         $config['upload_path']   = "./uploads/csv/";
//         $config['allowed_types'] = 'csv|text/plain|text/csv';
//         $config['max_size']      = '2048';
//         $config['file_name']     = $_FILES["csvMarks"]['name'];
//         $config['overwrite']     = TRUE;

//        $this->load->library('upload', $config);

// 	if (!$this->upload->do_upload("csvMarks")) {
// 		$errorMsg = $this->upload->display_errors(); // Get the detailed error
// 		$this->session->set_flashdata('error', "File upload failed: " . $errorMsg);
// 		redirect(base_url("mark/add"));
// 	}

//         $file_data = $this->upload->data();
//         $file_path = './uploads/csv/' . $file_data['file_name'];

//         // Load CSV content
//         $csv_array = @$this->csvimport->get_array($file_path);
//         if (!customCompute($csv_array)) {
//             $this->session->set_flashdata('error', "CSV data not found or invalid format.");
//             redirect(base_url("mark/add"));
//         }

//         // Extract classID, examID, sectionID from first few rows
//         $firstRow = reset($csv_array);
//         $classID = (int) str_replace('classID:', '', array_key_first(array_filter($firstRow, fn($v) => str_contains($v, 'classID:'))));
//         $examID  = (int) str_replace('examID:', '', array_key_first(array_filter($firstRow, fn($v) => str_contains($v, 'examID:'))));
//         $sectionID = (int) str_replace('sectionID:', '', array_key_first(array_filter($firstRow, fn($v) => str_contains($v, 'sectionID:'))));

//         // Sanitize header row (remove blank or null keys)
//         $headers = array_keys($firstRow);
//         $subjectColumns = []; // column => subjectID
//         foreach ($headers as $header) {
//             if (preg_match('/\^(\d+)$/', $header, $matches)) {
//                 $subjectID = $matches[1];
//                 $subjectColumns[$header] = $subjectID;
//             }
//         }
 

// 		$schoolyearID = $this->session->userdata('defaultschoolyearID');
// 		$mark = $this->mark_m->get_order_by_mark_new(array('schoolyearID' => $schoolyearID, "examID" => $examID, "classesID" => $classID));

//         $existingMarks = [];
//         foreach ($marks as $m) {
//             $existingMarks["{$m->studentID}_{$m->subjectID}_{$m->examID}"] = $m->markID;
//         }

//         $updateData = [];

//         foreach ($csv_array as $row) {
//             $studentID = isset($row['studentID']) ? trim($row['studentID']) : null;
//             if (!$studentID || !is_numeric($studentID)) continue;

//             foreach ($subjectColumns as $columnName => $subjectID) {
//                 if (!isset($row[$columnName])) continue;

//                 $markValue = trim($row[$columnName]);
//                 if ($markValue === '') continue;

//                 $key = "{$studentID}_{$subjectID}_{$examID}";
//                 if (isset($existingMarks[$key])) {
//                     $markID = $existingMarks[$key];
//                     $updateData[] = [
//                         'markID' => $markID,
//                         'mark'   => $markValue,
//                     ];
//                 }
//                 // Optional: add insert logic here if markID doesn't exist
//             }
//         }

//         // Perform batch update
// 		echo "<pre>";print_r($updateData);die;
//         if (!empty($updateData)) {
//             $this->db->update_batch('markrelation', $updateData, 'markID');
//             $this->session->set_flashdata('success', "Marks updated successfully.");
//         } else {
//             $this->session->set_flashdata('error', "No matching marks found to update.");
//         }

//         redirect(base_url("mark/add"));
//     } else {
//         $this->session->set_flashdata('error', "No file selected.");
//         redirect(base_url("mark/add"));
//     }
// }

}
