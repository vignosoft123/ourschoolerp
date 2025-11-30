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
// $_POST['subjectID'] = 1;
//     echo "<pre>";    print_r($_POST);
// die;
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

                        // $studentArray['sroptionalsubjectID'] = $subject->subjectID;
                    }
                }
                $sendStudent = $this->studentrelation_m->get_order_by_student($studentArray);
				//echo "<pre>";print_r($sendStudent);die;

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
                // Calculate totals exactly like frontend: sum individual marks, not subject totals
                
                $studentResults = [];

                foreach ($sendStudent as $stu) {
                    $sid = $stu->studentID;
                    $total = 0.0;
                    $isFail = false;
                    $zero_mark = 0;

                    // DEBUG: Start calculation for student
                    error_log("DEBUG: Starting frontend-style calculation for Student $sid");

                    // Loop through subjects and markpercentages exactly like frontend
                    if (customCompute($this->data['subjects'])) {
                        foreach ($this->data['subjects'] as $subject) {
                            if (customCompute($this->data['markpercentages'])) {
                                foreach ($this->data['markpercentages'] as $data) {
                                    if (customCompute($this->data['marks'])) {
                                        foreach ($this->data['marks'] as $mark) {
                                            if($subject->subjectID == $mark->subjectID && $sid == $mark->studentID) {
                                                
                                                // Get individual mark - same query as frontend
                                                $sql = "SELECT mark,eattendance FROM `mark` LEFT JOIN `markrelation` ON `markrelation`.`markID` = `mark`.`markID` WHERE `mark`.`schoolyearID` = ".$mark->schoolyearID." AND `mark`.`examID` = ".$mark->examID." AND `mark`.`classesID` = ".$mark->classesID." and studentId= ".$sid." and markpercentageID =".$data->markpercentageID." and subjectID=".$subject->subjectID;
                                                
                                                $all_marks = $this->db->query($sql)->row();
                                                
                                                if ($all_marks) {
                                                    $mrk = (int)$all_marks->mark;
                                                    $exam_absent = $all_marks->eattendance;
                                                    
                                                    // Track absent students
                                                    if($exam_absent == 'Absent') {
                                                        $isFail = true;
                                                    }
                                                    
                                                    // Count zero marks
                                                    if ($mrk == 0) {
                                                        $zero_mark++;
                                                    }
                                                    
                                                    // Sum individual marks exactly like frontend
                                                    $total += $mrk;
                                                    
                                                    // DEBUG: Log individual mark addition
                                                    error_log("DEBUG: Student $sid, Subject {$subject->subjectID}, MarkPercentage {$data->markpercentageID}, Mark: $mrk, Running Total: $total");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $studentResults[$sid] = ['total' => $total, 'isFail' => $isFail, 'zero_mark' => $zero_mark];
                    
                    // DEBUG: Log each student's final total
                    error_log("DEBUG: Student $sid - Frontend-style Total: $total, Failed: " . ($isFail ? 'Yes' : 'No') . ", Zero marks: $zero_mark");
                }

// rank only passed students (dense ranking)
$passed = [];
foreach ($studentResults as $sid => $res) {
    if (!$res['isFail']) {
        $passed[$sid] = $res['total'];
        // DEBUG: Log passed students
        error_log("DEBUG: Student $sid passed with total: " . $res['total']);
    }
}

// DEBUG: Log passed students before sorting
error_log("DEBUG: Passed students before sorting: " . json_encode($passed));

if (customCompute($passed)) {
    // Sort in descending order (highest marks first) - FIXED RANKING ISSUE
    arsort($passed, SORT_NUMERIC); 
    
    // DEBUG: Log passed students after sorting
    error_log("DEBUG: Passed students after sorting: " . json_encode($passed));

    $studentRanks = [];
    $currentRank = 1; // Current rank being assigned
    $prevTotal = null;

    foreach ($passed as $sid => $totalVal) {
        // If this is a new total (different from previous), move to next rank
        if ($prevTotal !== null && $totalVal != $prevTotal) {
            $currentRank++; // Dense ranking - increment by 1 only
        }
        
        $studentRanks[$sid] = $currentRank;
        $prevTotal = $totalVal;
        
        // DEBUG: Log rank assignment
        error_log("DEBUG: Student $sid assigned rank $currentRank with total $totalVal (dense ranking)");
    }

    foreach ($studentResults as $sid => $res) {
        $studentResults[$sid]['rank'] = isset($studentRanks[$sid]) ? $studentRanks[$sid] : '-';
    }
} else {
    foreach ($studentResults as $sid => $res) {
        $studentResults[$sid]['rank'] = '-';
    }
}

// DEBUG: Log final student results
error_log("DEBUG: Final student results: " . json_encode($studentResults));
error_log("=== RANK CALCULATION DEBUG END ===");

$this->data['studentResults'] = $studentResults;
                // ----------------- RANK CALCULATION END -----------------

				// echo "<pre>";print_r($this->data);die;
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
public function load_students_ajax() {
    $offset      = $this->input->post('offset') ?? 0;
    $examID      = $this->input->post('examID') ?? 0;
    $classesID   = $this->input->post('classesID') ?? 0;
    $sectionID   = $this->input->post('sectionID') ?? 0;
    $subjectID   = $this->input->post('subjectID') ?? 0;
    $schoolyearID= $this->input->post('schoolyearID') ?? 0;

    $limit = 20; // Students per batch

    // Fetch students for this class-section-schoolyear
    $this->db->where('classesID', $classesID);
    $this->db->where('sectionID', $sectionID);
    $this->db->where('schoolyearID', $schoolyearID);
    $students = $this->db->get('student', $limit, $offset)->result();

    if (!$students) {
        echo json_encode(['html' => '<tr><td colspan="50" class="text-center">No students found</td></tr>', 'count' => 0]);
        return;
    }

    // Fetch subjects for this class
    $subjects = $this->db->where('classesID', $classesID)->get('subject')->result();

    // If subjectID filter is applied
    if ($subjectID != 0) {
        $subjects = array_filter($subjects, fn($s) => $s->subjectID == $subjectID);
    }

    // Fetch marks for this exam, class, section, schoolyear
    $marks = $this->db->where('examID', $examID)
                      ->where('classesID', $classesID)
                      ->where('sectionID', $sectionID)
                      ->where('schoolyearID', $schoolyearID)
                      ->get('mark')->result();

    $html = '';
    $i = $offset + 1;

    foreach ($students as $student) {
        $tot = 0;
        $zero_mark = 0;
        $my_template = '';
        $html .= '<tr>';
        $html .= '<td class="no-export">' . $i . '</td>';
        $html .= '<td>' . profileproimage($student->photo) . '</td>';
        $html .= '<td class="excel-only1">' . $student->studentID . '</td>';
        $html .= '<td>' . $student->name . ' (' . $student->roll . ')</td>';

        foreach ($subjects as $subject) {
            $student_mark = 0;

            foreach ($marks as $mark) {
                if ($mark->studentID == $student->studentID && $mark->subjectID == $subject->subjectID) {
                    $student_mark = (int)$mark->mark;
                    if ($student_mark == 0) $zero_mark++;
                    $tot += $student_mark;
                    break;
                }
            }

            // Table columns: visible + Excel hidden
            $html .= '<td>' . ($student_mark ? $student_mark : 'Ab') . '</td>';
            $html .= '<td class="excel-only">' . $student_mark . '</td>';

            $my_template .= $subject->subject . '=' . ($student_mark ? $student_mark : 'Ab') . ',';
        }

        // Total and grade
        $out_of = array_sum(array_map(fn($s) => $s->max_mark, $subjects));
        $percent_cal = ($tot / ($out_of ?: 1)) * 100;

        if ($percent_cal >= 95 && $zero_mark == 0) $grade = 'A+';
        else if ($percent_cal >= 90 && $percent_cal < 95 && $zero_mark == 0) $grade = 'A';
        else if ($percent_cal >= 80 && $percent_cal < 90 && $zero_mark == 0) $grade = 'B+';
        else if ($percent_cal >= 70 && $percent_cal < 80 && $zero_mark == 0) $grade = 'B';
        else if ($percent_cal >= 60 && $percent_cal < 70 && $zero_mark == 0) $grade = 'C+';
        else if ($percent_cal >= 50 && $percent_cal < 60 && $zero_mark == 0) $grade = 'C';
        else $grade = 'D';

        $html .= '<td>' . $tot . '</td>';
        $html .= '<td><span class="grade-label grade-' . strtolower($grade) . '">' . $grade . '</span></td>';
        $html .= '<td>-</td>'; // Rank placeholder, calculate after full list if needed
        $html .= '<td><input type="checkbox" st_ids="' . $student->studentID . '" st_names="' . $student->name . '" name="send_sms_marks" class="checkbox"></td>';

        $html .= '</tr>';
        $i++;
    }

    echo json_encode([
        'html' => $html,
        'count' => count($students)
    ]);
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

	public function mark_send_bkp()
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


							// $this->db->where('markpercentageID',$value['markpercentageid']);
							// $this->db->where('markID',$data[1]);
							// $this->db->update('markrelation',array('mark' => abs($value['value']) ));



							$this->db->where('markpercentageID', $value['markpercentageid']);
							$this->db->where('markID', $data[1]);
							$query = $this->db->get('markrelation');

							if($query->num_rows() > 0) {
								// record exists → update
								$this->db->where('markpercentageID', $value['markpercentageid']);
								$this->db->where('markID', $data[1]);
								$this->db->update('markrelation', array('mark' => abs($value['value'])));
							} else {
								// record does not exist → insert
								$this->db->insert('markrelation', array(
									'markpercentageID' => $value['markpercentageid'],
									'markID'           => $data[1],
									'mark'             => abs($value['value']),
								));
							}


						 
					}
				}

 

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

public function loadStudentsAjax() {
    $classesID = $this->input->post('classesID');
    $examID = $this->input->post('examID');
    $sectionID = $this->input->post('sectionID');
    $offset = (int)$this->input->post('offset', TRUE) ?: 0;
    $loadAll = $this->input->post('loadAll', TRUE) ?: false;
    $limit = $loadAll ? null : 20; // No limit if loading all

    $schoolyearID = $this->session->userdata('defaultschoolyearID');

    if (!$classesID || !$examID || !$sectionID) {
        echo json_encode(['status' => false, 'message' => 'Missing required parameters']);
        return;
    }

    // Get exam details for all batches (needed for proper checkbox attributes)
    $exam = $this->exam_m->get_single_exam(['examID' => $examID]);
    $examDetails = [
        'examName' => $exam ? $exam->exam : '',
        'examDate' => $exam ? $exam->date : '',
    ];
    
    // Get static data for first load only
    $staticData = null;
    if ($offset == 0) {
        $classes = $this->classes_m->get_single_classes(['classesID' => $classesID]);
        $section = $this->section_m->get_single_section(['sectionID' => $sectionID]);
        
        $staticData = [
            'examName' => $exam ? $exam->exam : '',
            'className' => $classes ? $classes->classes : '',
            'sectionName' => $section ? $section->section : ''
        ];
    }

    // Get students using same logic as original add method
    $studentArray = [
        'srclassesID'   => $classesID,
        'srsectionID'   => $sectionID,
        'srschoolyearID' => $schoolyearID,
    ];
    
    // Get all students first (same as original add method - this applies proper filtering)
    $allStudents = $this->studentrelation_m->get_order_by_student($studentArray);
    
    // Apply pagination to the filtered student list
    $totalStudents = count($allStudents);
    if ($loadAll) {
        // Load all remaining students from offset
        $students = array_slice($allStudents, $offset);
    } else {
        // Load only the specified limit
        $students = array_slice($allStudents, $offset, $limit);
    }

    if (!$students) {
        echo json_encode(['status' => false, 'message' => 'No students found']);
        return;
    }

    // Get subjects for this class, exam, and section (same as original add method)
    $subjects = $this->subject_m->get_order_by_subject(['classesID' => $classesID], $examID, $sectionID);
    
    // Get mark percentages
    $markpercentages = $this->markpercentage_m->get_markpercentage();

    // Get marks data with relations
    $marks = $this->mark_m->get_order_by_mark_new([
        'schoolyearID' => $schoolyearID,
        'examID' => $examID,
        'classesID' => $classesID
    ]);

    // Get markrelation data for actual marks
    $markrelations = [];
    if (!empty($marks)) {
        $markIDs = array_column($marks, 'markID');
        if (!empty($markIDs)) {
            $this->db->where_in('markID', $markIDs);
            $markrelations = $this->db->get('markrelation')->result();
        }
    }

    // Create a lookup for marks by student and subject
    $marksLookup = [];
    foreach ($marks as $mark) {
        foreach ($markrelations as $relation) {
            if ($relation->markID == $mark->markID) {
                $marksLookup[$mark->studentID][$mark->subjectID] = [
                    'markID' => $mark->markID,
                    'mark' => $relation->mark,
                    'eattendance' => $mark->eattendance ?? 'Present'
                ];
                break;
            }
        }
    }
    
    // Debug: Log the marksLookup for troubleshooting
    error_log("DEBUG: Total marks found: " . count($marks));
    error_log("DEBUG: Total markrelations found: " . count($markrelations));
    error_log("DEBUG: MarksLookup structure: " . json_encode(array_keys($marksLookup)));

    // Calculate totals and grades for current batch students only
    // Ranks will be read from database (pre-calculated using Generate Rank button)
    $studentResults = [];
    
    foreach ($students as $student) {
        $total = 0;
        $zero_mark = 0;
        $isFail = false;

        // Calculate total using markrelations data (same as original)
        // Sum up all marks for this student across all subjects
        foreach ($subjects as $subject) {
            if (isset($marksLookup[$student->studentID][$subject->subjectID])) {
                $markData = $marksLookup[$student->studentID][$subject->subjectID];
                $mrk = (int)$markData['mark'];
                $exam_absent = $markData['eattendance'];
                
                // Track absent students
                if($exam_absent == 'Absent') {
                    $isFail = true;
                }
                
                // Count zero marks
                if ($mrk == 0) {
                    $zero_mark++;
                }
                
                // Sum marks for total (this should match what's shown in individual columns)
                $total += $mrk;
                
                // Debug individual mark addition
                error_log("DEBUG - Student {$student->studentID}, Subject {$subject->subjectID}, Mark: $mrk, Running Total: $total");
            } else {
                // Debug missing marks
                error_log("DEBUG - Student {$student->studentID}, Subject {$subject->subjectID}: NO MARK FOUND");
            }
        }

        // Get rank from database (pre-calculated)
        $rank = $student->rank ?: '-';

        $studentResults[$student->studentID] = [
            'total' => $total,
            'zero_mark' => $zero_mark,
            'isFail' => $isFail,
            'rank' => $rank
        ];
        
        // Debug log for troubleshooting
        error_log("DEBUG Total Calc - Student ID: {$student->studentID}, Name: {$student->name}, Calculated Total: $total");
    }

    // Generate table headers (only for first load)
    $headers = '';
    $out_of = 0; // Initialize total marks
    
    if ($offset == 0) {
        // Calculate total out_of marks properly
        foreach ($subjects as $subject) {
            $out_of += (int)$subject->max_mark;
        }
        
        $headers = '<tr>';
        $headers .= '<th class="no-export">SL No</th>';
        $headers .= '<th class="no-export">Photo</th>';
        $headers .= '<th class="excel-only1">studentID</th>';
        $headers .= '<th>Name (Roll)</th>';
        
        foreach ($subjects as $subject) {
            // Visible column
            $headers .= '<th class="no-export">' . $subject->subject . ' (' . $subject->max_mark . ')</th>';
            // Hidden column for Excel export
            $headers .= '<th class="excel-only" style="display:none;">' . $subject->subject . '^' . $subject->subjectID . '</th>';
        }
        
        $headers .= '<th class="no-export">Total (Out of ' . $out_of . ')</th>';
        $headers .= '<th class="no-export">Grade</th>';
        $headers .= '<th class="no-export">Rank</th>';
        $headers .= '<th class="no-export">Send SMS <input type="checkbox" class="" id="checkAll" name="checkAll"></th>';
        $headers .= '</tr>';
    }

    // Generate student rows
    $rowsHtml = '';
    $counter = $offset + 1;
    
    // Get first markpercentage ID for input IDs (simplified approach)
    $firstMarkPercentageID = !empty($markpercentages) ? $markpercentages[0]->markpercentageID : '1';
    
    // Calculate total out_of marks for grade calculation
    $out_of = 0;
    foreach ($subjects as $subject) {
        $out_of += (int)$subject->max_mark;
    }
    
    foreach ($students as $student) {
        $stuID = $student->studentID;
        $backendTotal = $studentResults[$stuID]['total'];
        $zero_mark = $studentResults[$stuID]['zero_mark'];
        
        // Get proper rank and total for this student from backend calculation
        $studentRank = $studentResults[$stuID]['rank'];
        $backendTotal = $studentResults[$stuID]['total'];
        $zero_mark = $studentResults[$stuID]['zero_mark'];

        // Calculate grade exactly like original
        $percent_cal = $out_of > 0 ? ($backendTotal / $out_of) * 100 : 0;
        
        // Debug logging
        error_log("DEBUG Grade Calc - Student: {$student->studentID}, Total: $backendTotal, Out of: $out_of, Percentage: $percent_cal, Zero marks: $zero_mark");
        
        if ($percent_cal >= 95 && $zero_mark == 0) {
            $grade = 'A+';
            $gradeClass = 'grade-a-plus';
        } else if ($percent_cal >= 90 && $percent_cal < 95 && $zero_mark == 0) {
            $grade = 'A';
            $gradeClass = 'grade-a';
        } else if ($percent_cal >= 80 && $percent_cal < 90 && $zero_mark == 0) {
            $grade = 'B+';
            $gradeClass = 'grade-b-plus';
        } else if ($percent_cal >= 70 && $percent_cal < 80 && $zero_mark == 0) {
            $grade = 'B';
            $gradeClass = 'grade-b';
        } else if ($percent_cal >= 60 && $percent_cal < 70 && $zero_mark == 0) {
            $grade = 'C+';
            $gradeClass = 'grade-c-plus';
        } else if ($percent_cal >= 50 && $percent_cal < 60 && $zero_mark == 0) {
            $grade = 'C';
            $gradeClass = 'grade-c';
        } else {
            $grade = 'D';
            $gradeClass = 'grade-d';
        }

        $rowsHtml .= '<tr>';
        $rowsHtml .= '<td class="no-export">' . $counter . '</td>';
        $rowsHtml .= '<td>' . profileproimage($student->photo) . '</td>';
        $rowsHtml .= '<td class="excel-only1">' . $student->studentID . '</td>';
        $rowsHtml .= '<td>' . $student->name . ' (' . $student->roll . ')';
        $rowsHtml .= '<br><button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#attendance-all-modal_' . $student->studentID . '">All Subjects Absent</button></td>';

        // Subject marks and total calculation
        $my_template = '';
        $totalFromColumns = 0; // Calculate total from what's actually displayed
        
        foreach ($subjects as $subject) {
            $markFound = false;
            if (isset($marksLookup[$student->studentID][$subject->subjectID])) {
                $markData = $marksLookup[$student->studentID][$subject->subjectID];
                $mrk = (int)$markData['mark'];
                $exam_absent = $markData['eattendance'];
                $markID = $markData['markID'];
                
                // Add to total what's actually displayed in the column
                if ($exam_absent !== 'Absent') {
                    $totalFromColumns += $mrk;
                }
                
                // Visible column
                $rowsHtml .= '<td>';
                if ($exam_absent == 'Absent') {
                    $rowsHtml .= '<i class="fa icon-eattendance pull-left" title="add exam attendance" data-toggle="modal" data-target="#attendance-subject-modal_' . $student->studentID . '_' . $subject->subjectID . '"></i>';
                    $rowsHtml .= '<span class="attendance-circle" style="margin-left: 20px;">A</span>';
                } else {
                    $rowsHtml .= '<i class="fa icon-eattendance pull-left" title="add exam attendance" data-toggle="modal" data-target="#attendance-subject-modal_' . $student->studentID . '_' . $subject->subjectID . '"></i>';
                    $rowsHtml .= '<input id="' . $firstMarkPercentageID . '" subj_id="' . $subject->subjectID . '" class="form-control mark input_mark" style="width: 80px !important; margin-left: 20px;" 
                                 name="' . $subject->subjectID . 'mark-' . $markID . '" value="' . $mrk . '" 
                                 min="0" max="' . $subject->max_mark . '">';
                }
                $rowsHtml .= '</td>';
                
                // Hidden Excel-only column
                $rowsHtml .= '<td class="excel-only" style="display:none;">' . $mrk . '</td>';
                
                $absent_or_mark = ($mrk !== null && $mrk !== '') ? ($mrk . "/" . $subject->max_mark) : 'Ab';
                $my_template .= $subject->subject . "=" . $absent_or_mark . ",";
                
                $markFound = true;
            }
            
            if (!$markFound) {
                // Create empty input for subjects with no existing marks
                // We'll need to create a mark record when user enters data
                $rowsHtml .= '<td>';
                $rowsHtml .= '<i class="fa icon-eattendance pull-left" title="add exam attendance" data-toggle="modal" data-target="#attendance-subject-modal_' . $student->studentID . '_' . $subject->subjectID . '"></i>';
                $rowsHtml .= '<input id="' . $firstMarkPercentageID . '" subj_id="' . $subject->subjectID . '" class="form-control mark input_mark" style="width: 80px !important; margin-left: 20px;" 
                             name="' . $subject->subjectID . 'mark-0" value="0" 
                             min="0" max="' . $subject->max_mark . '">';
                $rowsHtml .= '</td>';
                $rowsHtml .= '<td class="excel-only" style="display:none;">0</td>';
                $my_template .= $subject->subject . "=0/" . $subject->max_mark . ",";
            }
        }

        // Use the total calculated from displayed columns instead of studentResults
        $displayTotal = $totalFromColumns;
        
        // Recalculate grade using the correct total
        $percent_cal = $out_of > 0 ? ($displayTotal / $out_of) * 100 : 0;
        
        if ($percent_cal >= 95 && $zero_mark == 0) {
            $grade = 'A+';
            $gradeClass = 'grade-a-plus';
        } else if ($percent_cal >= 90 && $percent_cal < 95 && $zero_mark == 0) {
            $grade = 'A';
            $gradeClass = 'grade-a';
        } else if ($percent_cal >= 80 && $percent_cal < 90 && $zero_mark == 0) {
            $grade = 'B+';
            $gradeClass = 'grade-b-plus';
        } else if ($percent_cal >= 70 && $percent_cal < 80 && $zero_mark == 0) {
            $grade = 'B';
            $gradeClass = 'grade-b';
        } else if ($percent_cal >= 60 && $percent_cal < 70 && $zero_mark == 0) {
            $grade = 'C+';
            $gradeClass = 'grade-c-plus';
        } else if ($percent_cal >= 50 && $percent_cal < 60 && $zero_mark == 0) {
            $grade = 'C';
            $gradeClass = 'grade-c';
        } else {
            $grade = 'D';
            $gradeClass = 'grade-d';
        }

        // Get exam details for checkbox
        $examName = '';
        $examDate = '';
        if ($examDetails) {
            $examName = $examDetails['examName'] . ' held on ' . date('d-m-Y', strtotime($examDetails['examDate']));
            $examDate = $examDetails['examDate'];
        }

        // Total, Grade, Rank, SMS checkbox (use displayTotal)
        $rowsHtml .= '<td>' . $displayTotal . '</td>';
        $rowsHtml .= '<td><span class="grade-label ' . $gradeClass . '">' . $grade . '</span></td>';
        $rowsHtml .= '<td>' . $studentRank . '</td>';
        $rowsHtml .= '<td><input type="checkbox" st_ids="' . $student->studentID . '" st_names="' . $student->name . '" 
                     mobile_no="' . $student->phone . '" exam_name="' . $examName . '" 
                     total_marks="' . $displayTotal . '/' . $out_of . '" exam_date="' . $examDate . '" 
                     marks_template="' . rtrim($my_template, ',') . '" marks_grade="' . $grade . ' Rank ' . $studentRank . '" 
                     sms_rank="' . $studentRank . '" name="send_sms_marks" class="checkbox"></td>';
        $rowsHtml .= '</tr>';
        
        // Debug final total
        error_log("DEBUG Final - Student {$student->studentID}: Display Total = $displayTotal, Grade = $grade, Rank = $studentRank");

        $counter++;
    }
    
    // Generate attendance modals for all students in this batch
    $modalsHtml = '';
    foreach ($students as $student) {
        $modalsHtml .= $this->generateAttendanceModal($student, $examID, $classesID, $sectionID, $subjects);
    }

    echo json_encode([
        'status' => true,
        'headers' => $headers,
        'data' => $rowsHtml,
        'modals' => $modalsHtml,
        'count' => count($students),
        'offset' => $offset,
        'staticData' => $staticData,
        'examDetails' => $examDetails,
        'totalStudents' => $totalStudents
    ]);
}

private function generateAttendanceModal($student, $examID, $classesID, $sectionID, $subjects) {
    $modalsHtml = '';
    
    // Individual Subject Attendance Modals (one for each subject)
    foreach ($subjects as $subject) {
        $modalsHtml .= '<form class="form-horizontal attendance-subject-form" role="form" data-student-id="' . $student->studentID . '" data-subject-id="' . $subject->subjectID . '">
            <div class="modal fade" id="attendance-subject-modal_' . $student->studentID . '_' . $subject->subjectID . '">
                <div class="modal-dialog modal-md">
                    <div class="modal-content" style="border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                        <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px 8px 0 0;">
                            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;"><span>&times;</span></button>
                            <h4 class="modal-title">
                                <i class="fa fa-user-check" style="margin-right: 8px;"></i>
                                Subject Attendance
                            </h4>
                            <small style="display: block; margin-top: 5px; opacity: 0.9;">
                                <i class="fa fa-user"></i> ' . $student->name . ' • 
                                <i class="fa fa-book"></i> ' . $subject->subject . '
                            </small>
                        </div>
                        <div class="modal-body" style="padding: 25px; background-color: #f8f9fa;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group" style="margin-bottom: 20px;">
                                        <label class="col-sm-4 control-label" style="font-weight: 600; color: #495057;">
                                            <i class="fa fa-calendar-check" style="margin-right: 5px; color: #667eea;"></i>
                                            Attendance Status
                                        </label>
                                        <div class="col-sm-8">
                                            <select name="attendance" class="form-control" style="border-radius: 6px; border: 2px solid #e9ecef; padding: 10px;">
                                                <option value="Present" style="color: #28a745;">📍 Present</option>
                                                <option value="Absent" style="color: #dc3545;">❌ Absent</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="examID" value="' . $examID . '">
                            <input type="hidden" name="classesID" value="' . $classesID . '">
                            <input type="hidden" name="sectionID" value="' . $sectionID . '">
                            <input type="hidden" name="studentID" value="' . $student->studentID . '">
                            <input type="hidden" name="subjectID" value="' . $subject->subjectID . '">
                        </div>
                        <div class="modal-footer" style="background-color: #f8f9fa; border-radius: 0 0 8px 8px; padding: 15px 25px;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 6px; padding: 8px 20px;">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-primary save-subject-attendance-btn" data-student-id="' . $student->studentID . '" data-subject-id="' . $subject->subjectID . '" 
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 6px; padding: 8px 20px;">
                                <i class="fa fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>';
    }
    
    // Individual Attendance Modal
    $modalsHtml .= '<form class="form-horizontal attendance-form" role="form" data-student-id="' . $student->studentID . '">
        <div class="modal fade" id="attendance-modal_' . $student->studentID . '">
            <div class="modal-dialog modal-md">
                <div class="modal-content" style="border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                    <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 8px 8px 0 0;">
                        <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;"><span>&times;</span></button>
                        <h4 class="modal-title">
                            <i class="fa fa-clipboard-check" style="margin-right: 8px;"></i>
                            Individual Exam Attendance
                        </h4>
                        <small style="display: block; margin-top: 5px; opacity: 0.9;">
                            <i class="fa fa-user"></i> ' . $student->name . ' (' . $student->roll . ')
                        </small>
                    </div>
                    <div class="modal-body" style="padding: 25px; background-color: #f8f9fa;">
                        <div class="alert alert-info" style="border-radius: 6px; border-left: 4px solid #17a2b8;">
                            <i class="fa fa-info-circle"></i>
                            This will update the exam attendance record for this student.
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="col-sm-4 control-label" style="font-weight: 600; color: #495057;">
                                <i class="fa fa-calendar-check" style="margin-right: 5px; color: #28a745;"></i>
                                Attendance Status
                            </label>
                            <div class="col-sm-8">
                                <select name="attendance" class="form-control" style="border-radius: 6px; border: 2px solid #e9ecef; padding: 10px;">
                                    <option value="Present" style="color: #28a745;">📍 Present</option>
                                    <option value="Absent" style="color: #dc3545;">❌ Absent</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="examID" value="' . $examID . '">
                        <input type="hidden" name="classesID" value="' . $classesID . '">
                        <input type="hidden" name="sectionID" value="' . $sectionID . '">
                        <input type="hidden" name="studentID" value="' . $student->studentID . '">
                    </div>
                    <div class="modal-footer" style="background-color: #f8f9fa; border-radius: 0 0 8px 8px; padding: 15px 25px;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 6px; padding: 8px 20px;">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-success save-attendance-btn" data-student-id="' . $student->studentID . '" 
                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; border-radius: 6px; padding: 8px 20px;">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>';
    
    // All Subjects Absent Modal
    $modalsHtml .= '<form class="form-horizontal attendance-all-form" role="form" data-student-id="' . $student->studentID . '">
        <div class="modal fade" id="attendance-all-modal_' . $student->studentID . '">
            <div class="modal-dialog modal-md">
                <div class="modal-content" style="border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                    <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%); color: white; border-radius: 8px 8px 0 0;">
                        <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;"><span>&times;</span></button>
                        <h4 class="modal-title">
                            <i class="fa fa-exclamation-triangle" style="margin-right: 8px;"></i>
                            Mark All Subjects Attendance
                        </h4>
                        <small style="display: block; margin-top: 5px; opacity: 0.9;">
                            <i class="fa fa-user"></i> ' . $student->name . ' (' . $student->roll . ')
                        </small>
                    </div>
                    <div class="modal-body" style="padding: 25px; background-color: #f8f9fa;">
                        <div class="alert alert-warning" style="border-radius: 6px; border-left: 4px solid #ffc107;">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This will affect all subjects for this student in the current exam.
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="col-sm-4 control-label" style="font-weight: 600; color: #495057;">
                                <i class="fa fa-list-check" style="margin-right: 5px; color: #dc3545;"></i>
                                Attendance Status
                            </label>
                            <div class="col-sm-8">
                                <select name="attendance" class="form-control" style="border-radius: 6px; border: 2px solid #e9ecef; padding: 10px;">
                                    <option value="Present" style="color: #28a745;">📍 Present (All Subjects)</option>
                                    <option value="Absent" style="color: #dc3545;">❌ Absent (All Subjects)</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="examID" value="' . $examID . '">
                        <input type="hidden" name="classesID" value="' . $classesID . '">
                        <input type="hidden" name="sectionID" value="' . $sectionID . '">
                        <input type="hidden" name="studentID" value="' . $student->studentID . '">
                    </div>
                    <div class="modal-footer" style="background-color: #f8f9fa; border-radius: 0 0 8px 8px; padding: 15px 25px;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 6px; padding: 8px 20px;">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-danger save-all-attendance-btn" data-student-id="' . $student->studentID . '" 
                            style="background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%); border: none; border-radius: 6px; padding: 8px 20px;">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>';
    
    return $modalsHtml;
}

public function generateRanks() {
    $classesID = $this->input->post('classesID');
    $examID = $this->input->post('examID');
    $sectionID = $this->input->post('sectionID');
    $schoolyearID = $this->session->userdata('defaultschoolyearID');

    if (!$classesID || !$examID || !$sectionID) {
        echo json_encode(['status' => false, 'message' => 'Missing required parameters']);
        return;
    }

    // Get students using same logic as original add method
    $studentArray = [
        'srclassesID'   => $classesID,
        'srsectionID'   => $sectionID,
        'srschoolyearID' => $schoolyearID,
    ];
    
    $allStudents = $this->studentrelation_m->get_order_by_student($studentArray);
    
    if (empty($allStudents)) {
        echo json_encode(['status' => false, 'message' => 'No students found']);
        return;
    }

    // Get subjects for this class, exam, and section (same as original add method)
    $subjects = $this->subject_m->get_order_by_subject(['classesID' => $classesID], $examID, $sectionID);
    
    // Get mark percentages
    $markpercentages = $this->markpercentage_m->get_markpercentage();

    // Get marks data with relations for proper total calculation
    $marks = $this->mark_m->get_order_by_mark_new([
        'schoolyearID' => $schoolyearID,
        'examID' => $examID,
        'classesID' => $classesID
    ]);

    // Get markrelation data for actual marks
    $markrelations = [];
    if (!empty($marks)) {
        $markIDs = array_column($marks, 'markID');
        if (!empty($markIDs)) {
            $this->db->where_in('markID', $markIDs);
            $markrelations = $this->db->get('markrelation')->result();
        }
    }

    // Create a lookup for marks by student and subject (same as loadStudentsAjax)
    $marksLookup = [];
    foreach ($marks as $mark) {
        foreach ($markrelations as $relation) {
            if ($relation->markID == $mark->markID) {
                $marksLookup[$mark->studentID][$mark->subjectID] = [
                    'markID' => $mark->markID,
                    'mark' => $relation->mark,
                    'eattendance' => $mark->eattendance ?? 'Present'
                ];
                break;
            }
        }
    }

    // Calculate totals for ALL students using same corrected logic as loadStudentsAjax
    $allStudentResults = [];
    
    foreach ($allStudents as $student) {
        $total = 0;
        $zero_mark = 0;
        $isFail = false;

        // Calculate total using same approach as loadStudentsAjax - from displayed subject columns
        foreach ($subjects as $subject) {
            if (isset($marksLookup[$student->studentID][$subject->subjectID])) {
                $markData = $marksLookup[$student->studentID][$subject->subjectID];
                $mrk = (int)$markData['mark'];
                $exam_absent = $markData['eattendance'];
                
                // Track absent students
                if($exam_absent == 'Absent') {
                    $isFail = true;
                }
                
                // Count zero marks
                if ($mrk == 0) {
                    $zero_mark++;
                }
                
                // Sum marks for total (same logic as loadStudentsAjax)
                if ($exam_absent !== 'Absent') {
                    $total += $mrk;
                }
            }
        }

        $allStudentResults[$student->studentID] = [
            'total' => $total,
            'zero_mark' => $zero_mark,
            'isFail' => $isFail,
            'rank' => '-'
        ];
        
        // Debug log for rank generation
        error_log("RANK DEBUG - Student {$student->studentID}: Total = $total, Failed = " . ($isFail ? 'Yes' : 'No'));
    }

    // Calculate ranks for passed students only (exactly like original) - across ALL students
    $passed = [];
    foreach ($allStudentResults as $sid => $res) {
        if (!$res['isFail']) {
            $passed[$sid] = $res['total'];
        }
    }

    $ranksUpdated = 0;
    if (customCompute($passed)) {
        // Sort in descending order (highest marks first)
        arsort($passed, SORT_NUMERIC); 
        
        $studentRanks = [];
        $currentRank = 1;
        $prevTotal = null;

        foreach ($passed as $sid => $totalVal) {
            if ($prevTotal !== null && $totalVal != $prevTotal) {
                $currentRank++;
            }
            
            $studentRanks[$sid] = $currentRank;
            $prevTotal = $totalVal;
        }

        // Update ranks in database - save to student table
        foreach ($allStudentResults as $sid => $res) {
            $rank = isset($studentRanks[$sid]) ? $studentRanks[$sid] : null;
            
            // Update student table with rank
            $this->db->where('studentID', $sid);
            $this->db->update('student', ['rank' => $rank]);
            
            if ($this->db->affected_rows() > 0) {
                $ranksUpdated++;
            }
        }
    }

    echo json_encode([
        'status' => true,
        'message' => "Ranks generated successfully! Updated {$ranksUpdated} students."
    ]);
}

public function saveSubjectAttendance() {
    $schoolyearID = $this->session->userdata('defaultschoolyearID');

    $examID     = $this->input->post('examID');
    $classesID  = $this->input->post('classesID');
    $sectionID  = $this->input->post('sectionID');
    $studentID  = $this->input->post('studentID');
    $subjectID  = $this->input->post('subjectID');
    $attendance = $this->input->post('attendance');

    // Get subject details for max mark
    $subject = $this->subject_m->get_single_subject(['subjectID' => $subjectID]);
    $maxMark = $subject ? $subject->max_mark : 100;

    if ($attendance == 'Absent') {
        // Find the markID for this specific student, exam, class, and subject
        $mark = $this->db->select('markID')
            ->from('mark')
            ->where('schoolyearID', $schoolyearID)
            ->where('examID', $examID)
            ->where('classesID', $classesID)
            ->where('studentID', $studentID)
            ->where('subjectID', $subjectID)
            ->get()
            ->row();

        if ($mark) {
            // Reset markrelation for this specific subject
            $this->db->where('markID', $mark->markID);
            $this->db->update('markrelation', ['mark' => 0]);
            
            // Update attendance in mark table for this subject
            $this->db->where('markID', $mark->markID);
            $this->db->update('mark', ['eattendance' => 'Absent']);
        }
        
        $responseData = [
            'status' => true,
            'message' => 'Subject attendance updated successfully',
            'attendance' => 'Absent'
        ];
    } else {
        // For Present status, get current mark value and update attendance
        $currentMark = $this->db->select('mark.markID, markrelation.mark as current_mark')
            ->from('mark')
            ->join('markrelation', 'markrelation.markID = mark.markID', 'left')
            ->where('mark.schoolyearID', $schoolyearID)
            ->where('mark.examID', $examID)
            ->where('mark.classesID', $classesID)
            ->where('mark.studentID', $studentID)
            ->where('mark.subjectID', $subjectID)
            ->get()
            ->row();
        
        $markValue = $currentMark && $currentMark->current_mark !== null ? $currentMark->current_mark : 0;
        
        // Update attendance to Present
        $this->db->where('schoolyearID', $schoolyearID)
                 ->where('examID', $examID)
                 ->where('classesID', $classesID)
                 ->where('studentID', $studentID)
                 ->where('subjectID', $subjectID);
        $this->db->update('mark', ['eattendance' => 'Present']);
        
        $responseData = [
            'status' => true,
            'message' => 'Subject attendance updated successfully',
            'attendance' => 'Present',
            'markValue' => $markValue,
            'maxMark' => $maxMark,
            'markID' => $currentMark ? $currentMark->markID : 0,
            'markName' => $subjectID . 'mark-' . ($currentMark ? $currentMark->markID : 0)
        ];
    }

    echo json_encode($responseData);
}

public function saveIndividualAttendance() {
    $schoolyearID = $this->session->userdata('defaultschoolyearID');

    $examID     = $this->input->post('examID');
    $classesID  = $this->input->post('classesID');
    $sectionID  = $this->input->post('sectionID');
    $studentID  = $this->input->post('studentID');
    $attendance = $this->input->post('attendance');

    // Update attendance in the eattendance table (individual exam attendance)
    $data = ['eattendance' => $attendance];
    
    // Check if record exists
    $existing = $this->db->select('*')
        ->from('eattendance')
        ->where('schoolyearID', $schoolyearID)
        ->where('examID', $examID)
        ->where('classesID', $classesID)
        ->where('sectionID', $sectionID)
        ->where('studentID', $studentID)
        ->get()
        ->row();
    
    if ($existing) {
        // Update existing record
        $this->db->where('schoolyearID', $schoolyearID);
        $this->db->where('examID', $examID);
        $this->db->where('classesID', $classesID);
        $this->db->where('sectionID', $sectionID);
        $this->db->where('studentID', $studentID);
        $this->db->update('eattendance', $data);
    } else {
        // Insert new record
        $data['schoolyearID'] = $schoolyearID;
        $data['examID'] = $examID;
        $data['classesID'] = $classesID;
        $data['sectionID'] = $sectionID;
        $data['studentID'] = $studentID;
        $this->db->insert('eattendance', $data);
    }

    echo json_encode([
        'status' => true,
        'message' => 'Individual exam attendance updated successfully'
    ]);
}

public function saveAllAttendance() {
    $schoolyearID = $this->session->userdata('defaultschoolyearID');

    $examID     = $this->input->post('examID');
    $classesID  = $this->input->post('classesID');
    $sectionID  = $this->input->post('sectionID');
    $studentID  = $this->input->post('studentID');
    $attendance = $this->input->post('attendance');

    if($attendance == 'Absent') {
        // 🔹 Step 1: Get all markIDs for this student in this exam
        $marks = $this->db->select('markID')
            ->from('mark')
            ->where('schoolyearID', $schoolyearID)
            ->where('examID', $examID)
            ->where('classesID', $classesID)
            // ->where('sectionID', $sectionID)
            ->where('studentID', $studentID)
            ->get()
            ->result();

        if(!empty($marks)) {
            foreach($marks as $m) {
                // 🔹 Step 2: Reset all markrelation rows for this markID
                $this->db->where('markID', $m->markID);
                $this->db->update('markrelation', ['mark' => 0]);
            }
        }
    }

    // 🔹 Step 3: Update attendance in mark table
    $data = ['eattendance' => $attendance];
    $this->db->where('schoolyearID', $schoolyearID);
    $this->db->where('examID', $examID);
    $this->db->where('classesID', $classesID);
    // $this->db->where('sectionID', $sectionID);
    $this->db->where('studentID', $studentID);
    $this->db->update('mark', $data);

    // 🔹 Step 4: Also update eattendance table if required
    $this->db->where('schoolyearID', $schoolyearID);
    $this->db->where('examID', $examID);
    $this->db->where('classesID', $classesID);
    $this->db->where('sectionID', $sectionID);
    $this->db->where('studentID', $studentID);
    $this->db->update('eattendance', $data);

    echo json_encode([
        'status' => true,
        'message' => 'Successfully updated all subjects attendance'
    ]);
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

	// TESTING FUNCTION: Add this method to test ranking logic
	public function test_ranking() {
		// Sample data to test ranking
		$testStudents = [
			1 => ['total' => 95, 'name' => 'Student A'],
			2 => ['total' => 85, 'name' => 'Student B'], 
			3 => ['total' => 95, 'name' => 'Student C'], // Same as A
			4 => ['total' => 75, 'name' => 'Student D'],
			5 => ['total' => 85, 'name' => 'Student E'], // Same as B
		];

		echo "<h3>Testing Ranking Logic</h3>";
		echo "<p>Before ranking:</p>";
		echo "<pre>" . print_r($testStudents, true) . "</pre>";

		// Extract only totals for ranking
		$passed = [];
		foreach($testStudents as $sid => $data) {
			$passed[$sid] = $data['total'];
		}

		// Sort descending 
		arsort($passed, SORT_NUMERIC);
		echo "<p>After sorting (descending):</p>";
		echo "<pre>" . print_r($passed, true) . "</pre>";

		// Apply ranking logic (FIXED VERSION)
		$studentRanks = [];
		$rank = 1;
		$prevTotal = null;
		$studentsWithSameRank = 0;

		foreach ($passed as $sid => $totalVal) {
			if ($prevTotal !== null && $totalVal != $prevTotal) {
				$rank += $studentsWithSameRank;
				$studentsWithSameRank = 1;
			} else {
				$studentsWithSameRank++;
			}
			
			$studentRanks[$sid] = $rank;
			$prevTotal = $totalVal;
		}

		echo "<p>Final ranks (FIXED VERSION):</p>";
		foreach($testStudents as $sid => $data) {
			echo "Student $sid ({$data['name']}): Total {$data['total']} = Rank " . $studentRanks[$sid] . "<br>";
		}

		echo "<p><strong>Expected: A=1, C=1, B=3, E=3, D=5</strong></p>";
		echo "<p style='color: green;'>If you see A and C with rank 1, B and E with rank 3, and D with rank 5, then ranking is working correctly!</p>";
	}

}
