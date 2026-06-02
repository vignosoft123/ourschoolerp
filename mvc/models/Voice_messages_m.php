<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voice_messages_m extends MY_Model {
    protected $_table_name  = 'voice_messages';
    protected $_primary_key = 'id';
    protected $_order_by    = 'id DESC';
    public function __construct() {
        parent::__construct();
    }
    public function get_all($where = NULL) {
        return parent::get_order_by($where);
    }
    public function get_one($where = NULL) {
        return parent::get_single($where);
    }
}
