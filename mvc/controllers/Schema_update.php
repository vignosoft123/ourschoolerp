<?php 
class Schema_update extends Admin_Controller {


    public function index() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/datepicker/datepicker.css',
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/datepicker/datepicker.js',
				'assets/select2/select2.js'
			)
		);

		
		$this->data["subview"] = "schema_add";
		$this->load->view('_layout_main', $this->data);
	}

    // public function save_query() {
    //     $this->load->helper('file'); // for file_put_contents
    //     $json_path = APPPATH . 'migrations/schema_updates.json';
    //     $query = trim($this->input->post('sql_query'));

    //     if (empty($query)) {
            
    //         $this->data['error'] = 'Query cannot be empty.'; 
    //         $this->data["subview"] = "schema_add";
	// 	    $this->load->view('_layout_main', $this->data);
    //         return;
    //     }

    //     // Basic regex to extract table and column
    //     if (preg_match('/ALTER TABLE\s+`?(\w+)`?\s+ADD\s+`?(\w+)`?/i', $query, $matches)) {
    //         $table = $matches[1];
    //         $column = $matches[2];
    //     } else {
    //         // $this->load->view('schema_add', ['error' => 'Could not parse table or column from the query.']);
    //         $this->data['error'] = 'Could not parse table or column from the query.'; 
    //         $this->data["subview"] = "schema_add";
	// 	    $this->load->view('_layout_main', $this->data);
    //         return;
    //     }

    //     // Read existing JSON
    //     $updates = file_exists($json_path) ? json_decode(file_get_contents($json_path), true) : [];

    //     // Append new item
    //     $updates[] = [
    //         "query" => rtrim($query, ';') . ';',
    //         "check_column" => [
    //             "table" => $table,
    //             "column" => $column
    //         ]
    //     ];

    //     // Save JSON back
    //     if (file_put_contents($json_path, json_encode($updates, JSON_PRETTY_PRINT))) {
            
    //         $this->data['message'] = 'Query added successfully.'; 
    //         $this->data["subview"] = "schema_add";
	// 	    $this->load->view('_layout_main', $this->data);
    //     } else { 
    //         $this->data['error'] = 'Failed to write to JSON file.'; 
    //         $this->data["subview"] = "schema_add";
	// 	    $this->load->view('_layout_main', $this->data);
    //     }
    // }


    public function save_query() {
        $this->load->helper('file');
        $json_path = APPPATH . 'migrations/schema_updates.json';
        $query = trim($this->input->post('sql_query'));
    
        if (empty($query)) {
            $this->data['error'] = 'Query cannot be empty.'; 
            $this->data["subview"] = "schema_add";
		    $this->load->view('_layout_main', $this->data);
            return;
        }
    
        $updates = file_exists($json_path) ? json_decode(file_get_contents($json_path), true) : [];
    
        $entry = ["query" => rtrim($query, ';') . ';'];
    
        if (preg_match('/ALTER TABLE\s+`?(\w+)`?\s+ADD\s+`?(\w+)`?/i', $query, $matches)) {
            $entry["type"] = "alter";
            $entry["check_column"] = [
                "table" => $matches[1],
                "column" => $matches[2]
            ];
        } elseif (preg_match('/INSERT INTO\s+`?(\w+)`?\s*\(([^)]+)\)\s+VALUES\s*\(([^)]+)\)/i', $query, $matches)) {
            $entry["type"] = "insert";
            $table = $matches[1];
            $columns = array_map('trim', explode(',', str_replace('`', '', $matches[2])));
            $values = array_map('trim', explode(',', str_replace("'", '', $matches[3])));
    
            $where = [];
            if (count($columns) == count($values)) {
                $where[$columns[0]] = $values[0]; // Simple check on first column
            }
    
            $entry["check_row"] = [
                "table" => $table,
                "where" => $where
            ];
        } else {
            $this->data['error'] = 'Query type not supported or parse failed.'; 
                    $this->data["subview"] = "schema_add";
            	    $this->load->view('_layout_main', $this->data);
        }
    
        $updates[] = $entry;
    
        if (file_put_contents($json_path, json_encode($updates, JSON_PRETTY_PRINT))) {
            $this->data['message'] = 'Query added successfully.'; 
                    $this->data["subview"] = "schema_add";
            	    $this->load->view('_layout_main', $this->data);
        } else {
            $this->data['error'] = 'Failed to write to JSON file.'; 
                    $this->data["subview"] = "schema_add";
            	    $this->load->view('_layout_main', $this->data);
        }
    }
    


    public function apply_updates() {
        $this->load->database();

        echo '<style>
            body { font-family: monospace; font-size: 13px; padding: 20px; background:#111; color:#eee; }
            .ok   { color: #4caf50; }
            .skip { color: #aaa; }
            .err  { color: #f44336; font-weight: bold; }
            .info { color: #2196f3; }
            hr    { border-color: #333; }
        </style>';
        echo '<h3 style="color:#fff;">Schema Updates — ' . date('Y-m-d H:i:s') . '</h3><hr>';

        $json_path = APPPATH . 'migrations/schema_updates.json';
        if (!file_exists($json_path)) {
            echo '<p class="err">ERROR: schema_updates.json not found at ' . $json_path . '</p>';
            return;
        }

        $raw_json = file_get_contents($json_path);
        $updates  = json_decode($raw_json, true);

        if ($updates === null) {
            echo '<p class="err">ERROR: JSON parse failed — ' . json_last_error_msg() . '</p>';
            return;
        }

        echo '<p class="info">Total entries: ' . count($updates) . '</p><hr>';

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
                            echo "<p class='err'>[$n] ALTER FAILED — DB error: " . $this->db->error()['message'] . "<br>SQL: $sql</p>";
                        } else {
                            echo "<p class='ok'>[$n] ALTER executed: $column on $table</p>";
                        }
                    } else {
                        echo "<p class='skip'>[$n] ALTER skipped (column exists): $column in $table</p>";
                    }

                } elseif ($type === 'insert' && isset($update['check_row'])) {
                    $table = $update['check_row']['table'];

                    // Support both formats: where:{} and column+value
                    if (!empty($update['check_row']['where'])) {
                        $where = $update['check_row']['where'];
                    } elseif (!empty($update['check_row']['column']) && isset($update['check_row']['value'])) {
                        $where = [ $update['check_row']['column'] => $update['check_row']['value'] ];
                    } else {
                        $where = null;
                    }

                    if ($where !== null) {
                        $result = $this->db->get_where($table, $where);
                        if ($result === false) {
                            echo "<p class='err'>[$n] INSERT check FAILED — get_where error on table '$table': " . $this->db->error()['message'] . "</p>";
                            continue;
                        }
                        $exists = $result->num_rows() > 0;
                    } else {
                        $exists = false;
                        echo "<p class='err'>[$n] INSERT — no valid check_row format, attempting insert anyway</p>";
                    }

                    if (!$exists) {
                        $r = $this->db->query($sql);
                        if ($r === false) {
                            echo "<p class='err'>[$n] INSERT FAILED — " . $this->db->error()['message'] . "<br>SQL: $sql</p>";
                        } else {
                            echo "<p class='ok'>[$n] INSERT executed into $table</p>";
                        }
                    } else {
                        echo "<p class='skip'>[$n] INSERT skipped (row exists in $table)</p>";
                    }

                } elseif ($type === 'create' && isset($update['check_table'])) {
                    $table  = $update['check_table'];
                    $result = $this->db->query("SHOW TABLES LIKE '$table'");
                    if ($result === false) {
                        echo "<p class='err'>[$n] CREATE check FAILED — SHOW TABLES error: " . $this->db->error()['message'] . "</p>";
                        continue;
                    }
                    if ($result->num_rows() == 0) {
                        $r = $this->db->query($sql);
                        if ($r === false) {
                            echo "<p class='err'>[$n] CREATE FAILED — " . $this->db->error()['message'] . "<br>SQL: $sql</p>";
                        } else {
                            echo "<p class='ok'>[$n] CREATE TABLE $table executed</p>";
                        }
                    } else {
                        echo "<p class='skip'>[$n] CREATE skipped (TABLE $table already exists)</p>";
                    }

                } elseif ($type === 'index' && isset($update['check_index'])) {
                    $table = $update['check_index']['table'];
                    $index = $update['check_index']['index'];
                    if (!$this->_idx_exists($table, $index)) {
                        $r = $this->db->query($sql);
                        if ($r === false) {
                            echo "<p class='err'>[$n] INDEX FAILED — " . $this->db->error()['message'] . "<br>SQL: $sql</p>";
                        } else {
                            echo "<p class='ok'>[$n] INDEX executed: $index on $table</p>";
                        }
                    } else {
                        echo "<p class='skip'>[$n] INDEX skipped (already exists): $index on $table</p>";
                    }

                } elseif ($type === 'raw') {
                    $r = $this->db->query($sql);
                    if ($r === false) {
                        echo "<p class='err'>[$n] RAW FAILED — " . $this->db->error()['message'] . "<br>SQL: $sql</p>";
                    } else {
                        echo "<p class='ok'>[$n] RAW executed</p>";
                    }

                } else {
                    echo "<p class='err'>[$n] UNKNOWN type '$type' — skipped</p>";
                }

            } catch (Exception $e) {
                echo "<p class='err'>[$n] EXCEPTION: " . $e->getMessage() . "<br>SQL: $sql</p>";
            }
        }

        echo '<hr><p class="info">Done.</p>';
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

    private function column_exists($table, $column) {
        return $this->_col_exists($table, $column);
    }

    private function index_exists($table, $index) {
        return $this->_idx_exists($table, $index);
    }

    


    // public function apply_updates() {
    //     $this->load->database();

    //     $json_path = APPPATH . 'migrations/schema_updates.json';
    //     if (!file_exists($json_path)) {
    //         show_error('Schema update file not found.');
    //     }

    //     $updates = json_decode(file_get_contents($json_path), true);
    //     foreach ($updates as $update) {
    //         $table = $update['check_column']['table'];
    //         $column = $update['check_column']['column'];

    //         // Check if column exists
    //         if (!$this->column_exists($table, $column)) {
    //             $this->db->query($update['query']);
    //             echo "Executed: {$update['query']}<br>";
    //         } else {
    //             echo "Skipped (already exists): $column in $table<br>";
    //         }
    //     }
    // }

    // private function column_exists($table, $column) {
    //     $fields = $this->db->list_fields($table);
    //     return in_array($column, $fields);
    // }
}
