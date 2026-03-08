<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class College_group_m extends MY_Model {

    protected $_table_name = 'college_groups';
    protected $_primary_key = 'collegegroupID';
    protected $_primary_filter = 'intval';
    protected $_order_by = "collegegroupID asc";

    function __construct() {
        parent::__construct();
    }

    public function get_college_group($array=NULL, $signal=FALSE) {
        $query = parent::get($array, $signal);
        return $query;
    }

    public function get_single_college_group($array) {
        $query = parent::get_single($array);
        return $query;
    }

    public function get_order_by_college_group($array=NULL) {
        $query = parent::get_order_by($array);
        return $query;
    }

    public function insert_college_group($array) {
        $error = parent::insert($array);
        return $error;
    }

    public function update_college_group($data, $id = NULL) {
        parent::update($data, $id);
        return $id;
    }

    public function delete_college_group($id){
        parent::delete($id);
    }
}
