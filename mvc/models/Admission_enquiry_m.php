<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admission_enquiry_m extends MY_Model {

	protected $_table_name = 'admission_enquiry';
	protected $_primary_key = 'enquiryID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "enquiryID desc";

	function __construct() {
		parent::__construct();
	}

	public function get_admission_enquiry($array=NULL, $signal=FALSE) {
		return parent::get($array, $signal);
	}

	public function get_single_admission_enquiry($array=NULL) {
		return parent::get_single($array);
	}	

	public function get_order_by_admission_enquiry($array=NULL) {
		return parent::get_order_by($array);
	}

	public function insert_admission_enquiry($array) {
		return parent::insert($array);
	}

	public function update_admission_enquiry($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_admission_enquiry($id){
		parent::delete($id);
	}
}
