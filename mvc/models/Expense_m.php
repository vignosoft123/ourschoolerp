<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expense_m extends MY_Model {

	protected $_table_name = 'expense';
	protected $_primary_key = 'expenseID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "expenseID desc";

	function __construct() {
		parent::__construct();
	}

	function get_expense($array=NULL, $signal=FALSE) {
		$query = parent::get($array, $signal);
		return $query;
	}

	function get_single_expense($array=NULL) {
		$query = parent::get_single($array);
		return $query;
	}

	function get_order_by_expense($array=NULL) {
		$query = parent::get_order_by($array);
		return $query;
	}

	function insert_expense($array) {
		$error = parent::insert($array);
		return TRUE;
	}

	function update_expense($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_expense($id){
		parent::delete($id);
	}

	public function user_expense($table, $username, $email){
		$query = $this->db->get_where($table, array("username" => $username, "email" => $email));
		return $query->row();
	}

	public function get_expense_order_by_date($array) {
		$this->db->select('*');
		$this->db->from($this->_table_name);
		$this->db->where('date >=',$array['fromdate']);
		$this->db->where('date <=',$array['todate']);
		$this->db->where('schoolyearID',$array['schoolyearID']);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_expense_order_with_date_schoolyear($array) {
		$this->db->select_sum('amount');
		$this->db->from($this->_table_name);
		if(isset($array['fromdate']) && isset($array['todate'])) {
			$this->db->where('date >=',$array['fromdate']);
			$this->db->where('date <=',$array['todate']);
		}
		if(isset($array['schoolyearID'])) {
			$this->db->where('schoolyearID',$array['schoolyearID']);
		}
		$query = $this->db->get();
		return $query->row();
	}

    /* define for 4.4 */
    public function get_expense_with_user($array = ['expense.schoolyearID' => 1])
    {
        $this->db->select('expense.expenseID, expense.create_date, expense.date, expense.expenseday, expense.expensemonth, expense.expenseyear, expense.expense, expense.amount, expense.file, expense.userID, expense.usertypeID, expense.schoolyearID, expense.note, systemadmin.name as aname, teacher.name as tname, student.name as sname, parents.name as pname, user.name as uname');
        $this->db->from('expense');
        $this->db->join('systemadmin', 'systemadmin.usertypeID = expense.usertypeID AND systemadmin.systemadminID = expense.userID' , 'LEFT');
        $this->db->join('teacher', 'teacher.usertypeID = expense.usertypeID AND teacher.teacherID = expense.userID', 'LEFT');
        $this->db->join('student', 'student.usertypeID = expense.usertypeID AND student.studentID = expense.userID', 'LEFT');
        $this->db->join('parents', 'parents.usertypeID = expense.usertypeID AND parents.parentsID = expense.userID', 'LEFT');
        $this->db->join('user', 'user.usertypeID = expense.usertypeID AND user.userID = expense.userID', 'LEFT');
        $this->db->where($array);
        $this->db->order_by($this->_order_by);
        $query = $this->db->get();
        return $query->result();
    }


	//code for expense report
	public function get_all_expenses_for_report($queryArray) {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        $this->db->select('expense.*');
        $this->db->from('expense');
        $this->db->join('expensetypes', 'expense.expensetypesID = expensetypes.expensetypesID');

        if(isset($queryArray['expensetypesID']) && $queryArray['expensetypesID'] != 0) {
            $this->db->where('expense.expensetypesID', $queryArray['expensetypesID']);
        }

        
        if(isset($queryArray['reference_no']) && !empty($queryArray['reference_no'])) {
            $this->db->where('expense.expense_referenceno', $queryArray['reference_no']);
        }

        
        if((isset($queryArray['fromdate']) && $queryArray['fromdate'] != 0) && (isset($queryArray['todate']) && $queryArray['todate'] != 0)) {
            $fromdate = date('Y-m-d', strtotime($queryArray['fromdate']));
            $todate = date('Y-m-d', strtotime($queryArray['todate']));
            $this->db->where('date >=', $fromdate);
            $this->db->where('date <=', $todate);
        }

        $this->db->where('expense.schoolyearID',$schoolyearID);
        $query = $this->db->get();
        return $query->result_array();
    }



}

/* End of file expense_m.php */
/* Location: .//D/xampp/htdocs/school/mvc/models/expense_m.php */