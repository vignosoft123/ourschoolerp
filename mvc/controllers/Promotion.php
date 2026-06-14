<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Promotion extends Admin_Controller
{
    /*
    | -----------------------------------------------------
    | PRODUCT NAME:     INILABS SCHOOL MANAGEMENT SYSTEM
    | -----------------------------------------------------
    | AUTHOR:            INILABS TEAM
    | -----------------------------------------------------
    | EMAIL:            info@inilabs.net
    | -----------------------------------------------------
    | COPYRIGHT:        RESERVED BY INILABS IT
    | -----------------------------------------------------
    | WEBSITE:            http://inilabs.net
    | -----------------------------------------------------
     */

    protected $studentStatus   = [];
    protected $studentResult   = [];
    protected $separatedMarks  = [];
    protected $allStudentMarks = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->model("student_m");
        $this->load->model("subject_m");
        $this->load->model("promotionlog_m");
        $this->load->model("classes_m");
        $this->load->model("studentrelation_m");
        $this->load->model('marksetting_m');
        $this->load->model("exam_m");
        $this->load->model("grade_m");
        $this->load->model("mark_m");
        $this->load->model('section_m');
        $this->load->model('schoolyear_m');
        $this->load->model('studentextend_m');
        $this->load->library('updatechecker');

        $language = $this->session->userdata('lang');
        $this->lang->load('mark', $language);
        $this->lang->load('promotion', $language);

        set_time_limit(500);
    }

    protected function rules()
    {
        $rules = [
            [
                'field' => 'schoolyear',
                'label' => $this->lang->line("promotion_school_year"),
                'rules' => 'trim|required|xss_clean|max_length[11]',
            ],
            [
                'field' => 'classesID',
                'label' => $this->lang->line("promotion_classes"),
                'rules' => 'trim|required|xss_clean|max_length[11]',
            ], [
                'field' => 'jschoolyear',
                'label' => $this->lang->line('promotion_promotion') . ' ' . $this->lang->line('promotion_school_year'),
                'rules' => 'trim|required|xss_clean|max_length[11]',
            ], [
                'field' => 'jclassesID',
                'label' => $this->lang->line('promotion_promotion') . ' ' . $this->lang->line("promotion_classes"),
                'rules' => 'trim|required|xss_clean|max_length[11]',
            ],
        ];
        return $rules;
    }

    public function index()
    {
        $this->data['headerassets'] = [
            'css' => [
                'assets/icheck/skins/all.css',
                'assets/select2/css/select2.css',
                'assets/select2/css/select2-bootstrap.css',
            ],
            'js'  => [
                'assets/icheck/icheck.js',
                'assets/select2/select2.js',
            ],
        ];

        $classesID    = htmlentities(escapeString($this->uri->segment(3)));
        $schoolyearID = htmlentities(escapeString($this->uri->segment(4)));
        if ((int) $classesID && (int) $schoolyearID) {
            $this->data['set']          = $classesID;
            $this->data['schoolyearID'] = $schoolyearID;
            $this->data['classes']      = $this->classes_m->general_get_classes();
            $this->data['sections']     = pluck_multi_array($this->section_m->general_get_section(), 'obj', 'classesID');
            $this->data['subjects']     = $this->subject_m->general_get_order_by_subject(['classesID' => $classesID]);
            $this->data['exams']        = $this->marksetting_m->get_exam_with_class($classesID);
            $this->data['schoolyears']  = $this->data['topbarschoolyears'];
            $this->data['students']     = $this->studentrelation_m->general_get_order_by_student([
                'srclassesID'    => $classesID,
                'srschoolyearID' => $schoolyearID,
            ]);

            if ($_POST) {
                $rules = $this->rules();
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == false) {
                    $this->data["subview"] = "promotion/index";
                    $this->load->view('_layout_main', $this->data);
                } else {
                    // if (config_item('demo') == false) {
                    //     $updateValidation = $this->updatechecker->verifyValidUser();
                    //     if ($updateValidation->status == false) {
                    //         $this->session->set_flashdata('error', $updateValidation->message);
                    //         redirect(base_url('promotion/index'));
                    //     }
                    // }

                    $promotionLog = [
                        'promotionType'                => $this->input->post('promotionType'),
                        'classesID'                    => $classesID,
                        'jumpClassID'                  => $this->input->post('jclassesID'),
                        'schoolYearID'                 => $schoolyearID,
                        'jumpSchoolYearID'             => $this->input->post('jschoolyear'),
                        'status'                       => 0,
                        'subjectandsubjectcodeandmark' => json_encode($this->input->post('subject')),
                        'exams'                        => json_encode($this->input->post('exams')),
                        'markpercentages'              => json_encode([]),
                        'created_at'                   => date('Y-m-d h:i:s'),
                        'create_userID'                => $this->session->userdata('loginuserID')
                    ];

                    $this->promotionlog_m->insert_promotionlog($promotionLog);
                    $this->session->set_userdata(['promotionLogID' => $this->db->insert_id()]);
                    redirect("promotion/add/$classesID/$schoolyearID");
                }
            } else {
                $this->data["subview"] = "promotion/index";
                $this->load->view('_layout_main', $this->data);
            }
        } else {
            $this->data['schoolyears'] = $this->data['topbarschoolyears'];
            $this->data['classes']     = $this->classes_m->general_get_classes();
            $this->data["subview"]     = "promotion/search";
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function add()
    {
        // error_reporting(E_ALL);
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);

        $classID      = htmlentities(escapeString($this->uri->segment(3)));
        $schoolyearID = htmlentities(escapeString($this->uri->segment(4)));
        if ((int) $classID && (int) $schoolyearID) {
            $classes    = $this->classes_m->general_get_classes($classID);
            $schoolyear = $this->schoolyear_m->get_schoolyear($schoolyearID);
            if (customCompute($classes) && customCompute($schoolyear)) {
                $this->data['classes']      = pluck($this->classes_m->general_get_classes(), 'obj', 'classesID');
                $this->data['set']          = $classID;
                $this->data['schoolyears']  = pluck($this->data['topbarschoolyears'], 'obj', 'schoolyearID');
                $this->data['schoolyearID'] = $schoolyearID;
                $this->data['sections']     = pluck($this->section_m->general_get_order_by_section(['classesID' => $classID]),
                    'obj', 'sectionID');

                $this->studentPromotionCalculation($classID, $schoolyearID);
                $this->data['currentClass']      = $this->data['classes'][$classID];
                $this->data['currentSchoolYear'] = $this->data['schoolyears'][$schoolyearID];

                $this->data['promotionClass']      = $this->data['classes'][$this->data['promotionClassID']];
                $this->data['promotionSchoolYear'] = $this->data['schoolyears'][$this->data['promotionSchoolYearID']];

                $this->data["subview"] = "promotion/add";
                $this->load->view('_layout_main', $this->data);
            } else {
                $this->data["subview"] = "error";
                $this->load->view('_layout_main', $this->data);
            }
        } else {
            $this->data["subview"] = "error";
            $this->load->view('_layout_main', $this->data);
        }
    }

    private function studentPromotionCalculation($classID, $schoolyearID)
    {
        // Fetch promotionLog first so we can skip mark computation entirely for normal promotions
        $promotionLog = $this->promotionlog_m->get_promotionlog($this->session->userdata('promotionLogID'));

        $this->data['promotionType']         = $promotionLog->promotionType;
        $this->data['promotionClassID']      = $promotionLog->jumpClassID;
        $this->data['promotionSchoolYearID'] = $promotionLog->jumpSchoolYearID;

        $promotionExams           = array_keys((array) json_decode($promotionLog->exams, true));
        $promotionSubjectPassMark = json_decode($promotionLog->subjectandsubjectcodeandmark, true);

        $this->data['promotionExams'] = $promotionExams;

        $students = pluck($this->studentrelation_m->general_get_order_by_student([
            'srclassesID'    => $classID,
            'srschoolyearID' => $schoolyearID,
        ]), 'obj', 'studentID');

        // Normal promotion: marks are irrelevant — skip the entire heavy mark query
        if ($promotionLog->promotionType == 'normal') {
            $this->data['studentStatus']  = $students;
            $this->data['student_result'] = [];
            return;
        }

        // Non-normal: fetch only the 5 columns the loop needs; drop the examschedule JOIN
        $studentMarks = $this->mark_m->student_marks_for_promotion([
            'classesID'    => $classID,
            'schoolyearID' => $schoolyearID,
        ]);
        $marks = [];
        foreach ($studentMarks as $studentMark) {
            $marks[$studentMark->studentID][$studentMark->examID][$studentMark->subjectID][$studentMark->markpercentageID] = $studentMark->mark;
        }
        $this->allStudentMarks = $marks;

        $subjects     = pluck($this->subject_m->get_order_by_subject(['classesID' => $classID]), 'obj', 'subjectID');
        $marksettings = $this->marksetting_m->get_marksetting_markpercentages();

        $separatedMarks = [];
        $studentStatus  = [];
        $studentResult  = [];

        if (isset($marksettings[$classID])) {
            if (customCompute($students)) {
                foreach ($students as $student) {
                    foreach ($marksettings[$classID] as $examID => $markSubjects) {
                        if (in_array($examID, $promotionExams)) {
                            if (customCompute($markSubjects)) {
                                foreach ($markSubjects as $subjectID => $markPercentages) {
                                    $separatedMarks[$student->studentID][$examID][$subjectID]['subject']  = (isset($subjects[$subjectID]) ? $subjects[$subjectID]->subject : 'N/A');
                                    $separatedMarks[$student->studentID][$examID][$subjectID]['optional'] = (int) (isset($subjects[$subjectID]) ? $subjects[$subjectID]->type : 0);
                                    if (customCompute($markPercentages) && isset($markPercentages['own'])) {
                                        foreach ($markPercentages['own'] as $markPercentage) {
                                            $mark                                                                      = (isset($marks[$student->studentID][$examID][$subjectID][$markPercentage]) ? $marks[$student->studentID][$examID][$subjectID][$markPercentage] : 0);
                                            $separatedMarks[$student->studentID][$examID][$subjectID][$markPercentage] = $mark;

                                            if (!isset($separatedMarks[$student->studentID][$examID][$subjectID]['sum'])) {
                                                $separatedMarks[$student->studentID][$examID][$subjectID]['sum'] = 0;
                                            }
                                            $separatedMarks[$student->studentID][$examID][$subjectID]['sum'] += $mark;
                                            $studentStatus[$student->studentID]['status'] = 1;
                                            $studentStatus[$student->studentID]['total']  = 0;
                                            $studentResult[$student->studentID]           = 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->separatedMarks = $separatedMarks;
        $markTypeID           = $this->data['siteinfos']->marktypeID;

        $examMarks = [];
        foreach ($studentStatus as $studentID => $value) {
            if ($markTypeID == 2) {
                foreach ($promotionExams as $examID) {
                    foreach ($promotionSubjectPassMark as $subjectID => $passMark) {
                        if (isset($separatedMarks[$studentID][$examID][$subjectID])) {
                            if (!isset($examMarks[$studentID][$subjectID]['sum'])) {
                                $examMarks[$studentID][$subjectID]['sum'] = $separatedMarks[$studentID][$examID][$subjectID]['sum'];
                            } else {
                                $examMarks[$studentID][$subjectID]['sum'] += $separatedMarks[$studentID][$examID][$subjectID]['sum'];
                            }
                        }
                    }
                }

                $examSubjectChecker = [];
                foreach ($promotionExams as $examID) {
                    foreach ($promotionSubjectPassMark as $subjectID => $passMark) {
                        if (isset($separatedMarks[$studentID][$examID][$subjectID])) {
                            if ($examMarks[$studentID][$subjectID]['sum'] < $passMark && $separatedMarks[$studentID][$examID][$subjectID]['optional']) {
                                if (!isset($examSubjectChecker[$studentID][$subjectID])) {
                                    $studentStatus[$studentID]['status'] = 0;
                                    $studentResult[$studentID]           = 0;

                                    $studentStatus[$studentID]['exams'][$examID][$subjectID]['passmark'] = $passMark;
                                    $studentStatus[$studentID]['exams'][$examID][$subjectID]['havemark'] = $examMarks[$studentID][$subjectID]['sum'];
                                    $studentStatus[$studentID]['exams'][$examID][$subjectID]['subject']  = $separatedMarks[$studentID][$examID][$subjectID]['subject'];
                                    $examSubjectChecker[$studentID][$subjectID]                          = $subjectID;
                                }
                            }
                            $studentStatus[$studentID]['total'] += $separatedMarks[$studentID][$examID][$subjectID]['sum'];
                        }
                    }
                }
            } else {
                foreach ($promotionExams as $examID) {
                    foreach ($promotionSubjectPassMark as $subjectID => $passMark) {
                        if (isset($separatedMarks[$studentID][$examID][$subjectID])) {
                            if ($separatedMarks[$studentID][$examID][$subjectID]['sum'] < $passMark && $separatedMarks[$studentID][$examID][$subjectID]['optional']) {
                                $studentStatus[$studentID]['status'] = 0;
                                $studentResult[$studentID]           = 0;

                                $studentStatus[$studentID]['exams'][$examID][$subjectID]['passmark'] = $passMark;
                                $studentStatus[$studentID]['exams'][$examID][$subjectID]['havemark'] = $separatedMarks[$studentID][$examID][$subjectID]['sum'];
                                $studentStatus[$studentID]['exams'][$examID][$subjectID]['subject']  = $separatedMarks[$studentID][$examID][$subjectID]['subject'];
                            }
                            $studentStatus[$studentID]['total'] += $separatedMarks[$studentID][$examID][$subjectID]['sum'];
                        }
                    }
                }
            }

            if (isset($students[$studentID])) {
                $studentStatus[$studentID]['info'] = (object) [
                    "studentID"    => $students[$studentID]->studentID,
                    "name"         => $students[$studentID]->name,
                    "roll"         => $students[$studentID]->roll,
                    "photo"        => $students[$studentID]->photo,
                    "username"     => $students[$studentID]->username,
                    "classesID"    => $students[$studentID]->classesID,
                    "sectionID"    => $students[$studentID]->sectionID,
                    "schoolyearID" => $students[$studentID]->schoolyearID,
                ];
            } else {
                $studentStatus[$studentID]['info'] = (object) [
                    "studentID"    => $studentID,
                    "name"         => "Deleted User",
                    "roll"         => "0",
                    "photo"        => "default.png",
                    "username"     => "Deleted User",
                    "classesID"    => 0,
                    "sectionID"    => 0,
                    "schoolyearID" => 0,
                ];
            }
        }

        uasort($studentStatus, function ($a, $b) {
            if ($a['total'] == $b['total']) {
                return 0;
            }
            return ($a['total'] > $b['total']) ? -1 : 1;
        });

        $this->studentStatus = $studentStatus;
        $this->studentResult = $studentResult;

        $this->data['studentStatus']  = $this->studentStatus;
        $this->data['student_result'] = $this->studentResult;
    }

    public function summary()
    {
        $this->data['headerassets'] = [
            'css' => [
                'assets/custom-scrollbar/jquery.mCustomScrollbar.css',
            ],
            'js'  => [
                'assets/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js',
            ],
        ];

        $studentID    = htmlentities(escapeString($this->uri->segment(3)));
        $classID      = htmlentities(escapeString($this->uri->segment(4)));
        $schoolyearID = htmlentities(escapeString($this->uri->segment(5)));

        if ((int) $studentID && (int) $classID && (int) $schoolyearID) {
            $checkStudent = $this->studentrelation_m->get_single_student([
                'srstudentID'    => $studentID,
                'srclassesID'    => $classID,
                'srschoolyearID' => $schoolyearID,
            ]);
            $checkClass      = $this->classes_m->general_get_single_classes(['classesID' => $classID]);
            $checkSchoolyear = $this->schoolyear_m->get_single_schoolyear(['schoolyearID' => $schoolyearID]);

            if (customCompute($checkStudent) && customCompute($checkClass) && customCompute($checkSchoolyear)) {
                $this->data['set'] = $classID;
                $this->basicInfo($checkStudent);
                $this->getMark($studentID, $classID, $schoolyearID);
                $this->studentPromotionCalculation($classID, $schoolyearID);

                $this->data['studentStatus']    = $this->studentStatus[$studentID];
                $this->data['passschoolyearID'] = $schoolyearID;
                $this->data["subview"]          = "promotion/summary";
                $this->load->view('_layout_main', $this->data);
            } else {
                $this->data["subview"] = "error";
                $this->load->view('_layout_main', $this->data);
            }
        } else {
            $this->data["subview"] = "error";
            $this->load->view('_layout_main', $this->data);
        }
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

    private function getMark($studentID, $classesID, $schoolyearID)
    {
        if ((int) $studentID && (int) $classesID && (int) $schoolyearID) {
            $student = $this->studentrelation_m->get_single_student(array('srstudentID' => $studentID, 'srclassesID' => $classesID, 'srschoolyearID' => $schoolyearID));
            $classes = $this->classes_m->get_single_classes(array('classesID' => $classesID));

            if (customCompute($student) && customCompute($classes)) {
                $queryArray = [
                    'classesID'    => $student->srclassesID,
                    'sectionID'    => $student->srsectionID,
                    'studentID'    => $student->srstudentID,
                    'schoolyearID' => $schoolyearID,
                ];

                $exams           = pluck($this->exam_m->get_exam(), 'exam', 'examID');
                $grades          = $this->grade_m->get_grade();
                $marks           = $this->mark_m->student_all_mark_array($queryArray);
                $markpercentages = $this->markpercentage_m->get_markpercentage();

                $subjects           = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID));
                $subjectArr         = [];
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
                $marksettings = $this->marksetting_m->get_marksetting_markpercentages();

                $this->data['settingmarktypeID']  = $this->data['siteinfos']->marktypeID;
                $this->data['subjects']           = $subjectArr;
                $this->data['exams']              = $exams;
                $this->data['grades']             = $grades;
                $this->data['markpercentages']    = pluck($markpercentages, 'obj', 'markpercentageID');
                $this->data['optionalsubjectArr'] = $optionalsubjectArr;
                $this->data['marks']              = $retMark;
                $this->data['highestmarks']       = $highestMarks;
                $this->data['marksettings']       = isset($marksettings[$classesID]) ? $marksettings[$classesID] : [];
            } else {
                $this->data['settingmarktypeID']  = 0;
                $this->data['subjects']           = [];
                $this->data['exams']              = [];
                $this->data['grades']             = [];
                $this->data['markpercentages']    = [];
                $this->data['optionalsubjectArr'] = [];
                $this->data['marks']              = [];
                $this->data['highestmarks']       = [];
                $this->data['marksettings']       = [];
            }
        } else {
            $this->data['settingmarktypeID']  = 0;
            $this->data['subjects']           = [];
            $this->data['exams']              = [];
            $this->data['grades']             = [];
            $this->data['markpercentages']    = [];
            $this->data['optionalsubjectArr'] = [];
            $this->data['marks']              = [];
            $this->data['highestmarks']       = [];
            $this->data['marksettings']       = [];
        }
    }

    public function promotion_to_next_class()
    {
        $studentIDs = $this->input->post("studentIDs");
        $enroll     = $this->input->post('enroll');

        $promotionLogID = $this->session->userdata('promotionLogID');
        $promotionLog   = $this->promotionlog_m->get_promotionlog($promotionLogID);

        $previousClasseID = $promotionLog->classesID;
        $previousYearID   = $promotionLog->schoolYearID;
        $promotionClassID = $promotionLog->jumpClassID;
        $promotionYearID  = $promotionLog->jumpSchoolYearID;

        $targetClassID   = (isset($enroll) && $enroll) ? $previousClasseID : $promotionClassID;
        $explodeStudents = explode(",", $studentIDs);

        $students = pluck($this->studentrelation_m->general_get_order_by_student([
            "srclassesID"    => $previousClasseID,
            "srschoolyearID" => $previousYearID,
        ]), 'obj', 'studentID');

        $promoteClassPreviousStudentsList = pluck($this->studentrelation_m->general_get_order_by_student([
            "srclassesID"    => $targetClassID,
            "srschoolyearID" => $promotionYearID,
        ]), 'obj', 'studentID');

        $sections      = $this->section_m->general_get_order_by_section(["classesID" => $targetClassID]);
        $lastSectionID = $sections[customCompute($sections) - 1]->sectionID;
        $sections      = pluck($sections, 'obj', 'sectionID');

        $capacity = [];
        $roll     = 1;

        foreach ($promoteClassPreviousStudentsList as $studentID => $studentInfo) {
            if (isset($sections[$studentInfo->sectionID])) {
                if (isset($capacity[$studentInfo->sectionID])) {
                    $capacity[$studentInfo->sectionID]++;
                } else {
                    $capacity[$studentInfo->sectionID] = 1;
                }
                $roll++;
            }
        }

        if (!customCompute($students) || !customCompute($studentIDs) || !customCompute($previousClasseID)) {
            return;
        }

        // --- PRE-FETCH 1: resolve target class name (same for every student, fetch once) ---
        $setClassesID = $targetClassID;
        $setClasses   = null;
        if ($setClassesID > 0) {
            $classesRelation = $this->classes_m->general_get_classes($setClassesID);
            if (customCompute($classesRelation)) {
                $setClassesID = $classesRelation->classesID;
                $setClasses   = $classesRelation->classes;
            }
        }

        // Collect valid student IDs that need processing
        $validStudentIDs = [];
        foreach ($explodeStudents as $sid) {
            if ($sid != 0 && isset($students[$sid])) {
                $validStudentIDs[] = (int) $sid;
            }
        }

        if (empty($validStudentIDs)) {
            return;
        }

        // --- PRE-FETCH 2: existing studentrelations in new year (1 query instead of N) ---
        // Key: srstudentID => row (with studentrelationID and srclassesID).
        // Students already in the TARGET class → skip entirely.
        // Students in a DIFFERENT class for the new year → UPDATE that record instead of skipping.
        $existingRelationData = [];
        $existingRows = $this->db->select('srstudentID, studentrelationID, srclassesID')
            ->from('studentrelation')
            ->where('srschoolyearID', $promotionYearID)
            ->where_in('srstudentID', $validStudentIDs)
            ->get()->result();
        foreach ($existingRows as $row) {
            $existingRelationData[$row->srstudentID] = $row;
        }

        // --- PRE-FETCH 3: previous section names for all students (1 query instead of N) ---
        $prevSectionNames = [];
        $prevSecRows = $this->db->select('srstudentID, srsection')
            ->from('studentrelation')
            ->where('srclassesID', $previousClasseID)
            ->where('srschoolyearID', $previousYearID)
            ->where_in('srstudentID', $validStudentIDs)
            ->get()->result();
        foreach ($prevSecRows as $row) {
            $prevSectionNames[$row->srstudentID] = $row->srsection;
        }

        // --- PRE-FETCH 4: all sections for the target class (section name → sectionID map) ---
        $sectionNameToID   = [];
        $allTargetSections = $this->db->select('*')->from('section')->where('classesID', $setClassesID)->get()->result();
        foreach ($allTargetSections as $sec) {
            $sectionNameToID[$sec->section] = $sec->sectionID;
        }

        // Cache: section template details looked up by name across all classes (for new section creation)
        $sectionTemplateCache = [];

        $promoteStudents = isset($promotionLog->promoteStudents) && $promotionLog->promoteStudents != null
            ? json_decode($promotionLog->promoteStudents, true) : [];

        // Batch containers — built during the loop, executed after
        $studentUpdateBatch         = [];
        $studentRelationInsertBatch = [];
        $studentRelationUpdateBatch = [];
        $promotedStudentIDs         = [];

        foreach ($explodeStudents as $studentID) {
            $studentID = (int) $studentID;
            if ($studentID == 0 || !isset($students[$studentID])) {
                continue;
            }

            // Assign section based on capacity
            $promoteSectionID = 0;
            foreach ($sections as $sectionID => $sectionInfo) {
                if (isset($capacity[$sectionID])) {
                    if ($sectionInfo->capacity >= $capacity[$sectionID] + 1) {
                        $capacity[$sectionID]++;
                        $promoteSectionID = $sectionID;
                        break;
                    }
                } else {
                    $capacity[$sectionID] = 1;
                    $promoteSectionID     = $sectionID;
                    break;
                }
            }

            if ($promoteSectionID == 0 || (isset($enroll) && $enroll)) {
                $promoteSectionID = $lastSectionID;
            }

            $studentUpdateBatch[] = [
                'studentID'    => $studentID,
                'classesID'    => $targetClassID,
                'schoolyearID' => $promotionYearID,
                'roll'         => (isset($enroll) && $enroll) ? 0 : $roll,
                'sectionID'    => $promoteSectionID,
            ];
            $promotedStudentIDs[] = $studentID;

            $existingRecord  = isset($existingRelationData[$studentID]) ? $existingRelationData[$studentID] : null;
            $alreadyInTarget = $existingRecord && ((int)$existingRecord->srclassesID === (int)$setClassesID);

            // Build studentrelation insert OR update as needed
            if (!$alreadyInTarget) {
                $prev_sec_name = isset($prevSectionNames[$studentID]) ? $prevSectionNames[$studentID] : '';

                // Fallback: look up section name from student table when srsection was empty
                if (empty($prev_sec_name)) {
                    $r = $this->db->select('sec.section')
                        ->from('student s')
                        ->join('section sec', 'sec.sectionID = s.sectionID', 'left')
                        ->where('s.studentID', $studentID)
                        ->where('s.classesID', $previousClasseID)
                        ->where('s.schoolyearID', $previousYearID)
                        ->get()->row_array();
                    $prev_sec_name = isset($r['section']) ? $r['section'] : '';
                }

                // Resolve target sectionID — create the section in the new class if missing
                if (isset($sectionNameToID[$prev_sec_name])) {
                    $new_sec_id = $sectionNameToID[$prev_sec_name];
                } else {
                    // Fetch template from any existing class (cached to avoid repeat queries)
                    if (!isset($sectionTemplateCache[$prev_sec_name])) {
                        $tmpl = $this->db->get_where('section', ['section' => $prev_sec_name])->row_array();
                        $sectionTemplateCache[$prev_sec_name] = $tmpl ?: [];
                    }
                    $tmpl = $sectionTemplateCache[$prev_sec_name];

                    $this->section_m->insert_section([
                        "section"         => $prev_sec_name,
                        "category"        => isset($tmpl['category'])  ? $tmpl['category']  : '',
                        "capacity"        => isset($tmpl['capacity'])  ? $tmpl['capacity']  : 0,
                        "classesID"       => $setClassesID,
                        "teacherID"       => isset($tmpl['teacherID']) ? $tmpl['teacherID'] : 0,
                        "note"            => isset($tmpl['note'])      ? $tmpl['note']      : '',
                        "create_date"     => date("Y-m-d H:i:s"),
                        "modify_date"     => date("Y-m-d H:i:s"),
                        "create_userID"   => $this->session->userdata('loginuserID'),
                        "create_username" => $this->session->userdata('username'),
                        "create_usertype" => $this->session->userdata('usertype'),
                    ]);
                    $new_sec_id = $this->db->insert_id();
                    $sectionNameToID[$prev_sec_name] = $new_sec_id; // cache so subsequent students reuse it
                }

                $relationRow = [
                    'srname'              => $students[$studentID]->name,
                    'srclassesID'         => $setClassesID,
                    'srclasses'           => $setClasses,
                    'srroll'              => $roll,
                    'srregisterNO'        => $students[$studentID]->registerNO,
                    'srsectionID'         => $new_sec_id,
                    'srsection'           => $prev_sec_name,
                    'srstudentgroupID'    => $students[$studentID]->studentgroupID,
                    'sroptionalsubjectID' => 0,
                    'srschoolyearID'      => $promotionYearID,
                ];

                if ($existingRecord) {
                    // Already in the new year but in a different class → UPDATE to target class
                    $relationRow['studentrelationID'] = $existingRecord->studentrelationID;
                    $studentRelationUpdateBatch[] = $relationRow;
                } else {
                    // No record for new year → INSERT
                    $relationRow['srstudentID'] = $studentID;
                    $studentRelationInsertBatch[] = $relationRow;
                }
            }

            $promoteStudents[] = [
                'studentID' => $studentID,
                'roll'      => $roll,
                'enroll'    => $enroll,
                'sectionID' => $promoteSectionID,
            ];
            $roll++;
        }

        // --- BATCH WRITES (wrapped in a transaction) ---
        $this->db->trans_start();

        if (!empty($studentUpdateBatch)) {
            $this->db->update_batch('student', $studentUpdateBatch, 'studentID');
        }

        if (!empty($promotedStudentIDs)) {
            $this->db->where_in('studentID', $promotedStudentIDs)
                     ->update('studentextend', ['optionalsubjectID' => 0]);
        }

        if (!empty($studentRelationUpdateBatch)) {
            $this->db->update_batch('studentrelation', $studentRelationUpdateBatch, 'studentrelationID');
        }

        if (!empty($studentRelationInsertBatch)) {
            $this->db->insert_batch('studentrelation', $studentRelationInsertBatch);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->session->set_flashdata('error', $this->lang->line('promotion_create_class'));
            echo 'error';
        } else {
            $this->promotionlog_m->update_promotionlog([
                'promoteStudents' => json_encode($promoteStudents),
                'status'          => 1,
            ], $promotionLogID);
            $this->session->set_flashdata('success', $this->lang->line('menu_success'));
            echo 'success';
        }
    }

    public function print_preview()
    {
        if (permissionChecker('promotion')) {
            $studentID    = htmlentities(escapeString($this->uri->segment(3)));
            $classID      = htmlentities(escapeString($this->uri->segment(4)));
            $schoolyearID = htmlentities(escapeString($this->uri->segment(5)));

            if ((int) $studentID && (int) $classID && (int) $schoolyearID) {
                $checkStudent = $this->studentrelation_m->get_single_student([
                    'srstudentID'    => $studentID,
                    'srclassesID'    => $classID,
                    'srschoolyearID' => $schoolyearID,
                ]);
                $checkClass      = $this->classes_m->general_get_single_classes(['classesID' => $classID]);
                $checkSchoolyear = $this->schoolyear_m->get_single_schoolyear(['schoolyearID' => $schoolyearID]);

                if (customCompute($checkStudent) && customCompute($checkClass) && customCompute($checkSchoolyear)) {
                    $this->basicInfo($checkStudent);
                    $this->getMark($studentID, $classID, $schoolyearID);
                    $this->studentPromotionCalculation($classID, $schoolyearID);

                    $this->data['studentStatus']    = $this->studentStatus[$studentID];
                    $this->data['passschoolyearID'] = $schoolyearID;
                    $this->reportPDF('markmodule.css', $this->data, 'promotion/print_preview');
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
        $retArray['status']  = false;
        $retArray['message'] = '';
        if (permissionChecker('promotion')) {
            if ($_POST) {
                $rules = $this->send_mail_rules();
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == false) {
                    $retArray           = $this->form_validation->error_array();
                    $retArray['status'] = false;
                    echo json_encode($retArray);
                    exit;
                } else {
                    $studentID    = $this->input->post('studentID');
                    $classID      = $this->input->post('classesID');
                    $schoolyearID = $this->input->post('schoolyearID');

                    if ((int) $studentID && (int) $classID && (int) $schoolyearID) {
                        $checkStudent = $this->studentrelation_m->get_single_student([
                            'srstudentID'    => $studentID,
                            'srclassesID'    => $classID,
                            'srschoolyearID' => $schoolyearID,
                        ]);
                        $checkClass      = $this->classes_m->general_get_single_classes(['classesID' => $classID]);
                        $checkSchoolyear = $this->schoolyear_m->get_single_schoolyear(['schoolyearID' => $schoolyearID]);

                        if (customCompute($checkStudent) && customCompute($checkClass) && customCompute($checkSchoolyear)) {
                            $this->basicInfo($checkStudent);
                            $this->getMark($studentID, $classID, $schoolyearID);
                            $this->studentPromotionCalculation($classID, $schoolyearID);

                            $this->data['studentStatus']    = $this->studentStatus[$studentID];
                            $this->data['passschoolyearID'] = $schoolyearID;

                            $email   = $this->input->post('to');
                            $subject = $this->input->post('subject');
                            $message = $this->input->post('message');
                            $this->reportSendToMail('markmodule.css', $this->data, 'promotion/print_preview', $email, $subject, $message);
                            $retArray['message'] = "Message";
                            $retArray['status']  = true;
                            echo json_encode($retArray);
                            exit;
                        } else {
                            $retArray['message'] = $this->lang->line('promotion_data_not_found');
                            echo json_encode($retArray);
                            exit;
                        }
                    } else {
                        $retArray['message'] = $this->lang->line('promotion_data_not_found');
                        echo json_encode($retArray);
                        exit;
                    }
                }
            } else {
                $retArray['message'] = $this->lang->line('promotion_permissionmethod');
                echo json_encode($retArray);
                exit;
            }
        } else {
            $retArray['message'] = $this->lang->line('promotion_permissionmethod');
            echo json_encode($retArray);
            exit;
        }
    }

    public function send_mail_rules()
    {
        $rules = [
            [
                'field' => 'to',
                'label' => $this->lang->line("promotion_to"),
                'rules' => 'trim|required|max_length[60]|valid_email|xss_clean',
            ],
            [
                'field' => 'subject',
                'label' => $this->lang->line("promotion_subject"),
                'rules' => 'trim|required|xss_clean',
            ],
            [
                'field' => 'message',
                'label' => $this->lang->line("promotion_message"),
                'rules' => 'trim|xss_clean',
            ],
            [
                'field' => 'studentID',
                'label' => $this->lang->line("promotion_studentID"),
                'rules' => 'trim|required|max_length[10]|xss_clean|callback_unique_data',
            ],
            [
                'field' => 'classesID',
                'label' => $this->lang->line("promotion_classesID"),
                'rules' => 'trim|required|max_length[10]|xss_clean|callback_unique_data',
            ],
            [
                'field' => 'schoolyearID',
                'label' => $this->lang->line("promotion_academicyear"),
                'rules' => 'trim|required|max_length[10]|xss_clean|callback_unique_data',
            ],
        ];
        return $rules;
    }

    public function unique_data($data)
    {
        if ($data != '') {
            if ($data == '0') {
                $this->form_validation->set_message('unique_data', 'The %s field is required.');
                return false;
            }
            return true;
        }
        return true;
    }

    public function promotion_list()
    {
        $classID      = $this->input->post('id');
        $schoolyearID = $this->input->post('year');
        if ((int) $classID) {
            $string = base_url("promotion/index/$classID/$schoolyearID");
            echo $string;
        } else {
            redirect(base_url("promotion/index"));
        }
    }
}
