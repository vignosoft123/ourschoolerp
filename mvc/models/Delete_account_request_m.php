<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delete_account_request_m extends CI_Model {

    protected $_table = 'delete_account_requests';
    public $upload_data = array();

    public function insert_request($data) {
        if (!$this->_table_exists()) return false;
        $this->db->insert($this->_table, $data);
        return $this->db->insert_id();
    }

    public function request_exists($type, $user_id) {
        if (!$this->_table_exists()) return false;
        $result = $this->db->get_where($this->_table, [
            'type'    => $type,
            'user_id' => $user_id,
            'status'  => 'pending'
        ]);
        return ($result !== false) ? $result->row() : false;
    }

    public function get_all_requests($type = null) {
        if (!$this->_table_exists()) return [];

        // Raw SQL avoids CI query-builder mangling string literals in ON conditions.
        // `user` is a MySQL reserved word — must be backtick-quoted explicitly.
        $where = '';
        $binds = [];
        if ($type) {
            $where   = 'WHERE r.type = ?';
            $binds[] = $type;
        }

        $sql = "SELECT r.*,
                    s.name    AS student_name,
                    s.roll    AS student_roll,
                    s.phone   AS student_phone,
                    cl.classes  AS student_class,
                    sec.section AS student_section,
                    t.name    AS teacher_name,
                    u.name    AS user_name_join
                FROM `delete_account_requests` r
                LEFT JOIN `student`  s   ON s.studentID   = r.user_id AND r.type = 'student'
                LEFT JOIN `classes`  cl  ON cl.classesID  = s.classesID
                LEFT JOIN `section`  sec ON sec.sectionID = s.sectionID
                LEFT JOIN `teacher`  t   ON t.teacherID   = r.user_id AND r.type = 'teacher'
                LEFT JOIN `user`     u   ON u.userID      = r.user_id AND r.type = 'user'
                {$where}
                ORDER BY r.requested_at DESC";

        $result = $this->db->query($sql, $binds);
        return ($result !== false) ? $result->result() : [];
    }

    public function get_request_by_id($id) {
        if (!$this->_table_exists()) return null;
        $result = $this->db->get_where($this->_table, ['id' => $id]);
        return ($result !== false) ? $result->row() : null;
    }

    public function update_status($id, $status) {
        if (!$this->_table_exists()) return false;
        return $this->db->update($this->_table, ['status' => $status], ['id' => $id]);
    }

    public function deactivate_user($type, $user_id) {
        $map = [
            'student' => ['student', 'studentID'],
            'teacher' => ['teacher', 'teacherID'],
            'user'    => ['user',    'userID'],
        ];
        if (!isset($map[$type])) return false;
        [$table, $pk] = $map[$type];
        return $this->db->update($table, ['active' => 0], [$pk => $user_id]);
    }

    public function delete_request($id) {
        if (!$this->_table_exists()) return false;
        return $this->db->delete($this->_table, ['id' => $id]);
    }

    public function get_counts() {
        $counts = ['all' => 0, 'student' => 0, 'teacher' => 0, 'user' => 0];
        if (!$this->_table_exists()) return $counts;

        $result = $this->db->select('type, COUNT(*) AS cnt')
                           ->from($this->_table)
                           ->group_by('type')
                           ->get();

        if ($result === false) return $counts;

        $total = 0;
        foreach ($result->result() as $row) {
            $counts[$row->type] = (int)$row->cnt;
            $total += (int)$row->cnt;
        }
        $counts['all'] = $total;
        return $counts;
    }

    private function _table_exists() {
        return $this->db->table_exists($this->_table);
    }
}
