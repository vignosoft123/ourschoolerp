<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Incomecategories extends Admin_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("incomecategories_m");
		$language = $this->session->userdata('lang');
	}

	public function index() {
		$this->data['incomecategories'] = $this->incomecategories_m->get_order_by_incomecategories(array('status' => 0));
		$this->data["subview"] = "incomecategories/index";
		$this->load->view('_layout_main', $this->data);
	}

	protected function rules() {
		$rules = array(
				array(
					'field' => 'name', 
					'label' => 'Name', 
					'rules' => 'trim|required|xss_clean|max_length[60]|callback_unique_name'
				),
				array(
					'field' => 'note', 
					'label' => 'Note', 
					'rules' => 'trim|xss_clean|max_length[200]'
				)
			);
		return $rules;
	}

	public function add() {
		$retArray['status'] = FALSE;
		if($_POST) {
			$rules = $this->rules();
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE) {
				echo json_encode($this->form_validation->error_array());
			} else {
				$array = array(
					"name" => $this->input->post("name"),
					"note" => $this->input->post("note"),
					"status" => 0
				);
				$this->incomecategories_m->insert_incomecategories($array);
				$retArray['status'] = TRUE;
				$retArray['message'] = 'Success';
				echo json_encode($retArray);
			}
		}
	}

	public function edit() {
		$retArray['status'] = FALSE;
		if($_POST) {
			$id = $this->input->post('incomecategoriesID');
			$rules = $this->rules();
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE) {
				echo json_encode($this->form_validation->error_array());
			} else {
				$array = array(
					"name" => $this->input->post("name"),
					"note" => $this->input->post("note")
				);
				$this->incomecategories_m->update_incomecategories($array, $id);
				$retArray['status'] = TRUE;
				$retArray['message'] = 'Success';
				echo json_encode($retArray);
			}
		} else {
			$id = $this->input->get('id');
			if((int)$id) {
				$category = $this->incomecategories_m->get_single_incomecategories(array('incomecategoriesID' => $id));
				if($category) {
					echo json_encode($category);
				}
			}
		}
	}

	public function delete() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->incomecategories_m->update_incomecategories(array('status' => 1), $id);
			$this->session->set_flashdata('success', 'Success');
		}
		redirect(base_url("incomecategories/index"));
	}

	public function unique_name() {
		$id = $this->input->post('incomecategoriesID');
		if((int)$id) {
			$category = $this->incomecategories_m->get_order_by_incomecategories(array("name" => $this->input->post("name"), "incomecategoriesID !=" => $id, "status" => 0));
			if(customCompute($category)) {
				$this->form_validation->set_message("unique_name", "%s already exists");
				return FALSE;
			}
			return TRUE;
		} else {
			$category = $this->incomecategories_m->get_order_by_incomecategories(array("name" => $this->input->post("name"), "status" => 0));
			if(customCompute($category)) {
				$this->form_validation->set_message("unique_name", "%s already exists");
				return FALSE;
			}
			return TRUE;
		}	
	}
}
