<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Studycertificatereport extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('classes_m');
        $this->load->model('section_m');
        $this->load->model('studentrelation_m');
        $this->load->model('parents_m');
        $language = $this->session->userdata('lang');
        // Reuse admitcardreport language if present; not mandatory for labels below
        $this->lang->load('admitcardreport', $language);
    }

    protected function rules() {
        $rules = array(
            array(
                'field' => 'classesID',
                'label' => 'Class',
                'rules' => 'trim|required|xss_clean|numeric|callback_unique_data'
            ),
            array(
                'field' => 'sectionID',
                'label' => 'Section',
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'studentID',
                'label' => 'Student',
                'rules' => 'trim|xss_clean'
            ),
        );
        return $rules;
    }

    public function unique_data($data) {
        if ($data !== '') {
            if ($data === '0') {
                $this->form_validation->set_message('unique_data', 'The %s field is required.');
                return FALSE;
            }
            return TRUE;
        }
        return TRUE;
    }

    private function queryArray($posts) {
        $classesID = isset($posts['classesID']) ? $posts['classesID'] : 0;
        $sectionID = isset($posts['sectionID']) ? $posts['sectionID'] : 0;
        $studentID = isset($posts['studentID']) ? $posts['studentID'] : 0;
        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        $queryArray = array();
        $queryArray['srschoolyearID'] = $schoolyearID;

        if ((int)$classesID > 0) {
            $queryArray['srclassesID'] = $classesID;
        }
        if ((int)$sectionID > 0) {
            $queryArray['srsectionID'] = $sectionID;
        }
        if ((int)$studentID > 0) {
            $queryArray['srstudentID'] = $studentID;
        }

        return $queryArray;
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
        $this->data['classes'] = $this->classes_m->general_get_classes();
        $this->data['subview'] = 'report/studycertificatereport/StudyCertificateReportView';
        $this->load->view('_layout_main', $this->data);
    }

    public function getSection() {
        $classesID = $this->input->post('classesID');
        if ((int)$classesID) {
            $sections = $this->section_m->general_get_order_by_section(array('classesID' => $classesID));
            echo "<option value='0'>" . $this->lang->line('admitcardreport_please_select') . "</option>";
            if (customCompute($sections)) {
                foreach ($sections as $section) {
                    echo "<option value='" . $section->sectionID . "'>" . $section->section . "</option>";
                }
            }
        }
    }

    public function getStudent() {
        $classesID = $this->input->post('classesID');
        $sectionID = $this->input->post('sectionID');
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        if ((int)$classesID && (int)$sectionID) {
            $students = $this->studentrelation_m->general_get_order_by_student(array(
                'srclassesID' => $classesID,
                'srsectionID' => $sectionID,
                'srschoolyearID' => $schoolyearID
            ));
            echo "<option value='0'>" . $this->lang->line('admitcardreport_please_select') . "</option>";
            if (customCompute($students)) {
                foreach ($students as $student) {
                    echo "<option value='" . $student->srstudentID . "'>" . $student->srname . ' (Roll No: ' . $student->roll . ")</option>";
                }
            }
        }
    }

    public function getStudyCertificateReport() {
        $retArray = array('status' => FALSE, 'render' => '');

        if (!permissionChecker('admitcardreport')) { // Reuse similar permission gate
            $retArray['render'] = $this->load->view('report/reporterror', $this->data, true);
            $retArray['status'] = TRUE;
            echo json_encode($retArray);
            exit;
        }

        if ($_POST) {
            $rules = $this->rules();
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $retArray = $this->form_validation->error_array();
                $retArray['status'] = FALSE;
                echo json_encode($retArray);
                exit;
            } else {
                $queryArray = $this->queryArray($this->input->post());

                // Support multi-selection of students
                $studentIDs = $this->input->post('studentID');
                if (is_array($studentIDs)) {
                    $studentIDs = array_filter($studentIDs, function ($v) {
                        return $v !== "0" && $v !== null && $v !== '';
                    });
                    if (!empty($studentIDs)) {
                        $queryArray['srstudentID'] = $studentIDs;
                    }
                }

                if (method_exists($this->studentrelation_m, 'general_get_order_by_student_multi_selction')) {
                    $students = $this->studentrelation_m->general_get_order_by_student_multi_selction($queryArray);
                } else {
                    // Fallback to standard method if multi-selection variant is unavailable
                    $students = $this->studentrelation_m->general_get_order_by_student($queryArray);
                }

                // Collect IDs for enrichment
                $ids = array();
                $classIDs = array();
                $parentIDs = array();
                if (customCompute($students)) {
                    foreach ($students as $st) {
                        $sid = isset($st->srstudentID) ? $st->srstudentID : (isset($st->studentID) ? $st->studentID : 0);
                        if ($sid) $ids[] = (int)$sid;
                        $cid = isset($st->srclassesID) ? $st->srclassesID : (isset($st->classesID) ? $st->classesID : 0);
                        if ($cid) $classIDs[] = (int)$cid;
                        if (isset($st->parentID) && (int)$st->parentID) $parentIDs[] = (int)$st->parentID;
                    }
                }

                // Pull mediums and parentIDs from student table for selected IDs
                $mediumByStudentID = array();
                $parentByStudentID = array();
                if (!empty($ids)) {
                    $this->db->select('studentID, medium, parentID');
                    $this->db->from('student');
                    $this->db->where_in('studentID', $ids);
                    $studentRows = $this->db->get()->result();
                    foreach ($studentRows as $row) {
                        $mediumByStudentID[(int)$row->studentID] = $row->medium;
                        $parentByStudentID[(int)$row->studentID] = (int)$row->parentID;
                        if ((int)$row->parentID) $parentIDs[] = (int)$row->parentID;
                    }
                }

                // Unique parent IDs then fetch names
                $parentIDs = array_values(array_unique($parentIDs));
                $parentNameByID = array();
                if (!empty($parentIDs)) {
                    $this->db->select('parentsID, father_name');
                    $this->db->from('parents');
                    $this->db->where_in('parentsID', $parentIDs);
                    $parentRows = $this->db->get()->result();
                    foreach ($parentRows as $prow) {
                        $parentNameByID[(int)$prow->parentsID] = $prow->father_name;
                    }
                }
                // Map studentID -> parent name
                $parentNameByStudentID = array();
                foreach ($parentByStudentID as $sid => $pid) {
                    $parentNameByStudentID[$sid] = isset($parentNameByID[$pid]) ? $parentNameByID[$pid] : '';
                }

                // Class names
                $classesMap = array();
                $classes = $this->classes_m->general_get_classes();
                if (customCompute($classes)) {
                    foreach ($classes as $c) { $classesMap[(int)$c->classesID] = $c->classes; }
                }
                $classNameByStudentID = array();
                if (customCompute($students)) {
                    foreach ($students as $st) {
                        $sid = isset($st->srstudentID) ? (int)$st->srstudentID : (isset($st->studentID) ? (int)$st->studentID : 0);
                        $cid = isset($st->srclassesID) ? (int)$st->srclassesID : (isset($st->classesID) ? (int)$st->classesID : 0);
                        $name = isset($st->srclasses) && $st->srclasses ? $st->srclasses : (isset($classesMap[$cid]) ? $classesMap[$cid] : '');
                        if ($sid) $classNameByStudentID[$sid] = $name;
                    }
                }

                // Receive custom inputs
                $yearsText   = $this->input->post('years_text');
                $conductText = $this->input->post('conduct_text');
                $dateText    = $this->input->post('date_text');

                $this->data['students']                 = $students;
                $this->data['parentNameByStudentID']    = $parentNameByStudentID;
                $this->data['mediumByStudentID']        = $mediumByStudentID;
                $this->data['classNameByStudentID']     = $classNameByStudentID;
                $this->data['yearsText']                = $yearsText;
                $this->data['conductText']              = $conductText;
                $this->data['dateText']                 = $dateText;

                $retArray['render'] = $this->load->view('report/studycertificatereport/StudyCertificateReport', $this->data, true);
                $retArray['status'] = TRUE;
                echo json_encode($retArray);
                exit;
            }
        } else {
            $retArray['status'] = FALSE;
            echo json_encode($retArray);
            exit;
        }
    }
}

?>
