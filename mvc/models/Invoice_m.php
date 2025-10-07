<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice_m extends MY_Model {

	protected $_table_name = 'invoice';
	protected $_primary_key = 'invoiceID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "invoiceID asc";
	

	public function __construct() {
		parent::__construct();
	}

	public function get_invoice_with_studentrelation() {
		$this->db->select('*');
		$this->db->from('invoice');
		$this->db->join('studentrelation', 'studentrelation.srstudentID = invoice.studentID AND studentrelation.srclassesID = invoice.classesID AND studentrelation.srschoolyearID = invoice.schoolyearID', 'LEFT');
		$this->db->where('invoice.deleted_at', 1);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_invoice_with_studentrelation_by_studentID($studentID) {
		$this->db->select('*');
		$this->db->from('invoice');
		$this->db->join('studentrelation', 'studentrelation.srstudentID = invoice.studentID AND studentrelation.srclassesID = invoice.classesID AND studentrelation.srschoolyearID = invoice.schoolyearID', 'LEFT');
		$this->db->where('invoice.studentID', $studentID);
		$this->db->where('invoice.deleted_at', 1);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_invoice_with_studentrelation_by_invoiceID($invoiceID) {
		$this->db->select('*');
		$this->db->from('invoice');
		$this->db->join('studentrelation', 'studentrelation.srstudentID = invoice.studentID AND studentrelation.srclassesID = invoice.classesID AND studentrelation.srschoolyearID = invoice.schoolyearID', 'LEFT');
		$this->db->where('invoice.invoiceID', $invoiceID);
		$this->db->where('invoice.deleted_at', 1);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_invoice($array=NULL, $signal=FALSE) {
		$query = parent::get($array, $signal);
		return $query;
	}

	public function get_order_by_invoice_join_maininvoice_bkp($studentID=NULL,$schoolyearID=NULL,$deleted_at=NULL) {
		if(!empty($studentID) && !empty($schoolyearID) && !empty($deleted_at) ){
			$sql ='select i.* from invoice i inner join maininvoice m on m.maininvoiceID = i.maininvoiceID where studentID = "'.$studentID.'" and schoolyearID = "'.$schoolyearID.'" and deleted_at = "'.$deleted_at.'" ';
			// $sql ='select i.* from invoice i left join maininvoice m on m.maininvoiceID = i.maininvoiceID where schoolyearID = "'.$schoolyearID.'" and deleted_at = "'.$deleted_at.'" and m.maininvoicestudentID = "'.$studentID.'" ';
		return $this->db->query($sql)->result();
		}else{
			return array();
		}
	}

	public function get_order_by_invoice_join_maininvoice($studentID = NULL, $schoolyearID = NULL, $deleted_at = NULL) 
{
    if (!empty($studentID) && !empty($schoolyearID) && !empty($deleted_at)) {
        $sql = '
            SELECT i.* 
            FROM invoice i
            INNER JOIN maininvoice m 
                ON m.maininvoiceID = i.maininvoiceID 
               AND m.maininvoicestudentID = i.studentID 
               AND m.maininvoiceschoolyearID = i.schoolyearID
            WHERE i.studentID = "'.$studentID.'" 
              AND i.schoolyearID = "'.$schoolyearID.'" 
              AND i.deleted_at = "'.$deleted_at.'" 
         ';
// echo $sql;die;
        return $this->db->query($sql)->result();
    } else {
        return [];
    }
}


	public function get_order_by_invoice($array=NULL) {
		$query = parent::get_order_by($array);
		// echo $this->db->last_query();die;
		return $query;
	}


	public function get_single_invoice($array=NULL) {
		$query = parent::get_single($array);
		return $query;
	}

	public function insert_invoice($array) {
		$error = parent::insert($array);
		return $error;
	}

	public function insert_batch_invoice($array) {
		$id = parent::insert_batch($array);
		return $id;
	}

	public function update_invoice($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function update_invoice_by_maininvoiceID($data, $id = NULL) {
		$this->db->set($data);
		$this->db->where('maininvoiceID', $id);
		$this->db->update($this->_table_name);
		return $id;
	}

	public function update_batch_invoice($data, $id = NULL) {
        parent::update_batch($data, $id);
        return TRUE;
    }

	public function delete_invoice($id){
		parent::delete($id);
	}

	public function delete_invoice_by_maininvoiceID($id){
		$this->db->delete($this->_table_name, array('maininvoiceID' => $id)); 
		return TRUE;
	}	

	public function get_all_duefees_for_report($queryArray) {
		$this->db->select('invoice.*');
		$this->db->from('invoice');
		$this->db->where('invoice.schoolyearID',$queryArray['schoolyearID']);
        $this->db->join('student', 'student.studentID = invoice.studentID', 'LEFT');
		$this->db->where('student.active', 1);


		if((isset($queryArray['classesID']) && $queryArray['classesID'] != 0) || (isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) || (isset($queryArray['studentID']) && $queryArray['studentID'] != 0)) {
			
			if(isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
				$this->db->where('invoice.classesID', $queryArray['classesID']);
			}

			if(isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
				$this->db->where('invoice.studentID', $queryArray['studentID']);
			}
		}

		if(isset($queryArray['feetypeID']) && $queryArray['feetypeID'] != 0) {
			$this->db->where('invoice.feetypeID', $queryArray['feetypeID']);
		}

		if((isset($queryArray['fromdate']) && $queryArray['fromdate'] != 0) && (isset($queryArray['todate']) && $queryArray['todate'] != 0)) {
			$fromdate = date('Y-m-d', strtotime($queryArray['fromdate']));
			$todate = date('Y-m-d', strtotime($queryArray['todate']));
			$this->db->where('invoice.create_date >=', $fromdate);
			$this->db->where('invoice.create_date <=', $todate);
		}

		$this->db->where('invoice.paidstatus !=', 2);
		$this->db->where('invoice.deleted_at', 1);

		$query = $this->db->get();
		return $query->result();
	}

	/*public function get_all_duefees_for_report_multi($queryArray) {
    $this->db->select('invoice.*');
    $this->db->from('invoice');
    $this->db->join('student', 'student.studentID = invoice.studentID', 'LEFT');
    $this->db->where('invoice.schoolyearID', $queryArray['schoolyearID']);
    $this->db->where('student.active', 1);

    if(
        (isset($queryArray['classesID']) && $queryArray['classesID'] != 0) || 
        (isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) || 
        (isset($queryArray['studentID']) && $queryArray['studentID'] != 0)
    ) {
        if(isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
            $this->db->where('invoice.classesID', $queryArray['classesID']);
        }

        if(isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
            $this->db->where('invoice.studentID', $queryArray['studentID']);
        }
    }

    // ✅ Multi fee type filter
    if (isset($queryArray['feetypeID']) && !empty($queryArray['feetypeID'])) {
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
        $this->db->where('invoice.create_date >=', $fromdate);
        $this->db->where('invoice.create_date <=', $todate);
    }

    $this->db->where('invoice.paidstatus !=', 2);
    $this->db->where('invoice.deleted_at', 1);

    $query = $this->db->get();
    // echo $this->db->last_query();die;
    return $query->result();
}


public function get_all_duefees_for_report_multi($queryArray) {
    $this->db->select('invoice.*, student.sectionID, student.classesID');
    $this->db->from('invoice');
    $this->db->join('student', 'student.studentID = invoice.studentID', 'LEFT');
    $this->db->where('invoice.schoolyearID', $queryArray['schoolyearID']);
    $this->db->where('student.active', 1);

    // ✅ Filters
    if(isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
        $this->db->where('invoice.classesID', $queryArray['classesID']);
    }

    if(isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) {
        $this->db->where('student.sectionID', $queryArray['sectionID']);
    }

    if(isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
        $this->db->where('invoice.studentID', $queryArray['studentID']);
    }

    // ✅ Multi fee type filter
    if (isset($queryArray['feetypeID']) && !empty($queryArray['feetypeID'])) {
        if (is_array($queryArray['feetypeID'])) {
            $this->db->where_in('invoice.feetypeID', $queryArray['feetypeID']);
        } else {
            $this->db->where('invoice.feetypeID', $queryArray['feetypeID']);
        }
    }

    // ✅ Date filter
    if (!empty($queryArray['fromdate']) && !empty($queryArray['todate'])) {
        $fromdate = date('Y-m-d', strtotime($queryArray['fromdate']));
        $todate   = date('Y-m-d', strtotime($queryArray['todate']));
        $this->db->where('invoice.create_date >=', $fromdate);
        $this->db->where('invoice.create_date <=', $todate);
    }

    $this->db->where('invoice.paidstatus !=', 2);
    $this->db->where('invoice.deleted_at', 1);

    $query = $this->db->get();
    return $query->result();
}*/

public function get_all_duefees_for_report_multi($queryArray) {
    if (empty($queryArray['schoolyearID'])) {
        return [];
    }

    $schoolyearID = $queryArray['schoolyearID'];

    $this->db->select('invoice.*, student.sectionID, student.classesID, maininvoice.maininvoiceclassesID');
    $this->db->from('invoice');

    // Strict join: make sure the invoice actually belongs to the same maininvoice & student & schoolyear
    $this->db->join(
        'maininvoice',
        'maininvoice.maininvoiceID = invoice.maininvoiceID
         AND maininvoice.maininvoicestudentID = invoice.studentID
         AND maininvoice.maininvoiceschoolyearID = invoice.schoolyearID
         AND maininvoice.maininvoicedeleted_at = 1',
        'INNER', 
        FALSE
    );

    // student joined from invoice.studentID (invoice.studentID should equal maininvoice.maininvoicestudentID already)
    $this->db->join('student', 'student.studentID = invoice.studentID', 'LEFT');

    // required filters
    $this->db->where('invoice.schoolyearID', $schoolyearID);
    $this->db->where('student.active', 1);
    $this->db->where('invoice.deleted_at', 1);   // keep your existing semantics: 1 = active (as you used earlier)
    $this->db->where('invoice.paidstatus !=', 2); // due or partially paid

    // filters that map to maininvoice (class filter)
    if (isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
        $this->db->where('maininvoice.maininvoiceclassesID', $queryArray['classesID']);
    }

    if (isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) {
        $this->db->where('student.sectionID', $queryArray['sectionID']);
    }

    if (isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
        $this->db->where('invoice.studentID', $queryArray['studentID']);
    }

    if (isset($queryArray['feetypeID']) && $queryArray['feetypeID'] !== '') {
        if (is_array($queryArray['feetypeID'])) {
            $this->db->where_in('invoice.feetypeID', $queryArray['feetypeID']);
        } else {
            $this->db->where('invoice.feetypeID', $queryArray['feetypeID']);
        }
    }

    if (!empty($queryArray['fromdate']) && !empty($queryArray['todate'])) {
        $fromdate = date('Y-m-d', strtotime($queryArray['fromdate']));
        $todate   = date('Y-m-d', strtotime($queryArray['todate']));
        $this->db->where('invoice.create_date >=', $fromdate);
        $this->db->where('invoice.create_date <=', $todate);
    }

    $query = $this->db->get();
    // echo $this->db->last_query(); die;
    return $query->result();
}




	public function get_all_balancefees_for_report($queryArray) { 
		$this->db->select('*');
		$this->db->from('invoice ');
		$this->db->join('maininvoice m','m.maininvoiceID = invoice.maininvoiceID','inner');	//newly added the condition for duplicate trasport fee
		$this->db->where('invoice.schoolyearID',$queryArray['schoolyearID']);

		if((isset($queryArray['classesID']) && $queryArray['classesID'] != 0) || (isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) || (isset($queryArray['studentID']) && $queryArray['studentID'] != 0) || (isset($queryArray['feetypeID']) && $queryArray['feetypeID'] != 0) ) {
			
			if(isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
				$this->db->where('invoice.classesID', $queryArray['classesID']);
			}

			if(isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
				$this->db->where('invoice.studentID', $queryArray['studentID']);
				// $this->db->where('m.maininvoicestudentID', $queryArray['studentID']);
			}

			if(isset($queryArray['feetypeID']) && $queryArray['feetypeID'] != 0) {
				$this->db->where('invoice.feetypeID', $queryArray['feetypeID']);
			}

		}
		$this->db->where('invoice.deleted_at', 1);

		$query = $this->db->get();
		// echo $this->db->last_query();die;
		return $query->result();
	}

	public function get_all_balancefees_for_report_multi_bkp($queryArray) { 
    $this->db->select('*');
    $this->db->from('invoice');
    $this->db->join('maininvoice m','m.maininvoiceID = invoice.maininvoiceID','inner'); // condition for duplicate transport fee
    $this->db->where('invoice.schoolyearID', $queryArray['schoolyearID']);

    if (
        (isset($queryArray['classesID']) && $queryArray['classesID'] != 0) ||
        (isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) ||
        (isset($queryArray['studentID']) && $queryArray['studentID'] != 0) ||
        (isset($queryArray['feetypeID']) && !empty($queryArray['feetypeID']))
    ) {
        if (isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
            $this->db->where('invoice.classesID', $queryArray['classesID']);
        }

        if (isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
            $this->db->where('invoice.studentID', $queryArray['studentID']);
            // $this->db->where('m.maininvoicestudentID', $queryArray['studentID']);
        }

        if (isset($queryArray['feetypeID']) && !empty($queryArray['feetypeID'])) {
            if (is_array($queryArray['feetypeID'])) {
                $this->db->where_in('invoice.feetypeID', $queryArray['feetypeID']);
            } else {
                $this->db->where('invoice.feetypeID', $queryArray['feetypeID']);
            }
        }
    }

    $this->db->where('invoice.deleted_at', 1);

    $query = $this->db->get();
    // echo $this->db->last_query();die;
    return $query->result();
}


public function get_all_balancefees_for_report_multi($queryArray) 
{
    if (empty($queryArray['schoolyearID'])) {
        return [];
    }

    $this->db->select('invoice.*, m.maininvoiceclassesID, m.maininvoicestudentID, student.sectionID, student.classesID');
    $this->db->from('invoice');

    // ✅ Strict join to avoid duplicate/unrelated fee types
    $this->db->join(
        'maininvoice m',
        'm.maininvoiceID = invoice.maininvoiceID
         AND m.maininvoicestudentID = invoice.studentID
         AND m.maininvoiceschoolyearID = invoice.schoolyearID
         AND m.maininvoicedeleted_at = 1',
        'INNER',
        FALSE
    );

    $this->db->join('student', 'student.studentID = invoice.studentID', 'LEFT');

    // Filters
    $this->db->where('invoice.schoolyearID', $queryArray['schoolyearID']);
    $this->db->where('invoice.deleted_at', 1);
    $this->db->where('student.active', 1);

    if (isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
        $this->db->where('m.maininvoiceclassesID', $queryArray['classesID']);
    }

    if (isset($queryArray['sectionID']) && $queryArray['sectionID'] != 0) {
        $this->db->where('student.sectionID', $queryArray['sectionID']);
    }

    if (isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
        $this->db->where('invoice.studentID', $queryArray['studentID']);
    }

    if (isset($queryArray['feetypeID']) && !empty($queryArray['feetypeID'])) {
        if (is_array($queryArray['feetypeID'])) {
            $this->db->where_in('invoice.feetypeID', $queryArray['feetypeID']);
        } else {
            $this->db->where('invoice.feetypeID', $queryArray['feetypeID']);
        }
    }

    $query = $this->db->get();
    // echo $this->db->last_query(); die;
    return $query->result();
}


	public function get_dueamount($array) {
		$this->db->select('invoice.*,weaverandfine.weaver,weaverandfine.fine');
		$this->db->from('invoice');
		$this->db->join('weaverandfine','invoice.invoiceID=weaverandfine.invoiceID','LEFT');
		$this->db->where('invoice.schoolyearID',$array['schoolyearID']);
		$this->db->where('invoice.classesID',$array['classesID']);
		$this->db->where('invoice.deleted_at', 1);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_invoice_sum($array = NULL) {
		if(isset($array['maininvoiceID'])) {
			$string = "SELECT SUM(amount) AS amount, SUM(discount) AS discount, SUM((amount/100)*discount) AS discountamount, SUM(amount-((amount/100)*discount)) AS invoiceamount FROM ".$this->_table_name." WHERE maininvoiceID = '".$array['maininvoiceID']."'";
		} else {
			$string = "SELECT SUM(amount) AS amount, SUM(discount) AS discount, SUM((amount/100)*discount) AS discountamount, SUM(amount-((amount/100)*discount)) AS invoiceamount FROM ".$this->_table_name;
		}

		$query = $this->db->query($string);
		return $query->row();
	}

	// public function updateInvoices($invoiceIDs) {
    //     $this->db->where_in('invoiceID', $invoiceIDs)
    //              ->update('invoice', ['deleted_at' => 0]);
    // }

    // public function updateMainInvoices($maininvoiceIDs) {
    //     $this->db->where_in('maininvoiceID', $maininvoiceIDs)
    //              ->update('maininvoice', ['maininvoicedeleted_at' => 0]);
    // }

    // public function updateInvoice($invoiceID) {
    //     $this->db->where('invoiceID', $invoiceID)
    //              ->update('invoice', ['deleted_at' => 0]);
    // }

    // public function updateMainInvoice($maininvoiceID) {
    //     $this->db->where('maininvoiceID', $maininvoiceID)
    //              ->update('maininvoice', ['maininvoicedeleted_at' => 0]);
    // }


	 // Check if the invoice has a payment
	 private function hasPayment($invoiceID) {
        $this->db->select('paymentamount');
        $this->db->from('payment');
        $this->db->where('invoiceID', $invoiceID);
        $this->db->where('paymentamount IS NOT NULL AND paymentamount > 0');
        $query = $this->db->get();

        return $query->num_rows() > 0; // True if a payment exists
    }

    public function updateInvoices($invoiceIDs) {
        $filteredIDs = [];

        foreach ($invoiceIDs as $id) {
            if (!$this->hasPayment($id)) { // Only update if no payment
                $filteredIDs[] = $id;
            }
        }

        if (!empty($filteredIDs)) {
            $this->db->where_in('invoiceID', $filteredIDs)
                     ->update('invoice', ['deleted_at' => 0]);
        }
    }

    public function updateMainInvoices($maininvoiceIDs) {
        // Only update maininvoice if ALL related invoices are deletable
        foreach ($maininvoiceIDs as $maininvoiceID) {
            $this->db->select('invoiceID');
            $this->db->from('invoice');
            $this->db->where('maininvoiceID', $maininvoiceID);
            $query = $this->db->get();
            
            $shouldUpdate = true;
            foreach ($query->result() as $row) {
                if ($this->hasPayment($row->invoiceID)) {
                    $shouldUpdate = false;
                    break; // Skip update if any invoice has a payment
                }
            }

            if ($shouldUpdate) {
                $this->db->where('maininvoiceID', $maininvoiceID)
                         ->update('maininvoice', ['maininvoicedeleted_at' => 0]);
            }
        }
    }

    public function updateInvoice($invoiceID) {
        if (!$this->hasPayment($invoiceID)) {
            $this->db->where('invoiceID', $invoiceID)
                     ->update('invoice', ['deleted_at' => 0]);
        }
    }

    public function updateMainInvoice($maininvoiceID) {
        $this->db->select('invoiceID');
        $this->db->from('invoice');
        $this->db->where('maininvoiceID', $maininvoiceID);
        $query = $this->db->get();
        
        $shouldUpdate = true;
        foreach ($query->result() as $row) {
            if ($this->hasPayment($row->invoiceID)) {
                $shouldUpdate = false;
                break; // Skip update if any invoice has a payment
            }
        }

        if ($shouldUpdate) {
            $this->db->where('maininvoiceID', $maininvoiceID)
                     ->update('maininvoice', ['maininvoicedeleted_at' => 0]);
        }
    }

public function get_schoolyear()
{
    $this->db->order_by('schoolyearID', 'DESC');
    return $this->db->get('schoolyear')->result();
}

}