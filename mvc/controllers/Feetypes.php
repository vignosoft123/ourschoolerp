<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feetypes extends Admin_Controller {
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
		$this->load->model("feetypes_m");
		$this->load->model("section_m");
		$language = $this->session->userdata('lang');
		$this->lang->load('feetypes', $language);	
	}

	public function index() {
		$this->data['feetypes'] = $this->feetypes_m->get_feetypes();
		$this->data["subview"] = "feetypes/index";
		$this->load->view('_layout_main', $this->data);
	}

	protected function rules() {
		$rules = array(
				array(
					'field' => 'feetypes', 
					'label' => $this->lang->line("feetypes_name"), 
					'rules' => 'trim|required|xss_clean|max_length[60]|callback_unique_feetypes'
				),
				array(
					'field' => 'note', 
					'label' => $this->lang->line("feetypes_note"), 
					'rules' => 'trim|xss_clean|max_length[200]'
				),
				array(
                	'field' => 'monthly',
                	'label' => $this->lang->line("feetypes_monthly"),
                	'rules' => 'trim|xss_clean|max_length[11]|numeric',
            	)
			);
		return $rules;
	}

	public function add() {
		if($_POST) {
			$rules = $this->rules();
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE) {
				$this->data["subview"] = "feetypes/add";
				$this->load->view('_layout_main', $this->data);			
			} else {
				$monthly = $this->input->post('monthly');
                if($monthly) {  
                    for($i = 1; $i<=12; $i++) {
                        $month = date('M', mktime(0, 0, 0, $i));
                        $array = [
                            'feetypes' => $this->input->post('feetypes'). ' ['.$month.']',
                            "note"     => $this->input->post("note"),
                        ];
                        $this->feetypes_m->insert_feetypes($array);
                    }
                } else {
                    $array = [
                        "feetypes" => $this->input->post("feetypes"),
                        "note"     => $this->input->post("note"),
                    ];

                    $this->feetypes_m->insert_feetypes($array);
                }

				$this->session->set_flashdata('success', $this->lang->line('menu_success'));
				redirect(base_url("feetypes/index"));
			}
		} else {
			$this->data["subview"] = "feetypes/add";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function edit() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->data['feetypes'] = $this->feetypes_m->get_feetypes($id);
			if($this->data['feetypes']) {
				if($_POST) {
					$rules = $this->rules();
					$this->form_validation->set_rules($rules);
					if ($this->form_validation->run() == FALSE) {
						$this->data["subview"] = "feetypes/edit";
						$this->load->view('_layout_main', $this->data);			
					} else {
						$array = array(
							"feetypes" => $this->input->post("feetypes"),
							"note" => $this->input->post("note")
						);

						$this->feetypes_m->update_feetypes($array, $id);
						$this->session->set_flashdata('success', $this->lang->line('menu_success'));
						redirect(base_url("feetypes/index"));
					}
				} else {
					$this->data["subview"] = "feetypes/edit";
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

	public function delete() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->feetypes_m->delete_feetypes($id);
			$this->session->set_flashdata('success', $this->lang->line('menu_success'));
			redirect(base_url("feetypes/index"));
		} else {
			redirect(base_url("feetypes/index"));
		}
	}

	public function unique_feetypes() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$feetypes = $this->feetypes_m->get_order_by_feetypes(array("feetypes" => $this->input->post("feetypes"), "feetypesID !=" => $id));
			if(customCompute($feetypes)) {
				$this->form_validation->set_message("unique_feetypes", "%s already exists");
				return FALSE;
			}
			return TRUE;
		} else {
			$monthly = $this->input->post('monthly');
			if($monthly) {
				for($i = 1; $i<=12; $i++) {
                    $month = date('M', mktime(0, 0, 0, $i));
                    $array = [
                        'feetypes' => $this->input->post('feetypes'). ' ['.$month.']'
                    ];
					$feetypes = $this->feetypes_m->get_order_by_feetypes($array);

					if(customCompute($feetypes)) {
						$this->form_validation->set_message("unique_feetypes", "The ".$this->input->post('feetypes'). ' ['.$month.']' ." already exists");
						return FALSE;
					}
                }
				return TRUE;
			} else {
				$feetypes = $this->feetypes_m->get_order_by_feetypes(array("feetypes" => $this->input->post("feetypes")));
				if(customCompute($feetypes)) {
					$this->form_validation->set_message("unique_feetypes", "%s already exists");
					return FALSE;
				}
				return TRUE;
			}
		}	
	}

 
	public function fee_setup()
	{
	     
    
		if (($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID') || $this->session->userdata('usertypeID') == 1)) {
			$this->data['headerassets'] = array(
				'css' => array(
					'assets/select2/css/select2.css',
					'assets/select2/css/select2-bootstrap.css'
				),
				'js' => array(
					'assets/select2/select2.js'
				)
			);
			 
 
			$this->data['set_classes'] = 0;
			$this->data['set_section'] = 0; 

		 
			$this->data['classes']  = $this->classes_m->get_order_by_classes(['classesID !=' => $graduateclass]);

			if ($_POST) {
				 
			 
				 	$classesID       = $this->input->post('classesID'); 
					 $schoolyearID       = $this->session->userdata('defaultschoolyearID');
					 
					if ((int)$classesID) {
						//  $sql = "select s.* , sf.fee_amount,sf.id as sf_id from section s left join school_fees sf on sf.section_id=s.sectionID where s.classesID='".$classesID."' ";
						$sql = "select s.*  from section s where s.classesID='".$classesID."' ";
						  $this->data['sections'] = $this->db->query($sql)->result();// $this->section_m->get_order_by_section(array('classesID' => $classesID));
        			} else { 
        				$this->data['sections'] = [];
        			}
					
				// print_r($this->data['sections']);die;
					$this->data['set_classes'] = $classesID; 

					$classes         = $this->classes_m->get_single_classes(array('classesID' => $classesID));
					  
				  
					$this->data['sendClasses']  = $classes;
					 
				
				 

					$this->data["subview"] = "feetypes/fee_setup";
					$this->load->view('_layout_main', $this->data);
				 
					} 
				else {
				$this->data["subview"] = "feetypes/fee_setup";
				$this->load->view('_layout_main', $this->data);
			}
		}  
	}

	public function update_school_fee(){ 
		$schoolyearID       = $this->session->userdata('defaultschoolyearID');

		$vid = $_POST['vid'];
		$fee_amount = $_POST['my_value'];
		$section_id = $_POST['section_id'];
		$class_id = $_POST['class_id'];
		

		if(!empty($vid)){
			$data = array( 
				'fee_amount' => $fee_amount, 
			);
			$this->db->where('id',$vid);
			$this->db->update('school_fees',$data);
		}else{
			$data = array(
				'section_id' => $section_id,
				'class_id' => $class_id,
				'fee_amount' => $fee_amount,
				'year_id' => $schoolyearID
			);
			$this->db->insert('school_fees',$data);
		}
		
		echo 1;
	}

}
