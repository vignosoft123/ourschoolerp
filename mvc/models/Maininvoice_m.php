<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maininvoice_m extends MY_Model {

	protected $_table_name = 'maininvoice';
	protected $_primary_key = 'maininvoiceID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "maininvoiceID desc";
	

	public function __construct() {
		parent::__construct();
	}

	public function get_maininvoice_with_studentrelation_bkp($schoolyearID = NULL,$maininvoiceclassesID="") {
		$this->db->select('studentrelation.*,maininvoice.*,parents.father_name as parent_name');
		$this->db->from('maininvoice');
		$this->db->join('studentrelation', 'studentrelation.srstudentID = maininvoice.maininvoicestudentID AND studentrelation.srschoolyearID = maininvoice.maininvoiceschoolyearID', 'LEFT');
		$this->db->join('student', 'student.studentID = maininvoice.maininvoicestudentID AND student.schoolyearID = maininvoice.maininvoiceschoolyearID', 'LEFT');
		

 		$this->db->join('parents', 'parents.parentsID = student.parentID', 'LEFT');

		$this->db->where('maininvoice.maininvoicedeleted_at', 1);
		$this->db->where('maininvoice.maininvoicedeleted_at', 1);
		if($schoolyearID != NULL) {
			$this->db->where('maininvoice.maininvoiceschoolyearID', $schoolyearID);
			$this->db->where('maininvoice.maininvoiceuname IS NOT NULL', null, false);
			$this->db->where('maininvoice.maininvoiceuname !=', '');
		}
		if($maininvoiceclassesID != NULL) {
			$this->db->where('maininvoice.maininvoiceclassesID', $maininvoiceclassesID); 
		}
		$this->db->order_by('maininvoice.maininvoiceID', 'desc');
		$query = $this->db->get();
		// echo $this->db->last_query();die;
		return $query->result();
	}
 
public function get_maininvoice_with_studentrelation($schoolyearID = NULL, $maininvoiceclassesID = "", $limit = NULL, $offset = 0)
{
    // aggregate ONLY the invoices that belong to the same maininvoice + student + schoolyear
    $invAggSubQuery = "
        SELECT 
            i.maininvoiceID,
            i.studentID,
            i.schoolyearID,
            GROUP_CONCAT(DISTINCT i.feetype ORDER BY i.invoiceID SEPARATOR ', ')  AS feetypes,
            GROUP_CONCAT(DISTINCT i.invoiceID ORDER BY i.invoiceID SEPARATOR ', ') AS invoiceIDs
        FROM invoice i
        GROUP BY i.maininvoiceID, i.studentID, i.schoolyearID
    ";

    $this->db->select("
        studentrelation.*,
        maininvoice.*,
        parents.father_name AS parent_name,
        COALESCE(invagg.feetypes, '')  AS feetypes,
        COALESCE(invagg.invoiceIDs, '') AS invoiceIDs
    ", FALSE);

    $this->db->from('maininvoice');

    $this->db->join(
        'studentrelation',
        'studentrelation.srstudentID = maininvoice.maininvoicestudentID
         AND studentrelation.srschoolyearID = maininvoice.maininvoiceschoolyearID',
        'LEFT'
    );

    $this->db->join(
        'student',
        'student.studentID = maininvoice.maininvoicestudentID
         AND student.schoolyearID = maininvoice.maininvoiceschoolyearID',
        'LEFT'
    );

    $this->db->join('parents', 'parents.parentsID = student.parentID', 'LEFT');

    // LEFT JOIN the aggregated invoice data, matched by maininvoice + student + schoolyear
    $this->db->join(
        "($invAggSubQuery) invagg",
        "invagg.maininvoiceID = maininvoice.maininvoiceID
         AND invagg.studentID   = maininvoice.maininvoicestudentID
         AND invagg.schoolyearID = maininvoice.maininvoiceschoolyearID",
        'LEFT',
        FALSE // do not escape, this is a subquery
    );

    $this->db->where('maininvoice.maininvoicedeleted_at', 1);

    if ($schoolyearID !== NULL) {
        $this->db->where('maininvoice.maininvoiceschoolyearID', $schoolyearID);
        $this->db->where('studentrelation.srschoolyearID', $schoolyearID);
        $this->db->where('maininvoice.maininvoiceuname IS NOT NULL', NULL, FALSE);
        $this->db->where('maininvoice.maininvoiceuname !=', '');
    }

    // if ($maininvoiceclassesID !== NULL) {
    //     $this->db->where('maininvoice.maininvoiceclassesID', $maininvoiceclassesID);
    // }

	if (!empty($maininvoiceclassesID) && $maininvoiceclassesID > 0) {
    $this->db->where('maininvoice.maininvoiceclassesID', $maininvoiceclassesID);
}

    $this->db->order_by('maininvoice.maininvoiceID', 'DESC');

    // Apply limit and offset for pagination
    if ($limit !== NULL) {
        $this->db->limit($limit, $offset);
    } elseif ($offset > 0) {
        // If no limit but offset is provided, apply offset only
        $this->db->limit(999999, $offset);
    }

    $query = $this->db->get();
    // echo $this->db->last_query(); die;
    return $query->result();
}



public function get_maininvoice_with_studentrelation_new($schoolyearID = NULL, $maininvoiceclassesID = "")
{
    $this->db->select('
        studentrelation.*,
        maininvoice.*,
        parents.father_name as parent_name,
        GROUP_CONCAT(invoice.feetype SEPARATOR ", ") as feetypes,
        GROUP_CONCAT(invoice.invoiceID SEPARATOR ", ") as invoiceIDs
    ');
    $this->db->from($this->_table_name);
    $this->db->join(
        'studentrelation',
        'studentrelation.srstudentID = maininvoice.maininvoicestudentID 
         AND studentrelation.srschoolyearID = maininvoice.maininvoiceschoolyearID',
        'LEFT'
    );
    $this->db->join(
        'student',
        'student.studentID = maininvoice.maininvoicestudentID 
         AND student.schoolyearID = maininvoice.maininvoiceschoolyearID',
        'LEFT'
    );
    $this->db->join('parents', 'parents.parentsID = student.parentID', 'LEFT');
    $this->db->join(
        'invoice',
        'invoice.maininvoiceID = maininvoice.maininvoiceID 
         AND invoice.studentID = studentrelation.srstudentID',
        'LEFT'
    );

    $this->db->where('maininvoice.maininvoicedeleted_at', 1);

    if ($schoolyearID != NULL) {
        $this->db->where('maininvoice.maininvoiceschoolyearID', $schoolyearID);
        $this->db->where('studentrelation.srschoolyearID', $schoolyearID);
    }

    if ($maininvoiceclassesID != NULL) {
        $this->db->where('maininvoice.maininvoiceclassesID', $maininvoiceclassesID);
    }

    $this->db->group_by('maininvoice.maininvoiceID'); // needed for GROUP_CONCAT
    $this->db->order_by('maininvoice.maininvoiceID', 'desc');

    $query = $this->db->get();
    // echo $this->db->last_query();die;
    return $query->result();
}

public function get_maininvoice_with_studentrelation_new1($schoolyearID = NULL, $maininvoiceclassesID = "")
{
    $this->db->select('
        studentrelation.*,
        maininvoice.*,
        parents.father_name as parent_name,
        IFNULL(GROUP_CONCAT(DISTINCT invoice.feetype ORDER BY invoice.invoiceID SEPARATOR ", "), "") as feetypes,
        IFNULL(GROUP_CONCAT(DISTINCT invoice.invoiceID ORDER BY invoice.invoiceID SEPARATOR ", "), "") as invoiceIDs
    ', FALSE);

    $this->db->from($this->_table_name);

    $this->db->join(
        'studentrelation',
        'studentrelation.srstudentID = maininvoice.maininvoicestudentID 
         AND studentrelation.srschoolyearID = maininvoice.maininvoiceschoolyearID',
        'LEFT'
    );

    $this->db->join(
        'student',
        'student.studentID = maininvoice.maininvoicestudentID 
         AND student.schoolyearID = maininvoice.maininvoiceschoolyearID',
        'LEFT'
    );

    $this->db->join('parents', 'parents.parentsID = student.parentID', 'LEFT');

    // only include invoice rows that belong to the same maininvoice + same student
    $this->db->join(
        'invoice',
        'invoice.maininvoiceID = maininvoice.maininvoiceID 
         AND invoice.studentID = studentrelation.srstudentID',
        'LEFT'
    );

    $this->db->where('maininvoice.maininvoicedeleted_at', 1);

    if ($schoolyearID !== NULL) {
        $this->db->where('maininvoice.maininvoiceschoolyearID', $schoolyearID);
        $this->db->where('studentrelation.srschoolyearID', $schoolyearID);
    }

    if ($maininvoiceclassesID !== NULL) {
        $this->db->where('maininvoice.maininvoiceclassesID', $maininvoiceclassesID);
    }

    $this->db->group_by('maininvoice.maininvoiceID');
    $this->db->order_by('maininvoice.maininvoiceID', 'desc');

    $query = $this->db->get();
    // echo $this->db->last_query();die;
    return $query->result();
}




	public function get_maininvoice_with_studentrelation_by_studentID($studentID, $schoolyearID = NULL) {
		$this->db->select('*');
		$this->db->from($this->_table_name);
		$this->db->join('studentrelation', 'studentrelation.srstudentID = maininvoice.maininvoicestudentID AND studentrelation.srschoolyearID = maininvoice.maininvoiceschoolyearID', 'LEFT');
		$this->db->where('maininvoice.maininvoicestudentID', $studentID);
		$this->db->where('maininvoice.maininvoicedeleted_at', 1);

		if($schoolyearID != NULL) {
			$this->db->where('maininvoice.maininvoiceschoolyearID', $schoolyearID);
			$this->db->where('studentrelation.srschoolyearID', $schoolyearID);
		}
		$this->db->order_by('maininvoice.maininvoiceID', 'desc');
		$query = $this->db->get();
		return $query->result();
	}


	public function get_maininvoice_with_studentrelation_by_multi_studentID($studentIDArrays, $schoolyearID = NULL) {
		$this->db->select('*');
		$this->db->from($this->_table_name);
		$this->db->join('studentrelation', 'studentrelation.srstudentID = maininvoice.maininvoicestudentID AND studentrelation.srschoolyearID = maininvoice.maininvoiceschoolyearID', 'LEFT');
		$this->db->where('maininvoice.maininvoicedeleted_at', 1);

		if(customCompute($studentIDArrays)) {
			$this->db->where_in('maininvoice.maininvoicestudentID', $studentIDArrays);
		}

		if($schoolyearID != NULL) {
			$this->db->where('maininvoice.maininvoiceschoolyearID', $schoolyearID);
			$this->db->where('studentrelation.srschoolyearID', $schoolyearID);
		}

		$this->db->order_by('maininvoice.maininvoiceID', 'desc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_maininvoice_with_studentrelation_by_maininvoiceID($invoiceID, $schoolyearID = NULL) {
		$this->db->select('studentrelation.*,maininvoice.*,parents.father_name as parent_name');
		$this->db->from($this->_table_name);
		$this->db->join('studentrelation', 'studentrelation.srstudentID = maininvoice.maininvoicestudentID AND studentrelation.srschoolyearID = maininvoice.maininvoiceschoolyearID', 'LEFT');
		$this->db->join('student', 'student.studentID = maininvoice.maininvoicestudentID AND student.schoolyearID = maininvoice.maininvoiceschoolyearID', 'LEFT');

 		$this->db->join('parents', 'parents.parentsID = student.parentID', 'LEFT');
		$this->db->where('maininvoice.maininvoiceID', $invoiceID);
		$this->db->where('maininvoice.maininvoicedeleted_at', 1);

		if($schoolyearID != NULL) {
			$this->db->where('maininvoice.maininvoiceschoolyearID', $schoolyearID);
			$this->db->where('studentrelation.srschoolyearID', $schoolyearID);
		}

		$this->db->order_by('maininvoice.maininvoiceID', 'desc');
		$query = $this->db->get();
		return $query->row();
	}

	public function get_maininvoice($array=NULL, $signal=FALSE) {
		$query = parent::get($array, $signal);
		return $query;
	}

	public function get_order_by_maininvoice($array=NULL) {
		$query = parent::get_order_by($array);
		return $query;
	}

	public function get_single_maininvoice($array=NULL) {
		$query = parent::get_single($array);
		return $query;
	}

	public function insert_maininvoice($array) {
		$error = parent::insert($array);
		return $error;
	}

	public function insert_batch_maininvoice($array) {
		$id = parent::insert_batch($array);
		return $id;
	}

	public function update_maininvoice($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_maininvoice($id){
		parent::delete($id);
	}

	// Method to count total maininvoices for pagination
	public function count_maininvoice_with_studentrelation($schoolyearID = NULL, $maininvoiceclassesID = "") {
		$this->db->from('maininvoice');
		$this->db->join(
			'studentrelation',
			'studentrelation.srstudentID = maininvoice.maininvoicestudentID
			 AND studentrelation.srschoolyearID = maininvoice.maininvoiceschoolyearID',
			'LEFT'
		);

		$this->db->where('maininvoice.maininvoicedeleted_at', 1);

		if ($schoolyearID !== NULL) {
			$this->db->where('maininvoice.maininvoiceschoolyearID', $schoolyearID);
			$this->db->where('studentrelation.srschoolyearID', $schoolyearID);
			$this->db->where('maininvoice.maininvoiceuname IS NOT NULL', NULL, FALSE);
			$this->db->where('maininvoice.maininvoiceuname !=', '');
		}

		if (!empty($maininvoiceclassesID) && $maininvoiceclassesID > 0) {
			$this->db->where('maininvoice.maininvoiceclassesID', $maininvoiceclassesID);
		}

		return $this->db->count_all_results();
	}
}

/* End of file invoice_m.php */
/* Location: .//D/xampp/htdocs/school/mvc/models/invoice_m.php */