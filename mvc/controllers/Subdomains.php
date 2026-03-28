<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subdomains extends Admin_Controller {
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
		$this->load->model("subdomains_m");
		$language = $this->session->userdata('lang');
		$this->lang->load('subdomains', $language);
		
		// Check if current subdomain is staging.ourschoolerp.com
		if (!$this->is_staging_subdomain()) {
			show_error('Access denied. This feature is only available for staging subdomain.');
		}
	}

	private function is_staging_subdomain() {
		$current_host = $_SERVER['HTTP_HOST'];
		// return $current_host === 'staging.ourschoolerp.com';
        return $current_host === 'staging.ourschoolerp.localhost';
	}

	protected function rules() {
		$rules = array(
			array(
				'field' => 'server', 
				'label' => 'Server', 
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'subdomain', 
				'label' => 'Subdomain',
				'rules' => 'trim|required|xss_clean|max_length[100]|callback_unique_subdomain'
			),
			array(
				'field' => 'db_host', 
				'label' => 'Database Host',
				'rules' => 'trim|required|xss_clean|max_length[255]'
			),
			array(
				'field' => 'db_name', 
				'label' => 'Database Name',
				'rules' => 'trim|required|xss_clean|max_length[150]'
			),
			array(
				'field' => 'db_user', 
				'label' => 'Database User',
				'rules' => 'trim|required|xss_clean|max_length[150]'
			),
			array(
				'field' => 'db_pass', 
				'label' => 'Database Password',
				'rules' => 'trim|required|xss_clean|max_length[150]'
			),
			array(
				'field' => 'site_name', 
				'label' => 'Site Name',
				'rules' => 'trim|xss_clean|max_length[255]'
			),
			array(
				'field' => 'logo_url', 
				'label' => 'Logo URL',
				'rules' => 'trim|xss_clean|max_length[255]'
			),
			array(
				'field' => 'theme_color', 
				'label' => 'Theme Color',
				'rules' => 'trim|xss_clean|max_length[20]'
			),
			array(
				'field' => 'status', 
				'label' => 'Status',
				'rules' => 'trim|required|xss_clean|in_list[active,inactive]'
			)
		);
		return $rules;
	}

	public function index() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/datatables/dataTables.bootstrap.css',
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/datatables/jquery.dataTables.min.js',
				'assets/datatables/dataTables.bootstrap.min.js',
				'assets/select2/select2.js'
			)
		);

		$this->data['servers'] = $this->db->select('server')->distinct()->get('subdomain_settings')->result();
		$this->data["subview"] = "subdomains/index";
		$this->load->view('_layout_main', $this->data);
	}

	public function add() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/select2/select2.js'
			)
		);

		if($_POST) {
			$rules = $this->rules();
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE) {
				$this->data["subview"] = "subdomains/add";
				$this->load->view('_layout_main', $this->data);
			} else {
				$array = array(
					"server" => $this->input->post("server"),
					"subdomain" => $this->input->post("subdomain"),
					"db_host" => $this->input->post("db_host"),
					"db_name" => $this->input->post("db_name"),
					"db_user" => $this->input->post("db_user"),
					"db_pass" => $this->input->post("db_pass"),
					"site_name" => $this->input->post("site_name"),
					"logo_url" => $this->input->post("logo_url"),
					"theme_color" => $this->input->post("theme_color"),
					"status" => $this->input->post("status")
				);

				$this->subdomains_m->insert_subdomain($array);
				$this->session->set_flashdata('success', 'Subdomain added successfully');
				redirect(base_url("subdomains/index"));
			}
		} else {
			$this->data["subview"] = "subdomains/add";
			$this->load->view('_layout_main', $this->data);
		}
	}

	public function edit() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/select2/select2.js'
			)
		);

		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->data['subdomain'] = $this->subdomains_m->get_single_subdomain(array('id' => $id));
			if($this->data['subdomain']) {
				if($_POST) {
					$rules = $this->rules();
					$this->form_validation->set_rules($rules);
					if ($this->form_validation->run() == FALSE) {
						$this->data["subview"] = "subdomains/edit";
						$this->load->view('_layout_main', $this->data);
					} else {
						$array = array(
							"server" => $this->input->post("server"),
							"subdomain" => $this->input->post("subdomain"),
							"db_host" => $this->input->post("db_host"),
							"db_name" => $this->input->post("db_name"),
							"db_user" => $this->input->post("db_user"),
							"db_pass" => $this->input->post("db_pass"),
							"site_name" => $this->input->post("site_name"),
							"logo_url" => $this->input->post("logo_url"),
							"theme_color" => $this->input->post("theme_color"),
							"status" => $this->input->post("status")
						);

						$this->subdomains_m->update_subdomain($array, $id);
						$this->session->set_flashdata('success', 'Subdomain updated successfully');
						redirect(base_url("subdomains/index"));
					}
				} else {
					$this->data["subview"] = "subdomains/edit";
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
			$subdomain = $this->subdomains_m->get_single_subdomain(array('id' => $id));
			if(customCompute($subdomain)) {
				$this->subdomains_m->delete_subdomain($id);
				$this->session->set_flashdata('success', 'Subdomain deleted successfully');
				redirect(base_url("subdomains/index"));
			} else {
				redirect(base_url("subdomains/index"));
			}
		} else {
			redirect(base_url("subdomains/index"));
		}
	}

	public function ajax_list() {
		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$search = $this->input->post("search")['value'];
		$server = $this->input->post("server");

		$subdomains = $this->subdomains_m->get_subdomains_with_pagination($length, $start, $search, $server);
		$total_count = $this->subdomains_m->get_subdomains_count($search, $server);

		$data = array();
		foreach($subdomains as $key => $subdomain) {
			$row = array();
			$row[] = $start + $key + 1;
			$row[] = htmlspecialchars($subdomain->server);
			$row[] = htmlspecialchars($subdomain->subdomain);
			$row[] = htmlspecialchars($subdomain->db_host);
			$row[] = htmlspecialchars($subdomain->db_name);
			$row[] = htmlspecialchars($subdomain->site_name);
			$row[] = htmlspecialchars($subdomain->main_domain);
			$row[] = '<span class="badge badge-' . ($subdomain->status == 'active' ? 'success' : 'danger') . '">' . ucfirst($subdomain->status) . '</span>';
			
			$actions = '';
			$actions .= '<a href="javascript:void(0)" class="btn btn-success btn-sm" title="Create Tables" onclick="createTables(this, ' . $subdomain->id . ')"><i class="fa fa-database"></i></a> ';
			$actions .= '<a href="' . base_url('subdomains/edit/' . $subdomain->id) . '" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-edit"></i></a> ';
			$actions .= '<a href="' . base_url('subdomains/delete/' . $subdomain->id) . '" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm(\'Are you sure you want to delete this subdomain?\')"><i class="fa fa-trash"></i></a>';
			
			$row[] = $actions;
			$data[] = $row;
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $total_count,
			"recordsFiltered" => $total_count,
			"data" => $data
		);

		echo json_encode($output);
	}

	public function get_db_host() {
		$server = $this->input->post('server');
		$db_hosts = array(
			'hostgator' => '119.18.54.141',
			'godaddy' => '118.139.183.79',
			'myschools' => '119.18.54.166',
			'schoolhour' => '162.241.123.136',
			'collegehour' => '103.76.231.69'
		);
		
		$response = array(
			'success' => false,
			'db_host' => ''
		);
		
		if (isset($db_hosts[$server])) {
			$response['success'] = true;
			$response['db_host'] = $db_hosts[$server];
		}
		
		echo json_encode($response);
	}

	public function unique_subdomain() {
		$subdomain = $this->input->post('subdomain');
		$id = $this->uri->segment(3);
		
		if ($this->subdomains_m->subdomain_exists($subdomain, $id)) {
			$this->form_validation->set_message("unique_subdomain", "The %s already exists");
			return FALSE;
		}
		return TRUE;
	}
}