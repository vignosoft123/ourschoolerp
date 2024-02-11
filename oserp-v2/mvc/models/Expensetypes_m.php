<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expensetypes_m extends MY_Model {

	protected $_table_name = 'expensetypes';
	protected $_primary_key = 'expensetypesID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "expensetypesID asc";

	function __construct() {
		parent::__construct();
	}

	function get_expensetypes($array=NULL, $signal=FALSE) {
		$query = parent::get($array, $signal);
		return $query;
	}

	function get_order_by_expensetypes($array=NULL) {
		$query = parent::get_order_by($array);
		return $query;
	}

	function get_single_expensetypes($array=NULL) {
		$query = parent::get_single($array);
		return $query;
	}

	function insert_expensetypes($array) {
		$error = parent::insert($array);
		return TRUE;
	}

	function update_expensetypes($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_expensetypes($id){
		parent::delete($id);
	}

	function allexpensetypes($expensetypes) {
		$query = $this->db->query("SELECT * FROM expensetypes WHERE expensetypes LIKE '$expensetypes%'");
		return $query->result();
	}
}

/* End of file expensetypes_m.php */
/* Location: .//D/xampp/htdocs/school/mvc/models/expensetypes_m.php */