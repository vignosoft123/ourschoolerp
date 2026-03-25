<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Incomecategories_m extends MY_Model {

	protected $_table_name = 'income_categories';
	protected $_primary_key = 'incomecategoriesID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "incomecategoriesID asc";

	function __construct() {
		parent::__construct();
	}

	function get_incomecategories($array=NULL, $signal=FALSE) {
		$query = parent::get($array, $signal);
		return $query;
	}

	function get_order_by_incomecategories($array=NULL) {
		$query = parent::get_order_by($array);
		return $query;
	}

	function get_single_incomecategories($array=NULL) {
		$query = parent::get_single($array);
		return $query;
	}

	function insert_incomecategories($array) {
		$error = parent::insert($array);
		return TRUE;
	}

	function update_incomecategories($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_incomecategories($id){
		parent::delete($id);
	}
}
