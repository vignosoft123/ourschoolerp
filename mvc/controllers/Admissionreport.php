<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admissionreport extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('classes_m');
        $this->load->model('schoolyear_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('admissionreport', $language);
    }

    public function index() {
        $this->data['headerassets'] = [
            'css' => [
                'assets/datepicker/datepicker.css',
                'assets/select2/css/select2.css',
                'assets/select2/css/select2-bootstrap.css',
            ],
            'js' => [
                'assets/datepicker/datepicker.js',
                'assets/select2/select2.js',
            ],
        ];
        $this->data['classes']     = $this->classes_m->general_get_classes();
        $this->data['schoolyears'] = $this->schoolyear_m->get_schoolyear();
        $this->data['subview']     = 'report/admission/AdmissionReportView';
        $this->load->view('_layout_main', $this->data);
    }

    public function getAdmissionReport() {
        $retArray = ['status' => false, 'render' => ''];

        if (permissionChecker('studentreport')) {
            if ($_POST) {
                $schoolyearID = (int)$this->input->post('schoolyearID');
                $classesID    = (int)$this->input->post('classesID');
                $fromdate     = $this->input->post('fromdate');
                $todate       = $this->input->post('todate');

                $this->db->select(
                    'sy.schoolyearID, sy.schoolyear AS academic_year,
                     c.classesID, c.classes AS class_name, c.classes_numeric,
                     COUNT(sr.srstudentID) AS total,
                     SUM(CASE WHEN s.sex = "Male" THEN 1 ELSE 0 END) AS boys,
                     SUM(CASE WHEN s.sex = "Female" THEN 1 ELSE 0 END) AS girls',
                    false
                );
                $this->db->from('studentrelation sr');
                $this->db->join('schoolyear sy', 'sy.schoolyearID = sr.srschoolyearID');
                $this->db->join('classes c', 'c.classesID = sr.srclassesID');
                $this->db->join('student s', 's.studentID = sr.srstudentID');
                $this->db->where('s.active', 1);

                if ($schoolyearID > 0) {
                    $this->db->where('sr.srschoolyearID', $schoolyearID);
                }
                if ($classesID > 0) {
                    $this->db->where('sr.srclassesID', $classesID);
                }
                if (!empty($fromdate)) {
                    $p = explode('-', $fromdate);
                    if (count($p) === 3) {
                        $this->db->where('s.admission_date >=', $p[2].'-'.$p[1].'-'.$p[0]);
                    }
                }
                if (!empty($todate)) {
                    $p = explode('-', $todate);
                    if (count($p) === 3) {
                        $this->db->where('s.admission_date <=', $p[2].'-'.$p[1].'-'.$p[0]);
                    }
                }

                $this->db->group_by('sr.srschoolyearID, sr.srclassesID');
                $this->db->order_by('sy.schoolyearID ASC, c.classes_numeric ASC');
                $rows = $this->db->get()->result();

                // Build growth: compare each class-year combo to previous year in result set
                $yearIDs  = [];
                $countMap = [];
                foreach ($rows as $row) {
                    if (!in_array($row->schoolyearID, $yearIDs)) {
                        $yearIDs[] = $row->schoolyearID;
                    }
                    $countMap[$row->classesID][$row->schoolyearID] = (int)$row->total;
                }
                sort($yearIDs);
                $yearPos = array_flip($yearIDs);

                foreach ($rows as $row) {
                    $pos       = isset($yearPos[$row->schoolyearID]) ? $yearPos[$row->schoolyearID] : 0;
                    $row->growth = null;
                    for ($i = $pos - 1; $i >= 0; $i--) {
                        $prevID = $yearIDs[$i];
                        if (isset($countMap[$row->classesID][$prevID])) {
                            $prev = $countMap[$row->classesID][$prevID];
                            $row->growth = ($prev > 0)
                                ? round((($row->total - $prev) / $prev) * 100, 1)
                                : null;
                            break;
                        }
                    }
                }

                $totalStudents = 0;
                $totalBoys     = 0;
                $totalGirls    = 0;
                foreach ($rows as $row) {
                    $totalStudents += $row->total;
                    $totalBoys    += $row->boys;
                    $totalGirls   += $row->girls;
                }

                $this->data['rows']          = $rows;
                $this->data['totalStudents'] = $totalStudents;
                $this->data['totalBoys']     = $totalBoys;
                $this->data['totalGirls']    = $totalGirls;

                $retArray['render'] = $this->load->view('report/admission/AdmissionReport', $this->data, true);
                $retArray['status'] = true;
            }
        } else {
            $retArray['render'] = $this->load->view('report/reporterror', $this->data, true);
            $retArray['status'] = true;
        }
        echo json_encode($retArray);
        exit;
    }

    public function getStudentsForRow() {
        $schoolyearID = (int)$this->input->post('schoolyearID');
        $classesID    = (int)$this->input->post('classesID');
        $fromdate     = $this->input->post('fromdate');
        $todate       = $this->input->post('todate');

        if (!$schoolyearID || !$classesID) {
            echo json_encode(['status' => false]);
            exit;
        }

        $this->db->select('sr.srname, sr.srregisterNO, sr.srroll, s.sex, s.photo, s.admission_date');
        $this->db->from('studentrelation sr');
        $this->db->join('student s', 's.studentID = sr.srstudentID');
        $this->db->where('sr.srschoolyearID', $schoolyearID);
        $this->db->where('sr.srclassesID', $classesID);
        $this->db->where('s.active', 1);

        if (!empty($fromdate)) {
            $p = explode('-', $fromdate);
            if (count($p) === 3) {
                $this->db->where('s.admission_date >=', $p[2].'-'.$p[1].'-'.$p[0]);
            }
        }
        if (!empty($todate)) {
            $p = explode('-', $todate);
            if (count($p) === 3) {
                $this->db->where('s.admission_date <=', $p[2].'-'.$p[1].'-'.$p[0]);
            }
        }

        $this->db->order_by('sr.srroll ASC');
        $students = $this->db->get()->result();

        $boys = []; $girls = []; $others = [];
        foreach ($students as $s) {
            $s->admission_date = (!empty($s->admission_date) && $s->admission_date !== '0000-00-00')
                ? date('d-M-Y', strtotime($s->admission_date))
                : '—';
            $s->photo_url = (file_exists(FCPATH.'uploads/images/'.$s->photo) && $s->photo)
                ? base_url('uploads/images/'.$s->photo)
                : base_url('uploads/images/default.png');

            if ($s->sex === 'Male') {
                $boys[] = $s;
            } elseif ($s->sex === 'Female') {
                $girls[] = $s;
            } else {
                $others[] = $s;
            }
        }

        echo json_encode([
            'status' => true,
            'boys'   => $boys,
            'girls'  => $girls,
            'others' => $others,
            'total'  => count($students),
        ]);
        exit;
    }
}
