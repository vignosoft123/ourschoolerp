<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transport extends Admin_Controller {
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
		$this->load->model("transport_m");
		$this->load->model("student_m");
		$this->load->model("tmember_m");
		$language = $this->session->userdata('lang');
		$this->lang->load('transport', $language);	
	}

	public function index() {
		$this->data['transports'] = $this->transport_m->get_order_by_transport();
		$this->data["subview"] = "transport/index";
		$this->load->view('_layout_main', $this->data);
	}

	protected function rules() {
		$rules = array(
			array(
				'field' => 'route', 
				'label' => $this->lang->line("transport_route"), 
				'rules' => 'trim|required|xss_clean|max_length[128]|callback_unique_route'
			), 
			// array(
			// 	'field' => 'vehicle', 
			// 	'label' => $this->lang->line("transport_vehicle"),
			// 	'rules' => 'trim|required|max_length[11]|xss_clean|numeric|callback_valid_number'
			// ), 
			array(
				'field' => 'fare', 
				'label' => $this->lang->line("transport_fare"),
				'rules' => 'trim|max_length[11]|xss_clean|numeric|callback_valid_number_for_fare'
			),
			array(
				'field' => 'note', 
				'label' => $this->lang->line("transport_note"), 
				'rules' => 'trim|max_length[200]|xss_clean'
			),
			array(
				'field' => 'pickup_point', 
				'label' => $this->lang->line("pickup_point"), 
				'rules' => 'trim|xss_clean',
			)
		);
		return $rules;
	}

	public function add() {

        $driver_user_type_id = $this->db->query("select usertypeID from usertype where usertype='Driver'")->row()->usertypeID;
        $attender_user_type_id = $this->db->query("select usertypeID from usertype where usertype='Attender'")->row()->usertypeID;
		$this->db->where('usertypeID',$driver_user_type_id);
		$this->data["drivers"] = $this->db->get('user')->result();

		$this->db->where('usertypeID',$attender_user_type_id);
		$this->data["attenders"] = $this->db->get('user')->result();

		if($_POST) {
			$rules = $this->rules();
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE) {
				$this->data['form_validation'] = validation_errors(); 
				$this->data["subview"] = "transport/add";
				$this->load->view('_layout_main', $this->data);			
			} else {
				$array = array(
					"route" => $this->input->post("route"),
					"vehicle" => $this->input->post("vehicle"),
					"fare" => $this->input->post("fare"),
					"note" => $this->input->post("note"),
					"pickupPoint" => $this->input->post("pickup_point"),
					"driverID" => $this->input->post("driverID"),
					"attenderID" => $this->input->post("attenderID"),
					"vehicle_type" => $this->input->post("vehicle_type"),
					"capacity" => $this->input->post("capacity"),
				);

				$this->transport_m->insert_transport($array);
				// echo $this->db->last_query();die;
				$this->session->set_flashdata('success', $this->lang->line('menu_success'));
				redirect(base_url("transport/index"));
			}
		} else {
			$this->data["subview"] = "transport/add";
			$this->load->view('_layout_main', $this->data);
		}
	}
	public function add_new() {

		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css',
				'assets/datepicker/datepicker.css',
				'assets/timepicker/timepicker.css'
			),
			'js' => array(
				'assets/select2/select2.js',
				'assets/datepicker/datepicker.js',
				'assets/timepicker/timepicker.js'
			)
		);
		 
		// echo "<pre>";print_r($_POST);die;
		$this->data["transports"] = $this->db->get('transport')->result();

		$sql = "select p.*,t.route from pickup_points p left join transport t on t.transportID = p.route_id";
		$this->data['pickup_points'] = $this->db->query($sql)->result();

		if($_POST) {
			 
				$transportID = $this->input->post("transportID");
				// $array = array(
				// 	"capacity" => $this->input->post("capacity"),
				// 	"fare" => $this->input->post("fare"),
				// 	"pickupPoint" => $this->input->post("pickup_point"),
				// 	"pickup_time" => $this->input->post("pickup_time"),
				// 	"droping_time" => $this->input->post("drop_time"),
				// );
				// $this->db->where('transportID',$transportID);
				// $this->db->update('transport',$array);


			
				$insert_array = array(
					"fare" => $this->input->post("fare"),
					"pickupPoint" => $this->input->post("pickup_point"),
					"pickup_time" => $this->input->post("pickup_time"),
					"droping_time" => $this->input->post("drop_time"),
					"route_id" => $this->input->post("transportID"),
				);
				$this->db->insert('pickup_points',$insert_array);
 
				$this->session->set_flashdata('success', $this->lang->line('menu_success'));
				redirect(base_url("transport/add_new"));
			//}
		} else {
			$this->data["subview"] = "transport/add_new";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function edit() {
		$id = htmlentities(escapeString($this->uri->segment(3)));

		$this->db->where('usertypeID',13);
		$this->data["drivers"] = $this->db->get('user')->result();

		$this->db->where('usertypeID',14);
		$this->data["attenders"] = $this->db->get('user')->result();
		
		if((int)$id) {
			$this->data['transport'] = $this->transport_m->get_transport($id);
			if($this->data['transport']) {
				if($_POST) {
					$rules = $this->rules();
					$this->form_validation->set_rules($rules);
					if ($this->form_validation->run() == FALSE) {
						$this->data["subview"] = "transport/edit";
						$this->load->view('_layout_main', $this->data);			
					} else {
						$array = array(
							"route" => $this->input->post("route"),
							"vehicle" => $this->input->post("vehicle"),
							"fare" => $this->input->post("fare"),
							"note" => $this->input->post("note"),
							"pickupPoint" => $this->input->post("pickup_point"),
							"driverID" => $this->input->post("driverID"),
							"vehicle_type" => $this->input->post("vehicle_type"),
							"attenderID" => $this->input->post("attenderID"),
						);

						$this->transport_m->update_transport($array, $id);
						$this->session->set_flashdata('success', $this->lang->line('menu_success'));
						redirect(base_url("transport/index"));
					}
				} else {
					$this->data["subview"] = "transport/edit";
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
			$lmembers = $this->tmember_m->get_order_by_tmember(array("transportID" => $id));
			foreach ($lmembers as $lmember) {
				$this->student_m->update_student_classes(array("transport" => 0), array("studentID" => $lmember->studentID));
			}
			$this->tmember_m->delete_tmember_tID($id);
			$this->transport_m->delete_transport($id);
			$this->session->set_flashdata('success', $this->lang->line('menu_success'));
			redirect(base_url("transport/index"));
		} else {
			redirect(base_url("transport/index"));
		}
	}

	function unique_route() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$transport = $this->transport_m->get_order_by_transport(array("route" => $this->input->post("route"), "transportID !=" => $id));
			if(customCompute($transport)) {
				$this->form_validation->set_message("unique_route", "%s already exists");
				return FALSE;
			}
			return TRUE;
		} else {
			$transport = $this->transport_m->get_order_by_transport(array("route" => $this->input->post("route")));

			if(customCompute($transport)) {
				$this->form_validation->set_message("unique_route", "%s already exists");
				return FALSE;
			}
			return TRUE;
		}	
	}

	function valid_number() {
		if($this->input->post('vehicle') && $this->input->post('vehicle') < 0) {
			$this->form_validation->set_message("valid_number", "%s is invalid number");
			return FALSE;
		}
		return TRUE;
	}

	function valid_number_for_fare() {
		if($this->input->post('fare') && $this->input->post('fare') < 0) {
			$this->form_validation->set_message("valid_number_for_fare", "%s is invalid number");
			return FALSE;
		}
		return TRUE;
	}
}

/* End of file transport.php */
/* Location: .//D/xampp/htdocs/school/mvc/controllers/transport.php */