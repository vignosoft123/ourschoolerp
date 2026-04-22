<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Studentsiblings_m extends MY_Model {

    protected $_table_name = 'student_siblings';
    protected $_primary_key = 'id';

    function __construct() {
        parent::__construct();
    }

    public function get_siblings_by_student($studentID) {
        $this->db->select('student_siblings.sibling_studentID, student.name, student.studentID, student.classesID, student.sectionID, classes.classes, section.section');
        $this->db->from('student_siblings');
        $this->db->join('student', 'student.studentID = student_siblings.sibling_studentID', 'LEFT');
        $this->db->join('classes', 'classes.classesID = student.classesID', 'LEFT');
        $this->db->join('section', 'section.sectionID = student.sectionID', 'LEFT');
        $this->db->where('student_siblings.studentID', $studentID);
        return $this->db->get()->result();
    }

    public function insert_sibling($data) {
        $this->db->insert('student_siblings', $data);
        return $this->db->insert_id();
    }

    public function delete_pair($studentID, $siblingID) {
        $this->db->where('studentID', $studentID);
        $this->db->where('sibling_studentID', $siblingID);
        $this->db->delete('student_siblings');
    }

    public function delete_all_by_student($studentID) {
        $this->db->where('studentID', $studentID);
        $this->db->delete('student_siblings');
    }
}
