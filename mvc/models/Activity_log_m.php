<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_m extends CI_Model {

    protected $_table = 'activity_logs';

    /**
     * Save an activity log entry.
     *
     * @param array $params {
     *   string  module       Feature name, e.g. 'delete_account_request'
     *   string  action       'create'|'update'|'delete'|'deactivate'|'view'
     *   int     record_id    ID of the affected record
     *   string  record_type  'student'|'teacher'|'user' etc.
     *   mixed   old_value    Array or JSON string of the previous state
     *   mixed   new_value    Array or JSON string of the new state
     *   string  description  Human-readable sentence
     * }
     * @return int  Inserted log ID, or 0 if table missing
     */
    public function add($params = []) {
        if (!$this->db->table_exists($this->_table)) return 0;

        $CI =& get_instance();

        $encode = function($v) {
            if ($v === null || $v === '') return null;
            return is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v;
        };

        $data = [
            'module'                     => $params['module']      ?? '',
            'action'                     => $params['action']      ?? '',
            'record_id'                  => isset($params['record_id'])   ? (int)$params['record_id']   : null,
            'record_type'                => $params['record_type'] ?? null,
            'old_value'                  => $encode($params['old_value'] ?? null),
            'new_value'                  => $encode($params['new_value'] ?? null),
            'description'                => $params['description'] ?? null,
            'performed_by_id'            => (int)($CI->session->userdata('userID')    ?? 0) ?: null,
            'performed_by_name'          => $CI->session->userdata('name')             ?? null,
            'performed_by_usertype'      => (int)($CI->session->userdata('usertypeID') ?? 0) ?: null,
            'performed_by_usertype_name' => $CI->session->userdata('usertype')         ?? null,
            'ip_address'                 => $CI->input->ip_address(),
            'created_at'                 => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->_table, $data);
        return (int)$this->db->insert_id();
    }

    public function get_logs($filters = [], $limit = 25, $offset = 0) {
        if (!$this->db->table_exists($this->_table)) return [];
        $this->_apply_filters($filters);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);
        $r = $this->db->get($this->_table);
        return $r ? $r->result() : [];
    }

    public function count_logs($filters = []) {
        if (!$this->db->table_exists($this->_table)) return 0;
        $this->_apply_filters($filters);
        return (int)$this->db->count_all_results($this->_table);
    }

    private function _apply_filters($filters) {
        if (!empty($filters['module']))       $this->db->where('module',       $filters['module']);
        if (!empty($filters['action']))       $this->db->where('action',       $filters['action']);
        if (!empty($filters['record_type']))  $this->db->where('record_type',  $filters['record_type']);
        if (!empty($filters['performed_by_id'])) $this->db->where('performed_by_id', (int)$filters['performed_by_id']);
        if (!empty($filters['date_from']))    $this->db->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        if (!empty($filters['date_to']))      $this->db->where('created_at <=', $filters['date_to']   . ' 23:59:59');
        if (!empty($filters['search'])) {
            $s = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                ->like('description',         $filters['search'])
                ->or_like('performed_by_name', $filters['search'])
                ->or_like('record_type',       $filters['search'])
                ->group_end();
        }
    }
}
