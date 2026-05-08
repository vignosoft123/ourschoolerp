<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Banks_m extends MY_Model {

    protected $_table_name     = 'banks';
    protected $_primary_key    = 'banksID';
    protected $_primary_filter = 'intval';
    protected $_order_by       = 'bank_name asc';

    public function get_banks($array = NULL, $signal = FALSE) {
        return parent::get($array, $signal);
    }

    public function get_order_by_banks($array = NULL) {
        return parent::get_order_by($array);
    }

    public function get_single_bank($array = NULL) {
        return parent::get_single($array);
    }

    public function insert_bank($array) {
        parent::insert($array);
        return $this->db->insert_id();
    }

    public function update_bank($data, $id = NULL) {
        parent::update($data, $id);
    }

    public function delete_bank($id) {
        parent::delete($id);
    }

    public function get_active_banks() {
        return $this->db->where('status', 1)->order_by('bank_name', 'asc')->get('banks')->result();
    }
}
