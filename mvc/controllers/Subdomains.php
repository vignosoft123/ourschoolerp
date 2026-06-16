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

		$domain_map = [
			'hostgator'  => 'ourschoolerp.com',
			'myschools'  => 'myschoolserp.com',
			'schoolhour' => 'schoolhour.in',
			'collegehour'=> 'collegeerp.in',
			'godaddy'    => 'ourcollegeerp.com',
		];

		$data = array();
		foreach ($subdomains as $key => $subdomain) {

			$srv         = strtolower($subdomain->server);
			$base_domain = isset($domain_map[$srv]) ? $domain_map[$srv] : '';
			$full_domain = $base_domain ? ($subdomain->subdomain . '.' . $base_domain) : '';
			$domain_html = $full_domain
				? '<a href="https://' . $full_domain . '" target="_blank" rel="noopener" onclick="event.stopPropagation()" style="color:#1565c0;font-size:12px;white-space:nowrap;">' . $full_domain . ' <i class="fa fa-external-link" style="font-size:10px;"></i></a>'
				: '<span style="color:#999;">—</span>';

			$sep = '<span class="btn-group-sep"></span>';

			$actions  = '<div class="action-group">';

			// Group 1 — Data
			$actions .= '<span class="btn-group-wrap">';
			$actions .= '<a href="javascript:void(0)" class="btn btn-success btn-sm" title="Create Tables" onclick="event.stopPropagation();createTables(this,' . $subdomain->id . ')"><i class="fa fa-database"></i></a>';
			$actions .= '<a href="javascript:void(0)" class="btn btn-sm btn-statistics" title="Statistics" onclick="event.stopPropagation();showStatistics(' . $subdomain->id . ',\'' . addslashes(htmlspecialchars($subdomain->site_name ?: $subdomain->subdomain)) . '\')"><i class="fa fa-bar-chart"></i></a>';
			$actions .= '</span>';

			$actions .= $sep;

			// Group 2 — Deploy (order: Plug → CSS → Rocket → Archive)
			$actions .= '<span class="btn-group-wrap">';
			$actions .= '<a href="javascript:void(0)" class="btn btn-sm btn-bootstrap" title="Bootstrap: copy Cssupdate+Mvcdeploy to live server" onclick="event.stopPropagation();bootstrapSubdomain(this,' . $subdomain->id . ',\'' . addslashes($subdomain->subdomain) . '\')"><i class="fa fa-plug"></i></a>';
			$actions .= '<a href="javascript:void(0)" class="btn btn-warning btn-sm" title="Sync CSS to live server" onclick="event.stopPropagation();updateCss(this,' . $subdomain->id . ',\'' . addslashes($subdomain->subdomain) . '\')"><i class="fa fa-cloud-upload"></i></a>';
			$actions .= '<a href="javascript:void(0)" class="btn btn-sm btn-deploy-mvc" title="Deploy MVC from localhost to live server" onclick="event.stopPropagation();deployMvc(this,' . $subdomain->id . ',\'' . addslashes($subdomain->subdomain) . '\',\'' . addslashes($subdomain->server) . '\')"><i class="fa fa-rocket"></i></a>';
			$actions .= '<a href="javascript:void(0)" class="btn btn-sm btn-full-deploy" title="Full Deploy: extract all zip files (new domain)" onclick="event.stopPropagation();fullDeploy(this,' . $subdomain->id . ',\'' . addslashes($subdomain->subdomain) . '\')"><i class="fa fa-archive"></i></a>';
			$actions .= '</span>';

			$actions .= $sep;

			// Group 3 — Manage
			$actions .= '<span class="btn-group-wrap">';
			$actions .= '<a href="' . base_url('subdomains/edit/' . $subdomain->id) . '" class="btn btn-primary btn-sm" title="Edit" onclick="event.stopPropagation()"><i class="fa fa-edit"></i></a>';
			$actions .= '<a href="' . base_url('subdomains/delete/' . $subdomain->id) . '" class="btn btn-danger btn-sm" title="Delete" onclick="event.stopPropagation();return confirm(\'Are you sure you want to delete this subdomain?\')"><i class="fa fa-trash"></i></a>';
			$actions .= '</span>';

			$actions .= '</div>';

			$data[] = array(
				// Visible columns
				'serial'          => $start + $key + 1,
				'server'          => htmlspecialchars($subdomain->server),
				'subdomain'       => htmlspecialchars($subdomain->subdomain),
				'domain'          => $domain_html,
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

	public function stop_python_server() {
		header('Content-Type: application/json');

		// Collect all unique PIDs associated with port 8000
		$output = shell_exec('netstat -ano | findstr ":8000 "');
		$pids   = [];
		if ($output) {
			foreach (explode("\n", $output) as $line) {
				$line = trim($line);
				if (!$line) continue;
				$parts = preg_split('/\s+/', $line);
				$pid   = intval(end($parts));
				if ($pid > 4) $pids[$pid] = true; // skip PID 0/4 (system)
			}
		}

		$killed = [];
		foreach (array_keys($pids) as $pid) {
			exec("taskkill /F /T /PID $pid 2>&1", $out, $ret);
			if ($ret === 0) $killed[] = $pid;
		}

		if (!empty($killed)) {
			echo json_encode(['success' => true, 'message' => 'Python server stopped (PIDs: ' . implode(', ', $killed) . ').']);
			return;
		}

		// Fallback: kill all python.exe processes
		exec('taskkill /F /IM python.exe 2>&1', $out2, $ret2);
		if ($ret2 === 0) {
			echo json_encode(['success' => true, 'message' => 'Python server stopped (killed python.exe).']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Could not find or stop Python process on port 8000.']);
		}
	}

	/**
	 * Deploy MVC to a HostGator or BigRock subdomain by calling bootstrap_copy.php on the dummy server.
	 * mvc.zip must already be on the dummy server (uploaded via upload_mvc_zip_php).
	 * POST /subdomains/deploy_mvc_php/{subdomain_id}
	 */
	public function deploy_mvc_php($subdomain_id = 0) {
		header('Content-Type: application/json');

		$subdomain = $this->subdomains_m->get_single_subdomain(['id' => (int)$subdomain_id]);
		if (!$subdomain) {
			echo json_encode(['success' => false, 'message' => "Subdomain ID $subdomain_id not found"]);
			return;
		}

		$server_domains = [
			'hostgator'  => 'ourschoolerp.com',
			'myschools'  => 'myschoolserp.com',
			'schoolhour' => 'schoolhour.in',
			'collegehour'=> 'collegeerp.in',
		];
		$dummy_servers = [
			'hostgator'  => 'dummy1.ourschoolerp.com',
			'myschools'  => 'dummy1.myschoolserp.com',
			'schoolhour' => 'dummy1.schoolhour.in',
			'collegehour'=> 'dummy1.collegehour.in',
		];

		$server = $subdomain->server;
		if (!isset($dummy_servers[$server])) {
			echo json_encode(['success' => false, 'message' => "deploy_mvc_php not supported for server '$server' — use Python endpoint"]);
			return;
		}

		$dummy_host   = $dummy_servers[$server];
		$domain       = $server_domains[$server];
		$sub_name     = $subdomain->subdomain;
		$this->config->load('css_update_config');
		$api_key = $this->config->item('css_update_api_key');

		$url = "https://{$dummy_host}/bootstrap_copy.php?" . http_build_query([
			'k' => $api_key,
			's' => $sub_name,
			'd' => ".{$domain}",
		]);

		$ctx = stream_context_create(['http' => [
			'timeout' => 120,
			'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
		], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);

		$body = @file_get_contents($url, false, $ctx);
		if ($body === false) {
			echo json_encode(['success' => false, 'message' => "Could not reach {$dummy_host}/bootstrap_copy.php — check if dummy server is up"]);
			return;
		}

		$result = json_decode($body, true);
		if ($result === null) {
			// bootstrap_copy returned non-JSON (HTML error page)
			if (stripos($body, 'mvc.zip') !== false && stripos($body, 'not') !== false) {
				echo json_encode(['success' => false, 'message' => "mvc.zip not on dummy server — click 'Upload MVC to Dummy' first, then retry Rocket."]);
			} else {
				echo json_encode(['success' => false, 'message' => "Unexpected response from bootstrap_copy.php: " . substr($body, 0, 200)]);
			}
			return;
		}

		echo json_encode($result);
	}

	/**
	 * Upload local mvc.zip to the dummy server via FTP (PHP native ftp_* functions).
	 * Called ONCE per server before clicking Rocket on multiple subdomains.
	 * POST /subdomains/upload_mvc_zip_php/{server}
	 */
	public function upload_mvc_zip_php($server = '') {
		header('Content-Type: application/json');

		$mvc_zip_path = 'C:/xampp/htdocs/ourschoolerp/mvc.zip';
		if (!file_exists($mvc_zip_path)) {
			echo json_encode(['success' => false, 'message' => "mvc.zip not found at: $mvc_zip_path"]);
			return;
		}

		// FTP config per server (mirrors python/.env FTP_CONFIGS)
		// IMPORTANT: If cPanel/FTP password changes, update 'pass' here AND in python/.env FTP_CONFIGS
		$ftp_configs = [
			'hostgator' => [
				'host'       => 'cs3005.hostgator.in',
				'port'       => 21,
				'user'       => 'mindw2ft',
				'pass'       => 'Mindwhile$1986@',
				'dummy_dir'  => 'dummy1.ourschoolerp.com',
			],
			'myschools' => [
				'host'       => 'sh203.bigrock.com',
				'port'       => 21,
				'user'       => 'myschknc',
				'pass'       => 'Kiran$1986@',
				'dummy_dir'  => 'dummy1.myschoolserp.com',
			],
			'schoolhour' => [
				'host'       => 'schoolhour.in',
				'port'       => 21,
				'user'       => 'schoodj8',
				'pass'       => 'School@123456@',
				'dummy_dir'  => 'dummy1.schoolhour.in',
			],
			'collegehour' => [
				'host'       => 'collegehour.in',
				'port'       => 21,
				'user'       => 'collenv4p',
				'pass'       => 'Satya$1986$',
				'dummy_dir'  => 'dummy1.collegehour.in',
			],
		];

		if (!isset($ftp_configs[$server])) {
			echo json_encode(['success' => false, 'message' => "No FTP config for server '$server'. Valid: " . implode(', ', array_keys($ftp_configs))]);
			return;
		}

		$cfg  = $ftp_configs[$server];
		$conn = @ftp_connect($cfg['host'], $cfg['port'], 60);
		if (!$conn) {
			echo json_encode(['success' => false, 'message' => "FTP connect failed to {$cfg['host']}:{$cfg['port']}"]);
			return;
		}

		if (!@ftp_login($conn, $cfg['user'], $cfg['pass'])) {
			ftp_close($conn);
			echo json_encode(['success' => false, 'message' => "FTP login failed — check credentials in Subdomains.php upload_mvc_zip_php()"]);
			return;
		}

		ftp_pasv($conn, true);

		if (!@ftp_chdir($conn, $cfg['dummy_dir'])) {
			ftp_close($conn);
			echo json_encode(['success' => false, 'message' => "FTP directory not found: {$cfg['dummy_dir']} — check dummy server path"]);
			return;
		}

		$mvc_ok = @ftp_put($conn, 'mvc.zip', $mvc_zip_path, FTP_BINARY);

		// Also upload bootstrap_copy.php (for Rocket) and full_deploy.php (for Full Deploy)
		$bootstrap_path    = 'C:/xampp/htdocs/ourschoolerp/bootstrap_copy.php';
		$full_deploy_path  = 'C:/xampp/htdocs/ourschoolerp/full_deploy.php';
		$bootstrap_ok      = file_exists($bootstrap_path)   ? @ftp_put($conn, 'bootstrap_copy.php', $bootstrap_path,   FTP_ASCII) : false;
		$full_deploy_ok    = file_exists($full_deploy_path)  ? @ftp_put($conn, 'full_deploy.php',    $full_deploy_path,  FTP_ASCII) : false;

		ftp_close($conn);

		if ($mvc_ok) {
			$size_mb = round(filesize($mvc_zip_path) / 1024 / 1024, 2);
			$extras  = [];
			if ($bootstrap_ok)   $extras[] = 'bootstrap_copy.php';
			if ($full_deploy_ok) $extras[]  = 'full_deploy.php';
			$extra_msg = !empty($extras) ? ' + ' . implode(' + ', $extras) . ' uploaded.' : '';
			echo json_encode(['success' => true, 'message' => "mvc.zip ({$size_mb} MB) uploaded to {$cfg['dummy_dir']}/.{$extra_msg} Now click Rocket on each subdomain."]);
		} else {
			echo json_encode(['success' => false, 'message' => "FTP put failed — could not write mvc.zip to {$cfg['dummy_dir']}/"]);
		}
	}

	// ── Auto Create Subdomain — fetch DB details from /main1 and insert record ──
	public function auto_create_subdomain() {
		header('Content-Type: application/json');

		$url    = trim($this->input->post('url'));
		$server = strtolower(trim($this->input->post('server')));

		if (!$url || !$server) {
			echo json_encode(['success' => false, 'message' => 'URL and server are required']);
			return;
		}

		// Normalize URL — ensure https:// prefix and trailing slash
		if (!preg_match('/^https?:\/\//i', $url)) {
			$url = 'https://' . $url;
		}
		$fetch_url = rtrim($url, '/') . '/main1';

		// Extract subdomain name from URL
		// e.g. https://gowthamcumbum.ourcollegeerp.com → gowthamcumbum
		$host     = parse_url($fetch_url, PHP_URL_HOST);
		$parts    = explode('.', $host);
		$sub_name = $parts[0];

		if (!$sub_name) {
			echo json_encode(['success' => false, 'message' => "Could not extract subdomain name from URL: $url"]);
			return;
		}

		// db_host per server (remote IP — used by Python to connect)
		$db_hosts = [
			'hostgator'  => '119.18.54.141',
			'godaddy'    => '118.139.183.79',
			'myschools'  => '119.18.54.166',
			'schoolhour' => '162.241.123.136',
			'collegehour'=> '103.76.231.69',
		];
		$db_host = isset($db_hosts[$server]) ? $db_hosts[$server] : 'localhost';

		// Fetch /main1 page via POST with password (single request authenticates + returns DB details)
		$ch = curl_init($fetch_url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => 'm1_password=ganishkha',
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
			CURLOPT_FOLLOWLOCATION => true,
		]);
		$body    = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_err  = curl_error($ch);
		curl_close($ch);

		if ($body === false || $curl_err) {
			echo json_encode(['success' => false, 'message' => "Could not connect to $fetch_url — $curl_err — check the URL is correct and the subdomain is live"]);
			return;
		}
		if ($http_code >= 400) {
			echo json_encode(['success' => false, 'message' => "HTTP $http_code from $fetch_url — subdomain may not be live"]);
			return;
		}

		// Parse response lines: DATABSE (typo in source), USERNAME, PASSWORD
		// /main1 uses <br/> instead of real newlines — convert to \n first so .+ stops at the right place
		$plain = str_ireplace(['<br/>', '<br />', '<br>'], "\n", $body);
		$plain = strip_tags($plain);

		$db_name = '';
		$db_user = '';
		$db_pass = '';

		if (preg_match('/DATABSE[:\s]+([^\n]+)/i', $plain, $m))  $db_name = trim($m[1]);
		if (!$db_name && preg_match('/DATABASE[:\s]+([^\n]+)/i', $plain, $m)) $db_name = trim($m[1]);
		if (preg_match('/USERNAME[:\s]+([^\n]+)/i', $plain, $m)) $db_user = trim($m[1]);
		if (preg_match('/PASSWORD[:\s]+([^\n]+)/i', $plain, $m)) $db_pass = trim($m[1]);

		if (!$db_name || !$db_user) {
			echo json_encode(['success' => false, 'message' => "Could not parse DB details from $fetch_url — make sure the URL is live and /main1 returns DATABSE/USERNAME/PASSWORD lines"]);
			return;
		}

		// Check for duplicate
		$existing = $this->subdomains_m->get_single_subdomain(['subdomain' => $sub_name, 'server' => $server]);
		if ($existing) {
			echo json_encode(['success' => false, 'message' => "Subdomain '$sub_name' already exists for server '$server' (id: {$existing->id})"]);
			return;
		}

		// Insert record
		$data = [
			'server'      => $server,
			'subdomain'   => $sub_name,
			'main_domain' => null,
			'db_host'     => $db_host,
			'db_name'     => $db_name,
			'db_user'     => $db_user,
			'db_pass'     => $db_pass,
			'site_name'   => ucfirst($sub_name),
			'logo_url'    => '',
			'theme_color' => '#ffffff',
			'status'      => 'active',
		];

		$this->subdomains_m->insert_subdomain($data);

		echo json_encode([
			'success' => true,
			'message' => "Subdomain '$sub_name' created successfully!",
			'data'    => [
				'subdomain' => $sub_name,
				'db_host'   => $db_host,
				'db_name'   => $db_name,
				'db_user'   => $db_user,
			],
		]);
	}

	public function run_schema_updates() {
		header('Content-Type: application/json');
		$server      = strtolower(trim($this->input->post('server')));
		$ids_raw     = trim($this->input->post('subdomain_ids'));

		if (!$server) {
			echo json_encode(['success' => false, 'message' => 'Server is required']);
			return;
		}

		$selected_ids = [];
		if (!empty($ids_raw)) {
			$selected_ids = array_values(array_filter(array_map('intval', explode(',', $ids_raw))));
		}
		if (empty($selected_ids)) {
			echo json_encode(['success' => false, 'message' => 'No subdomains selected. Please check at least one row.']);
			return;
		}

		$domain_map = [
			'hostgator'   => 'ourschoolerp.com',
			'myschools'   => 'myschoolserp.com',
			'schoolhour'  => 'schoolhour.in',
			'collegehour' => 'collegeerp.in',
			'godaddy'     => 'ourcollegeerp.com',
		];
		$base_domain = isset($domain_map[$server]) ? $domain_map[$server] : '';
		if (!$base_domain) {
			echo json_encode(['success' => false, 'message' => "Unknown server '$server'"]);
			return;
		}

		$this->db->where('server', $server);
		$this->db->where('status', 'active');
		$this->db->where_in('id', $selected_ids);
		$rows = $this->db->get('subdomain_settings')->result();

		if (empty($rows)) {
			echo json_encode(['success' => false, 'message' => 'No matching active subdomains found']);
			return;
		}

		$results    = [];
		$schema_key = SCHEMA_UPDATE_KEY;

		foreach ($rows as $sub) {
			$full_domain = $sub->subdomain . '.' . $base_domain;
			$url         = 'https://' . $full_domain . '/schema_runner/run?key=' . urlencode($schema_key);

			$ch = curl_init($url);
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT        => 120,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
			]);
			$body = curl_exec($ch);
			$err  = curl_error($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if (!$body || $err) {
				$results[$sub->subdomain] = [
					'status'  => 'error',
					'message' => 'Connection failed: ' . ($err ?: "HTTP $code"),
				];
			} else {
				$data = json_decode($body, true);
				$results[$sub->subdomain] = $data ?: [
					'status'  => 'error',
					'message' => 'Invalid response: ' . substr($body, 0, 150),
				];
			}
		}

		echo json_encode([
			'success'          => true,
			'results'          => $results,
			'total_subdomains' => count($rows),
		]);
	}

	public function ftp_upload_file() {
		header('Content-Type: application/json');
		$server    = strtolower(trim($this->input->post('server')));
		$file_path = trim($this->input->post('file_path'));
		if (!$server || !$file_path) {
			echo json_encode(['success' => false, 'message' => 'Server and file path are required']);
			return;
		}
		$ch = curl_init('http://localhost:8000/ftp-upload-file');
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
			CURLOPT_POSTFIELDS     => json_encode(['server' => $server, 'file_path' => $file_path]),
			CURLOPT_TIMEOUT        => 120,
		]);
		$body = curl_exec($ch); $err = curl_error($ch); curl_close($ch);
		if (!$body || $err) {
			echo json_encode(['success' => false, 'message' => 'Python server error: ' . $err]);
			return;
		}
		$res = json_decode($body, true);
		if (!$res) {
			echo json_encode(['success' => false, 'message' => 'Invalid response from Python server: ' . substr($body, 0, 200)]);
			return;
		}
		// FastAPI HTTPException returns {"detail":"..."} — normalize to {"success":false,"message":"..."}
		if (isset($res['detail']) && !isset($res['message'])) {
			echo json_encode(['success' => false, 'message' => $res['detail']]);
			return;
		}
		echo json_encode($res);
	}

	public function create_cpanel_subdomain() {
		header('Content-Type: application/json');

		$server    = strtolower(trim($this->input->post('server')));
		$subdomain = strtolower(trim($this->input->post('subdomain')));

		if (!$server || !$subdomain) {
			echo json_encode(['success' => false, 'message' => 'Server and subdomain name are required']);
			return;
		}

		$ch = curl_init('http://localhost:8000/create-cpanel-subdomain');
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
			CURLOPT_POSTFIELDS     => json_encode(['server' => $server, 'subdomain' => $subdomain]),
			CURLOPT_TIMEOUT        => 300,
		]);
		$body     = curl_exec($ch);
		$curl_err = curl_error($ch);
		curl_close($ch);

		if (!$body || $curl_err) {
			echo json_encode(['success' => false, 'message' => 'Could not connect to Python server: ' . $curl_err]);
			return;
		}

		$res = json_decode($body, true);
		echo json_encode($res ?: ['success' => false, 'message' => 'Invalid response from Python server']);
	}
}