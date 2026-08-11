<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workspace extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("bookmark_m");
		$this->ensureBookmarksTable();
	}

	private function ensureBookmarksTable() {
		$this->db->query("CREATE TABLE IF NOT EXISTS bookmarks (
			id INT AUTO_INCREMENT PRIMARY KEY,
			user_id INT NOT NULL DEFAULT 0,
			category VARCHAR(100) NOT NULL,
			name VARCHAR(150) NOT NULL,
			url VARCHAR(500) NOT NULL,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
	}

	public function index() {
		$user_id = intval($this->session->userdata('loginuserID'));
		$this->data['activemenu'] = 'bookmarks';
		$this->data['bookmarks'] = $this->bookmark_m->get_order_by_bookmark(array('user_id' => $user_id));
		$this->data["subview"] = "workspace/bookmarks";
		$this->load->view('_layout_workspace', $this->data);
	}

	public function mybusiness() {
		$this->data['activemenu'] = 'mybusiness';
		$this->data["subview"] = "workspace/mybusiness";
		$this->load->view('_layout_workspace', $this->data);
	}

	protected function rules() {
		return array(
			array(
				'field' => 'category',
				'label' => 'Category',
				'rules' => 'trim|required|xss_clean|max_length[100]'
			),
			array(
				'field' => 'name',
				'label' => 'Name',
				'rules' => 'trim|required|xss_clean|max_length[150]'
			),
			array(
				'field' => 'url',
				'label' => 'URL',
				'rules' => 'trim|required|xss_clean|max_length[500]'
			)
		);
	}

	public function ajax_add() {
		header('Content-Type: application/json');
		$retArray['status'] = FALSE;
		$rules = $this->rules();
		$this->form_validation->set_rules($rules);
		if ($this->form_validation->run() == FALSE) {
			echo json_encode($this->form_validation->error_array());
			return;
		}
		$user_id = intval($this->session->userdata('loginuserID'));
		$array = array(
			"user_id"  => $user_id,
			"category" => $this->input->post("category"),
			"name"     => $this->input->post("name"),
			"url"      => $this->input->post("url")
		);
		$this->bookmark_m->insert_bookmark($array);
		$retArray['status'] = TRUE;
		$retArray['message'] = 'Success';
		echo json_encode($retArray);
	}

	public function ajax_update() {
		header('Content-Type: application/json');
		$retArray['status'] = FALSE;
		$user_id = intval($this->session->userdata('loginuserID'));
		$id = intval($this->input->post('id'));

		$existing = $this->bookmark_m->get_single_bookmark(array('id' => $id, 'user_id' => $user_id));
		if (!$existing) {
			$retArray['message'] = 'Bookmark not found.';
			echo json_encode($retArray);
			return;
		}

		$rules = $this->rules();
		$this->form_validation->set_rules($rules);
		if ($this->form_validation->run() == FALSE) {
			echo json_encode($this->form_validation->error_array());
			return;
		}
		$array = array(
			"category" => $this->input->post("category"),
			"name"     => $this->input->post("name"),
			"url"      => $this->input->post("url")
		);
		$this->bookmark_m->update_bookmark($array, $id);
		$retArray['status'] = TRUE;
		$retArray['message'] = 'Success';
		echo json_encode($retArray);
	}

	public function ajax_delete() {
		header('Content-Type: application/json');
		$user_id = intval($this->session->userdata('loginuserID'));
		$id = intval($this->input->post('id'));

		$existing = $this->bookmark_m->get_single_bookmark(array('id' => $id, 'user_id' => $user_id));
		if (!$existing) {
			echo json_encode(array('success' => FALSE, 'message' => 'Bookmark not found.'));
			return;
		}
		$this->bookmark_m->delete_bookmark($id);
		echo json_encode(array('success' => TRUE));
	}

	// ── Sticky Notes (shared with Subdomains page, same table) ────────────────

	public function sticky_notes_get() {
		header('Content-Type: application/json');
		$this->db->query("CREATE TABLE IF NOT EXISTS sticky_notes (
			id INT AUTO_INCREMENT PRIMARY KEY,
			user_id INT NOT NULL DEFAULT 0,
			note TEXT,
			color VARCHAR(20) DEFAULT '#fff9c4',
			sort_order INT DEFAULT 0,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$user_id = intval($this->session->userdata('loginuserID'));
		$notes   = $this->db->where('user_id', $user_id)->order_by('id', 'DESC')->get('sticky_notes')->result();
		echo json_encode(['success' => true, 'notes' => $notes]);
	}

	public function sticky_notes_save() {
		header('Content-Type: application/json');
		$id      = intval($this->input->post('id'));
		$note    = $this->input->post('note');
		$color   = $this->input->post('color') ?: '#fff9c4';
		$user_id = intval($this->session->userdata('loginuserID'));
		if ($id) {
			$this->db->where('id', $id)->where('user_id', $user_id)
			         ->update('sticky_notes', ['note' => $note, 'color' => $color]);
			echo json_encode(['success' => true, 'id' => $id]);
		} else {
			$this->db->insert('sticky_notes', ['user_id' => $user_id, 'note' => $note, 'color' => $color]);
			echo json_encode(['success' => true, 'id' => $this->db->insert_id()]);
		}
	}

	public function sticky_notes_delete() {
		header('Content-Type: application/json');
		$id      = intval($this->input->post('id'));
		$user_id = intval($this->session->userdata('loginuserID'));
		$this->db->where('id', $id)->where('user_id', $user_id)->delete('sticky_notes');
		echo json_encode(['success' => true]);
	}
}
