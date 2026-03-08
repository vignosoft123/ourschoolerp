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
    
        $json_path = APPPATH . 'migrations/schema_updates.json';
        if (!file_exists($json_path)) {
            show_error('Schema update file not found.');
        }
    
        $updates = json_decode(file_get_contents($json_path), true);
    
        foreach ($updates as $update) {
            if ($update['type'] === 'alter' && isset($update['check_column'])) {
                $table = $update['check_column']['table'];
                $column = $update['check_column']['column'];
    
                if (!$this->column_exists($table, $column)) {
                    $this->db->query($update['query']);
                    echo "Executed ALTER: {$update['query']}<br>";
                } else {
                    echo "Skipped ALTER (already exists): $column in $table<br>";
                }
    
            } elseif ($update['type'] === 'insert' && isset($update['check_row'])) {
                $table = $update['check_row']['table'];
                $where = $update['check_row']['where'];
    
                $exists = $this->db->get_where($table, $where)->num_rows() > 0;
    
                if (!$exists) {
                    $this->db->query($update['query']);
                    echo "Executed INSERT: {$update['query']}<br>";
                } else {
                    echo "Skipped INSERT (row already exists in $table)<br>";
                }
    
            } elseif ($update['type'] === 'create' && isset($update['check_table'])) {
                $table = $update['check_table'];
                $query = $this->db->query("SHOW TABLES LIKE '$table'");
                if ($query->num_rows() == 0) {
                    $this->db->query($update['query']);
                    echo "Executed CREATE: TABLE $table<br>";
                } else {
                    echo "Skipped CREATE (TABLE $table already exists)<br>";
                }
            } elseif ($update['type'] === 'raw') {
                $this->db->query($update['query']);
                echo "Executed RAW Query: {$update['query']}<br>";
            } else {
                echo "Invalid update entry or unsupported type.<br>";
            }
        }
    }
    
    private function column_exists($table, $column) {
        $fields = $this->db->list_fields($table);
        return in_array($column, $fields);
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
