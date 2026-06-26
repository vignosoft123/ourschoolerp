<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Daysheet_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Get saved opening balance for a specific date → keyed by account_type
    public function get_opening_balance($date, $schoolyearID) {
        $rows = $this->db
            ->where('date', $date)
            ->where('schoolyearID', $schoolyearID)
            ->get('daysheet_opening_balance')
            ->result();
        $result = [];
        foreach ($rows as $row) {
            $result[$row->account_type] = (float)$row->opening_amount;
        }
        return $result;
    }

    // Save/overwrite opening balance rows (upsert per account_type)
    public function save_opening_balance($accounts, $date, $schoolyearID, $createdBy) {
        foreach ($accounts as $account_type => $amount) {
            $existing = $this->db
                ->where('date', $date)
                ->where('account_type', $account_type)
                ->where('schoolyearID', $schoolyearID)
                ->get('daysheet_opening_balance')
                ->row();
            if ($existing) {
                $this->db->where('id', $existing->id)
                    ->update('daysheet_opening_balance', ['opening_amount' => (float)$amount]);
            } else {
                $this->db->insert('daysheet_opening_balance', [
                    'date'           => $date,
                    'account_type'   => $account_type,
                    'opening_amount' => (float)$amount,
                    'schoolyearID'   => $schoolyearID,
                    'created_by'     => $createdBy,
                ]);
            }
        }
    }

    // Fee collection grouped by payment type for a date
    // Returns array: [ ['paymenttype'=>'Cash','bank'=>'','total'=>1000], ... ]
    public function get_fee_by_paymenttype($date, $schoolyearID) {
        $sql = "SELECT paymenttype, COALESCE(payment_other_details,'') AS bank,
                       SUM(paymentamount) AS total
                FROM payment
                WHERE paymentdate = ? AND schoolyearID = ?
                GROUP BY paymenttype, payment_other_details
                ORDER BY paymenttype";
        return $this->db->query($sql, [$date, $schoolyearID])->result();
    }

    // Fee count for a date (for summary card)
    public function get_fee_count($date, $schoolyearID) {
        return $this->db
            ->where('paymentdate', $date)
            ->where('schoolyearID', $schoolyearID)
            ->count_all_results('payment');
    }

    // Other income grouped by category for a date
    public function get_income_by_category($date, $schoolyearID) {
        $this->db->select('ic.name AS category_name, SUM(i.amount) AS total');
        $this->db->from('income i');
        $this->db->join('income_categories ic', 'ic.incomecategoriesID = i.incomecategoriesID', 'left');
        $this->db->where('i.date', $date);
        $this->db->where('i.schoolyearID', $schoolyearID);
        $this->db->group_by('i.incomecategoriesID');
        $this->db->order_by('ic.name');
        return $this->db->get()->result();
    }

    // Expenses grouped by payment type + bank for a date
    public function get_expense_by_paymenttype($date, $schoolyearID) {
        $sql = "SELECT COALESCE(expense_payment_type,'Cash') AS payment_type,
                       COALESCE(expense_bank_name,'') AS bank,
                       SUM(amount) AS total,
                       COUNT(*) AS cnt
                FROM expense
                WHERE date = ? AND schoolyearID = ?
                GROUP BY expense_payment_type, expense_bank_name
                ORDER BY expense_payment_type";
        return $this->db->query($sql, [$date, $schoolyearID])->result();
    }

    // Expense line items per account group (for Section 4 detail)
    public function get_expense_items_by_paymenttype($date, $schoolyearID) {
        $sql = "SELECT e.expense, e.amount,
                       COALESCE(e.expense_payment_type,'Cash') AS payment_type,
                       COALESCE(e.expense_bank_name,'') AS bank,
                       et.expensetypes AS category
                FROM expense e
                LEFT JOIN expensetypes et ON et.expensetypesID = e.expensetypesID
                WHERE e.date = ? AND e.schoolyearID = ?
                ORDER BY e.expense_payment_type, e.expense_bank_name, e.expense";
        return $this->db->query($sql, [$date, $schoolyearID])->result();
    }

    // Expenses grouped by category for a date (Section 7)
    public function get_expense_by_category($date, $schoolyearID) {
        $this->db->select('et.expensetypes AS category, SUM(e.amount) AS total');
        $this->db->from('expense e');
        $this->db->join('expensetypes et', 'et.expensetypesID = e.expensetypesID', 'left');
        $this->db->where('e.date', $date);
        $this->db->where('e.schoolyearID', $schoolyearID);
        $this->db->group_by('e.expensetypesID');
        $this->db->order_by('et.expensetypes');
        return $this->db->get()->result();
    }

    // Fee collection grouped by fee type (Section 8)
    public function get_fee_by_feetype($date, $schoolyearID) {
        $sql = "SELECT ft.feetypes AS feetype, SUM(p.paymentamount) AS total
                FROM payment p
                LEFT JOIN invoice i ON i.invoiceID = p.invoiceID
                LEFT JOIN feetypes ft ON ft.feetypesID = i.feetypeID
                WHERE p.paymentdate = ? AND p.schoolyearID = ?
                GROUP BY i.feetypeID
                ORDER BY ft.feetypes";
        return $this->db->query($sql, [$date, $schoolyearID])->result();
    }

    // Fines collected for a date
    public function get_fine_total($date, $schoolyearID) {
        $sql = "SELECT COALESCE(SUM(wf.fine),0) AS total
                FROM weaverandfine wf
                JOIN payment p ON p.paymentID = wf.paymentID
                WHERE p.paymentdate = ? AND p.schoolyearID = ?";
        $row = $this->db->query($sql, [$date, $schoolyearID])->row();
        return $row ? (float)$row->total : 0;
    }

    // Salary paid for a date
    public function get_salary_total($date, $schoolyearID) {
        $sql = "SELECT COALESCE(SUM(payment_amount),0) AS total
                FROM make_payment
                WHERE DATE(create_date) = ? AND schoolyearID = ?";
        $row = $this->db->query($sql, [$date, $schoolyearID])->row();
        return $row ? (float)$row->total : 0;
    }

    // Individual salary payment rows for breakdown popup
    // usertypeID=1 → systemadmin, usertypeID=2 → teacher, others → `user`
    public function get_salary_detail($date, $schoolyearID) {
        $sql = "SELECT mp.payment_amount, mp.payment_method,
                       COALESCE(mp.salary_bank_name,'') AS bank_name,
                       COALESCE(
                           CASE WHEN mp.usertypeID = 1 THEN sa.name
                                WHEN mp.usertypeID = 2 THEN t.name
                                ELSE u.name END,
                           CONCAT('Staff #', IFNULL(mp.userID,''))
                       ) AS staff_name
                FROM make_payment mp
                LEFT JOIN systemadmin sa ON sa.systemadminID = mp.userID AND mp.usertypeID = 1
                LEFT JOIN teacher     t  ON t.teacherID      = mp.userID AND mp.usertypeID = 2
                LEFT JOIN `user`      u  ON u.userID         = mp.userID AND mp.usertypeID NOT IN (1,2)
                WHERE DATE(mp.create_date) = ? AND mp.schoolyearID = ?
                ORDER BY staff_name";
        return $this->db->query($sql, [$date, $schoolyearID])->result();
    }

    // Compute previous day's closing balance → returned as assoc array account_type => amount
    // closing = opening + received(fees per account) - spent(expenses per account)
    public function get_previous_closing($prevDate, $schoolyearID, $banks) {
        // Get previous opening balance
        $opening = $this->get_opening_balance($prevDate, $schoolyearID);

        // Build account list
        $accounts = ['Cash', 'Digital', 'Cheque', 'Others'];
        if (customCompute($banks)) {
            foreach ($banks as $b) {
                $accounts[] = $b->bank_name;
            }
        }

        // Previous day fee received per account
        $feeRows = $this->get_fee_by_paymenttype($prevDate, $schoolyearID);
        $received = [];
        foreach ($accounts as $acct) { $received[$acct] = 0; }
        foreach ($feeRows as $row) {
            $key = ($row->paymenttype === 'Others' && $row->bank !== '')
                ? $row->bank
                : $row->paymenttype;
            if (isset($received[$key])) {
                $received[$key] += (float)$row->total;
            } else {
                $received['Others'] = ($received['Others'] ?? 0) + (float)$row->total;
            }
        }

        // Previous day expense spent per account
        $expRows = $this->get_expense_by_paymenttype($prevDate, $schoolyearID);
        $spent = [];
        foreach ($accounts as $acct) { $spent[$acct] = 0; }
        foreach ($expRows as $row) {
            $key = ($row->payment_type === 'Others' && $row->bank !== '')
                ? $row->bank
                : $row->payment_type;
            if (isset($spent[$key])) {
                $spent[$key] += (float)$row->total;
            } else {
                $spent['Others'] = ($spent['Others'] ?? 0) + (float)$row->total;
            }
        }

        // closing = opening + received - spent
        $closing = [];
        foreach ($accounts as $acct) {
            $op = isset($opening[$acct]) ? (float)$opening[$acct] : 0;
            $rc = isset($received[$acct]) ? (float)$received[$acct] : 0;
            $sp = isset($spent[$acct]) ? (float)$spent[$acct] : 0;
            $closing[$acct] = $op + $rc - $sp;
        }
        return $closing;
    }
}
