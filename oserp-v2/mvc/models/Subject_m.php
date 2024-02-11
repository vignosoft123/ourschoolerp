<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "Teachersubject_m.php";
require_once "Studentparentsubject_m.php";

class Subject_m extends MY_Model {
	protected $_table_name = 'subject';
	protected $_primary_key = 'subjectID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "subject.classesID asc";

	function __construct() {
		parent::__construct();
	}

	public function get_join_subject($id) {
		$usertypeID = $this->session->userdata('usertypeID');
		if($usertypeID == 2) {
			$teachersubject = new Teachersubject_m;
	    	return $teachersubject->get_subject_with_class($id);
		} elseif($usertypeID == 3 || $usertypeID == 4) {
			$studentsubject = new Studentparentsubject_m;
	    	return $studentsubject->get_subject_with_class($id);
		} else {
			$this->db->select('subject.*, classes.classesID, classes.classes, classes.classes_numeric, classes.studentmaxID, classes.note');
			$this->db->from('subject');
			$this->db->join('classes', 'classes.classesID = subject.classesID', 'LEFT');
			$this->db->where('subject.classesID', $id);
			$query = $this->db->get();
			return $query->result();
		}
	}

	public function general_get_subject($array=NULL, $signal=FALSE) {
		$query = parent::get($array, $signal);
		return $query;
	}

	public function general_get_single_subject($array) {
        $query = parent::get_single($array);
        return $query;
    }
    
	public function general_get_order_by_subject($array=NULL) {

		// public function general_get_order_by_subject($array1=NULL) {
	// 	$array = array();
	// 	foreach($array1 as $k =>$v){
	// 	   // if($k == 'classesID'){
	// 			$k = 'subject.'.$k;
	// 	   // }
	// 	  $array[$k] = $v; 
	// 	}
	//    // echo "<pre>";print_r($array);
	// //join query for getting max and min marks - srinu
	// $this->db->select("subject.*,examschedule.max_mark");
	// $this->db->join('examschedule','examschedule.subjectID = subject.subjectID','left');
	// $query = parent::get_order_by($array);

	$query = parent::get_order_by($array); 
// 			echo $this->db->last_query();die;
	return $query;

	
}

	public function general_get_order_by_subject_left_examschedule($classID="",$type="",$examID="",$sectionID="") {
// 		$array = array();
// 		foreach($array1 as $k =>$v){
// 		   // if($k == 'classesID'){
// 				$k = 'subject.'.$k;
// 		   // }
// 		  $array[$k] = $v; 
// 		}
// 	   // echo "<pre>";print_r($array);
// 	//join query for getting max and min marks - srinu
// 	$this->db->select("subject.*,examschedule.max_mark");
// 	$this->db->join('examschedule','examschedule.subjectID = subject.subjectID','left');
// 	$query = parent::get_order_by($array); 
	
	// $sql = "SELECT subject.*, examschedule.max_mark FROM examschedule LEFT JOIN subject ON examschedule.subjectID = subject.subjectID WHERE subject.classesID = $classID AND subject.type = $type and examschedule.examID = $examID ";
	$sql = "SELECT subject.*, examschedule.max_mark FROM examschedule LEFT JOIN subject ON examschedule.subjectID = subject.subjectID WHERE subject.classesID = $classID AND subject.type = $type ";

	if(!empty($sectionID)){
		$sql.= " and examschedule.sectionID =". $sectionID;
	}

	if(!empty($examID)){
		$sql.= " and examschedule.examID =". $examID;
	}

	$sql.= " ORDER BY subject.classesID asc";
	$query = $this->db->query($sql)->result();
			// echo $this->db->last_query();die;
	return $query;
}

public function general_get_order_by_subject_only_subjects($array1=NULL) {
		$array = array();
		foreach($array1 as $k =>$v){
		   // if($k == 'classesID'){
				$k = 'subject.'.$k;
		   // }
		  $array[$k] = $v; 
		}
	   // echo "<pre>";print_r($array);
	//join query for getting max and min marks - srinu
	
	$query = parent::get_order_by($array); 
// 			echo $this->db->last_query();die;
	return $query;
}

	public function get_subject($id=NULL, $single=FALSE) {
		$usertypeID = $this->session->userdata('usertypeID');
		if($usertypeID == 2) {
			$teachersubject = new Teachersubject_m;
	    	return $teachersubject->get_teacher_subject($id, $single);
		} elseif($usertypeID == 3 || $usertypeID == 4) {
			$studentsubject = new Studentparentsubject_m;
	    	return $studentsubject->get_studentparent_subject($id, $single);
		} else {
			$query = parent::get($id, $single);
			return $query;
		}
	}

	public function get_single_subject($array) {
		$usertypeID = $this->session->userdata('usertypeID');
		if($usertypeID == 2) {
			$teachersubject = new Teachersubject_m;
	    	return $teachersubject->get_single_teacher_subject($array);
		} elseif($usertypeID == 3 || $usertypeID == 4) {
			$studentsubject = new Studentparentsubject_m;
	    	return $studentsubject->get_single_studentparent_subject($array);
		} else {
			$query = parent::get_single($array);
        	return $query;
		}
    }
    
	public function get_order_by_subject($array1=NULL,$examID="",$sectionID="") {
	    
	    foreach($array1 as $k =>$v){
		   // if($k == 'classesID'){
				$k = 'subject.'.$k;
		   // }
		  $array[$k] = $v; 
		}
		
		$usertypeID = $this->session->userdata('usertypeID');
		if($usertypeID == 2) {  
			$teachersubject = new Teachersubject_m;
	    	return $teachersubject->get_order_by_teacher_subject($array);
		} elseif($usertypeID == 3 || $usertypeID == 4) {  
			$studentsubject = new Studentparentsubject_m;
	    	return $studentsubject->get_order_by_studentparent_subject($array);
		} else {  //code for getting max & min marks - srinu
			$this->db->select("subject.*,examschedule.max_mark");
			
			$this->db->where('examschedule.sectionID',$sectionID);
			$this->db->where('examschedule.examID',$examID);
			
			$this->db->join('examschedule','examschedule.subjectID = subject.subjectID','left'); 
			
			$query = parent::get_order_by($array);
// 			echo $this->db->last_query();die;
        	return $query;
		}
	}

	public function insert_subject($array) {
		$error = parent::insert($array);
		return TRUE;
	}

	public function update_subject($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_subject($id){
		parent::delete($id);
	}
	
	public function general_get_order_by_subject_with_exam($array=NULL) {
		$this->db->select('subject.subjectID, examschedule.edate, examschedule.examfrom, examschedule.examto');
		$this->db->from('subject');
		$this->db->join('examschedule', 'subject.subjectID = examschedule.subjectID AND subject.classesID = examschedule.classesID', 'LEFT');
		if($array!='')
		{
		    $this->db->where($array);
		}
		$query = $this->db->get();
		return $query->result();
	}
}