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
		$draw   = intval($this->input->post("draw"));
		$start  = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$search = $this->input->post("search")['value'];
		$server = $this->input->post("server");

		$subdomains  = $this->subdomains_m->get_subdomains_with_pagination($length, $start, $search, $server);
		$total_count = $this->subdomains_m->get_subdomains_count($search, $server);

		$data = array();
		foreach ($subdomains as $key => $subdomain) {

			$actions  = '';
			$actions .= '<a href="javascript:void(0)" class="btn btn-success btn-sm" title="Create Tables" onclick="event.stopPropagation();createTables(this,' . $subdomain->id . ')"><i class="fa fa-database"></i></a> ';
			$actions .= '<a href="javascript:void(0)" class="btn btn-sm btn-statistics" title="Statistics" onclick="event.stopPropagation();showStatistics(' . $subdomain->id . ',\'' . addslashes(htmlspecialchars($subdomain->site_name ?: $subdomain->subdomain)) . '\')"><i class="fa fa-bar-chart"></i></a> ';
			$actions .= '<a href="' . base_url('subdomains/edit/' . $subdomain->id) . '" class="btn btn-primary btn-sm" title="Edit" onclick="event.stopPropagation()"><i class="fa fa-edit"></i></a> ';
			$actions .= '<a href="' . base_url('subdomains/delete/' . $subdomain->id) . '" class="btn btn-danger btn-sm" title="Delete" onclick="event.stopPropagation();return confirm(\'Are you sure you want to delete this subdomain?\')"><i class="fa fa-trash"></i></a>';

			$data[] = array(
				// Visible columns
				'serial'          => $start + $key + 1,
				'server'          => htmlspecialchars($subdomain->server),
				'subdomain'       => htmlspecialchars($subdomain->subdomain),
				'db_name'         => htmlspecialchars($subdomain->db_name),
				'school_age'      => isset($subdomain->school_age)      ? intval($subdomain->school_age)      : '—',
				'total_students'  => isset($subdomain->total_students)  ? intval($subdomain->total_students)  : '—',
				'total_app_users' => isset($subdomain->total_app_users) ? intval($subdomain->total_app_users) : '—',
				'actions'         => $actions,
				// Full detail for row-click popup (prefixed with underscore)
				'_id'          => $subdomain->id,
				'_site_name'   => htmlspecialchars($subdomain->site_name),
				'_main_domain' => htmlspecialchars($subdomain->main_domain),
				'_db_host'     => htmlspecialchars($subdomain->db_host),
				'_db_user'     => htmlspecialchars($subdomain->db_user),
				'_logo_url'    => htmlspecialchars($subdomain->logo_url),
				'_theme_color' => htmlspecialchars($subdomain->theme_color),
				'_status'      => $subdomain->status,
				'_created_at'  => $subdomain->created_at,
			);
		}

		echo json_encode(array(
			"draw"            => $draw,
			"recordsTotal"    => $total_count,
			"recordsFiltered" => $total_count,
			"data"            => $data,
		));
	}

	public function ajax_school_age_info() {
		header('Content-Type: application/json');

		// Check table existence with raw query (CI3 active-record aliases break table_exists)
		$check = $this->db->query("SHOW TABLES LIKE 'school_age_info'");
		if (!$check || $check->num_rows() === 0) {
			echo json_encode(array('years' => array(), 'subdomains' => array(), 'total_subdomains' => 0));
			return;
		}

		// Raw SQL to avoid CI3 backtick-wrapping alias names
		$sql = "SELECT sai.subdomain_id, sai.finyear,
		               sai.numberofstudents, sai.numberofappusers,
		               ss.subdomain, ss.site_name, ss.server
		        FROM   school_age_info sai
		        INNER JOIN subdomain_settings ss ON ss.id = sai.subdomain_id
		        ORDER BY ss.subdomain ASC, sai.finyear ASC";
		$rows = $this->db->query($sql)->result();

		$seen_years    = array();
		$years         = array();
		$subdomain_map = array();

		foreach ($rows as $row) {
			if (!in_array($row->finyear, $seen_years)) {
				$seen_years[] = $row->finyear;
				$years[]      = $row->finyear;
			}
			if (!isset($subdomain_map[$row->subdomain_id])) {
				$subdomain_map[$row->subdomain_id] = array(
					'subdomain_id' => $row->subdomain_id,
					'subdomain'    => $row->subdomain,
					'site_name'    => $row->site_name ?: $row->subdomain,
					'server'       => $row->server,
					'data'         => array(),
				);
			}
			$subdomain_map[$row->subdomain_id]['data'][$row->finyear] = array(
				'students'  => (int)$row->numberofstudents,
				'app_users' => (int)$row->numberofappusers,
			);
		}

		sort($years);

		echo json_encode(array(
			'years'            => $years,
			'subdomains'       => array_values($subdomain_map),
			'total_subdomains' => count($subdomain_map),
		));
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

	public function python_server_status() {
		$conn = @fsockopen('127.0.0.1', 8000, $errno, $errstr, 1);
		if ($conn) {
			fclose($conn);
			echo json_encode(['running' => true]);
		} else {
			echo json_encode(['running' => false]);
		}
	}

	public function start_python_server() {
		// Check if already running
		$conn = @fsockopen('127.0.0.1', 8000, $errno, $errstr, 1);
		if ($conn) {
			fclose($conn);
			echo json_encode(['success' => true, 'message' => 'Python server is already running on port 8000.']);
			return;
		}

		$bat = 'C:\\xampp\\htdocs\\ourschoolerp\\python\\start_server.bat';
		if (!file_exists($bat)) {
			echo json_encode(['success' => false, 'message' => 'start_server.bat not found.']);
			return;
		}

		// Start in background (Windows)
		pclose(popen('start /B "" "' . $bat . '" >NUL 2>&1', 'r'));

		// Wait up to 5s for server to come up
		$started = false;
		for ($i = 0; $i < 10; $i++) {
			sleep(1);
			$check = @fsockopen('127.0.0.1', 8000, $e, $s, 1);
			if ($check) {
				fclose($check);
				$started = true;
				break;
			}
		}

		if ($started) {
			echo json_encode(['success' => true, 'message' => 'Python server started successfully on port 8000.']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Server started but not yet responding. Check the terminal window.']);
		}
	}
}