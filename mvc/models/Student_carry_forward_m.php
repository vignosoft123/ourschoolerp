<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_carry_forward_m extends MY_Model {

    protected $_table_name   = 'student_carry_forward';
    protected $_primary_key  = 'id';
    protected $_primary_filter = 'intval';
    protected $_order_by     = 'from_schoolyearID DESC';

    function __construct() {
        parent::__construct();
    }

    function get_carry_forward($array = NULL) {
        return parent::get_order_by($array);
    }

    function get_single_carry_forward($array) {
        return parent::get_single($array);
    }

    function insert_carry_forward($array) {
        return parent::insert($array);
    }

    function update_carry_forward($data, $id = NULL) {
        parent::update($data, $id);
        return $id;
    }

    function delete_carry_forward($id) {
        parent::delete($id);
    }

    // Safe upsert — idempotent, can be called on every page load
    public function upsert_carry_forward($data) {
        $sql = "INSERT INTO student_carry_forward
            (studentID, from_schoolyearID, to_schoolyearID, from_year_name,
             total_fee, total_discount, total_paid_in_year, total_waiver,
             carry_forward_due, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
              total_fee           = VALUES(total_fee),
              total_discount      = VALUES(total_discount),
              total_paid_in_year  = VALUES(total_paid_in_year),
              total_waiver        = VALUES(total_waiver),
              carry_forward_due   = VALUES(carry_forward_due),
              status              = VALUES(status),
              updated_at          = NOW()";

        $this->db->query($sql, [
            $data['studentID'],
            $data['from_schoolyearID'],
            $data['to_schoolyearID'],
            $data['from_year_name'],
            $data['total_fee'],
            $data['total_discount'],
            $data['total_paid_in_year'],
            $data['total_waiver'],
            $data['carry_forward_due'],
            $data['status'],
        ]);
    }

    // Get all carry-forward records for a student in a given target year
    public function get_by_student_year($studentID, $to_schoolyearID) {
        return $this->get_carry_forward([
            'studentID'        => $studentID,
            'to_schoolyearID'  => $to_schoolyearID,
        ]);
    }
}
