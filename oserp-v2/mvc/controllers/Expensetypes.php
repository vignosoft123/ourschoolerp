<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expensetypes extends Admin_Controller {
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
		$this->load->model("expensetypes_m");
		$language = $this->session->userdata('lang');
		// $this->lang->load('expensetypes', $language);	
	}

	public function index() {
		$this->db->where('status',0);
		$this->data['expensetypes'] = $this->db->get('expensetypes')->result();//$this->expensetypes_m->get_expensetypes(array('status'=>0));
		$this->data["subview"] = "expensetypes/index";
		$this->load->view('_layout_main', $this->data);
	}

	protected function rules() {
		$rules = array(
				array(
					'field' => 'expensetypes', 
					'label' => $this->lang->line("expensetypes_name"), 
					'rules' => 'trim|required|xss_clean|max_length[60]|callback_unique_expensetypes'
				),
				array(
					'field' => 'note', 
					'label' => $this->lang->line("expensetypes_note"), 
					'rules' => 'trim|xss_clean|max_length[200]'
				),
				// array(
                // 	'field' => 'monthly',
                // 	'label' => $this->lang->line("expensetypes_monthly"),
                // 	'rules' => 'trim|xss_clean|max_length[11]|numeric',
            	// )
			);
		return $rules;
	}

	public function add() {
		if($_POST) {
			$rules = $this->rules();
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE) {
				$this->data["subview"] = "expensetypes/add";
				$this->load->view('_layout_main', $this->data);			
			} else {
				$monthly = $this->input->post('monthly');
                if($monthly) {  
                    for($i = 1; $i<=12; $i++) {
                        $month = date('M', mktime(0, 0, 0, $i));
                        $array = [
                            'expensetypes' => $this->input->post('expensetypes'). ' ['.$month.']',
                            "note"     => $this->input->post("note"),
                        ];
                        $this->expensetypes_m->insert_expensetypes($array);
                    }
                } else {
                    $array = [
                        "expensetypes" => $this->input->post("expensetypes"),
                        "note"     => $this->input->post("note"),
                    ];

                    $this->expensetypes_m->insert_expensetypes($array);
                }

				$this->session->set_flashdata('success', $this->lang->line('menu_success'));
				redirect(base_url("expensetypes/index"));
			}
		} else {
			$this->data["subview"] = "expensetypes/add";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function edit() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->data['expensetypes'] = $this->expensetypes_m->get_expensetypes($id);
			if($this->data['expensetypes']) {
				if($_POST) {
					$rules = $this->rules();
					$this->form_validation->set_rules($rules);
					if ($this->form_validation->run() == FALSE) {
						$this->data["subview"] = "expensetypes/edit";
						$this->load->view('_layout_main', $this->data);			
					} else {
						$array = array(
							"expensetypes" => $this->input->post("expensetypes"),
							"note" => $this->input->post("note")
						);

						$this->expensetypes_m->update_expensetypes($array, $id);
						$this->session->set_flashdata('success', $this->lang->line('menu_success'));
						redirect(base_url("expensetypes/index"));
					}
				} else {
					$this->data["subview"] = "expensetypes/edit";
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

	public function delete_exp_category($id=""){
		if(!empty($id)){
			$data = array('status'=>1);
			$this->db->where('expensetypesID',$id);
			$this->db->update("expensetypes",$data);
			// echo $this->db->last_query();die;

			$this->session->set_flashdata('success', $this->lang->line('menu_success'));
			redirect(base_url("expensetypes/index"));
		}
	}

	public function delete() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->expensetypes_m->delete_expensetypes($id);
			$this->session->set_flashdata('success', $this->lang->line('menu_success'));
			redirect(base_url("expensetypes/index"));
		} else {
			redirect(base_url("expensetypes/index"));
		}
	}

	public function unique_expensetypes() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$expensetypes = $this->expensetypes_m->get_order_by_expensetypes(array("expensetypes" => $this->input->post("expensetypes"), "expensetypesID !=" => $id));
			if(customCompute($expensetypes)) {
				$this->form_validation->set_message("unique_expensetypes", "%s already exists");
				return FALSE;
			}
			return TRUE;
		} else {
			$monthly = $this->input->post('monthly');
			if($monthly) {
				for($i = 1; $i<=12; $i++) {
                    $month = date('M', mktime(0, 0, 0, $i));
                    $array = [
                        'expensetypes' => $this->input->post('expensetypes'). ' ['.$month.']'
                    ];
					$expensetypes = $this->expensetypes_m->get_order_by_expensetypes($array);

					if(customCompute($expensetypes)) {
						$this->form_validation->set_message("unique_expensetypes", "The ".$this->input->post('expensetypes'). ' ['.$month.']' ." already exists");
						return FALSE;
					}
                }
				return TRUE;
			} else {
				$expensetypes = $this->expensetypes_m->get_order_by_expensetypes(array("expensetypes" => $this->input->post("expensetypes")));
				if(customCompute($expensetypes)) {
					$this->form_validation->set_message("unique_expensetypes", "%s already exists");
					return FALSE;
				}
				return TRUE;
			}
		}	
	}
}
