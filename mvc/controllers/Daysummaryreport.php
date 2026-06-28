<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Daysummaryreport extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('daysummary_m');
        $this->load->model('daysheet_m');
        $this->load->model('banks_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('daysummaryreport', $language);
    }

    public function index() {
        $this->data['headerassets'] = [
            'css' => ['assets/datepicker/datepicker.css'],
            'js'  => ['assets/datepicker/datepicker.js'],
        ];
        $this->data['subview'] = 'report/daysummary/DaysummaryreportView';
        $this->load->view('_layout_main', $this->data);
    }

    public function getReport() {
        $retArray = ['status' => FALSE, 'render' => ''];

        if (!permissionChecker('daysummaryreport')) {
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

        // --- Opening balance (sum across all accounts) ---
        $openingByAccount = $this->daysheet_m->get_opening_balance($date, $schoolyearID);
        $totalOpening = 0;
        foreach ($openingByAccount as $amt) { $totalOpening += (float)$amt; }

        // --- Fetch individual transactions ---
        $fees     = $this->daysummary_m->get_fee_transactions($date, $schoolyearID);
        $incomes  = $this->daysummary_m->get_income_transactions($date, $schoolyearID);
        $expenses = $this->daysummary_m->get_expense_transactions($date, $schoolyearID);
        $salaries = $this->daysummary_m->get_salary_transactions($date, $schoolyearID);

        // --- Normalize into a flat array ---
        $transactions = [];

        foreach ($fees as $f) {
            $mode = ($f->paymenttype === 'Others' && $f->bank !== '') ? $f->bank : $f->paymenttype;
            $transactions[] = [
                'type'      => 'fee',
                'time'      => $f->txn_time ?: '—',
                'label'     => 'Fee Collection',
                'particular'=> $f->student_name,
                'category'  => $f->feetype,
                'mode'      => $mode,
                'receipt'   => (float)$f->paymentamount,
                'payment'   => 0.0,
                'sort_key'  => 0 * 1e9 + $f->paymentID,
            ];
        }

        foreach ($incomes as $i) {
            $transactions[] = [
                'type'      => 'income',
                'time'      => $i->txn_time ?: '—',
                'label'     => 'Other Income',
                'particular'=> $i->income_name,
                'category'  => $i->category_name,
                'mode'      => 'Cash',
                'receipt'   => (float)$i->amount,
                'payment'   => 0.0,
                'sort_key'  => 1 * 1e9 + $i->incomeID,
            ];
        }

        foreach ($expenses as $e) {
            $mode = ($e->payment_type === 'Others' && $e->bank !== '') ? $e->bank : $e->payment_type;
            $transactions[] = [
                'type'      => 'expense',
                'time'      => $e->txn_time ?: '—',
                'label'     => 'Expense',
                'particular'=> $e->expense,
                'category'  => $e->category,
                'mode'      => $mode,
                'receipt'   => 0.0,
                'payment'   => (float)$e->amount,
                'sort_key'  => 2 * 1e9 + $e->expenseID,
            ];
        }

        foreach ($salaries as $s) {
            $pm = (int)$s->payment_method;
            $mode = ($pm === 1) ? 'Cash' : (($pm === 2) ? 'Cheque' : ($s->bank_name ?: 'Others'));
            $transactions[] = [
                'type'      => 'salary',
                'time'      => $s->txn_time ?: '—',
                'label'     => 'Salary',
                'particular'=> $s->staff_name,
                'category'  => 'Salary (Payroll)',
                'mode'      => $mode,
                'receipt'   => 0.0,
                'payment'   => (float)$s->payment_amount,
                'sort_key'  => 3 * 1e9 + $s->make_paymentID,
            ];
        }

        // Sort by sort_key
        usort($transactions, function($a, $b) {
            return $a['sort_key'] <=> $b['sort_key'];
        });

        // --- Running balance ---
        $balance = $totalOpening;
        foreach ($transactions as &$txn) {
            $balance += $txn['receipt'] - $txn['payment'];
            $txn['balance'] = $balance;
        }
        unset($txn);
        $closingBalance = $balance;

        // --- Totals ---
        $totalReceipts      = 0.0;
        $totalFeeReceipts   = 0.0;
        $totalOtherIncome   = 0.0;
        $totalPayments      = 0.0;
        foreach ($transactions as $txn) {
            $totalReceipts += $txn['receipt'];
            $totalPayments += $txn['payment'];
            if ($txn['type'] === 'fee')    $totalFeeReceipts += $txn['receipt'];
            if ($txn['type'] === 'income') $totalOtherIncome += $txn['receipt'];
        }

        // --- Payment mode summary ---
        $modeSummary = [];
        foreach ($transactions as $txn) {
            $m = $txn['mode'];
            if (!isset($modeSummary[$m])) {
                $modeSummary[$m] = ['collection' => 0.0, 'payment' => 0.0];
            }
            $modeSummary[$m]['collection'] += $txn['receipt'];
            $modeSummary[$m]['payment']    += $txn['payment'];
        }

        // --- Build tab list: Cash, Digital, Cheque, active banks, Others ---
        $tabModes = ['Cash', 'Digital', 'Cheque'];
        if (customCompute($banks)) {
            foreach ($banks as $b) { $tabModes[] = $b->bank_name; }
        }
        $tabModes[] = 'Others';

        $this->data['openingByAccount']  = $openingByAccount;
        $this->data['totalFeeReceipts']  = $totalFeeReceipts;
        $this->data['totalOtherIncome']  = $totalOtherIncome;
        $this->data['rawDate']           = $rawDate;
        $this->data['date']           = $date;
        $this->data['banks']          = $banks;
        $this->data['tabModes']       = $tabModes;
        $this->data['transactions']   = $transactions;
        $this->data['totalOpening']   = $totalOpening;
        $this->data['totalReceipts']  = $totalReceipts;
        $this->data['totalPayments']  = $totalPayments;
        $this->data['closingBalance'] = $closingBalance;
        $this->data['modeSummary']    = $modeSummary;

        $retArray['render']  = $this->load->view('report/daysummary/DaysummaryreportReport', $this->data, true);
        $retArray['status']  = TRUE;
        echo json_encode($retArray); exit;
    }
}
