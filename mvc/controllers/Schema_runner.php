<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Schema_runner — key-protected API controller.
 * Called by Subdomains::run_schema_updates() on each live subdomain to execute
 * schema_updates.json against that subdomain's own local database.
 * Extends CI_Controller (not Admin_Controller) so no login session is required.
 */
class Schema_runner extends CI_Controller {

    public function run() {
        header('Content-Type: application/json');

        $key = $this->input->get('key') ?: $this->input->post('key');
        if ($key !== SCHEMA_UPDATE_KEY) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $this->load->database();

        $json_path = APPPATH . 'migrations/schema_updates.json';
        if (!file_exists($json_path)) {
            echo json_encode(['success' => false, 'message' => 'schema_updates.json not found']);
            return;
        }

        $updates = json_decode(file_get_contents($json_path), true);
        if ($updates === null) {
            echo json_encode(['success' => false, 'message' => 'JSON parse failed: ' . json_last_error_msg()]);
            return;
        }

        $passed = 0; $skipped = 0; $failed = 0;
        $details = [];

        foreach ($updates as $i => $update) {
            $n    = $i + 1;
            $type = $update['type'] ?? 'unknown';
            $sql  = $update['query'] ?? '';

            try {
                if ($type === 'alter' && isset($update['check_column'])) {
                    $table  = $update['check_column']['table'];
                    $column = $update['check_column']['column'];
                    if (!$this->_col_exists($table, $column)) {
                        $r = $this->db->query($sql);
                        if ($r === false) {
                            $details[] = ['n' => $n, 'status' => 'failed', 'msg' => "ALTER $column on $table: " . $this->db->error()['message']];
                            $failed++;
                        } else {
                            $details[] = ['n' => $n, 'status' => 'ok', 'msg' => "ALTER: added $column to $table"];
                            $passed++;
                        }
                    } else {
                        $details[] = ['n' => $n, 'status' => 'skip', 'msg' => "SKIP: $column already in $table"];
                        $skipped++;
                    }

                } elseif ($type === 'insert' && isset($update['check_row'])) {
                    $table = $update['check_row']['table'];
                    if (!empty($update['check_row']['where'])) {
                        $where = $update['check_row']['where'];
                    } elseif (!empty($update['check_row']['column']) && isset($update['check_row']['value'])) {
                        $where = [$update['check_row']['column'] => $update['check_row']['value']];
                    } else {
                        $where = null;
                    }
                    $exists = false;
                    if ($where !== null) {
                        $res    = $this->db->get_where($table, $where);
                        $exists = $res && $res->num_rows() > 0;
                    }
                    if (!$exists) {
                        $r = $this->db->query($sql);
                        if ($r === false) {
                            $details[] = ['n' => $n, 'status' => 'failed', 'msg' => "INSERT into $table: " . $this->db->error()['message']];
                            $failed++;
                        } else {
                            $details[] = ['n' => $n, 'status' => 'ok', 'msg' => "INSERT into $table"];
                            $passed++;
                        }
                    } else {
                        $details[] = ['n' => $n, 'status' => 'skip', 'msg' => "SKIP: row exists in $table"];
                        $skipped++;
                    }

                } elseif ($type === 'index' && isset($update['check_index'])) {
                    $table = $update['check_index']['table'];
                    $index = $update['check_index']['index'];
                    if (!$this->_idx_exists($table, $index)) {
                        $r = $this->db->query($sql);
                        if ($r === false) {
                            $details[] = ['n' => $n, 'status' => 'failed', 'msg' => "INDEX $index on $table: " . $this->db->error()['message']];
                            $failed++;
                        } else {
                            $details[] = ['n' => $n, 'status' => 'ok', 'msg' => "INDEX $index on $table"];
                            $passed++;
                        }
                    } else {
                        $details[] = ['n' => $n, 'status' => 'skip', 'msg' => "SKIP: index $index exists on $table"];
                        $skipped++;
                    }

                } elseif ($type === 'create' && isset($update['check_table'])) {
                    $tbl    = $update['check_table'];
                    $result = $this->db->query("SHOW TABLES LIKE '$tbl'");
                    if ($result && $result->num_rows() == 0) {
                        $r = $this->db->query($sql);
                        if ($r === false) {
                            $details[] = ['n' => $n, 'status' => 'failed', 'msg' => "CREATE TABLE $tbl: " . $this->db->error()['message']];
                            $failed++;
                        } else {
                            $details[] = ['n' => $n, 'status' => 'ok', 'msg' => "CREATE TABLE $tbl"];
                            $passed++;
                        }
                    } else {
                        $details[] = ['n' => $n, 'status' => 'skip', 'msg' => "SKIP: TABLE $tbl already exists"];
                        $skipped++;
                    }

                } elseif ($type === 'raw') {
                    $r = $this->db->query($sql);
                    if ($r === false) {
                        $details[] = ['n' => $n, 'status' => 'failed', 'msg' => "RAW failed: " . $this->db->error()['message']];
                        $failed++;
                    } else {
                        $details[] = ['n' => $n, 'status' => 'ok', 'msg' => "RAW executed"];
                        $passed++;
                    }

                } else {
                    $details[] = ['n' => $n, 'status' => 'skip', 'msg' => "UNKNOWN type '$type' — skipped"];
                    $skipped++;
                }

            } catch (Exception $e) {
                $details[] = ['n' => $n, 'status' => 'failed', 'msg' => "Exception: " . $e->getMessage()];
                $failed++;
            }
        }

        echo json_encode([
            'success' => true,
            'passed'  => $passed,
            'skipped' => $skipped,
            'failed'  => $failed,
            'details' => $details,
        ]);
    }

    private function _col_exists($table, $column) {
        $check = $this->db->query("SHOW TABLES LIKE '$table'");
        if (!$check || $check->num_rows() == 0) return false;
        return in_array($column, $this->db->list_fields($table));
    }

    private function _idx_exists($table, $index) {
        $q = $this->db->query("SHOW INDEX FROM `$table` WHERE Key_name = ?", [$index]);
        return $q && $q->num_rows() > 0;
    }
}
