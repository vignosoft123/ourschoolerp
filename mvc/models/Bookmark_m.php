<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bookmark_m extends MY_Model {

	protected $_table_name = 'bookmarks';
	protected $_primary_key = 'id';
	protected $_primary_filter = 'intval';
	protected $_order_by = "id desc";

	function __construct() {
		parent::__construct();
	}

	function get_order_by_bookmark($array=NULL) {
		$query = parent::get_order_by($array);
		return $query;
	}

	function get_single_bookmark($array=NULL) {
		$query = parent::get_single($array);
		return $query;
	}

	function insert_bookmark($array) {
		$id = parent::insert($array);
		return $id;
	}

	function update_bookmark($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_bookmark($id) {
		parent::delete($id);
	}
}
