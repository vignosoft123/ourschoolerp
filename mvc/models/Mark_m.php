<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mark_m extends MY_Model {

	protected $_table_name = 'mark';
	protected $_primary_key = 'markID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "subject asc";

	function __construct() {
		parent::__construct();
	}

	public function get_mark($array=NULL, $signal=FALSE) {
		$query = parent::get($array, $signal);
		return $query;
	}

	public function get_single_mark($array) {
		$query = parent::get_single($array);
		return $query;
	}

	public function get_single_mark_new($params)
	{
		// Check if the necessary parameters are passed
		if (empty($params['examID']) || empty($params['classesID']) || empty($params['subjectID']) || empty($params['studentID']) || empty($params['schoolyearID'])) {
			return false; // Return false if any required parameter is missing
		}
	
		// Construct the SQL query to fetch the mark and attendance information
		$this->db->select('mark.mark AS mark, mark.eattendance');
		$this->db->from('mark');
		$this->db->join('markrelation', 'markrelation.markID = mark.markID', 'left'); // Assuming markrelation table is still relevant for this join
		$this->db->where('mark.schoolyearID', $params['schoolyearID']);
		$this->db->where('mark.examID', $params['examID']);
		$this->db->where('mark.classesID', $params['classesID']);
		$this->db->where('mark.studentID', $params['studentID']); // Corrected column name from studentId to studentID
		$this->db->where('mark.subjectID', $params['subjectID']); // Corrected column name for subjectID
		$this->db->where('markrelation.markpercentageID', isset($params['markpercentageID']) ? $params['markpercentageID'] : null); // Added a fallback for markpercentageID if not passed
		
		// Execute the query
		$query = $this->db->get();
	
		// Check if any record was returned
		if ($query->num_rows() > 0) {
			// Return the row (single record)
			return $query->row();
		} else {
			// Return false if no record found
			return false;
		}
	}

	public function get_single_markrelation($conditions)
	{
		$this->db->select('markrelation.*, mark.markID');
		$this->db->from('markrelation');
		$this->db->join('mark', 'mark.markID = markrelation.markID');
	
		// Apply conditions on mark table
		if (isset($conditions['examID'])) {
			$this->db->where('mark.examID', $conditions['examID']);
		}
		if (isset($conditions['classesID'])) {
			$this->db->where('mark.classesID', $conditions['classesID']);
		}
		if (isset($conditions['subjectID'])) {
			$this->db->where('mark.subjectID', $conditions['subjectID']);
		}
		if (isset($conditions['studentID'])) {
			$this->db->where('mark.studentID', $conditions['studentID']);
		}
		if (isset($conditions['schoolyearID'])) {
			$this->db->where('mark.schoolyearID', $conditions['schoolyearID']);
		}
		if (isset($conditions['markpercentageID'])) {
			$this->db->where('markrelation.markpercentageID', $conditions['markpercentageID']);
		}
	
		$query = $this->db->get();
		return $query->row(); // Return single result or NULL
	}
	
// Method to update markrelation
public function update_markrelation($data, $markID, $markpercentageID)
{
    $this->db->where('markID', $markID);
    $this->db->where('markpercentageID', $markpercentageID);
    $this->db->update('markrelation', ['mark' => $data['mark']]);
    return $this->db->affected_rows();
}

// Method to insert new markrelation
public function insert_markrelation($data)
{
    $this->db->insert('markrelation', $data);
    return $this->db->insert_id();
}

	
	


	public function get_order_by_mark($array=NULL) {
		$query = parent::get_order_by($array);
		return $query;
	}

	
 	public function get_order_by_mark_new($array = NULL) {
		if ($array) {
			$schoolyearID = $array['schoolyearID'];
			$examID = $array['examID'];
			$classesID = $array['classesID'];
	
			$sql = "SELECT m.*
					FROM mark m
					INNER JOIN (
						SELECT studentID, MIN(markID) as minMarkID
						FROM mark
						WHERE schoolyearID = ? AND examID = ? AND classesID = ?
						GROUP BY studentID,subjectID
					) first ON m.markID = first.minMarkID
					ORDER BY m.studentID DESC";
	
			$query = $this->db->query($sql, array($schoolyearID, $examID, $classesID));
			return $query->result();
		} else {
			return array();
		}
	}

	public function get_order_by_marka($array = NULL) {
		if ($array) {
			$schoolyearID = $array['schoolyearID'];
			$examID = $array['examID'];
			$classesID = $array['classesID'];
	
			$sql = "SELECT m.*
					FROM mark m
					INNER JOIN (
						SELECT studentID, MAX(markID) as maxMarkID
						FROM mark
						WHERE schoolyearID = ? AND examID = ? AND classesID = ?
						GROUP BY studentID,subjectID
					) latest ON m.markID = latest.maxMarkID
					ORDER BY m.studentID DESC";
	
			$query = $this->db->query($sql, array($schoolyearID, $examID, $classesID));
			return $query->result();
		} else {
			return array();
		}
	}


	
	
	
	public function insert_mark($array) {
		$error = parent::insert($array);
		return TRUE;
	}

	public function insert_batch_mark($array) {
		$id = parent::insert_batch($array);
		return $id;
	}

	public function update_mark($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function update_mark_classes($array, $id) {
		$this->db->update($this->_table_name, $array, $id);
		return $id;
	}

	public function update_mark_with_condition($array, $id) {
		$this->db->update($this->_table_name, $array, $id);
		return $this->db->affected_rows();
	}

	public function delete_mark($id){
		parent::delete($id);
	}

	public function sum_student_subject_mark($studentID, $classesID, $subjectID) {
		$array = array(
			"studentID" => $studentID,
			"classesID" => $classesID,
			"subjectID" => $subjectID
		);
		$this->db->select_sum('mark');
		$this->db->where($array);
		$query = $this->db->get('mark');
		return $query->row();
	}

	public function student_all_mark_array($array) {
		$this->db->select('*');
		$this->db->from('mark');
		$this->db->join('markrelation', 'markrelation.markID = mark.markID', 'LEFT');
		
		if(isset($array['subjectID'])) {
			$this->db->where('mark.subjectID', $array['subjectID']);
		}

		if(isset($array['schoolyearID'])) {
			$this->db->where('mark.schoolyearID', $array['schoolyearID']);
		}

		if(isset($array['examID'])) {
			$this->db->where('mark.examID', $array['examID']);
		}

		if(isset($array['classesID'])) {
			$this->db->where('mark.classesID', $array['classesID']);
		}

		if(isset($array['studentID'])) {
			$this->db->where('mark.studentID', $array['studentID']);
		}

		$query = $this->db->get();
		// echo $this->db->last->query();die;
		return $query->result();
	}

	public function student_all_mark_array_new($array) {
		$this->db->select('*');
		$this->db->from('mark');
		$this->db->join('markrelation', 'markrelation.markID = mark.markID', 'LEFT');
	
		if (isset($array['subjectID'])) {
			$this->db->where('mark.subjectID', $array['subjectID']);
		}
	
		if (isset($array['schoolyearID'])) {
			$this->db->where('mark.schoolyearID', $array['schoolyearID']);
		}
	
		if (isset($array['examID'])) {
			$this->db->where('mark.examID', $array['examID']);
		}
	
		if (isset($array['classesID'])) {
			$this->db->where('mark.classesID', $array['classesID']);
		}
	
		if (isset($array['studentID'])) {
			$this->db->where('mark.studentID', $array['studentID']);
		}
	
		// ✅ NEW: Support multiple students
		if (isset($array['studentIDs']) && is_array($array['studentIDs']) && count($array['studentIDs']) > 0) {
			$this->db->where_in('mark.studentID', $array['studentIDs']);
		}
	
		$query = $this->db->get();
		return $query->result();
	}
	

	public function student_all_mark_array_api($array) {
    $this->db->select('
        mark.*,
        markrelation.*,
        examschedule.min_mark,
        examschedule.max_mark,
        examschedule.edate AS exam_schedule_date,
        exam.date AS exam_date,
        exam.exam AS exam_name
    ');
    $this->db->from('mark');
    $this->db->join('markrelation', 'markrelation.markID = mark.markID', 'LEFT');
    $this->db->join('examschedule', 'examschedule.examID = mark.examID AND examschedule.subjectID = mark.subjectID AND examschedule.classesID = mark.classesID ', 'LEFT');
    $this->db->join('exam', 'exam.examID = mark.examID', 'LEFT');

    if (isset($array['subjectID'])) {
        $this->db->where('mark.subjectID', $array['subjectID']);
    }

    if (isset($array['schoolyearID'])) {
        $this->db->where('mark.schoolyearID', $array['schoolyearID']);
    }

    if (isset($array['examID'])) {
        $this->db->where('mark.examID', $array['examID']);
    }

    if (isset($array['classesID'])) {
        $this->db->where('mark.classesID', $array['classesID']);
    }

    if (isset($array['studentID'])) {
        $this->db->where('mark.studentID', $array['studentID']);
    }

    $query = $this->db->get();
	// echo $this->db->last_query();die;
    return $query->result();
}

	public function count_subject_mark($studentID, $classesID, $subjectID) {
		$query = "SELECT COUNT(*) as 'total_semester' FROM mark WHERE studentID = $studentID AND classesID = $classesID AND subjectID = $subjectID AND (mark != '' || mark <= 0 || mark >0)";
	    $query = $this->db->query($query);
	    $result = $query->row();
	    return $result;
	}

	public function get_order_by_mark_with_subject($classes,$year) {
		$this->db->select('*');
		$this->db->from('subject');
		$this->db->join('mark', 'subject.subjectID = mark.subjectID', 'LEFT');
		$this->db->join('exam', 'exam.examID = mark.examID');
		$this->db->where('mark.classesID', $classes);
		$this->db->where('mark.year', $year);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_order_by_student_mark_with_subject($classID, $yearID, $studentID=NULL) {
		$this->db->select('*,subject.subjectID subjectID, subject.classesID');
		$this->db->from('subject');
		$this->db->join('mark', 'subject.subjectID = mark.subjectID', 'LEFT');
		$this->db->join('markrelation', 'markrelation.markID = mark.markID', 'LEFT');
		$this->db->join('markpercentage', 'markpercentage.markpercentageID = markrelation.markpercentageID', 'LEFT');
		$this->db->join('student', 'mark.studentID = student.studentID', 'LEFT');
		$this->db->join('exam', 'exam.examID = mark.examID');
		$this->db->where('mark.classesID', $classID);
		if(isset($studentID) && $studentID != NULL) {
			$this->db->where('mark.studentID', $studentID);
		}
		$this->db->where('student.schoolyearID', $yearID);
		$this->db->where('mark.schoolyearID', $yearID);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_order_by_all_student_mark_with_markrelation($markArray) {
		$this->db->select('mark.*, markrelation.markrelationID, markrelation.markpercentageID, markrelation.mark');
		$this->db->from('mark');
		$this->db->join('markrelation', 'markrelation.markID = mark.markID', 'LEFT');
		if(isset($markArray['schoolyearID'])) {
			$this->db->where('mark.schoolyearID', $markArray['schoolyearID']);
		}

		if(isset($markArray['classesID'])) {
			$this->db->where('mark.classesID', $markArray['classesID']);
		}

		if(isset($markArray['examID'])) {
			$this->db->where('mark.examID', $markArray['examID']);
		}
		
		$query = $this->db->get();
		return $query->result();
	}

	public function get_order_by_mark_with_highest_mark($classID,$studentID) {
		$this->db->select('M.markID,M.examID, M.exam, M.subjectID, M.subject, M.studentID, M.classesID,  M.mark, M.year, (
		SELECT Max( mark.mark )
		FROM mark
		WHERE mark.subjectID = M.subjectID
		AND mark.examID = M.examID
		) highestmark');
		$this->db->from('exam E');
		$this->db->join('mark M', 'M.examID = E.examID', 'LEFT');
		$this->db->join('subject S', 'M.subjectID = S.subjectID');
		$this->db->where('M.classesID', $classID);
		$this->db->where('M.studentID', $studentID);
		$query = $this->db->get();
		return $query->result();
	}
}

/* End of file mark_m.php */
/* Location: .//D/xampp/htdocs/school/mvc/models/mark_m.php */
