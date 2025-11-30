<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subdomains_m extends MY_Model {

	protected $_table_name = 'subdomain_settings';
	protected $_primary_key = 'id';
	protected $_primary_filter = 'intval';
	protected $_order_by = "id desc";

	function __construct() {
		parent::__construct();
	}

	function get_subdomains($array=NULL, $signal=FALSE) {
		$query = parent::get($array, $signal);
		return $query;
	}

	function get_single_subdomain($array=NULL) {
		$query = parent::get_single($array);
		return $query;
	}

	function get_order_by_subdomains($array=NULL) {
		$query = parent::get_order_by($array);
		return $query;
	}

	function insert_subdomain($array) {
		$error = parent::insert($array);
		return $error;
	}

	function update_subdomain($data, $id = NULL) {
		parent::update($data, $id);
		return $id;
	}

	public function delete_subdomain($id){
		parent::delete($id);
	}

	// Get subdomains with pagination
	function get_subdomains_with_pagination($limit = 10, $start = 0, $search = '') {
		$this->db->select('*');
		$this->db->from($this->_table_name);
		
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('subdomain', $search);
			$this->db->or_like('site_name', $search);
			$this->db->or_like('server', $search);
			$this->db->or_like('status', $search);
			$this->db->or_like('main_domain', $search);
			$this->db->group_end();
		}
		
		$this->db->order_by($this->_order_by);
		$this->db->limit($limit, $start);
		
		$query = $this->db->get();
		return $query->result();
	}

	// Get total count for pagination
	function get_subdomains_count($search = '') {
		$this->db->from($this->_table_name);
		
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('subdomain', $search);
			$this->db->or_like('site_name', $search);
			$this->db->or_like('server', $search);
			$this->db->or_like('status', $search);
			$this->db->or_like('main_domain', $search);
			$this->db->group_end();
		}
		
		return $this->db->count_all_results();
	}

	// Check if subdomain exists
	function subdomain_exists($subdomain, $exclude_id = null) {
		$this->db->where('subdomain', $subdomain);
		if ($exclude_id) {
			$this->db->where('id !=', $exclude_id);
		}
		$query = $this->db->get($this->_table_name);
		return $query->num_rows() > 0;
	}
}

/* End of file Subdomains_m.php */
/* Location: ./mvc/models/Subdomains_m.php */