<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Global_payment_new extends Admin_Controller
{
    function __construct() {
        parent::__construct();
        $this->load->model("student_m");
        $this->load->model("classes_m");
        $this->load->model("section_m");
        $this->load->model('studentgroup_m');
        $this->load->model('feetypes_m');
        $this->load->model('invoice_m');
        $this->load->model('payment_m');
        $this->load->model('globalpayment_m');
        $this->load->model('weaverandfine_m');
        $this->load->model('maininvoice_m');
        $this->load->model('studentrelation_m');
        $this->load->model('student_carry_forward_m');
        $this->load->model('Whatsapp_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('global_payment_new', $language);
        $this->load->library("msg91");
    }

    protected function rules() {
        return [
            ['field' => 'classesID', 'label' => $this->lang->line("global_classes"),
             'rules' => 'trim|required|xss_clean|max_length[11]|callback_unique_classes'],
            ['field' => 'sectionID', 'label' => $this->lang->line("global_section"),
             'rules' => 'trim|xss_clean|max_length[11]'],
            ['field' => 'studentID', 'label' => $this->lang->line("global_student"),
             'rules' => 'trim|required|xss_clean|max_length[11]|callback_unique_student'],
        ];
    }

    protected function paymentRules() {
        return [
            ['field' => 'studentID',        'label' => $this->lang->line("global_student"),
             'rules' => 'trim|required|xss_clean|numeric|max_length[11]'],
            ['field' => 'classesID',        'label' => $this->lang->line("global_classes"),
             'rules' => 'trim|required|xss_clean|numeric|max_length[11]'],
            ['field' => 'invoicename',      'label' => $this->lang->line("global_invoice_name"),
             'rules' => 'trim|required|xss_clean|max_length[127]'],
            ['field' => 'invoicedescription','label' => $this->lang->line("global_description"),
             'rules' => 'trim|xss_clean|max_length[127]'],
            ['field' => 'invoicenumber',    'label' => $this->lang->line("global_invoice_number"),
             'rules' => 'trim|required|xss_clean|min_length[6]'],
            ['field' => 'paymentyear',      'label' => $this->lang->line("global_payment_year"),
             'rules' => 'trim|required|numeric|xss_clean|max_length[4]'],
            ['field' => 'payment_status',   'label' => $this->lang->line("global_payment_status"),
             'rules' => 'trim|required|xss_clean|max_length[7]'],
            ['field' => 'payment_type',     'label' => $this->lang->line("global_payment_type"),
             'rules' => 'trim|required|xss_clean|max_length[10]'],
        ];
    }

    public function unique_classes() {
        if ($this->input->post('classesID') == 0) {
            $this->form_validation->set_message("unique_classes", "The %s field is required");
            return FALSE;
        }
        return TRUE;
    }

    public function unique_student() {
        if ($this->input->post('studentID') == 0) {
            $this->form_validation->set_message("unique_student", "The %s field is required");
            return FALSE;
        }
        return TRUE;
    }

    // -------------------------------------------------------------------------
    // Carry-forward computation — flat-amount discount (consistent with reports)
    // -------------------------------------------------------------------------
    private function computeCarryForward($studentID, $currentSchoolyearID) {
        // All school years older than current, newest first
        $allYears = $this->db->select('schoolyearID, schoolyear')
                             ->where('schoolyearID <', $currentSchoolyearID)
                             ->order_by('schoolyearID', 'DESC')
                             ->get('schoolyear')->result();

        $prevBalances         = [];
        $totalCarryForwardDue = 0;

        foreach ($allYears as $year) {
            $prevInvoices = $this->invoice_m->get_order_by_invoice_join_maininvoice(
                $studentID, $year->schoolyearID, 1
            );
            if (!customCompute($prevInvoices)) {
                continue;
            }

            $this->payment_m->order_payment('paymentID asc');
            $prevPayments = $this->payment_m->get_order_by_payment([
                'studentID'    => $studentID,
                'schoolyearID' => $year->schoolyearID,
            ]);
            $prevWaivers = $this->weaverandfine_m->get_order_by_weaverandfine([
                'studentID'    => $studentID,
                'schoolyearID' => $year->schoolyearID,
            ]);

            $paymentsByInvoice = $this->generateAllPaymentAmount($prevPayments);
            $waiversByInvoice  = $this->generateAllWeaverAmount($prevWaivers);

            $total_fee      = 0;
            $total_discount = 0;
            $total_paid     = 0;
            $total_waiver   = 0;
            $year_due       = 0;

            foreach ($prevInvoices as $inv) {
                // Flat-amount discount — consistent with Due Fees Report
                $net      = $inv->amount - $inv->discount;
                $paid     = isset($paymentsByInvoice[$inv->invoiceID]) ? $paymentsByInvoice[$inv->invoiceID] : 0;
                $waiver   = isset($waiversByInvoice[$inv->invoiceID])  ? $waiversByInvoice[$inv->invoiceID]  : 0;
                $inv_due  = $net - $paid - $waiver;

                $total_fee      += $net;
                $total_discount += $inv->discount;
                $total_paid     += $paid;
                $total_waiver   += $waiver;
                $year_due       += $inv_due;
            }

            $year_due = round($year_due, 2);
            if ($year_due <= 0) {
                continue; // fully cleared, skip
            }

            $totalCarryForwardDue += $year_due;

            if ($total_paid == 0 && $total_waiver == 0) {
                $cfStatus = 'pending';
            } elseif ($year_due > 0) {
                $cfStatus = 'partial';
            } else {
                $cfStatus = 'cleared';
            }

            // Cache in student_carry_forward table
            $this->student_carry_forward_m->upsert_carry_forward([
                'studentID'          => $studentID,
                'from_schoolyearID'  => $year->schoolyearID,
                'to_schoolyearID'    => $currentSchoolyearID,
                'from_year_name'     => $year->schoolyear,
                'total_fee'          => round($total_fee, 2),
                'total_discount'     => round($total_discount, 2),
                'total_paid_in_year' => round($total_paid, 2),
                'total_waiver'       => round($total_waiver, 2),
                'carry_forward_due'  => $year_due,
                'status'             => $cfStatus,
            ]);

            $prevBalances[] = [
                'schoolyearID' => $year->schoolyearID,
                'year_name'    => $year->schoolyear,
                'total_fee'    => round($total_fee, 2),
                'total_paid'   => round($total_paid, 2),
                'total_waiver' => round($total_waiver, 2),
                'due'          => $year_due,
                'invoices'     => $prevInvoices,
                'payments'     => $paymentsByInvoice,
                'waivers'      => $waiversByInvoice,
            ];
        }

        return [
            'prev_balances'           => $prevBalances,
            'total_carry_forward_due' => round($totalCarryForwardDue, 2),
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers (identical to original Global_payment)
    // -------------------------------------------------------------------------
    private function generateAllPaymentAmount($payments) {
        $returnArray = [];
        if (customCompute($payments)) {
            foreach ($payments as $payment) {
                $returnArray[$payment->invoiceID] = isset($returnArray[$payment->invoiceID])
                    ? $returnArray[$payment->invoiceID] + $payment->paymentamount
                    : $payment->paymentamount;
            }
        }
        return $returnArray;
    }

    private function generateAllPaymentAmountWithGlobalID($payments) {
        $returnArray   = [];
        $weaverandfine = pluck($this->weaverandfine_m->get_weaverandfine(), 'obj', 'paymentID');

        if (customCompute($payments)) {
            $invoiceIDs = array_unique(array_map(function($p) { return $p->invoiceID; }, $payments));
            $this->db->select('invoiceID, feetype')->from('invoice')->where_in('invoiceID', $invoiceIDs);
            $invoiceMap = [];
            foreach ($this->db->get()->result() as $inv) {
                $invoiceMap[$inv->invoiceID] = $inv->feetype;
            }

            foreach ($payments as $payment) {
                $gid = $payment->globalpaymentID;
                $returnArray['paid'][$gid] = isset($returnArray['paid'][$gid])
                    ? $returnArray['paid'][$gid] + $payment->paymentamount
                    : $payment->paymentamount;

                if (isset($weaverandfine[$payment->paymentID])) {
                    $wf = $weaverandfine[$payment->paymentID];
                    $returnArray['weaver'][$gid] = ($returnArray['weaver'][$gid] ?? 0) + $wf->weaver;
                    $returnArray['fine'][$gid]   = ($returnArray['fine'][$gid] ?? 0)   + $wf->fine;
                }

                if (!isset($returnArray['paiddate'][$gid])) {
                    $returnArray['paiddate'][$gid] = $payment->paymentdate;
                }
                $returnArray['invoice_id'][$gid][]       = $invoiceMap[$payment->invoiceID] ?? '';
                $returnArray['paid_per_type'][$gid][]    = $payment->paymentamount;
            }
        }
        return $returnArray;
    }

    private function generateAllWeaverAmount($weaverandfines) {
        $returnArray = [];
        if (customCompute($weaverandfines)) {
            foreach ($weaverandfines as $wf) {
                $returnArray[$wf->invoiceID] = isset($returnArray[$wf->invoiceID])
                    ? $returnArray[$wf->invoiceID] + $wf->weaver
                    : $wf->weaver;
            }
        }
        return $returnArray;
    }

    // -------------------------------------------------------------------------
    // AJAX dropdown helpers
    // -------------------------------------------------------------------------
    public function sectioncall() {
        $id = $this->input->post('id');
        if ((int)$id) {
            $sections = $this->section_m->get_order_by_section(["classesID" => $id]);
            echo "<option value='0'>", $this->lang->line("global_select_section"), "</option>";
            foreach ($sections as $s) {
                echo "<option value=\"{$s->sectionID}\">{$s->section}</option>";
            }
        } else {
            echo "<option value='0'>", $this->lang->line("global_select_section"), "</option>";
        }
    }

    public function studentcall() {
        $classesID    = $this->input->post('classesID');
        $sectionID    = $this->input->post('sectionID');
        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        if ((int)$classesID && (int)$sectionID) {
            $students = $this->studentrelation_m->get_order_by_student([
                'srclassesID' => $classesID, 'srsectionID' => $sectionID, 'srschoolyearID' => $schoolyearID,
            ]);
        } elseif ((int)$classesID) {
            $students = $this->studentrelation_m->get_order_by_student([
                'srclassesID' => $classesID, 'srschoolyearID' => $schoolyearID,
            ]);
        } else {
            echo "<option value='0'>", $this->lang->line("global_select_section"), "</option>";
            return;
        }

        echo "<option value='0'>", $this->lang->line("global_select_student"), "</option>";
        foreach ($students as $s) {
            echo "<option value=\"{$s->srstudentID}\">{$s->srname} - {$this->lang->line('global_roll')} - {$s->srroll}</option>";
        }
    }

    // -------------------------------------------------------------------------
    // Main index — student selector + current-year invoices + carry-forward
    // -------------------------------------------------------------------------
    public function index($classesID = 0, $studentID = 0) {
        $this->data['headerassets'] = [
            'css' => ['assets/datepicker/datepicker.css',
                      'assets/select2/css/select2.css',
                      'assets/select2/css/select2-bootstrap.css'],
            'js'  => ['assets/datepicker/datepicker.js',
                      'assets/select2/select2.js'],
        ];

        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        $this->data['classes']      = $this->classes_m->get_classes();
        $this->data['sections']     = 0;
        $this->data['students']     = [];
        $this->data['globalpayments'] = [];
        $this->data['prev_balances']           = [];
        $this->data['total_carry_forward_due'] = 0;

        $this->data['set_classesID'] = 0;
        $this->data['set_sectionID'] = 0;
        $this->data['set_studentID'] = 0;
        $this->data['set_groupID']   = 0;

        if ($this->input->post('classesID') > 0) {
            $this->data['sections'] = $this->section_m->get_order_by_section(['classesID' => $this->input->post('classesID')]);
            $srFilter = ['srclassesID' => $this->input->post('classesID'), 'srschoolyearID' => $schoolyearID];
            if ($this->input->post('sectionID') > 0) {
                $srFilter['srsectionID'] = $this->input->post('sectionID');
            }
            $this->data['students'] = $this->studentrelation_m->get_order_by_student($srFilter);
        }

        $this->data['single_student'] = [];

        if ($_POST || (!empty($classesID) && !empty($studentID))) {
            $rules = $this->rules();
            $this->form_validation->set_rules($rules);

            $classesID = $this->input->post('classesID') ? $this->input->post('classesID') : $classesID;
            $sectionID = $this->input->post('sectionID');
            $studentID = $this->input->post('studentID') ? $this->input->post('studentID') : $studentID;

            $this->data['set_classesID'] = $classesID;
            $this->data['set_sectionID'] = $sectionID;
            $this->data['set_studentID'] = $studentID;

            $this->data['feetypes']      = pluck($this->feetypes_m->get_feetypes(), 'feetypes', 'feetypesID');
            $this->data['single_student'] = $this->studentrelation_m->get_single_student([
                'srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID,
            ]);

            $this->payment_m->order_payment('paymentID asc');
            $allPaymentList = $this->payment_m->get_order_by_payment([
                'studentID' => $studentID, 'schoolyearID' => $schoolyearID,
            ]);
            $allWeaverList = $this->weaverandfine_m->get_order_by_weaverandfine([
                'studentID' => $studentID, 'schoolyearID' => $schoolyearID,
            ]);

            $this->data['payments']   = $this->generateAllPaymentAmount($allPaymentList);
            $this->data['weavers']    = $this->generateAllWeaverAmount($allWeaverList);
            $this->data['paymenteds'] = $allPaymentList;
            $this->data['weavereds']  = $allWeaverList;

            $this->data['globalpayment_max'] = $this->globalpayment_m->get_max_globalpayment();

            if (customCompute($this->data['single_student'])) {
                $single_student = $this->data['single_student'];

                $this->data['single_classes'] = $this->classes_m->get_single_classes(['classesID' => $single_student->srclassesID]);
                $this->data['single_section'] = $this->section_m->get_single_section(['sectionID' => $single_student->srsectionID]);
                $this->data['single_group']   = $this->studentgroup_m->get_single_studentgroup(['studentgroupID' => $single_student->srstudentgroupID]);

                $this->data['invoices']      = $this->invoice_m->get_order_by_invoice_join_maininvoice($single_student->srstudentID, $schoolyearID, 1);
                $this->data['invoicefeetype'] = pluck($this->data['invoices'], 'feetypeID', 'invoiceID');

                $this->data['globalpayments'] = $this->globalpayment_m->get_order_by_globalpayment([
                    'schoolyearID' => $schoolyearID, 'studentID' => $single_student->srstudentID,
                ]);
                $this->data['paidpayments']   = $this->generateAllPaymentAmountWithGlobalID($allPaymentList);
                $this->data['weaverandfines']  = pluck(
                    $this->weaverandfine_m->get_order_by_weaverandfine(['studentID' => $single_student->srstudentID, 'schoolyearID' => $schoolyearID]),
                    'obj', 'paymentID'
                );

                // Carry-forward computation (new feature)
                $cfResult = $this->computeCarryForward($single_student->srstudentID, $schoolyearID);
                $this->data['prev_balances']           = $cfResult['prev_balances'];
                $this->data['total_carry_forward_due'] = $cfResult['total_carry_forward_due'];

            } else {
                $this->data['single_classes'] = [];
                $this->data['single_section'] = [];
                $this->data['single_group']   = [];
                $this->data['invoices']        = [];
                $this->data['globalpayments']  = [];
                $this->data['prev_balances']           = [];
                $this->data['total_carry_forward_due'] = 0;
            }
        }

        $this->data['subview'] = 'global_payment_new/index';
        $this->load->view('_layout_main', $this->data);
    }

    // -------------------------------------------------------------------------
    // getPaymentDetails — AJAX: returns previous-year payment rows for popup
    // -------------------------------------------------------------------------
    public function getPaymentDetails() {
        if (!permissionChecker('global_payment_new')) {
            echo json_encode(['status' => false]); exit;
        }
        $studentID    = (int) $this->input->post('studentID');
        $schoolyearID = (int) $this->input->post('schoolyearID');
        if (!$studentID || !$schoolyearID) {
            echo json_encode(['status' => false, 'message' => 'Invalid request']); exit;
        }
        $sql = "
            SELECT
                gp.globalpaymentID,
                CONCAT('INV-G-', gp.globalpaymentID) AS invoicenumber,
                i.feetype,
                p.paymentamount,
                i.discount,
                COALESCE(wf.fine,  0) AS fine,
                COALESCE(wf.weaver,0) AS waiver,
                (p.paymentamount + COALESCE(wf.fine, 0)) AS total_collection,
                COALESCE(gp.clearancetype, 'paid') AS payment_status,
                DATE_FORMAT(p.paymentdate, '%d-%b-%Y') AS payment_date
            FROM payment p
            LEFT JOIN invoice       i  ON  i.invoiceID       = p.invoiceID
            LEFT JOIN globalpayment gp ON gp.globalpaymentID = p.globalpaymentID
            LEFT JOIN weaverandfine wf ON wf.paymentID       = p.paymentID
            WHERE p.studentID = ? AND p.schoolyearID = ?
            ORDER BY gp.globalpaymentID ASC, p.paymentID ASC
        ";
        $rows = $this->db->query($sql, [$studentID, $schoolyearID])->result_array();
        echo json_encode(['status' => true, 'rows' => $rows]);
        exit;
    }

    // -------------------------------------------------------------------------
    // paymentSend — with two bug-fixes vs original Global_payment
    // -------------------------------------------------------------------------
    public function paymentSend() {
        $retArray = ['status' => FALSE, 'message' => ''];

        if (!$_POST) {
            $retArray['message'] = 'Something wrong';
            echo json_encode($retArray);
            exit;
        }

        $rules = $this->paymentRules();
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            $retArray = $this->form_validation->error_array();
            $retArray['status'] = FALSE;
            echo json_encode($retArray);
            exit;
        }

        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        // BUG FIX 1: use POST schoolyearID when paying previous-year invoices
        $post_schoolyearID = $this->input->post('schoolyearID');
        $effectiveYearID   = (!empty($post_schoolyearID)) ? (int)$post_schoolyearID : (int)$schoolyearID;

        $paids              = $this->input->post('paid')   ?: [];
        $weavers            = $this->input->post('weaver') ?: [];
        $isPrevYear         = $this->input->post('is_previous_year_amount') ?: 0;
        $studentID          = $this->input->post('studentID');
        $classesID          = $this->input->post('classesID');
        $invoicename        = $this->input->post('invoicename');
        $invoicedescription = $this->input->post('invoicedescription') ?: '';
        $invoicenumber      = $this->input->post('invoicenumber');
        $paymentyear        = $this->input->post('paymentyear');
        $payment_status     = $this->input->post('payment_status');
        $payment_type          = $this->input->post('payment_type');
        $payment_other_details = (strtolower($this->input->post('payment_type')) === 'others')
                                 ? ($this->input->post('payment_other_details') ?: '')
                                 : '';
        $sectionID             = 0;

        $payment_date = $this->input->post('created_date')
            ? date("Y-m-d", strtotime($this->input->post('created_date')))
            : date("Y-m-d");

        $day1   = date("d", strtotime($payment_date));
        $month1 = date("m", strtotime($payment_date));
        $year1  = date("Y", strtotime($payment_date));

        if ($studentID) {
            $student = $this->studentrelation_m->get_single_student(['srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID]);
            if (customCompute($student)) {
                $sectionID = $student->srsectionID;
            }
        }

        $globalpayment = [
            'classesID'          => $classesID,
            'sectionID'          => $sectionID,
            'studentID'          => $studentID,
            'clearancetype'      => $payment_status,
            'invoicename'        => $invoicename,
            'invoicedescription' => $invoicedescription,
            'paymentyear'        => $paymentyear,
            'schoolyearID'       => $effectiveYearID,
        ];

        $this->globalpayment_m->insert_globalpayment($globalpayment);
        $globalLastID = $this->db->insert_id();

        if (!$globalLastID) {
            $retArray['message'] = 'Failed to create payment record';
            echo json_encode($retArray);
            exit;
        }

        // Build invoiceID-keyed maps — skip empty entries upfront
        $paidMap   = [];
        $weaverMap = [];
        foreach ($paids as $p) {
            if ($p['value'] === '' || $p['value'] === null) continue;
            $parts = explode('-', $p['paidFieldID']);
            if (isset($parts[1])) $paidMap[$parts[1]] = (float)$p['value'];
        }
        foreach ($weavers as $w) {
            $wv = (float)($w['value'] ?? 0);
            if ($wv <= 0) continue;
            $parts = explode('-', $w['weaverFieldID']);
            if (isset($parts[1])) $weaverMap[$parts[1]] = $wv;
        }
        $activeInvoiceIDs = array_unique(array_merge(array_keys($paidMap), array_keys($weaverMap)));

        $insertPaymentIDS = [];
        $entered_payment  = 0;

        // Wrap all inserts in one transaction — single disk flush instead of N
        $this->db->trans_start();
        foreach ($activeInvoiceIDs as $invoiceID) {
            $amount = $paidMap[$invoiceID]   ?? 0;
            $wv     = $weaverMap[$invoiceID] ?? 0;

            $this->payment_m->insert_payment([
                'schoolyearID'            => $effectiveYearID,
                'invoiceID'               => $invoiceID,
                'studentID'               => $studentID,
                'paymentamount'           => $amount > 0 ? $amount : NULL,
                'paymenttype'             => ucfirst($payment_type),
                'paymentdate'             => $payment_date,
                'paymentday'              => $day1,
                'paymentmonth'            => $month1,
                'paymentyear'             => $year1,
                'userID'                  => $this->session->userdata('loginuserID'),
                'usertypeID'              => $this->session->userdata('usertypeID'),
                'uname'                   => $this->session->userdata('name'),
                'transactionID'           => 'CASHANDCHEQUE' . random19(),
                'globalpaymentID'         => $globalLastID,
                'is_previous_year_amount' => $isPrevYear,
                'payment_other_details'   => $payment_other_details,
            ]);
            $pid = $this->db->insert_id();
            $insertPaymentIDS[$invoiceID] = $pid;
            $entered_payment += $amount;

            if ($wv > 0) {
                $this->weaverandfine_m->insert_weaverandfine([
                    'weaver'          => $wv,
                    'fine'            => 0,
                    'globalpaymentID' => $globalLastID,
                    'invoiceID'       => $invoiceID,
                    'studentID'       => $studentID,
                    'schoolyearID'    => $effectiveYearID,
                    'paymentID'       => $pid,
                ]);
            }
        }
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            $retArray['message'] = 'Failed to save payment details';
            echo json_encode($retArray);
            exit;
        }
        $retArray['status'] = !empty($activeInvoiceIDs);

        // BUG FIX 1: fetch invoices/payments for the EFFECTIVE year (prev or current)
        $invoices = $this->invoice_m->get_order_by_invoice_join_maininvoice($studentID, $effectiveYearID, 1);

        $allPaymentList = $this->payment_m->get_order_by_payment([
            'studentID' => $studentID, 'schoolyearID' => $effectiveYearID,
        ]);
        $allWeaverList  = $this->weaverandfine_m->get_order_by_weaverandfine([
            'studentID' => $studentID, 'schoolyearID' => $effectiveYearID,
        ]);

        $allPaymentsAgg = $this->generateAllPaymentAmount($allPaymentList);
        $allWeaversAgg  = $this->generateAllWeaverAmount($allWeaverList);

        // Compute balance for SMS
        $totalInvoiceAmount = 0;
        if (customCompute($invoices)) {
            foreach ($invoices as $invoice) {
                // BUG FIX 2: flat-amount discount
                $totalInvoiceAmount += ($invoice->amount - $invoice->discount);
            }
        }
        $totalPaymentAmount = 0;
        foreach ((array)$allPaymentList as $p) { $totalPaymentAmount += $p->paymentamount; }
        $totalWeaverAmount  = 0;
        foreach ((array)$allWeaverList  as $w) { $totalWeaverAmount  += $w->weaver; }

        $student->balance_amount = number_format(($totalInvoiceAmount - ($totalPaymentAmount + $totalWeaverAmount)), 2, '.', '');

        // Build invoice map for O(1) lookup instead of nested loop per invoice
        $invoiceMap = [];
        if (customCompute($invoices)) {
            foreach ($invoices as $inv) { $invoiceMap[$inv->invoiceID] = $inv; }
        }

        // Update paidstatus only for invoices that actually received payment/waiver
        foreach ($activeInvoiceIDs as $invoiceID) {
            if (!isset($invoiceMap[$invoiceID])) continue;
            $matchedInvoice = $invoiceMap[$invoiceID];

            $totalPaymentWeaver = ($allPaymentsAgg[$invoiceID] ?? 0) + ($allWeaversAgg[$invoiceID] ?? 0);
            $totalAmount        = $matchedInvoice->amount - $matchedInvoice->discount;

            if (number_format($totalAmount, 2, '.', '') == number_format($totalPaymentWeaver, 2, '.', '')) {
                $status = 2;
            } elseif ($totalPaymentWeaver > 0) {
                $status = 1;
            } else {
                $status = 0;
            }

            $this->invoice_m->update_invoice(['paidstatus' => $status], $invoiceID);

            if (!isset($student->category)) {
                $student->category = $matchedInvoice->feetype;
            }
        }

        // Update maininvoice status
        $maininvoices = $this->maininvoice_m->get_order_by_maininvoice([
            'maininvoiceschoolyearID' => $effectiveYearID,
            'maininvoicestudentID'    => $studentID,
            'maininvoicedeleted_at'   => 1,
        ]);

        if (customCompute($maininvoices)) {
            $invoicesPlucked = pluck_multi_array(
                $this->invoice_m->get_order_by_invoice(['studentID' => $studentID, 'schoolyearID' => $effectiveYearID, 'deleted_at' => 1]),
                'obj', 'maininvoiceID'
            );
            foreach ($maininvoices as $mi) {
                if (!isset($invoicesPlucked[$mi->maininvoiceID])) continue;
                $allPaid    = [];
                $anyPartial = [];
                foreach ($invoicesPlucked[$mi->maininvoiceID] as $inv) {
                    $allPaid[]    = ($inv->paidstatus == 2);
                    $anyPartial[] = ($inv->paidstatus == 1);
                }
                if (!in_array(FALSE, $allPaid)) {
                    $miStatus = 2;
                } elseif (in_array(TRUE, $anyPartial)) {
                    $miStatus = 1;
                } else {
                    $miStatus = 0;
                }
                $this->maininvoice_m->update_maininvoice(['maininvoicestatus' => $miStatus], $mi->maininvoiceID);
            }
        }

        // Refresh carry-forward cache if this was a previous-year payment
        if (!empty($post_schoolyearID) && (int)$post_schoolyearID != (int)$schoolyearID) {
            $this->computeCarryForward($studentID, $schoolyearID);
        }

        $student->paidamount = $entered_payment;
        $student->date       = date("d-m-Y");

        $sms_status = $this->userConfigSMS($student, 'msg91');

        if ($this->input->post('send_whatsapp')) {
            $whatsapp_config_send = $this->Whatsapp_m->whatsapp_config_send($student);
            if (empty($whatsapp_config_send)) {
                $messege = "phone or template or params missing!, So Whatsapp Not Sent";
            }
        }

        $this->session->set_flashdata('paymentGenerateStatus', TRUE);
        $this->session->set_flashdata('paymentGenerateGlobalLastID', $globalLastID);
        $this->session->set_flashdata('paymentGenerateLastStudentID', $studentID);
        $this->session->set_flashdata('success', $this->lang->line('menu_success'));

        $retArray['sms_resp']    = $sms_status ?? null;
        $retArray['studentID']   = $studentID;
        $retArray['globalLastID'] = $globalLastID;
        $retArray['message']     = $messege ?? '';
        echo json_encode($retArray);
        exit;
    }

    // -------------------------------------------------------------------------
    // Receipt
    // -------------------------------------------------------------------------
    public function print_reciept($studentID, $globalpaymentID, $prev_schoolyearID = 0) {
        $this->data['headerassets'] = [
            'css' => ['assets/datepicker/datepicker.css',
                      'assets/select2/css/select2.css',
                      'assets/select2/css/select2-bootstrap.css'],
            'js'  => ['assets/datepicker/datepicker.js',
                      'assets/select2/select2.js'],
        ];

        $schoolyearID = !empty($prev_schoolyearID)
            ? $prev_schoolyearID
            : $this->session->userdata('defaultschoolyearID');

        // Pass year name to view so fee rows can show "(2025-26)" label
        $yearRow = $this->db->select('schoolyear')->where('schoolyearID', $schoolyearID)->get('schoolyear')->row();
        $this->data['receipt_year_name']     = $yearRow ? $yearRow->schoolyear : '';
        $this->data['is_prev_year_receipt']  = !empty($prev_schoolyearID);

        // Back button URL — resolves after single_student is loaded below
        $this->data['receipt_back_url']      = '';

        $this->data['single_student'] = $this->studentrelation_m->get_single_student([
            'srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID,
        ]);

        $cnt = $this->db->query("SELECT * FROM setting WHERE fieldoption='isrecieptphone' AND value='1'")->num_rows();
        $this->data['is_phone_display'] = $cnt;

        $this->payment_m->order_payment('paymentID asc');
        $allPaymentList = $this->payment_m->get_order_by_payment(['studentID' => $studentID, 'schoolyearID' => $schoolyearID]);
        $allWeaverList  = $this->weaverandfine_m->get_order_by_weaverandfine(['studentID' => $studentID, 'schoolyearID' => $schoolyearID]);

        $this->data['payments']   = $this->generateAllPaymentAmount($allPaymentList);
        $this->data['weavers']    = $this->generateAllWeaverAmount($allWeaverList);
        $this->data['paymenteds'] = $allPaymentList;
        $this->data['weavereds']  = $allWeaverList;
        $this->data['globalpayment_max'] = $this->globalpayment_m->get_max_globalpayment();
        $this->data['feetypes']   = pluck($this->feetypes_m->get_feetypes(), 'feetypes', 'feetypesID');

        if (customCompute($this->data['single_student'])) {
            $single_student = $this->data['single_student'];
            $this->data['receipt_back_url'] = base_url('global_payment/new/' . $single_student->srclassesID . '/' . $studentID);
            $this->data['single_classes'] = $this->classes_m->get_single_classes(['classesID' => $single_student->srclassesID]);
            $this->data['single_section'] = $this->section_m->get_single_section(['sectionID' => $single_student->srsectionID]);
            $this->data['single_group']   = $this->studentgroup_m->get_single_studentgroup(['studentgroupID' => $single_student->srstudentgroupID]);
            $this->data['invoices']       = $this->invoice_m->get_order_by_invoice_join_maininvoice($single_student->srstudentID, $schoolyearID, 1);
            $this->data['invoicefeetype'] = pluck($this->data['invoices'], 'feetypeID', 'invoiceID');
            $this->data['globalpayment']  = $this->globalpayment_m->get_order_by_globalpayment([
                'globalpaymentID' => $globalpaymentID, 'schoolyearID' => $schoolyearID, 'studentID' => $single_student->srstudentID,
            ]);
            $this->data['paidpayments']   = $this->generateAllPaymentAmountWithGlobalID($allPaymentList);
            $this->data['weaverandfines']  = pluck(
                $this->weaverandfine_m->get_order_by_weaverandfine(['studentID' => $single_student->srstudentID, 'schoolyearID' => $schoolyearID]),
                'obj', 'paymentID'
            );
        } else {
            $this->data['single_classes'] = [];
            $this->data['single_section'] = [];
            $this->data['single_group']   = [];
            $this->data['invoices']        = [];
            $this->data['globalpayments']  = [];
        }

        $this->data['globalpayments'] = $this->globalpayment_m->get_order_by_globalpayment([
            'schoolyearID' => $schoolyearID, 'studentID' => $studentID,
        ]);
        $this->data['paidpayments'] = $this->generateAllPaymentAmountWithGlobalID($allPaymentList);

        $this->data['subview'] = 'common_views/invoice';
        $this->load->view('_layout_main', $this->data);
    }

    // -------------------------------------------------------------------------
    // Delete invoice helpers
    // -------------------------------------------------------------------------
    public function updateMultiple() {
        $invoiceIDs    = $this->input->post('invoiceIDs');
        $maininvoiceIDs = $this->input->post('maininvoiceIDs');
        if (!empty($invoiceIDs))     $this->invoice_m->updateInvoices($invoiceIDs);
        if (!empty($maininvoiceIDs)) $this->invoice_m->updateMainInvoices($maininvoiceIDs);
        echo "Selected invoices deleted successfully (only if no payments were found).";
    }

    public function updateSingle() {
        $invoiceID    = $this->input->post('invoiceID');
        $maininvoiceID = $this->input->post('maininvoiceID');
        if ($invoiceID)    $this->invoice_m->updateInvoice($invoiceID);
        if ($maininvoiceID) $this->invoice_m->updateMainInvoice($maininvoiceID);
        echo "Invoice deleted successfully (only if no payments were found).";
    }

    // -------------------------------------------------------------------------
    // SMS helpers (verbatim from original)
    // -------------------------------------------------------------------------
    private function userConfigSMS($user, $getway = 'msg91') {
        $this->load->model('mailandsmstemplate_m');
        $this->load->model('mailandsmstemplatetag_m');
        $cnt = $this->db->query("SELECT * FROM setting WHERE fieldoption='is_fee_sms' AND value='1'")->num_rows();
        if ($cnt > 0) {
            $template = $this->mailandsmstemplate_m->get_mailandsmstemplate(5);
            $template_id = $template->templ_id;
            $message     = $template->template;
            if ($user) {
                $userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(['usertypeID' => 3]);
                $message  = $this->tagConvertor($userTags, $user, $message, 'SMS');
                if ($user->phone) {
                    return $this->allgetway_send_message($getway, $user->phone, $message, $template_id);
                }
            }
        }
        return null;
    }

    private function tagConvertor($userTags, $user, $message, $sendType) {
        if (customCompute($userTags)) {
            $this->load->model('setting_m');
            $school_name = isset($this->setting_m->get_setting()->sname) ? $this->setting_m->get_setting()->sname : '';
            foreach ($userTags as $tag) {
                if ($tag->tagname == '{{paid_amount}}')
                    $message = str_replace('{{paid_amount}}', $user->paidamount ?? ' ', $message);
                elseif ($tag->tagname == '{{category}}')
                    $message = str_replace('{{category}}', $user->category ?? ' ', $message);
                elseif ($tag->tagname == '{{school_name}}')
                    $message = str_replace('{{school_name}}', $school_name, $message);
                elseif ($tag->tagname == '{{student_name}}')
                    $message = str_replace('{{student_name}}', $user->srname ?? '', $message);
            }
        }
        $message = str_replace('{{balance_amount}}', $user->balance_amount ?? '0.00', $message);
        return $message;
    }

    private function allgetway_send_message($getway, $to, $message, $template_id = 0) {
        return $this->msg91->send($to, $message, $template_id);
    }
}
