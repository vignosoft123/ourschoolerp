<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Daysummary_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Individual fee payment rows for a date, with student name + fee type
    public function get_fee_transactions($date, $schoolyearID) {
        $sql = "SELECT p.paymentID, p.paymentamount, p.paymenttype,
                       COALESCE(p.payment_other_details,'') AS bank,
                       COALESCE(s.name, CONCAT('Student #', IFNULL(inv.studentID,''))) AS student_name,
                       COALESCE(ft.feetypes, 'General') AS feetype,
                       DATE_FORMAT(p.created_at, '%h:%i %p') AS txn_time
                FROM payment p
                LEFT JOIN invoice  inv ON inv.invoiceID   = p.invoiceID
                LEFT JOIN student  s   ON s.studentID     = inv.studentID
                LEFT JOIN feetypes ft  ON ft.feetypesID   = inv.feetypeID
                WHERE p.paymentdate = ? AND p.schoolyearID = ?
                ORDER BY p.created_at, p.paymentID";
        return $this->db->query($sql, [$date, $schoolyearID])->result();
    }

    // Individual other-income rows for a date, with category name
    public function get_income_transactions($date, $schoolyearID) {
        $sql = "SELECT i.incomeID, i.amount, i.name AS income_name,
                       COALESCE(ic.name, 'Others') AS category_name,
                       DATE_FORMAT(i.created_at, '%h:%i %p') AS txn_time
                FROM income i
                LEFT JOIN income_categories ic ON ic.incomecategoriesID = i.incomecategoriesID
                WHERE i.date = ? AND i.schoolyearID = ?
                ORDER BY i.created_at, i.incomeID";
        return $this->db->query($sql, [$date, $schoolyearID])->result();
    }

    // Individual expense rows for a date (returns expenseID for sort key)
    public function get_expense_transactions($date, $schoolyearID) {
        $sql = "SELECT e.expenseID, e.expense, e.amount,
                       COALESCE(e.expense_payment_type,'Cash') AS payment_type,
                       COALESCE(e.expense_bank_name,'')        AS bank,
                       COALESCE(et.expensetypes, 'General')    AS category,
                       DATE_FORMAT(e.created_at, '%h:%i %p')   AS txn_time
                FROM expense e
                LEFT JOIN expensetypes et ON et.expensetypesID = e.expensetypesID
                WHERE e.date = ? AND e.schoolyearID = ?
                ORDER BY e.created_at, e.expenseID";
        return $this->db->query($sql, [$date, $schoolyearID])->result();
    }

    // Individual salary payment rows for a date (with time from create_date)
    // usertypeID=1→systemadmin, 2→teacher, other→`user`
    public function get_salary_transactions($date, $schoolyearID) {
        $sql = "SELECT mp.make_paymentID, mp.payment_amount, mp.payment_method,
                       COALESCE(mp.salary_bank_name,'') AS bank_name,
                       DATE_FORMAT(mp.create_date,'%h:%i %p') AS txn_time,
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
                ORDER BY mp.create_date, mp.make_paymentID";
        return $this->db->query($sql, [$date, $schoolyearID])->result();
    }
}
