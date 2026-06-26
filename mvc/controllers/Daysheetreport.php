<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Daysheetreport extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('daysheet_m');
        $this->load->model('banks_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('daysheetreport', $language);
    }

    public function index() {
        $this->data['headerassets'] = [
            'css' => ['assets/datepicker/datepicker.css'],
            'js'  => ['assets/datepicker/datepicker.js'],
        ];
        $this->data['subview'] = 'report/daysheet/DaysheetreportView';
        $this->load->view('_layout_main', $this->data);
    }

    public function getDaysheetReport() {
        $retArray = ['status' => FALSE, 'render' => '', 'need_opening_balance' => FALSE];

        if (!permissionChecker('daysheetreport')) {
            $retArray['render'] = $this->load->view('report/reporterror', $this->data, true);
            $retArray['status'] = TRUE;
            echo json_encode($retArray); exit;
        }

        if (!$_POST) {
            echo json_encode($retArray); exit;
        }

        $rawDate = $this->input->post('date');
        if (!$rawDate) {
            $retArray['error'] = 'Date is required.';
            echo json_encode($retArray); exit;
        }

        $date         = date('Y-m-d', strtotime($rawDate));
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $banks        = $this->banks_m->get_active_banks();

        // Build ordered account list
        $accounts = ['Cash', 'Digital', 'Cheque', 'Others'];
        if (customCompute($banks)) {
            foreach ($banks as $b) {
                $accounts[] = $b->bank_name;
            }
        }

        // --- Opening Balance ---
        $opening = $this->daysheet_m->get_opening_balance($date, $schoolyearID);

        if (empty($opening)) {
            // Try auto-fill from previous day's closing balance
            $prevDate    = date('Y-m-d', strtotime($date . ' -1 day'));
            $prevOpening = $this->daysheet_m->get_opening_balance($prevDate, $schoolyearID);
            if (!empty($prevOpening)) {
                $opening = $this->daysheet_m->get_previous_closing($prevDate, $schoolyearID, $banks);
            }

            // If still empty (first day of operation), default all accounts to 0
            if (empty($opening)) {
                foreach ($accounts as $acct) {
                    $opening[$acct] = 0;
                }
            }

            // Auto-save computed opening so next load reads from DB
            $createdBy = $this->session->userdata('loginuserID');
            $this->daysheet_m->save_opening_balance($opening, $date, $schoolyearID, $createdBy);
        }

        // Fill missing accounts with 0
        foreach ($accounts as $acct) {
            if (!isset($opening[$acct])) $opening[$acct] = 0;
        }

        // --- Section 2: Fee collection by payment type ---
        $feeByType    = $this->daysheet_m->get_fee_by_paymenttype($date, $schoolyearID);
        $feeCount     = $this->daysheet_m->get_fee_count($date, $schoolyearID);

        // --- Section 3: Other income by category ---
        $incomeBycat  = $this->daysheet_m->get_income_by_category($date, $schoolyearID);

        // --- Section 4: Expense items by payment account ---
        $expenseItems = $this->daysheet_m->get_expense_items_by_paymenttype($date, $schoolyearID);

        // --- Section 7: Expense by category ---
        $expenseByCat = $this->daysheet_m->get_expense_by_category($date, $schoolyearID);

        // --- Section 8: Fee by fee type ---
        $feeByFeetype = $this->daysheet_m->get_fee_by_feetype($date, $schoolyearID);

        // --- Salary total + breakdown ---
        $salaryTotal  = $this->daysheet_m->get_salary_total($date, $schoolyearID);
        $salaryDetail = $this->daysheet_m->get_salary_detail($date, $schoolyearID);

        // --- Aggregate totals ---
        $totalFeeCollection = 0;
        foreach ($feeByType as $row) {
            $totalFeeCollection += (float)$row->total;
        }

        $totalOtherIncome = 0;
        foreach ($incomeBycat as $row) {
            $totalOtherIncome += (float)$row->total;
        }

        $totalExpenses = 0;
        $expByTypeAgg  = []; // account_type → total spent
        foreach ($accounts as $acct) { $expByTypeAgg[$acct] = 0; }

        foreach ($expenseItems as $item) {
            $totalExpenses += (float)$item->amount;
            $key = ($item->payment_type === 'Others' && $item->bank !== '')
                ? $item->bank
                : $item->payment_type;
            if (isset($expByTypeAgg[$key])) {
                $expByTypeAgg[$key] += (float)$item->amount;
            } else {
                $expByTypeAgg['Others'] = ($expByTypeAgg['Others'] ?? 0) + (float)$item->amount;
            }
        }
        $totalExpenses += $salaryTotal;

        // Distribute salary payments into per-account spent tracker by payment method
        foreach ($salaryDetail as $sd) {
            $pm = (int)$sd->payment_method;
            if ($pm === 1) {
                $key = 'Cash';
            } elseif ($pm === 2) {
                $key = 'Cheque';
            } elseif ($pm === 3 && !empty($sd->bank_name)) {
                $key = $sd->bank_name;
            } else {
                $key = 'Cash';
            }
            if (isset($expByTypeAgg[$key])) {
                $expByTypeAgg[$key] += (float)$sd->payment_amount;
            } else {
                $expByTypeAgg['Cash'] = ($expByTypeAgg['Cash'] ?? 0) + (float)$sd->payment_amount;
            }
        }

        // --- Section 6: Closing balance per account ---
        // received[acct] from fees only
        $received = [];
        foreach ($accounts as $acct) { $received[$acct] = 0; }
        foreach ($feeByType as $row) {
            $key = ($row->paymenttype === 'Others' && $row->bank !== '')
                ? $row->bank
                : $row->paymenttype;
            if (isset($received[$key])) {
                $received[$key] += (float)$row->total;
            } else {
                $received['Others'] = ($received['Others'] ?? 0) + (float)$row->total;
            }
        }

        $closing = [];
        $totalOpening = 0; $totalReceived = 0; $totalSpent = 0; $totalClosing = 0;
        foreach ($accounts as $acct) {
            $op = (float)($opening[$acct] ?? 0);
            $rc = (float)($received[$acct] ?? 0);
            $sp = (float)($expByTypeAgg[$acct] ?? 0);
            $cl = $op + $rc - $sp;
            $closing[$acct] = $cl;
            $totalOpening  += $op;
            $totalReceived += $rc;
            $totalSpent    += $sp;
            $totalClosing  += $cl;
        }

        $netCashFlow = $totalFeeCollection + $totalOtherIncome - $totalExpenses;

        // Pass to view
        $this->data['date']               = $rawDate;
        $this->data['date_ymd']           = $date;
        $this->data['accounts']           = $accounts;
        $this->data['banks']              = $banks;
        $this->data['opening']            = $opening;
        $this->data['feeByType']          = $feeByType;
        $this->data['feeCount']           = $feeCount;
        $this->data['incomeBycat']        = $incomeBycat;
        $this->data['expenseItems']       = $expenseItems;
        $this->data['expByTypeAgg']       = $expByTypeAgg;
        $this->data['expenseByCat']       = $expenseByCat;
        $this->data['feeByFeetype']       = $feeByFeetype;
        $this->data['salaryTotal']        = $salaryTotal;
        $this->data['salaryDetail']       = $salaryDetail;
        $this->data['received']           = $received;
        $this->data['closing']            = $closing;
        $this->data['totalFeeCollection'] = $totalFeeCollection;
        $this->data['totalOtherIncome']   = $totalOtherIncome;
        $this->data['totalExpenses']      = $totalExpenses;
        $this->data['netCashFlow']        = $netCashFlow;
        $this->data['totalOpening']       = $totalOpening;
        $this->data['totalReceived']      = $totalReceived;
        $this->data['totalSpent']         = $totalSpent;
        $this->data['totalClosing']       = $totalClosing;

        $retArray['render']  = $this->load->view('report/daysheet/DaysheetreportReport', $this->data, true);
        $retArray['status']  = TRUE;
        echo json_encode($retArray); exit;
    }

    public function saveOpeningBalance() {
        $retArray = ['status' => FALSE, 'message' => ''];

        if (!permissionChecker('daysheetreport')) {
            $retArray['message'] = 'Permission denied.';
            echo json_encode($retArray); exit;
        }

        $rawDate      = $this->input->post('date');
        $amounts      = $this->input->post('opening_amounts'); // associative array account => amount
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $createdBy    = $this->session->userdata('loginuserID');

        if (!$rawDate || !is_array($amounts)) {
            $retArray['message'] = 'Invalid data.';
            echo json_encode($retArray); exit;
        }

        $date = date('Y-m-d', strtotime($rawDate));
        $cleaned = [];
        foreach ($amounts as $acct => $amt) {
            $cleaned[$acct] = max(0, (float)$amt);
        }

        $this->daysheet_m->save_opening_balance($cleaned, $date, $schoolyearID, $createdBy);
        $retArray['status'] = TRUE;
        echo json_encode($retArray); exit;
    }

    public function date_valid($date) {
        if (!$date) return TRUE;
        if (strlen($date) < 10) {
            $this->form_validation->set_message('date_valid', 'The %s is not valid dd-mm-yyyy');
            return FALSE;
        }
        $arr = explode('-', $date);
        if (checkdate($arr[1], $arr[0], $arr[2])) return TRUE;
        $this->form_validation->set_message('date_valid', 'The %s is not valid dd-mm-yyyy');
        return FALSE;
    }
}
