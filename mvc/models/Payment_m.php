<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_m extends MY_Model {

	protected $_table_name = 'payment';
	protected $_primary_key = 'paymentID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "paymentID desc";

	public function __construct() {
		parent::__construct();
	}

	public function order_payment($order) {
		parent::order($order);
	}

	public function get_payment_with_studentrelation_by_studentID($studentID, $schoolyearID) {

		
		$this->db->select('payment.*, invoice.invoiceID, invoice.feetype, invoice.amount, studentrelation.*');
		$this->db->from('payment');
		$this->db->join('studentrelation', 'studentrelation.srstudentID = payment.studentID AND studentrelation.srschoolyearID = payment.schoolyearID', 'LEFT');
		$this->db->join('invoice', 'invoice.invoiceID = payment.invoiceID', 'LEFT');

		if(is_array($studentID)) {
			$this->db->where_in('payment.studentID', $studentID);
		} else {
			$this->db->where(array('payment.studentID' => $studentID));
		}

		$this->db->where(array('payment.schoolyearID' => $schoolyearID));

	
		$this->db->order_by($this->_order_by);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_payment_with_studentrelation($schoolyearID) {
		$this->db->select('payment.*, invoice.invoiceID, invoice.feetype, invoice.amount, studentrelation.*');
		$this->db->from('payment');
		$this->db->join('studentrelation', 'studentrelation.srstudentID = payment.studentID AND studentrelation.srschoolyearID = payment.schoolyearID', 'LEFT');
		$this->db->join('invoice', 'invoice.invoiceID = payment.invoiceID', 'LEFT');
		$this->db->where(array('payment.schoolyearID' => $schoolyearID));

		$usertypeID = $this->session->userdata('usertypeID');
		$userID = $this->session->userdata('loginuserID');
		
		if($usertypeID != 1)
		{
			$this->db->where(array('payment.userID' => $userID));
			$this->db->where(array('payment.usertypeID' => $usertypeID));
		}
		$this->db->order_by($this->_order_by);
		$query = $this->db->get();
		
		return $query->result();
	}

	public function get_payment_with_studentrelation_by_studentID_and_schoolyearID($studentID, $schoolyearID) {
		$this->db->select('payment.*, invoice.invoiceID, invoice.feetype, invoice.feetypeID, invoice.amount, weaverandfine.weaver, weaverandfine.fine');
		$this->db->from('payment');
		$this->db->join('invoice', 'invoice.invoiceID = payment.invoiceID', 'LEFT');
		$this->db->join('weaverandfine', 'payment.paymentID = weaverandfine.paymentID', 'LEFT');
		$this->db->where(array('payment.studentID' => $studentID));
		$this->db->where(array('payment.schoolyearID' => $schoolyearID));
		$this->db->where(array('payment.paymentamount !=' => ''));
		$query = $this->db->get();
		return $query->result();
	}

	public function get_payment_by_sum($invoiceID) {
		$this->db->select_sum('paymentamount');
		$this->db->where(array('invoiceID' => $invoiceID));
		$query = $this->db->get($this->_table_name);
		return $query->row();
	}

	public function get_payment_by_sum_for_edit($invoiceID, $paymentID) {
		$this->db->select_sum('paymentamount');
		$this->db->where(array('invoiceID' => $invoiceID, 'paymentID !=' => $paymentID));
		$query = $this->db->get($this->_table_name);
		return $query->row();
	}

	public function get_payment($array=NULL, $signal=FALSE) {
		$query = parent::get($array, $signal);
		return $query;
	}

	public function get_order_by_payment($array=NULL) {
		$query = parent::get_order_by($array);
		// echo $this->db->last_query();die;
		return $query;
	}

	public function get_order_by_payment_new_original($schoolyearID="",$fee_type=""){

		$invoice_ids =array();
		$sql = "SELECT invoiceID FROM `invoice` i where 1 ";
		if(!empty($fee_type)){
			$sql .= " and i.feetypeID=".$fee_type;
		}
		$invoiceIDs = $this->db->query($sql)->result_array();
		foreach($invoiceIDs as $inv){
			array_push($invoice_ids,$inv['invoiceID']);
		}
		$invoiceID = implode(",",$invoice_ids);
		
		//echo "SELECT i.feetype,i.amount,IFNULL((w.weaver), 0) AS weaver,p.* FROM `payment` p LEFT JOIN `invoice` i ON i.invoiceID = p.invoiceID left join weaverandfine w on w.invoiceID = p.invoiceID WHERE p.`schoolyearID` = '".$schoolyearID."' and p.invoiceID in ($invoiceID) ORDER BY p.`paymentID` desc";die;

		// $query = $this->db->query("SELECT i.feetype,i.amount,IFNULL((w.weaver), 0) AS weaver,p.* FROM `payment` p LEFT JOIN `invoice` i ON i.invoiceID = p.invoiceID left join weaverandfine w on w.invoiceID = p.invoiceID WHERE p.`schoolyearID` = '".$schoolyearID."' and p.invoiceID in ($invoiceID) ORDER BY p.`paymentID` desc")->result();

		$query = $this->db->query("SELECT 
    i.invoiceID,
    i.feetype,
    i.amount,
    IFNULL(i.discount, 0) AS discount,
    IFNULL(w.weaver, 0) AS weaver,
    SUM(p.paymentamount) AS paymentamount,
    p.studentID
    FROM payment p
    LEFT JOIN invoice i ON i.invoiceID = p.invoiceID
    LEFT JOIN (
        SELECT invoiceID, SUM(weaver) AS weaver
        FROM weaverandfine
        GROUP BY invoiceID
    ) w ON w.invoiceID = p.invoiceID
    WHERE p.schoolyearID = '$schoolyearID'
    AND p.invoiceID IN ($invoiceID)
    GROUP BY i.invoiceID, i.feetype, i.amount, i.discount, w.weaver, p.studentID
    ORDER BY i.invoiceID DESC
    
    
    
    ")->result();

    echo $this->db->last_query();die;
		
		// echo "<pre>";print_r($query);die;
		return $query;
	}
	
	
	public function get_order_by_payment_new_multi($schoolyearID, $fee_types = null, $studentID = "") {
    $this->db->select('
        s.studentID,
        s.name AS student_name,
        i.invoiceID,
        i.feetype,
        i.amount,
        IFNULL(i.discount, 0) AS discount,
        IFNULL(w.weaver, 0) AS weaver,
        IFNULL(SUM(p.paymentamount), 0) AS total_paid
    ');
    $this->db->from('invoice i');
    $this->db->join('student s', 's.studentID = i.studentID', 'LEFT');
    $this->db->join(
        '(SELECT invoiceID, SUM(weaver) AS weaver FROM weaverandfine GROUP BY invoiceID) w',
        'w.invoiceID = i.invoiceID',
        'LEFT'
    );
    $this->db->join(
        'payment p',
        'p.invoiceID = i.invoiceID AND p.schoolyearID = ' . $this->db->escape($schoolyearID),
        'LEFT'
    );

    $this->db->where('i.schoolyearID', $schoolyearID);

    if (!empty($fee_types)) {
        if (is_array($fee_types)) {
            $this->db->where_in('i.feetypeID', $fee_types);
        } else {
            $this->db->where('i.feetypeID', $fee_types);
        }
    }

    if (!empty($studentID)) {
        $this->db->where('i.studentID', $studentID);
    }

    $this->db->group_by('i.invoiceID, i.feetype, i.amount, i.discount, w.weaver, s.studentID, s.name');
    $this->db->order_by('i.invoiceID', 'DESC');

    $query = $this->db->get();
    return $query->result();
}


public function get_order_by_payment_new($schoolyearID, $fee_type = null,$studentID="") {
    $this->db->select('
        s.studentID,
        s.name AS student_name,
        i.invoiceID,
        i.feetype,
        i.amount,
        IFNULL(i.discount, 0) AS discount,
        IFNULL(w.weaver, 0) AS weaver,
        IFNULL(SUM(p.paymentamount), 0) AS total_paid
    ');
    $this->db->from('invoice i');
    $this->db->join('student s', 's.studentID = i.studentID', 'LEFT');
    $this->db->join(
        '(SELECT invoiceID, SUM(weaver) AS weaver FROM weaverandfine GROUP BY invoiceID) w',
        'w.invoiceID = i.invoiceID',
        'LEFT'
    );
    $this->db->join('payment p', 'p.invoiceID = i.invoiceID AND p.schoolyearID = ' . $this->db->escape($schoolyearID), 'LEFT');

    $this->db->where('i.schoolyearID', $schoolyearID);

    if (!empty($fee_type)) {
        $this->db->where('i.feetypeID', $fee_type);
    }

	 if (!empty($studentID)) {
        $this->db->where('i.studentID', $studentID);
    }

    $this->db->group_by('i.invoiceID, i.feetype, i.amount, i.discount, w.weaver, s.studentID, s.name');
    $this->db->order_by('i.invoiceID', 'DESC');

    $query = $this->db->get();
    return $query->result();
}

	
public function get_order_by_payment_new_new($schoolyearID, $fee_type = null) {
    $this->db->select('
        s.studentID,
        s.name AS student_name,
        i.invoiceID,
        i.feetype,
        i.amount,
        IFNULL(i.discount,0) AS discount,
        IFNULL(w.weaver,0) AS weaver,
        IFNULL(SUM(p.paymentamount),0) AS total_paid
    ');
    $this->db->from('invoice i');
    $this->db->join('student s', 's.studentID = i.studentID', 'LEFT'); // adjust if student relation differs
    $this->db->join(
        '(SELECT invoiceID, SUM(weaver) AS weaver FROM weaverandfine GROUP BY invoiceID) w',
        'w.invoiceID = i.invoiceID',
        'LEFT'
    );
    $this->db->join('payment p', 'p.invoiceID = i.invoiceID AND p.schoolyearID = ' . $this->db->escape($schoolyearID), 'LEFT');
    
    if (!empty($fee_type)) {
        $this->db->where('i.feetypeID', $fee_type);
    }
    $this->db->where('i.schoolyearID', $schoolyearID);
    
    $this->db->group_by('i.invoiceID, i.feetype, i.amount, i.discount, w.weaver, s.studentID, s.name');
    // Keep only invoices with no payment or zero payment
    $this->db->having('total_paid = 0');

    $query = $this->db->get();
    return $query->result();
}


public function get_order_by_payment_new_summary($schoolyearID, $fee_type = null) {
    $this->db->select('
        s.studentID,
        s.name AS student_name,
        i.invoiceID,
        i.feetype,
        i.amount,
        IFNULL(i.discount,0) AS discount,
        IFNULL(w.weaver,0) AS weaver,
        IFNULL(SUM(p.paymentamount),0) AS total_paid
    ');
    $this->db->from('invoice i');
    $this->db->join('student s', 's.studentID = i.studentID', 'LEFT');
    $this->db->join(
        '(SELECT invoiceID, SUM(weaver) AS weaver FROM weaverandfine GROUP BY invoiceID) w',
        'w.invoiceID = i.invoiceID',
        'LEFT'
    );
    $this->db->join('payment p', 'p.invoiceID = i.invoiceID AND p.schoolyearID = ' . $this->db->escape($schoolyearID), 'LEFT');

    $this->db->where('i.schoolyearID', $schoolyearID);
    if (!empty($fee_type)) {
        $this->db->where('i.feetypeID', $fee_type);
    }

    $this->db->group_by('i.invoiceID, i.feetype, i.amount, i.discount, w.weaver, s.studentID, s.name');
    $this->db->order_by('i.invoiceID', 'DESC');

    $query = $this->db->get();
    return $query->result();
}



	public function get_single_payment($array=NULL) {
		$query = parent::get_single($array);
		return $query;
	}

	public function get_where_in_payment($array, $key=NULL) {
		$query = parent::get_where_in($array, $key);
		return $query;
	}

	public function insert_payment($array) {
		$error = parent::insert($array);
		return TRUE;
	}

	public function insert_batch_payment($array) {
        $id = parent::insert_batch($array);
        return $id;
    }

	public function update_payment($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_payment($id){
		parent::delete($id);
	}

	public function delete_batch_payment($array){
		parent::delete_batch($array);
	}

	public function get_all_payment_for_report($queryArray) {
		$this->db->select('*');
		$this->db->from('payment');
		// $this->db->where('payment.schoolyearID',$queryArray['schoolyearID']);

		if((isset($queryArray['classesID']) && $queryArray['classesID'] != 0) || (isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) || (isset($queryArray['studentID']) && $queryArray['studentID'] != 0)) {

			$this->db->join('globalpayment', 'payment.globalpaymentID = globalpayment.globalpaymentID','LEFT');
			
			if(isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
				$this->db->where('globalpayment.classesID', $queryArray['classesID']);
			}

			if(isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) {
				$this->db->where('globalpayment.sectionID', $queryArray['sectionID']);
			}

			if(isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
				$this->db->where('globalpayment.studentID', $queryArray['studentID']);
			}
		}
		

		if(isset($queryArray['feetypeID']) && $queryArray['feetypeID'] != 0) {
			$this->db->join('invoice', 'payment.invoiceID = invoice.invoiceID','LEFT');
			$this->db->where('invoice.feetypeID', $queryArray['feetypeID']);
		}

		if((isset($queryArray['fromdate']) && $queryArray['fromdate'] != 0) && (isset($queryArray['todate']) && $queryArray['todate'] != 0)) {
			$fromdate = date('Y-m-d', strtotime($queryArray['fromdate']));
			$todate = date('Y-m-d', strtotime($queryArray['todate']));
			$this->db->where('paymentdate >=', $fromdate);
			$this->db->where('paymentdate <=', $todate);
		}

		$userID=$this->session->userdata('loginuserID');
		$usertypeID=$this->session->userdata('usertypeID');
		if($usertypeID!=1)
		{
			$this->db->where(array('payment.userID' => $userID));
			$this->db->where(array('payment.usertypeID' => 5));	
		}
		elseif($queryArray['userID']!=0)
		{
			$this->db->where(array('payment.userID' => $queryArray['userID']));
			$this->db->where(array('payment.usertypeID' => 5));	
		}

		$query = $this->db->get();
		// echo $this->db->last_query();die;
		return $query->result();
	}

	public function get_all_payment_for_report_multi($queryArray) {
    $this->db->select('payment.*');
    $this->db->from('payment');

    // Join globalpayment if filters exist
    if(
        (isset($queryArray['classesID']) && $queryArray['classesID'] != 0) || 
        (isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) || 
        (isset($queryArray['studentID']) && $queryArray['studentID'] != 0)
    ) {
        $this->db->join('globalpayment', 'payment.globalpaymentID = globalpayment.globalpaymentID','LEFT');

        if(isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
            $this->db->where('globalpayment.classesID', $queryArray['classesID']);
        }

        if(isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) {
            $this->db->where('globalpayment.sectionID', $queryArray['sectionID']);
        }

        if(isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
            $this->db->where('globalpayment.studentID', $queryArray['studentID']);
        }
    }

    // ✅ Multi fee type filter
    if (isset($queryArray['feetypeID']) && !empty($queryArray['feetypeID'])) {
        $this->db->join('invoice', 'payment.invoiceID = invoice.invoiceID','LEFT');

        if (is_array($queryArray['feetypeID'])) {
            $this->db->where_in('invoice.feetypeID', $queryArray['feetypeID']);
        } else {
            $this->db->where('invoice.feetypeID', $queryArray['feetypeID']);
        }
    }

    // Date filter
    if (!empty($queryArray['fromdate']) && !empty($queryArray['todate'])) {
        $fromdate = date('Y-m-d', strtotime($queryArray['fromdate']));
        $todate   = date('Y-m-d', strtotime($queryArray['todate']));
        $this->db->where('paymentdate >=', $fromdate);
        $this->db->where('paymentdate <=', $todate);
    }

    // User filter
    $userID    = $this->session->userdata('loginuserID');
    $usertypeID= $this->session->userdata('usertypeID');

    if ($usertypeID != 1) {
        $this->db->where(array('payment.userID' => $userID));
        $this->db->where(array('payment.usertypeID' => 5));    
    } elseif (!empty($queryArray['userID']) && $queryArray['userID'] != 0) {
        $this->db->where(array('payment.userID' => $queryArray['userID']));
        $this->db->where(array('payment.usertypeID' => 5));    
    }

    $query = $this->db->get();
    // echo $this->db->last_query();die;
    return $query->result();
}


	public function get_payments($array) {
		$this->db->select('*');
		$this->db->from($this->_table_name);
		$this->db->join('invoice','payment.invoiceID = invoice.invoiceID');
		$this->db->where('payment.paymentdate >=',$array['fromdate']);
		$this->db->where('payment.paymentdate <=',$array['todate']);
		$this->db->where('payment.schoolyearID',$array['schoolyearID']);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_all_fee_types($array) {
		$this->db->select('payment.*,invoice.feetypeID,invoice.feetype,invoice.classesID');
		$this->db->from('payment');
		$this->db->join('invoice','invoice.invoiceID = payment.invoiceID','LEFT');
		$this->db->where('payment.schoolyearID',$array['schoolyearID']);
		$this->db->where('invoice.classesID',$array['classesID']);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_globalpayments($id) {
        $this->db->select('*');
        $this->db->from($this->_table_name);
        $this->db->join('invoice','payment.invoiceID = invoice.invoiceID');
        $this->db->join('globalpayment','payment.globalpaymentID = globalpayment.globalpaymentID');
        $this->db->where('payment.globalpaymentID',$id);
        $query = $this->db->get();
        return $query->result();

    }

    public function get_singlepayments($id) {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $this->db->select('*');
        $this->db->from($this->_table_name);
        $this->db->where('invoiceID',$id);
        $this->db->where('schoolyearID',$schoolyearID);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_payment_sum($clmn, $array) {
		$query = parent::get_sum($clmn, $array);
		return $query;
	}

	public function get_where_payment_sum($clmn, $wherein, $arrays, $groupID = NULL) {
		$query = parent::get_where_sum($clmn, $wherein, $arrays, $groupID);
		return $query;
	}

	public function get_single_payment_by_globalpaymentID($globalpaymentID) {
		$this->db->select('payment.*,weaverandfine.weaver,weaverandfine.fine,invoice.feetypeID,invoice.paidstatus');
        $this->db->from('globalpayment');
        $this->db->join('payment','globalpayment.globalpaymentID=payment.globalpaymentID');
        $this->db->join('invoice','invoice.invoiceID=payment.invoiceID');
        $this->db->join('weaverandfine','payment.paymentID=weaverandfine.paymentID','LEFT');
        $this->db->where('globalpayment.globalpaymentID',$globalpaymentID);
        $query = $this->db->get();
        return $query->result();
	}


	public function get_payment_with_fine_schoolyear($array) {
		$this->db->select('payment.*,weaverandfine.fine');
		$this->db->from($this->_table_name);
		$this->db->join('weaverandfine','payment.paymentID = weaverandfine.paymentID','LEFT');
		if(isset($array['fromdate']) && isset($array['todate'])) {
			$this->db->where('payment.paymentdate >=',$array['fromdate']);
			$this->db->where('payment.paymentdate <=',$array['todate']);
		} 
		if(isset($array['schoolyearID'])) {
			$this->db->where('payment.schoolyearID',$array['schoolyearID']);
		}
		$query = $this->db->get();
		return $query->result();
	}
	
	public function get_payment_sum_with_qry($condition=NULL) {
		$query = $this->db->query("select SUM(paymentamount) as paymentamount from payment where 1 ".$condition)->row();
		return $query;
	}
}
