<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Village extends Admin_Controller {
/*
| -----------------------------------------------------
| PRODUCT NAME: 	INILABS SCHOOL MANAGEMENT SYSTEM
| -----------------------------------------------------
| AUTHOR:			INILABS TEAM
| -----------------------------------------------------
| EMAIL:			info@inilabs.net
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY INILABS IT
| -----------------------------------------------------
| WEBSITE:			http://inilabs.net
| -----------------------------------------------------
*/
	function __construct() {
		parent::__construct();
		$this->load->model("village_m");
		$language = $this->session->userdata('lang');
		$this->lang->load('village', $language);	
	}

	public function index() {
		$this->data['villages'] = $this->village_m->get_order_by_village();
		$this->data["subview"] = "village/index";
		$this->load->view('_layout_main', $this->data);
	}

	protected function rules() {
		$rules = array(
				array(
					'field' => 'villageName', 
					'label' => $this->lang->line("village_name"), 
					'rules' => 'trim|required|xss_clean|max_length[250]|callback_unique_village'
				), 
				array(
					'field' => 'status', 
					'label' => $this->lang->line("village_statue"), 
					'rules' => 'trim|required|xss_clean'
				)
			);
		return $rules;
	}

	public function add() {
		if($_POST) {
			$rules = $this->rules();
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE) {
				$this->data["subview"] = "village/add";
				$this->load->view('_layout_main', $this->data);			
			} else {
				$array = array(
					"villageName" => $this->input->post("villageName"),
					"status" => $this->input->post("status")
				);
				$this->village_m->insert_village($array);
				$this->session->set_flashdata('success', $this->lang->line('menu_success'));
				redirect(base_url("village/index"));
			}
		} else {
			$this->data["subview"] = "village/add";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function edit() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->data['village'] = $this->village_m->get_village($id);
			if($this->data['village']) {
				if($_POST) {
					$rules = $this->rules();
					$this->form_validation->set_rules($rules);
					if ($this->form_validation->run() == FALSE) {
						$this->data['form_validation'] = validation_errors(); 
						$this->data["subview"] = "village/edit";
						$this->load->view('_layout_main', $this->data);			
					} else {
						$array = array(
							"villageName" => $this->input->post("villageName"),
							"status" => $this->input->post("status")
						);
						$this->village_m->update_village($array, $id);
						$this->session->set_flashdata('success', $this->lang->line('menu_success'));
						redirect(base_url("village/index"));
					}
				} else {
					$this->data["subview"] = "village/edit";
					$this->load->view('_layout_main', $this->data);
				}
			} else {
				$this->data["subview"] = "error";
				$this->load->view('_layout_main', $this->data);
			}
		} else {
			$this->data["subview"] = "error";
			$this->load->view('_layout_main', $this->data);
		}
	}


	public function unique_village() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$student = $this->village_m->get_order_by_village(array("villageName" => $this->input->post("villageName"), "villageID !=" => $id, "villageName" => $this->input->post('villageName')));
			if(customCompute($student)) {
				$this->form_validation->set_message("unique_village", "%s already exists");
				return FALSE;
			}
			return TRUE;
		} else {
			$student = $this->village_m->get_order_by_village(array("villageName" => $this->input->post("villageName")));
			if(customCompute($student)) {
				$this->form_validation->set_message("unique_village", "%s already exists");
				return FALSE;
			}
			return TRUE;
		}	
	}

	function valid_number() {
		if($this->input->post('price') && $this->input->post('price') < 0) {
			$this->form_validation->set_message("valid_number", "%s is invalid number");
			return FALSE;
		}
		return TRUE;
	}

	function valid_number_for_quantity() {
		if($this->input->post('quantity') && $this->input->post('quantity') < 0) {
			$this->form_validation->set_message("valid_number_for_quantity", "%s is invalid number");
			return FALSE;
		}
		return TRUE;
	}
}

/* End of file village.php */
/* Location: .//D/xampp/htdocs/school/mvc/controllers/village.php */