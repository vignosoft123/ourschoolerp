<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailandsmstemplate extends Admin_Controller {
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
		$this->load->model('usertype_m');
		$this->load->model("mailandsmstemplate_m");
		$this->load->model("mailandsmstemplatetag_m");
		$language = $this->session->userdata('lang');
		$this->lang->load('mailandsmstemplate', $language);
	}

	public function index() {
		$this->data['mailandsmstemplates'] = $this->mailandsmstemplate_m->get_order_by_mailandsmstemplate_with_usertypeID();
		$this->data["subview"] = "mailandsmstemplate/index";
		$this->load->view('_layout_main', $this->data);
	}

	public function whatsapp_index() {
		$this->data['mailandsmstemplates'] = $this->mailandsmstemplate_m->get_order_by_whatsapptemplate_with_usertypeID();
		$this->data["subview"] = "mailandsmstemplate/whatsapp_index";
		$this->load->view('_layout_main', $this->data);
	}

	public function whatsapp_edit() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if(!(int)$id) { redirect(base_url('mailandsmstemplate/whatsapp_index')); }

		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/select2/select2.js'
			)
		);

		$this->data['whatsapp_template'] = $this->db->where('mailandsmstemplateID', $id)->get('whatapp_templates')->row();
		if(!$this->data['whatsapp_template']) { redirect(base_url('mailandsmstemplate/whatsapp_index')); }

		if($_POST) {
			$array = array(
				'template_name' => $this->input->post('whatsapp_name'),
				'usertypeID'    => $this->input->post('whatsapp_user'),
				'template'      => $this->input->post('whatsapp_temp_name'),
				'templ_id'      => $this->input->post('template_id'),
				'params'        => $this->input->post('params'),
				'short_name'    => $this->input->post('short_name'),
			);
			$this->db->where('mailandsmstemplateID', $id)->update('whatapp_templates', $array);
			$this->session->set_flashdata('success', $this->lang->line('menu_success'));
			redirect(base_url('mailandsmstemplate/whatsapp_index'));
		}

		$usertypes = $this->usertype_m->get_usertype();
		$this->data['usertypes'] = $usertypes;
		$this->all_tag($usertypes);
		$this->data['whatsapp_user'] = $this->data['whatsapp_template']->usertypeID;
		$this->data["subview"] = "mailandsmstemplate/whatsapp_edit";
		$this->load->view('_layout_main', $this->data);
	}

	protected function rules_email() {
		$rules = array(
				array(
					'field' => 'type',
					'label' => '',
					'rules' => 'trim|required|xss_clean|max_length[10]'
				),
				array(
					'field' => 'email_name',
					'label' => $this->lang->line("mailandsmstemplate_name"),
					'rules' => 'trim|required|xss_clean|max_length[128]'
				),
				array(
					'field' => 'email_user',
					'label' => $this->lang->line("mailandsmstemplate_user"),
					'rules' => 'trim|required|xss_clean|max_length[15]|callback_email_user_check'
				),
				array(
					'field' => 'email_template',
					'label' => $this->lang->line("mailandsmstemplate_template"),
					'rules' => 'trim|required|xss_clean|max_length[20000]'
				)
			);
		return $rules;
	}

	protected function rules_sms() {
		$rules = array(
				array(
					'field' => 'type',
					'label' => '',
					'rules' => 'trim|required|xss_clean|max_length[10]'
				),
				array(
					'field' => 'sms_name',
					'label' => $this->lang->line("mailandsmstemplate_name"),
					'rules' => 'trim|required|xss_clean|max_length[128]'
				),
				array(
					'field' => 'sms_user',
					'label' => $this->lang->line("mailandsmstemplate_user"),
					'rules' => 'trim|required|xss_clean|max_length[15]|callback_sms_user_check'
				),
				array(
					'field' => 'sms_template',
					'label' => $this->lang->line("mailandsmstemplate_template"),
					'rules' => 'trim|required|xss_clean|max_length[1500]'
				),
				array(
					'field' => 'template_id',
					'label' => "template_id",
					'rules' => 'trim|required|xss_clean|max_length[100]'
				)
			);
		return $rules;
	}

	protected function rules_voice() {
		$rules = array(
				array(
					'field' => 'type',
					'label' => '',
					'rules' => 'trim|required|xss_clean|max_length[10]'
				),
				array(
					'field' => 'voice_name',
					'label' => $this->lang->line("mailandsmstemplate_name"),
					'rules' => 'trim|required|xss_clean|max_length[128]'
				),
				array(
					'field' => 'voice_user',
					'label' => $this->lang->line("mailandsmstemplate_user"),
					'rules' => 'trim|required|xss_clean|max_length[15]|callback_sms_user_check'
				),
				array(
					'field' => 'voice_template',
					'label' => $this->lang->line("mailandsmstemplate_template"),
					'rules' => 'trim|required|xss_clean|max_length[1500]'
				),
			);
		return $rules;
	}

	public function add() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/editor/jquery-te-1.4.0.css',
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/editor/jquery-te-1.4.0.min.js',
				'assets/select2/select2.js'
			)
		);
		$usertypes = $this->usertype_m->get_usertype();
		$this->data['usertypes'] = $usertypes;
		$this->all_tag($usertypes);

		if($_POST) {
			$type = $this->input->post('type');
			if($type == 'email') {
				$this->data["email"] = 1;
				$this->data["sms"] = 0;
				$this->data["email_user"] = $this->input->post('email_user');
				$this->data["sms_user"] = 'select';
				$this->data["whatsapp_user"] = 'select';
				$rules = $this->rules_email();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data['form_validation'] = validation_errors();
					$this->data["subview"] = "mailandsmstemplate/add";
					$this->load->view('_layout_main', $this->data);
				} else {
					$array = array(
						'name' => $this->input->post('email_name'),
						'usertypeID' => $this->input->post('email_user'),
						'type' => $this->input->post('type'),
						'template' => $this->input->post('email_template'),
					);

					$this->mailandsmstemplate_m->insert_mailandsmstemplate($array);
					$this->session->set_flashdata('success', $this->lang->line('menu_success'));
					redirect(base_url('mailandsmstemplate/index'));
				}
			} elseif($type == "sms") {
				$this->data["email"] = 0;
				$this->data["sms"] = 1;
				$this->data["email_user"] = 'select';
				$this->data["sms_user"] = $this->input->post('sms_user');
				$this->data["whatsapp_user"] = 'select';
				$rules = $this->rules_sms();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data['form_validation'] = validation_errors();
					$this->data["subview"] = "mailandsmstemplate/add";
					$this->load->view('_layout_main', $this->data);
				} else {
					$array = array(
						'name' => $this->input->post('sms_name'),
						'usertypeID' => $this->input->post('sms_user'),
						'type' => $this->input->post('type'),
						'template' => $this->input->post('sms_template'),
						'templ_id' => $this->input->post('template_id'),
					);

					$this->mailandsmstemplate_m->insert_mailandsmstemplate($array);
					$this->session->set_flashdata('success', $this->lang->line('menu_success'));
					redirect(base_url('mailandsmstemplate/index'));
				}
			}elseif($type == "voice") {
				$this->data["email"] = 0;
				$this->data["sms"] = 0;
				$this->data["voice"] = 1;
				$this->data["email_user"] = 'select';
				$this->data["sms_user"] = 'select';
				$this->data["whatsapp_user"] = 'select';
				$rules = $this->rules_voice();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data['form_validation'] = validation_errors();
					$this->data["subview"] = "mailandsmstemplate/add";
					$this->load->view('_layout_main', $this->data);
				} else {
					$array = array(
						'name' => $this->input->post('voice_name'),
						'usertypeID' => $this->input->post('voice_user'),
						'type' => $this->input->post('type'),
						'template' => $this->input->post('voice_template'),
					);

					$this->mailandsmstemplate_m->insert_mailandsmstemplate($array);
					$this->session->set_flashdata('success', $this->lang->line('menu_success'));
					redirect(base_url('mailandsmstemplate/index'));
				}
			} elseif($type == "whatsapp") {
				$this->data["email"] = 0;
				$this->data["sms"] = 0;
				$this->data["whatsapp"] = 1;
				$this->data["email_user"] = 'select';
				$this->data["sms_user"] = 'select';
				$this->data["whatsapp_user"] = $this->input->post('whatsapp_user');
				//$rules = $this->rules_sms();
				// $this->form_validation->set_rules($rules);
				// if ($this->form_validation->run() == FALSE) {
				// 	$this->data['form_validation'] = validation_errors();
				// 	$this->data["subview"] = "mailandsmstemplate/add";
				// 	$this->load->view('_layout_main', $this->data);
				// } else {
					$array = array(
						'template_name' => $this->input->post('whatsapp_name'),
						'usertypeID' => $this->input->post('whatsapp_user'),
						'type' => $this->input->post('type'),
						'template' => $this->input->post('whatsapp_temp_name'),
						'templ_id' => $this->input->post('template_id'),
						'params' => $this->input->post('params'),
						'short_name' => $this->input->post('short_name'),
					);

					// $this->mailandsmstemplate_m->insert_mailandsmstemplate($array);

					$this->db->insert('whatapp_templates',$array);
					$this->session->set_flashdata('success', $this->lang->line('menu_success'));
					redirect(base_url('mailandsmstemplate/index'));
				//}
			}
		} else {
			$this->data["email"] = 1;
			$this->data["sms"] = 0;
			$this->data["email_user"] = 'select';
			$this->data["sms_user"] = 'select';
			$this->data["whatsapp_user"] = 'select';
				$this->data["subview"] = "mailandsmstemplate/add";
			$this->load->view('_layout_main', $this->data);
		}
	}

	function email_user_check() {
		if($this->input->post('email_user') == 'select') {
			$this->form_validation->set_message("email_user_check", "The %s field is required");
	     	return FALSE;
		}
		return TRUE;
	}

	function sms_user_check() {
		if($this->input->post('sms_user') == 'select') {
			$this->form_validation->set_message("sms_user_check", "The %s field is required");
	     	return FALSE;
		}
		return TRUE;
	}

	public function edit() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/editor/jquery-te-1.4.0.css',
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/editor/jquery-te-1.4.0.min.js',
				'assets/select2/select2.js'
			)
		);

		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->data['mailandsmstemplate'] = $this->mailandsmstemplate_m->get_mailandsmstemplate($id);

			// echo "<pre>";print_r($this->data['mailandsmstemplate']);die;
			if($this->data['mailandsmstemplate']) {

				$usertypes = $this->usertype_m->get_usertype();
				$this->data['usertypes'] = $usertypes;
				$this->all_tag($usertypes);

				if($_POST) {
					if($this->data['mailandsmstemplate']->type == 'email') {
						/* For Email */
						$this->data['email'] = 1;
						$this->data['sms'] = 0;
						$this->data["email_user"] = $this->data['mailandsmstemplate']->usertypeID;
						$this->data["sms_user"] = $this->data['mailandsmstemplate']->usertypeID;
						$rules = $this->rules_email();
						unset($rules[0]);
						$this->form_validation->set_rules($rules);
						if ($this->form_validation->run() == FALSE) {
							$this->data["subview"] = "mailandsmstemplate/edit";
							$this->load->view('_layout_main', $this->data);
						} else {
							$array = array(
								'name' => $this->input->post('email_name'),
								'usertypeID' => $this->input->post('email_user'),
								'template' => $this->input->post('email_template'),
							);

							$this->mailandsmstemplate_m->update_mailandsmstemplate($array, $id);
							$this->session->set_flashdata('success', $this->lang->line('menu_success'));
							redirect(base_url('mailandsmstemplate/index'));
						}
					} elseif($this->data['mailandsmstemplate']->type == 'sms') {
						/* For SMS */
						$this->data['email'] = 0;
						$this->data['sms'] = 1;
						$this->data["email_user"] = $this->data['mailandsmstemplate']->usertypeID;
						$this->data["sms_user"] = $this->data['mailandsmstemplate']->usertypeID;
						$rules = $this->rules_sms();
						unset($rules[0]);
						$this->form_validation->set_rules($rules);
						if ($this->form_validation->run() == FALSE) {
							$this->data["subview"] = "mailandsmstemplate/edit";
							$this->load->view('_layout_main', $this->data);
						} else {
							$array = array(
								'name' => $this->input->post('sms_name'),
								'usertypeID' => $this->input->post('sms_user'),
								'template' => $this->input->post('sms_template'),
						        'templ_id' => $this->input->post('template_id'),
							);

							$this->mailandsmstemplate_m->update_mailandsmstemplate($array, $id);
							$this->session->set_flashdata('success', $this->lang->line('menu_success'));
							redirect(base_url('mailandsmstemplate/index'));
						}
					} elseif($this->data['mailandsmstemplate']->type == 'voice') {
						/* For voice sMS */
						$this->data['email'] = 0;
						$this->data['sms'] = 0;
						$this->data['voice'] = 1;
						$this->data["email_user"] = $this->data['mailandsmstemplate']->usertypeID;
						$this->data["sms_user"] = $this->data['mailandsmstemplate']->usertypeID;
						$this->data["voice_user"] = $this->data['mailandsmstemplate']->usertypeID;
						$rules = $this->rules_voice();
						unset($rules[0]);
						$this->form_validation->set_rules($rules);
						if ($this->form_validation->run() == FALSE) {
							$this->data["subview"] = "mailandsmstemplate/edit";
							$this->load->view('_layout_main', $this->data);
						} else {
							 
							$array = array(
								'name' => $this->input->post('voice_name'),
								'usertypeID' => $this->input->post('voice_user'),
								'template' => $this->input->post('voice_template'),
							);

							$this->mailandsmstemplate_m->update_mailandsmstemplate($array, $id);
							$this->session->set_flashdata('success', $this->lang->line('menu_success'));
							redirect(base_url('mailandsmstemplate/index'));
						}
					}
				} else {
					if($this->data['mailandsmstemplate']->type == 'email') {
						$this->data['email'] = 1;
						$this->data['sms'] = 0;
					} elseif($this->data['mailandsmstemplate']->type == 'sms') {
						$this->data['email'] = 0;
						$this->data['sms'] = 1;
					}elseif($this->data['mailandsmstemplate']->type == 'voice') {
						$this->data['email'] = 0;
						$this->data['sms'] = 0;
						$this->data['voice'] = 1;
					}
					$this->data["email_user"] = $this->data['mailandsmstemplate']->usertypeID;
					$this->data["sms_user"] = $this->data['mailandsmstemplate']->usertypeID;
					$this->data["voice_user"] = $this->data['mailandsmstemplate']->usertypeID;
					$this->data["subview"] = "mailandsmstemplate/edit";
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

	public function view() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		if((int)$id) {
			$this->data['mailandsmstemplate'] = $this->mailandsmstemplate_m->get_mailandsmstemplate($id);
			if($this->data['mailandsmstemplate']) {
				$this->data["subview"] = "mailandsmstemplate/view";
				$this->load->view('_layout_main', $this->data);
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
		$whatsapp = htmlentities(escapeString($this->uri->segment(4)));
		if((int)$id) {
			if($whatsapp == 'whatsapp'){
				$this->db->where('mailandsmstemplateID',$id);
				$this->db->delete('whatapp_templates');
				$this->session->set_flashdata('success', $this->lang->line('menu_success'));
				redirect(base_url("mailandsmstemplate/whatsapp_index"));
			}else{
			$this->data['mailandsmstemplate'] = $this->mailandsmstemplate_m->get_mailandsmstemplate($id);
			if($this->data['mailandsmstemplate']) {
				$this->mailandsmstemplate_m->delete_mailandsmstemplate($id);
				$this->session->set_flashdata('success', $this->lang->line('menu_success'));
				redirect(base_url("mailandsmstemplate/index"));
			} else {
				redirect(base_url("mailandsmstemplate/index"));
			}
		}
		} else {
			redirect(base_url("mailandsmstemplate/index"));
		}
	}

 

	public function all_tag($usertypes) {
		if(is_array($usertypes)) {
 			foreach ($usertypes as $key => $usertype) {
				if($usertype->usertypeID == 2) {
					$teachertags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => $usertype->usertypeID));

					$flag = '';
					if(customCompute($teachertags)) {
						foreach ($teachertags as $teachertagkey => $teachertag) {
							$flag .= '<input class="btn bg-black btn-xs email_alltag sms_alltag" type="button" value="'.$teachertag->tagname.'"> ';
						}
					}

					$this->data['teachers'] = $flag;
				} elseif($usertype->usertypeID == 3) {
					$studenttags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => $usertype->usertypeID));
					$flag = '';
					if(customCompute($studenttags)) {
						foreach ($studenttags as $studenttagkey => $studenttag) {
							$flag .= '<input class="btn bg-black btn-xs email_alltag sms_alltag" type="button" value="'.$studenttag->tagname.'"> ';
						}
					}

					$this->data['students'] = $flag;
				} elseif($usertype->usertypeID == 4) {
					$parentstags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => $usertype->usertypeID));

					$flag = '';
					if(customCompute($parentstags)) {
						foreach ($parentstags as $parentstagkey => $parentstag) {
							$flag .= '<input class="btn bg-black btn-xs email_alltag sms_alltag" type="button" value="'.$parentstag->tagname.'"> ';
						}
					}

					$this->data['parents'] = $flag;
				} else {
					$usertags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 1));

					$flag = '';
					if(customCompute($usertags)) {
						foreach ($usertags as $usertagkey => $usertag) {
							$flag .= '<input class="btn bg-black btn-xs email_alltag sms_alltag" type="button" value="'.$usertag->tagname.'"> ';
						}
					}

					$this->data['users'] = $flag;
				}
			}
		}
	}
}
