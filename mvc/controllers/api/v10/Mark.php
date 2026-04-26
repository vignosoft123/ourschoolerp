<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Mark extends Api_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('classes_m');
        $this->load->model('section_m');
        $this->load->model('subject_m');
        $this->load->model('exam_m');
        $this->load->model('grade_m');
        $this->load->model('mark_m');
        $this->load->model('markpercentage_m');
        $this->load->model('studentrelation_m');
        $this->load->model('marksetting_m');
    }

    public function index_get($id = null) 
    {
        $myProfile = false;
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        if($this->session->userdata('usertypeID') == 3) {
            $id = $this->data['myclass'];
            if(!permissionChecker('mark_view')) {
                $myProfile = true;
            }
        }
        $myProfile = true;
        // echo $this->session->userdata('usertypeID').'===='.$myProfile;die;
        if($this->session->userdata('usertypeID') == 3 && $myProfile) {
            $url = $id;
            $id = $this->session->userdata('loginuserID');
            $this->view_get($id, $url);
        } else {
            $this->retdata['classesID'] = $id;
            $this->retdata['classes']   = $this->classes_m->get_classes();

            if((int)$id) {
                $fetchClass = pluck($this->retdata['classes'], 'classesID', 'classesID');
                if(isset($fetchClass[$id])) {
                    $this->retdata['students'] = $this->studentrelation_m->get_order_by_student(array('srclassesID' => $id, 'srschoolyearID' => $schoolyearID));
                    if(customCompute($this->retdata['students'])) {
                        $sections = $this->section_m->general_get_order_by_section(array("classesID" => $id));
                        $this->retdata['sections'] = $sections;
                        if(customCompute($sections)) {
                            foreach ($sections as $key => $section) {
                                $this->retdata['allsection'][$section->sectionID] = $this->studentrelation_m->get_order_by_student(array('srclassesID' => $id, "srsectionID" => $section->sectionID, 'srschoolyearID' => $schoolyearID));
                            }
                        }
                    } else {
                        $this->retdata['students'] = [];
                    }
                } else {
                    $this->retdata['students'] = [];
                }
            } else {
                $this->retdata['students'] = [];
            }

            $this->response([
                'status'    => true,
                'message'   => 'Success',
                'data'      => $this->retdata
            ], REST_Controller::HTTP_OK);
        }
    }

    public function view_get($studentID = null, $classID = null) 
    {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        if((int)$studentID && (int)$classID) {
            $schoolyearID = $this->session->userdata('defaultschoolyearID');
            $student = $this->studentrelation_m->get_single_student(array('srstudentID' => $studentID, 'srclassesID' => $classID, 'srschoolyearID' => $schoolyearID));
            if(customCompute($student)) {
                $fetchClass = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
                if(isset($fetchClass[$classID])) {
                    $this->getView($studentID, $classID);
                } else {
                    $this->retdata['classesID']        = $classID;
                    $this->retdata['profile']          = [];
                    $this->retdata['usertype']         = [];
                    $this->retdata['class']            = [];
                    $this->retdata['section']          = [];
                    $this->retdata['classesID']        = $url;
                    $this->retdata["exams"]            = [];
                    $this->retdata["grades"]           = [];
                    $this->retdata['markpercentages']  = [];
                    $this->retdata["highestmarks"]     = [];
                    $this->retdata["settingmarktypeID"]  = 0;
                    $this->retdata["optionalsubjectArr"] = [];
                    $this->retdata["marksettings"]       = [];


                    $this->response([
                        'status'    => false,
                        'message'   => 'Error 404',
                        'data'      => $this->retdata,
                    ], REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $this->retdata['classesID']        = $classID;
                $this->retdata['profile']          = [];
                $this->retdata['usertype']         = [];
                $this->retdata['class']            = [];
                $this->retdata['section']          = [];
                $this->retdata['classesID']        = $url;
                $this->retdata["exams"]            = [];
                $this->retdata["grades"]           = [];
                $this->retdata['markpercentages']  = [];
                $this->retdata["highestmarks"]     = [];
                $this->retdata["settingmarktypeID"]  = 0;
                $this->retdata["optionalsubjectArr"] = [];
                $this->retdata["marksettings"]       = [];

                $this->response([
                    'status'    => false,
                    'message'   => 'Error 404',
                    'data'      => $this->retdata,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $this->retdata['classesID']        = $classID;
            $this->retdata['profile']          = [];
            $this->retdata['usertype']         = [];
            $this->retdata['class']            = [];
            $this->retdata['section']          = [];
            $this->retdata['classesID']        = $url;
            $this->retdata["exams"]            = [];
            $this->retdata["grades"]           = [];
            $this->retdata['markpercentages']  = [];
            $this->retdata["highestmarks"]     = [];
            $this->retdata["settingmarktypeID"]  = 0;
            $this->retdata["optionalsubjectArr"] = [];
            $this->retdata["marksettings"]       = [];

            $this->response([
                'status'    => false,
                'message'   => 'Error 404',
                'data'      => $this->retdata,
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    private function getView($id, $url) 
    {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        if((int)$id && (int)$url) {
            $schoolyearID = $this->session->userdata('defaultschoolyearID');
            $studentInfo = $this->studentrelation_m->get_single_student(array('srstudentID' => $id, 'srclassesID' => $url, 'srschoolyearID' => $schoolyearID));


            if(customCompute($studentInfo)) {
                $this->pluckInfo();
                $this->basicInfo($studentInfo);
                $this->markInfo($studentInfo);
            } else {
                $this->retdata['classesID']        = 0;
                $this->retdata['profile']          = [];
                $this->retdata['usertype']         = [];
                $this->retdata['class']            = [];
                $this->retdata['section']          = [];
                $this->retdata['classesID']        = $url;
                $this->retdata["exams"]            = [];
                $this->retdata["grades"]           = [];
                $this->retdata['markpercentages']  = [];
                $this->retdata["highestmarks"]     = [];
                $this->retdata["settingmarktypeID"]  = 0;
                $this->retdata["optionalsubjectArr"] = [];
                $this->retdata["marksettings"]       = [];
            }

            $this->response([
                'status'    => true,
                'message'   => 'Success',
                'data'      => $this->retdata
            ], REST_Controller::HTTP_OK);
        }
    }

    private function pluckInfo() 
    {
        // $this->retdata['subjects'] = pluck($this->subject_m->general_get_subject(), 'subject', 'subjectID');
    }

    private function basicInfo($studentInfo) 
    {
        if(customCompute($studentInfo)) {
            $this->retdata['profile']  = $studentInfo;
            $this->retdata['usertype'] = $this->usertype_m->get_single_usertype(array('usertypeID' => $studentInfo->usertypeID));
            // $this->retdata['class']    = $this->classes_m->get_single_classes(array('classesID' => $studentInfo->srclassesID));
            // $this->retdata['section']  = $this->section_m->general_get_single_section(array('sectionID' => $studentInfo->srsectionID));

            $optionalsubject = null;
            if($studentInfo->sroptionalsubjectID > 0) {
                $optionalsubject = $this->subject_m->general_get_single_subject(array('type' => 0, 'classesID' => $studentInfo->srclassesID, 'subjectID' => $studentInfo->sroptionalsubjectID));
            }
            $this->retdata['optionalsubject'] = $optionalsubject;
        } else {
            $this->retdata['profile']           = [];
            $this->retdata['usertype']          = [];
            $this->retdata['class']             = [];
            $this->retdata['section']           = [];
            $this->retdata['optionalsubject']   = null;
        }
    }

    private function markInfo($studentInfo) 
    {
        if(customCompute($studentInfo)) {
            $this->getMark($studentInfo->studentID, $studentInfo->srclassesID);
        } else {
            $this->retdata['classesID']        = 0;
            $this->retdata["exams"]            = [];
            $this->retdata["grades"]           = [];
            $this->retdata['markpercentages']  = [];
            $this->retdata["highestmarks"]     = [];
            $this->retdata["section"]          = [];
            $this->retdata["settingmarktypeID"]  = 0;
            $this->retdata["optionalsubjectArr"] = [];
            $this->retdata["marksettings"]       = [];
        }
    }

    private function getMark($studentID, $classesID) {
    if ((int)$studentID && (int)$classesID) {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $student = $this->studentrelation_m->get_single_student([
            'srstudentID'    => $studentID,
            'srclassesID'    => $classesID,
            'srschoolyearID' => $schoolyearID
        ]);
        $classes = $this->classes_m->get_single_classes(['classesID' => $classesID]);

        if (customCompute($student) && customCompute($classes)) {
            $retMark = [];
            $examSummaries = [];

            // Direct query: INNER JOIN examschedule with sectionID filter so only subjects
            // actually scheduled for the student's section are returned. Also joins subject
            // table to get the subject name (student_all_mark_array_api omits both).
            $this->db->select('
                mark.markID,
                mark.examID,
                mark.subjectID,
                mark.eattendance,
                markrelation.markpercentageID,
                markrelation.mark,
                examschedule.max_mark,
                examschedule.min_mark,
                subject.subject,
                exam.date   AS exam_date,
                exam.exam   AS exam_name
            ');
            $this->db->from('mark');
            $this->db->join('markrelation', 'markrelation.markID = mark.markID', 'left');
            $this->db->join(
                'examschedule',
                'examschedule.examID    = mark.examID
                 AND examschedule.subjectID = mark.subjectID
                 AND examschedule.classesID = mark.classesID
                 AND examschedule.sectionID = ' . (int)$student->srsectionID,
                'inner'   // INNER excludes subjects not scheduled for this section
            );
            $this->db->join('subject', 'subject.subjectID = mark.subjectID', 'left');
            $this->db->join('exam',    'exam.examID = mark.examID',          'left');
            $this->db->where('mark.studentID',    $student->srstudentID);
            $this->db->where('mark.classesID',    $student->srclassesID);
            $this->db->where('mark.schoolyearID', $schoolyearID);
            $marks = $this->db->get()->result();

            if (customCompute($marks)) {
                foreach ($marks as $mark) {
                    // Skip optional subject if required
                    if ((int)$student->sroptionalsubjectID > 0 && $mark->subjectID == $student->sroptionalsubjectID) {
                        continue;
                    }

                    $exam        = $mark->exam_name;
                    $subject     = $mark->subject;
                    $percentageID = $mark->markpercentageID;

                    if (!isset($retMark[$exam][$subject])) {
                        $retMark[$exam][$subject] = [
                            'min_mark'  => $mark->min_mark,
                            'max_mark'  => $mark->max_mark,
                            'exam_date' => $mark->exam_date,
                            'marks'     => []
                        ];
                    }

                    $retMark[$exam][$subject]['marks'][$percentageID] = $mark->mark;
                }

                // Compute exam-wise summary
                foreach ($retMark as $examName => $subjects) {
                    $totalObtained = 0;
                    $totalMax      = 0;
                    $zeroMark      = 0;

                    foreach ($subjects as $subject => $details) {
                        $subjectTotal = 0;

                        foreach ($details['marks'] as $mark) {
                            $mark = (float)$mark;
                            $subjectTotal += $mark;
                            if ($mark == 0 || $mark === null) {
                                $zeroMark++;
                            }
                        }

                        $totalObtained += $subjectTotal;
                        $totalMax      += (float)$details['max_mark'];
                    }

                    $percent_cal = $totalMax > 0 ? ($totalObtained / $totalMax) * 100 : 0;

                    if ($percent_cal >= 95 && $zeroMark == 0) {
                        $grade = "A+";
                    } else if ($percent_cal >= 90) {
                        $grade = "A";
                    } else if ($percent_cal >= 80) {
                        $grade = "B+";
                    } else if ($percent_cal >= 70) {
                        $grade = "B";
                    } else if ($percent_cal >= 60) {
                        $grade = "C+";
                    } else if ($percent_cal >= 50) {
                        $grade = "C";
                    } else {
                        $grade = "D";
                    }

                    $examSummaries[$examName] = [
                        'total_obtained' => $totalObtained,
                        'total_max'      => $totalMax,
                        'percentage'     => round($percent_cal, 2),
                        'grade'          => $grade
                    ];
                }
            }

            $this->retdata['marks']         = $retMark;
            $this->retdata['exam_summary']  = $examSummaries;
        } else {
            // $this->setEmptyMarksData();
        }
    } else {
        // $this->setEmptyMarksData();
    }
}

    public function load_marks_post()
    {
        $examID      = $this->post('examID');
        $classesID   = $this->post('classesID');
        $sectionID   = $this->post('sectionID');
        $subjectID   = $this->post('subjectID'); // Optional
        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        if (!$examID || !$classesID || !$sectionID) {
            $this->response([
                'status'  => false,
                'message' => 'Missing required parameters: examID, classesID, sectionID',
                'data'    => []
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Fetch students with a direct query — bypasses the usertypeID-based routing in
        // get_order_by_student() which would restrict subject teachers (not class teachers)
        // to an empty list because userRelation() finds no classes for them.
        $this->db->select('studentrelation.*, student.studentID, student.name, student.photo, student.active, studentrelation.srroll as roll');
        $this->db->from('studentrelation');
        $this->db->join('student', 'student.studentID = studentrelation.srstudentID', 'left');
        $this->db->where('studentrelation.srclassesID',    $classesID);
        $this->db->where('studentrelation.srsectionID',    $sectionID);
        $this->db->where('studentrelation.srschoolyearID', $schoolyearID);
        $this->db->where('student.active', 1);
        $this->db->order_by('studentrelation.srroll', 'asc');
        $students = $this->db->get()->result();

        if (!customCompute($students)) {
            $this->response([
                'status'  => true,
                'message' => 'No students found',
                'data'    => ['students' => []]
            ], REST_Controller::HTTP_OK);
            return;
        }

        // Determine which subjects this user may access.
        // - Admin/superadmin (usertypeID != 2): all scheduled subjects
        // - Teacher who is class teacher for this classesID: all scheduled subjects
        // - Teacher assigned to specific subjects only: only those subjects
        $allowedSubjectIDs = null; // null = no filter
        if ($this->session->userdata('usertypeID') == 2) {
            $teacherID = (int)$this->session->userdata('loginuserID');
            $isClassTeacher = $this->db->get_where('classes', [
                'classesID' => $classesID,
                'teacherID' => $teacherID
            ])->row();

            if (!$isClassTeacher) {
                $assigned = $this->db->get_where('subjectteacher', [
                    'classesID' => $classesID,
                    'teacherID' => $teacherID
                ])->result();
                $allowedSubjectIDs = array_column($assigned, 'subjectID');
            }
        }

        // Fetch subjects from examschedule (INNER JOIN ensures only scheduled subjects are returned
        // with correct max/min marks), then apply teacher subject filter if needed.
        $this->db->select('subject.*, examschedule.max_mark, examschedule.min_mark');
        $this->db->from('examschedule');
        $this->db->join('subject', 'subject.subjectID = examschedule.subjectID', 'inner');
        $this->db->where('examschedule.examID', $examID);
        $this->db->where('examschedule.sectionID', $sectionID);
        $this->db->where('subject.classesID', $classesID);
        if ($allowedSubjectIDs !== null) {
            if (empty($allowedSubjectIDs)) {
                $this->db->where('subject.subjectID', 0); // no assignments → empty result
            } else {
                $this->db->where_in('subject.subjectID', $allowedSubjectIDs);
            }
        }
        $this->db->order_by('subject.subjectID', 'ASC');
        $subjects = $this->db->get()->result();

        // Fetch all mark percentages
        $markpercentageArr = [
            'marktypeID' => $this->data['siteinfos']->marktypeID,
            'classesID'  => $classesID,
            'examID'     => $examID,
            // 'subjectID' is not used here because we want all mark settings for the class/exam
        ];
        $markSettings = $this->marksetting_m->get_marksetting_markpercentages_add($markpercentageArr);

        // Fetch canonical marks (one per student/subject via MIN markID, same as web loadStudentsAjax)
        $marksAll = $this->mark_m->get_order_by_mark_new([
            'examID'       => $examID,
            'classesID'    => $classesID,
            'schoolyearID' => $schoolyearID
        ]);

        // Map marks to [studentID][subjectID] and collect canonical markIDs
        $marksMap = [];
        $canonicalMarkIDs = [];
        foreach ($marksAll as $m) {
            $marksMap[$m->studentID][$m->subjectID] = $m;
            $canonicalMarkIDs[] = $m->markID;
        }

        // Fetch markrelations ONLY for canonical markIDs (same approach as web version)
        // This avoids duplicate mark rows overwriting correct values
        $markRelationsMap = [];
        if (!empty($canonicalMarkIDs)) {
            $this->db->where_in('markID', $canonicalMarkIDs);
            $markRelations = $this->db->get('markrelation')->result();

            // Build [markID][markpercentageID] => mark first
            $relationsByMarkID = [];
            foreach ($markRelations as $mr) {
                $relationsByMarkID[$mr->markID][$mr->markpercentageID] = $mr->mark;
            }

            // Then map to [studentID][subjectID][markpercentageID] using canonical mark records
            foreach ($marksAll as $m) {
                if (isset($relationsByMarkID[$m->markID])) {
                    foreach ($relationsByMarkID[$m->markID] as $mpID => $markVal) {
                        $markRelationsMap[$m->studentID][$m->subjectID][$mpID] = $markVal;
                    }
                }
            }
        }

        $studentData = [];
        foreach ($students as $student) {
            $totalObtained = 0;
            $totalMax = 0;
            $isFail = false;
            $zeroMarkCount = 0;

            $studentMarks = [];
            foreach ($subjects as $subject) {
                // If subjectID is provided, only include that subject
                if($subjectID > 0 && $subject->subjectID != $subjectID) continue;

                $subjectMarks = [];
                $subjectTotal = 0;
                $markID = 0;
                $eattendance = 'Present';

                if (isset($marksMap[$student->studentID][$subject->subjectID])) {
                    $markRecord = $marksMap[$student->studentID][$subject->subjectID];
                    $markID = $markRecord->markID;
                    $eattendance = $markRecord->eattendance ?: 'Present';
                }

                foreach ($markSettings as $setting) {
                    $val = isset($markRelationsMap[$student->studentID][$subject->subjectID][$setting->markpercentageID])
                           ? (float)$markRelationsMap[$student->studentID][$subject->subjectID][$setting->markpercentageID]
                           : 0.0;

                    $subjectMarks[] = [
                        'markpercentageID' => $setting->markpercentageID,
                        'markpercentage'   => $setting->markpercentage,
                        'mark'             => $val,
                        'markID'           => $markID // Needed for the 'mark' name in mark_send: {subjectID}mark-{markID}
                    ];
                    $subjectTotal  += $val;
                    $totalObtained += $val;
                    if ($val == 0) $zeroMarkCount++;
                }

                $studentMarks[] = [
                    'subjectID'   => $subject->subjectID,
                    'subject'     => $subject->subject,
                    'marks'       => $subjectMarks,
                    'total_marks' => $subjectTotal, // This is marks for THIS subject
                    'max_mark'    => (float)$subject->max_mark,
                    'min_mark'    => (float)$subject->min_mark,
                    'eattendance' => $eattendance
                ];

                // Note: Rank/Grade usually based on ALL subjects.
                // We'll return full list of subjects unless filtered.
            }

            $currentStudentResult = [
                'studentID'   => $student->studentID,
                'name'        => $student->name,
                'roll'        => $student->roll,
                'photo'       => $student->photo,
                'subjects'    => $studentMarks,
                'total_marks' => $totalObtained, // This will be total of all loaded subjects
                'grade'       => '-',
                'rank'        => '-' 
            ];
            $studentData[] = $currentStudentResult;
        }

        $this->response([
            'status'  => true,
            'message' => 'Success',
            'data'    => [
                'students' => $studentData,
                'subjects' => $subjects,
                'markpercentages' => $markSettings
            ]
        ], REST_Controller::HTTP_OK);
    }

    public function mark_send_post()
    {
        $examID      = $this->post("examID");
        $classesID   = $this->post("classesID");
        $subjectID   = $this->post("subjectID");
        $studentID   = $this->post("studentID");
        $inputs      = $this->post("inputs");
        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        if (!$examID || !$classesID || !$subjectID || !customCompute($inputs)) {
            $this->response([
                'status'  => false,
                'message' => 'Missing required parameters',
                'data'    => []
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        foreach ($inputs as $key => $value) {
            // Expected input structure: [mark: "1mark-123", markpercentageid: 1, value: 25]
            $data = explode('-', $value['mark']);
            if (count($data) != 2) continue;

            $markIDFromName = $data[1];
            $markpercentageID = $value['markpercentageid'];
            $markValue = (float)$value['value'];

            if ($markIDFromName == '0') {
                $extractedSubjectID = str_replace('mark', '', $data[0]);
                if (!$studentID || $studentID == '0') continue;

                $this->db->where([
                    'examID'       => $examID,
                    'classesID'    => $classesID,
                    'subjectID'    => $extractedSubjectID,
                    'studentID'    => $studentID,
                    'schoolyearID' => $schoolyearID
                ]);
                $existingMarkQuery = $this->db->get('mark');

                if ($existingMarkQuery->num_rows() > 0) {
                    $markID = $existingMarkQuery->row()->markID;
                } else {
                    $exam = $this->exam_m->get_single_exam(['examID' => $examID]);
                    $subject = $this->subject_m->get_single_subject(['subjectID' => $extractedSubjectID]);
                    
                    $markData = [
                        'examID'       => $examID,
                        'classesID'    => $classesID,
                        'subjectID'    => $extractedSubjectID,
                        'studentID'    => $studentID,
                        'schoolyearID' => $schoolyearID,
                        'create_date'  => date('Y-m-d H:i:s'),
                        'create_userID' => $this->session->userdata('loginuserID'),
                        'create_usertypeID' => $this->session->userdata('usertypeID'),
                        'year'         => date('Y'),
                        'exam'         => $exam->exam ?? '',
                        'subject'      => $subject->subject ?? '',
                        'eattendance'  => NULL
                    ];
                    $this->db->insert('mark', $markData);
                    $markID = $this->db->insert_id();
                }
            } else {
                $markID = $markIDFromName;
            }

            // Record exists?
            $this->db->where(['markpercentageID' => $markpercentageID, 'markID' => $markID]);
            $query = $this->db->get('markrelation');

            if ($query->num_rows() > 0) {
                $this->db->where(['markpercentageID' => $markpercentageID, 'markID' => $markID]);
                $this->db->update('markrelation', ['mark' => $markValue]);
            } else {
                $this->db->insert('markrelation', [
                    'markpercentageID' => $markpercentageID,
                    'markID'           => $markID,
                    'mark'             => $markValue,
                ]);
            }
        }

        $this->response([
            'status'  => true,
            'message' => 'Marks updated successfully',
            'data'    => []
        ], REST_Controller::HTTP_OK);
    }

    public function saveIndividualAttendance_post()
    {
        $attendance = $this->post('attendance');
        $examID     = $this->post('examID');
        $classesID  = $this->post('classesID');
        $sectionID  = $this->post('sectionID');
        $studentID  = $this->post('studentID');

        if (!$attendance || !$examID || !$classesID || !$sectionID || !$studentID) {
            $this->response(['status' => false, 'message' => 'Missing parameters'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Logical implementation for saving individual attendance
        // (Simplified mirror of Mark::saveIndividualAttendance)
        $this->load->model('examattendance_m');
        $array = [
            "examID"       => $examID,
            "classesID"    => $classesID,
            "sectionID"    => $sectionID,
            "studentID"    => $studentID,
            "schoolyearID" => $this->session->userdata('defaultschoolyearID')
        ];

        // Check if attendance already exists
        $attendanceRecord = $this->examattendance_m->get_single_examattendance($array);
        if (customCompute($attendanceRecord)) {
            $this->examattendance_m->update_examattendance(['attendance' => $attendance], $attendanceRecord->examattendanceID);
        } else {
            $array['attendance'] = $attendance;
            $this->examattendance_m->insert_examattendance($array);
        }

        $this->response(['status' => true, 'message' => 'Attendance saved successfully'], REST_Controller::HTTP_OK);
    }

    public function saveSubjectAttendance_post()
    {
        $attendance = $this->post('attendance');
        $examID     = $this->post('examID');
        $classesID  = $this->post('classesID');
        $sectionID  = $this->post('sectionID');
        $studentID  = $this->post('studentID');
        $subjectID  = $this->post('subjectID');

        if (!$attendance || !$examID || !$classesID || !$sectionID || !$studentID || !$subjectID) {
            $this->response(['status' => false, 'message' => 'Missing parameters'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Logical implementation for subject-specific attendance
        // (Simplified mirror of Mark::saveSubjectAttendance)
        $this->load->model('mark_m');
        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        $this->db->where([
            'examID'       => $examID,
            'classesID'    => $classesID,
            'subjectID'    => $subjectID,
            'studentID'    => $studentID,
            'schoolyearID' => $schoolyearID
        ]);
        $markRecord = $this->db->get('mark')->row();

        if (customCompute($markRecord)) {
            $this->db->where('markID', $markRecord->markID);
            $this->db->update('mark', ['eattendance' => $attendance]);
        } else {
            // Create record if not exists
            $exam = $this->exam_m->get_single_exam(['examID' => $examID]);
            $subject = $this->subject_m->get_single_subject(['subjectID' => $subjectID]);
            
            $markData = [
                'examID'       => $examID,
                'classesID'    => $classesID,
                'subjectID'    => $subjectID,
                'studentID'    => $studentID,
                'schoolyearID' => $schoolyearID,
                'create_date'  => date('Y-m-d H:i:s'),
                'create_userID' => $this->session->userdata('loginuserID'),
                'create_usertypeID' => $this->session->userdata('usertypeID'),
                'year'         => date('Y'),
                'exam'         => $exam->exam ?? '',
                'subject'      => $subject->subject ?? '',
                'eattendance'  => $attendance
            ];
            $this->db->insert('mark', $markData);
        }

        $this->response(['status' => true, 'message' => 'Subject attendance saved successfully'], REST_Controller::HTTP_OK);
    }

    public function saveAllAttendance_post()
    {
        $attendance = $this->post('attendance');
        $examID     = $this->post('examID');
        $classesID  = $this->post('classesID');
        $sectionID  = $this->post('sectionID');
        $studentID  = $this->post('studentID');

        if (!$attendance || !$examID || !$classesID || !$sectionID || !$studentID) {
            $this->response(['status' => false, 'message' => 'Missing parameters'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Logic for saving all subject attendance for a student
        // (Mirror of Mark::saveAllAttendance)
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $subjects = $this->subject_m->general_get_order_by_subject(['classesID' => $classesID]);

        if (customCompute($subjects)) {
            foreach ($subjects as $subject) {
                // Update/Insert logic for each subject
                $this->db->where([
                    'examID'       => $examID,
                    'classesID'    => $classesID,
                    'subjectID'    => $subject->subjectID,
                    'studentID'    => $studentID,
                    'schoolyearID' => $schoolyearID
                ]);
                $markRecord = $this->db->get('mark')->row();

                if (customCompute($markRecord)) {
                    $this->db->where('markID', $markRecord->markID);
                    $this->db->update('mark', ['eattendance' => $attendance]);
                } else {
                    $examData = $this->exam_m->get_single_exam(['examID' => $examID]);
                    $subjectData = $this->subject_m->get_single_subject(['subjectID' => $subject->subjectID]);
                    
                    $markData = [
                        'examID'       => $examID,
                        'classesID'    => $classesID,
                        'subjectID'    => $subject->subjectID,
                        'studentID'    => $studentID,
                        'schoolyearID' => $schoolyearID,
                        'create_date'  => date('Y-m-d H:i:s'),
                        'create_userID' => $this->session->userdata('loginuserID'),
                        'create_usertypeID' => $this->session->userdata('usertypeID'),
                        'year'         => date('Y'),
                        'exam'         => $examData->exam ?? '',
                        'subject'      => $subjectData->subject ?? '',
                        'eattendance'  => $attendance
                    ];
                    $this->db->insert('mark', $markData);
                }
            }
        }

        $this->response(['status' => true, 'message' => 'Overall attendance saved successfully'], REST_Controller::HTTP_OK);
    }
}

/**
 * API Endpoints & Sample Payloads (cURL):
 * 
 * 1. Get Class/Student List (Teacher/Admin View)
 *    URL Pattern: api/v10/mark/index/<classID>
 *    curl --location --request GET 'http://localhost/ourschoolerp/api/v10/mark/index/1' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 2. Get Individual Student Marks
 *    URL Pattern: api/v10/mark/view/<studentID>/<classID>
 *    curl --location --request GET 'http://localhost/ourschoolerp/api/v10/mark/view/10/1' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 3. Load Marks Entry Data (Bulk Fetch)
 *    URL: api/v10/mark/load_marks
 *    Inputs: { "examID": "1", "classesID": "1", "sectionID": "1", "subjectID": "0" }
 *    curl --location --request POST 'http://localhost/ourschoolerp/api/v10/mark/load_marks' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --header 'Content-Type: application/json' \
 *    --data '{ "examID": "1", "classesID": "1", "sectionID": "1" }'
 * 
 * 4. Save/Update Marks (Individual Subject for Student)
 *    URL: api/v10/mark/mark_send
 *    Required Body Parameters:
 *    - examID (string): Exam ID
 *    - classesID (string): Class ID
 *    - subjectID (string): Subject ID being updated
 *    - studentID (string): Student ID being updated
 *    - inputs (array): List of mark components
 *    
 *    Payload Pattern Details:
 *    - mark: Must be formatted as "{subjectID}mark-{markID}". If markID is 0 from load_marks, use 0.
 *    - markpercentageid: The ID for the component (e.g., Written=1, Viva=2) as received from load_marks response.
 *    - value: The score achieved.
 * 
 *    curl --location --request POST 'http://localhost/ourschoolerp/api/v10/mark/mark_send' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --header 'Content-Type: application/json' \
 *    --data '{
 *        "examID": "1",
 *        "classesID": "1",
 *        "subjectID": "10",
 *        "studentID": "456",
 *        "inputs": [
 *            {
 *                "mark": "10mark-123",
 *                "markpercentageid": 1,
 *                "value": 25.5
 *            },
 *            {
 *                "mark": "10mark-0",
 *                "markpercentageid": 2,
 *                "value": 10
 *            }
 *        ]
 *    }'
 * 
 * 5. Save Individual Attendance
 *    URL: api/v10/mark/saveIndividualAttendance
 *    Inputs: { "attendance": "Absent", "examID": "1", "classesID": "5", "sectionID": "2", "studentID": "456" }
 *    curl --location --request POST 'http://localhost/ourschoolerp/api/v10/mark/saveIndividualAttendance' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --header 'Content-Type: application/json' \
 *    --data '{ "attendance": "Absent", "examID": "1", "classesID": "5", "sectionID": "2", "studentID": "456" }'
 * 
 * 6. Save Subject Attendance
 *    URL: api/v10/mark/saveSubjectAttendance
 *    Inputs: { "attendance": "Absent", "examID": "1", "classesID": "1", "sectionID": "1", "studentID": "456", "subjectID": "10" }
 *    curl --location --request POST 'http://localhost/ourschoolerp/api/v10/mark/saveSubjectAttendance' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --header 'Content-Type: application/json' \
 *    --data '{ "attendance": "Absent", "examID": "1", "classesID": "1", "sectionID": "1", "studentID": "456", "subjectID": "10" }'
 * 
 * 7. Save All Subject Attendance (Overall)
 *    URL: api/v10/mark/saveAllAttendance
 *    Inputs: { "attendance": "Absent", "examID": "1", "classesID": "1", "sectionID": "1", "studentID": "456" }
 *    curl --location --request POST 'http://localhost/ourschoolerp/api/v10/mark/saveAllAttendance' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --header 'Content-Type: application/json' \
 *    --data '{ "attendance": "Absent", "examID": "1", "classesID": "1", "sectionID": "1", "studentID": "456" }'
 */
