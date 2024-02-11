<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Village_m extends MY_Model
{

	protected $_table_name = 'villages';
	protected $_primary_key = 'villageID';
	protected $_primary_filter = 'intval';
	protected $_order_by = "villageID desc";

	function __construct()
	{
		parent::__construct();
	}

	function get_village($array = NULL, $signal = FALSE)
	{
		$query = parent::get($array, $signal);
		return $query;
	}

	function get_active_villages($array = NULL, $signal = FALSE)
	{
		$query = $this->db->query("SELECT * FROM villages WHERE status = 1");
		return $query->result();
	}

	function get_order_by_village($array = NULL)
	{
		$query = parent::get_order_by($array);
		return $query;
	}

	function get_single_village($array = NULL)
	{
		$query = parent::get_single($array);
		return $query;
	}

	function insert_village($array)
	{
		$error = parent::insert($array);
		return TRUE;
	}

	function update_village($data, $id = NULL)
	{
		parent::update($data, $id);
		return $id;
	}

	public function delete_village($id)
	{
		parent::delete($id);
	}

	function allVillages($name)
	{
		$query = $this->db->query("SELECT * FROM villages WHERE villageName LIKE '$name%'");
		return $query->result();
	}
}
