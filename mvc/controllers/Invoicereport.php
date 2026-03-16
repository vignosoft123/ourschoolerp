<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Invoicereport extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('classes_m');
        $this->load->model('section_m');
        $this->load->model('feetypes_m');
        $this->load->model('studentrelation_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('invoicereport', $language);
    }

    public function index()
    {
        $this->data['headerassets'] = [
            'css' => [
                'assets/select2/css/select2.css',
                'assets/select2/css/select2-bootstrap.css',
            ],
            'js' => [
                'assets/select2/select2.js',
            ],
        ];

        $this->data['classes']   = $this->classes_m->general_get_classes();
        $this->data['feetypes']  = $this->feetypes_m->get_feetypes();
        $this->data['subview']   = 'report/invoicereport/InvoicereportView';
        $this->load->view('_layout_main', $this->data);
    }

    // --------------- AJAX cascading dropdowns ---------------

    public function getSection()
    {
        $classesID = $this->input->post('classesID');
        if ((int)$classesID) {
            echo "<option value='0'>" . $this->lang->line('invoicereport_please_select') . "</option>";
            $sections = $this->section_m->general_get_order_by_section(['classesID' => $classesID]);
            if (customCompute($sections)) {
                foreach ($sections as $s) {
                    echo "<option value='{$s->sectionID}'>{$s->section}</option>";
                }
            }
        }
    }

    public function getStudent()
    {
        $classesID    = $this->input->post('classesID');
        $sectionID    = $this->input->post('sectionID');
        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        if ((int)$classesID && (int)$sectionID) {
            echo "<option value='0'>" . $this->lang->line('invoicereport_please_select') . "</option>";
            $students = $this->studentrelation_m->general_get_order_by_student([
                'srclassesID'    => $classesID,
                'srsectionID'    => $sectionID,
                'srschoolyearID' => $schoolyearID,
            ]);
            if (customCompute($students)) {
                foreach ($students as $s) {
                    echo "<option value='{$s->srstudentID}'>{$s->srname}</option>";
                }
            }
        }
    }

    // --------------- Main report AJAX ---------------

    public function getInvoiceReport()
    {
        $retArray = ['status' => FALSE, 'render' => ''];

        if (!$_POST) {
            echo json_encode($retArray);
            exit;
        }

        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $classesID    = (int)$this->input->post('classesID');
        $sectionID    = (int)$this->input->post('sectionID');
        $studentID    = (int)$this->input->post('studentID');
        $feetypeID    = (int)$this->input->post('feetypeID');

        // ── Query 1: All students matching filters (regardless of invoices) ──
        $stuSql    = "SELECT sr.srstudentID, sr.srname, sr.srclassesID, sr.srsectionID
                      FROM studentrelation sr
                      WHERE sr.srschoolyearID = ?";
        $stuParams = [$schoolyearID];
        if ($classesID) { $stuSql .= " AND sr.srclassesID = ?"; $stuParams[] = $classesID; }
        if ($sectionID) { $stuSql .= " AND sr.srsectionID = ?"; $stuParams[] = $sectionID; }
        if ($studentID) { $stuSql .= " AND sr.srstudentID = ?"; $stuParams[] = $studentID; }
        $stuSql .= " ORDER BY sr.srclassesID, sr.srsectionID, sr.srname";

        $students = [];
        foreach ($this->db->query($stuSql, $stuParams)->result() as $r) {
            $students[$r->srstudentID] = $r;
        }

        // ── Query 2: Invoice amount + discount + paid per student × fee type ──
        //
        // IMPORTANT: payment is pre-aggregated in a subquery (pagg) BEFORE joining.
        // Direct LEFT JOIN payment ON invoiceID would multiply invoice rows by the
        // number of payment entries, inflating SUM(i.amount) on every new payment.
        $pivotSql = "
            SELECT
                sr.srstudentID,
                i.feetypeID,
                ft.feetypes,
                SUM(i.amount)                        AS total_amount,
                SUM(COALESCE(i.discount, 0))         AS total_discount,
                COALESCE(SUM(pagg.paid_sum), 0)      AS total_paid
            FROM studentrelation sr
            JOIN maininvoice mi
                ON  mi.maininvoicestudentID   = sr.srstudentID
                AND mi.maininvoiceschoolyearID = sr.srschoolyearID
                AND mi.maininvoicedeleted_at   = 1
            JOIN invoice i
                ON  i.maininvoiceID = mi.maininvoiceID
                AND i.studentID     = sr.srstudentID
            JOIN feetypes ft ON ft.feetypesID = i.feetypeID
            LEFT JOIN (
                SELECT invoiceID, SUM(paymentamount) AS paid_sum
                FROM payment
                GROUP BY invoiceID
            ) pagg ON pagg.invoiceID = i.invoiceID
            WHERE sr.srschoolyearID = ?
        ";
        $pivotParams = [$schoolyearID];
        if ($classesID) { $pivotSql .= " AND sr.srclassesID = ?"; $pivotParams[] = $classesID; }
        if ($sectionID) { $pivotSql .= " AND sr.srsectionID = ?"; $pivotParams[] = $sectionID; }
        if ($studentID) { $pivotSql .= " AND sr.srstudentID = ?"; $pivotParams[] = $studentID; }
        if ($feetypeID) { $pivotSql .= " AND i.feetypeID = ?";    $pivotParams[] = $feetypeID; }
        $pivotSql .= " GROUP BY sr.srstudentID, i.feetypeID ORDER BY ft.feetypes";

        $feetypesList = [];  // [feetypeID] => name
        $pivot        = [];  // [srstudentID][feetypeID] => ['amount'=>x, 'discount'=>y, 'paid'=>z]

        foreach ($this->db->query($pivotSql, $pivotParams)->result() as $row) {
            if (!isset($feetypesList[$row->feetypeID])) {
                $feetypesList[$row->feetypeID] = $row->feetypes;
            }
            $pivot[$row->srstudentID][$row->feetypeID] = [
                'amount'   => (float)$row->total_amount,
                'discount' => (float)$row->total_discount,
                'paid'     => (float)$row->total_paid,
            ];
        }

        $this->data['classes']      = pluck($this->classes_m->general_get_classes(), 'classes', 'classesID');
        $this->data['sections']     = pluck($this->section_m->general_get_section(), 'section', 'sectionID');
        $this->data['students']     = $students;
        $this->data['feetypesList'] = $feetypesList;
        $this->data['pivot']        = $pivot;

        $retArray['render'] = $this->load->view('report/invoicereport/InvoicereportReport', $this->data, true);
        $retArray['status'] = TRUE;
        echo json_encode($retArray);
        exit;
    }
}
