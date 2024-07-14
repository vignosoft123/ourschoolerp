<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailandsms extends Admin_Controller {
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
	function __construct () {
		parent::__construct();
		$this->load->model('usertype_m');
		$this->load->model('systemadmin_m');
		$this->load->model('teacher_m');
		$this->load->model('student_m');
		$this->load->model('parents_m');
		$this->load->model('user_m');
		$this->load->model('classes_m');
		$this->load->model('section_m');
		$this->load->model("mark_m");
		$this->load->model("grade_m");
		$this->load->model("exam_m");
		$this->load->model('mailandsms_m');
		$this->load->model('mailandsmstemplate_m');
		$this->load->model('mailandsmstemplatetag_m');
		$this->load->model('studentgroup_m');
		$this->load->model('studentrelation_m');
		$this->load->model('emailsetting_m');
		$this->load->model('subject_m');
		$this->load->library("email");
		$this->load->library("clickatell");
		$this->load->library("twilio");
		$this->load->library("bulk");
		$this->load->library("msg91");
		$this->load->model("setting_m");
		$this->load->library("inilabs",$this->data);
		
		$language = $this->session->userdata('lang');
		$this->lang->load('mailandsms', $language);
		
		$this->load->model("smssettings_m");
		$voice_settings = $this->smssettings_m->get_voice();
		$this->bearer = $voice_settings['field_values'];
	}
	
	protected function rules_mail() {
		$rules = array(
			array(
				'field' => 'email_usertypeID',
				'label' => $this->lang->line("mailandsms_usertype"),
				'rules' => 'trim|required|xss_clean|max_length[15]|callback_check_email_usertypeID'
			),
			array(
				'field' => 'email_schoolyear',
				'label' => $this->lang->line("mailandsms_schoolyear"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'email_class',
				'label' => $this->lang->line("mailandsms_class"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'email_users',
				'label' => $this->lang->line("mailandsms_users"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'email_template',
				'label' => $this->lang->line("mailandsms_template"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'email_subject',
				'label' => $this->lang->line("mailandsms_subject"),
				'rules' => 'trim|required|xss_clean|max_length[255]'
			),
			array(
				'field' => 'email_message',
				'label' => $this->lang->line("mailandsms_message"),
				'rules' => 'trim|required|xss_clean|max_length[20000]'
			),
		);
		return $rules;
	}

	protected function rules_whatsapp() {
		$rules = array(
			array(
				'field' => 'whatsapp_usertypeID',
				'label' => $this->lang->line("mailandwhatsapp_usertype"),
				'rules' => 'trim|required|xss_clean|max_length[15]|callback_check_whatsapp_usertypeID'
			),
			array(
				'field' => 'whatsapp_schoolyear',
				'label' => $this->lang->line("mailandwhatsapp_schoolyear"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'whatsapp_class',
				'label' => $this->lang->line("mailandwhatsapp_select_class"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'whatsapp_users',
				'label' => $this->lang->line("mailandwhatsapp_users"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'whatsapp_template',
				'label' => $this->lang->line("mailandwhatsapp_template"),
				'rules' => 'trim|xss_clean'
			),
		 
			array(
				'field' => 'whatsapp_message',
				'label' => $this->lang->line("mailandwhatsapp_message"),
				'rules' => 'trim|required|xss_clean|max_length[20000]'
			),
		);
		return $rules;
	}

	protected function rules_sms() {
		$rules = array(
			array(
				'field' => 'sms_usertypeID',
				'label' => $this->lang->line("mailandsms_usertype"),
				'rules' => 'trim|required|xss_clean|max_length[15]|callback_check_sms_usertypeID'
			),
			array(
				'field' => 'sms_schoolyear',
				'label' => $this->lang->line("mailandsms_schoolyear"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'sms_class',
				'label' => $this->lang->line("mailandsms_select_class"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'sms_users',
				'label' => $this->lang->line("mailandsms_users"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'sms_template',
				'label' => $this->lang->line("mailandsms_template"),
				'rules' => 'trim|xss_clean'
			),
			array(
				'field' => 'sms_getway',
				'label' => $this->lang->line("mailandsms_getway"),
				'rules' => 'trim|required|xss_clean|max_length[15]|callback_check_getway'
			),
			array(
				'field' => 'sms_message',
				'label' => $this->lang->line("mailandsms_message"),
				'rules' => 'trim|required|xss_clean|max_length[20000]'
			),
		);
		return $rules;
	}

	protected function rules_otheremail() {
		$rules = array(
			array(
				'field' => 'otheremail_name',
				'label' => $this->lang->line("mailandsms_name"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'otheremail_email',
				'label' => $this->lang->line("mailandsms_email"),
				'rules' => 'trim|required|xss_clean|valid_email'
			),
			array(
				'field' => 'otheremail_subject',
				'label' => $this->lang->line("mailandsms_subject"),
				'rules' => 'trim|required|xss_clean|max_length[255]'
			),
			array(
				'field' => 'otheremail_message',
				'label' => $this->lang->line("mailandsms_message"),
				'rules' => 'trim|required|xss_clean|max_length[20000]'
			)
		);
		return $rules;
	}

	protected function rules_othersms() {
		$rules = array(
			array(
				'field' => 'othersms_name',
				'label' => $this->lang->line("mailandsms_name"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'othersms_phone',
				'label' => $this->lang->line("mailandsms_phone"),
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'sms_getway',
				'label' => $this->lang->line("mailandsms_getway"),
				'rules' => 'trim|required|xss_clean|callback_unique_data|max_length[15]|callback_check_getway'
			),
			array(
				'field' => 'othersms_message',
				'label' => $this->lang->line("mailandsms_message"),
				'rules' => 'trim|required|xss_clean|max_length[20000]'
			),
		);
		return $rules;
	}
	
	protected function rules_voice() {
		$rules = array(
			array(
				'field' => 'from_call',
				'label' => "From Call",
				'rules' => 'trim|required|xss_clean'
			),
			array(
				'field' => 'voice_file',
				'label' => "Voice File",
				'rules' => 'trim|required|xss_clean'
			)
		);
		return $rules;
	}

	public function index() {
		$this->data['mailandsmss'] = $this->mailandsms_m->get_mailandsms_with_usertypeID();
		// echo "<pre>";print_r($this->data['mailandsmss']);die;
		$this->data["subview"] = "mailandsms/index";
		$this->load->view('_layout_main', $this->data);
	}

	public function errors() {
		$this->db->order_by('id','desc');
		$this->data['errors'] = $this->db->get('sms_error_logs')->result(); 
		$this->data["subview"] = "mailandsms/errors";
		$this->load->view('_layout_main', $this->data);
	}

	public function add() {
		// ECHO "<PRE>";print_r($_POST);DIE;
		

		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css',
				'assets/editor/jquery-te-1.4.0.css'
			),
			'js' => array(
				'assets/select2/select2.js',
				'assets/editor/jquery-te-1.4.0.min.js'
			)
		);
		$this->data['usertypes'] = $this->usertype_m->get_usertype();
		$this->data['schoolyears'] = $this->schoolyear_m->get_schoolyear();
		$this->data['allClasses'] = $this->classes_m->general_get_classes();
        $this->data['sections'] = [];
        $classesID = $this->input->post("classesID");

        if($classesID > 0) {
            $this->data['sections'] = $this->section_m->get_order_by_section(array("classesID" => $classesID));
        } else {
            $this->data['sections'] = [];
        }


        /* Start For Email */
		$email_usertypeID = $this->input->post("email_usertypeID");
		if($email_usertypeID && $email_usertypeID != 'select') {
			$this->data['email_usertypeID'] = $email_usertypeID;
		} else {
			$this->data['email_usertypeID'] = 'select';
		}
		/* End For Email */

		/* Start For SMS */
		$sms_usertypeID = $this->input->post("sms_usertypeID");
		if($sms_usertypeID && $sms_usertypeID != 'select') {
			$this->data['sms_usertypeID'] = $sms_usertypeID;
		} else {
			$this->data['sms_usertypeID'] = 'select';
		}
		/* End For SMS */
		
		/* Start for Voice */
        $bearer = $this->bearer;//"gjK_1igpeGdsHGJwvN1u_c7-QZ7RFyGKFOClGgYMmdVxi7yrkysOK-0nRUFb9-cE";
		$curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://voice.vignosoft.com/api_v2/voice-call/list_caller_id',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$bearer
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $this->data['caller_list'] = json_decode($response)->data;
        
        $curl = curl_init();
        curl_setopt_array($curl, array( CURLOPT_URL => "https://voice.vignosoft.com/api_v2/voice-call/voice_file",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer ".$bearer
        ),
        ));
        $response2 = curl_exec($curl);
        curl_close($curl);
        $this->data['voice_list'] = json_decode($response2)->data;
        /* End For Voice */
		if($_POST) {
		// echo "<pre>";print_r($_POST);die;

			$this->data['submittype'] = $this->input->post('type');
			if($this->input->post('type') == "email") {
				$rules = $this->rules_mail();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data['emailUserID'] = $this->input->post('email_users');
					$this->data['emailTemplateID'] = $this->input->post('email_template');

					$this->data['allStudents'] = $this->studentrelation_m->general_get_order_by_student(array('srschoolyearID' => $this->input->post('email_schoolyear'), 'srclassesID' => $this->input->post('email_class')), TRUE);

					$this->data['smsUserID'] = 0;
					$this->data['smsTemplateID'] = 0;

					$this->data["email"] = 1;
					$this->data["sms"] = 0;
					$this->data["otheremail"] = 0;
					$this->data["othersms"] = 0;
					$this->data["whatsapp"] = 0;

					$this->data["subview"] = "mailandsms/add";
					$this->load->view('_layout_main', $this->data);
				} else {
					$usertypeID = $this->input->post('email_usertypeID');
					$schoolyearID = $this->input->post('email_schoolyear');

					if($usertypeID == 1) { /* FOR ADMIN */
						$systemadminID = $this->input->post('email_users');
						if($systemadminID == 'select') {
							$message = $this->input->post('email_message');
							$multisystemadmins = $this->systemadmin_m->get_systemadmin();
							if(customCompute($multisystemadmins)) {
								$countusers = '';
								foreach ($multisystemadmins as $key => $multisystemadmin) {
									$this->userConfigEmail($message, $multisystemadmin, $usertypeID, $schoolyearID);
									$countusers .= $multisystemadmin->name .' ,';
								}
								$array = array(
									'usertypeID' => $usertypeID,
									'users' => $countusers,
									'type' => ucfirst($this->input->post('type')),
									'message' => $this->input->post('email_message'),
									'year' => date('Y'),
									'senderusertypeID' => $this->session->userdata('usertypeID'),
									'senderID' => $this->session->userdata('loginuserID')
								);
								$this->mailandsms_m->insert_mailandsms($array);
								redirect(base_url('mailandsms/index'));
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						} else {
							$message = $this->input->post('email_message');
							$singlesystemadmin = $this->systemadmin_m->get_systemadmin($systemadminID);
							if(customCompute($singlesystemadmin)) {
								$this->userConfigEmail($message, $singlesystemadmin, $usertypeID);
								$array = array(
									'usertypeID' => $usertypeID,
									'users' => $singlesystemadmin->name,
									'type' => ucfirst($this->input->post('type')),
									'message' => $this->input->post('email_message'),
									'year' => date('Y'),
									'senderusertypeID' => $this->session->userdata('usertypeID'),
									'senderID' => $this->session->userdata('loginuserID')
								);
								$this->mailandsms_m->insert_mailandsms($array);
								redirect(base_url('mailandsms/index'));
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					} elseif($usertypeID == 2) { /* FOR TEACHER */
						$teacherID = $this->input->post('email_users');
						if($teacherID == 'select') {
							$message = $this->input->post('email_message');
							$multiteachers = $this->teacher_m->general_get_teacher();
							if(customCompute($multiteachers)) {
								$countusers = '';
								foreach ($multiteachers as $key => $multiteacher) {
									$this->userConfigEmail($message, $multiteacher, $usertypeID);
									$countusers .= $multiteacher->name .' ,';
								}
								$array = array(
									'usertypeID' => $usertypeID,
									'users' => $countusers,
									'type' => ucfirst($this->input->post('type')),
									'message' => $this->input->post('email_message'),
									'year' => date('Y'),
									'senderusertypeID' => $this->session->userdata('usertypeID'),
									'senderID' => $this->session->userdata('loginuserID')
								);
								$this->mailandsms_m->insert_mailandsms($array);
								redirect(base_url('mailandsms/index'));
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						} else {
							$message = $this->input->post('email_message');
							$singleteacher = $this->teacher_m->general_get_teacher($teacherID);
							if(customCompute($singleteacher)) {
								$this->userConfigEmail($message, $singleteacher, $usertypeID);
								$array = array(
									'usertypeID' => $usertypeID,
									'users' => $singleteacher->name,
									'type' => ucfirst($this->input->post('type')),
									'message' => $this->input->post('email_message'),
									'year' => date('Y'),
									'senderusertypeID' => $this->session->userdata('usertypeID'),
									'senderID' => $this->session->userdata('loginuserID')
								);
								$this->mailandsms_m->insert_mailandsms($array);
								redirect(base_url('mailandsms/index'));

							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					} elseif($usertypeID == 3) { /* FOR STUDENT */
						$studentID = $this->input->post('email_users');
						if($studentID == 'select') {
							$class = $this->input->post('email_class');
							if($class == 'select') {
								/* Multi School Year */
								$schoolyear = $this->input->post('email_schoolyear');
								if($schoolyear == 'select') {
									$message = $this->input->post('email_message');
									$multiSchoolYearStudents = $this->studentrelation_m->general_get_student(TRUE);
									if(customCompute($multiSchoolYearStudents)) {
										$countusers = '';
										foreach ($multiSchoolYearStudents as $key => $multiSchoolYearStudent) {
											$this->userConfigEmail($message, $multiSchoolYearStudent, $usertypeID, $multiSchoolYearStudent->srschoolyearID);
											$countusers .= $multiSchoolYearStudent->srname .' ,';
										}
										$array = array(
											'usertypeID' => $usertypeID,
											'users' => $countusers,
											'type' => ucfirst($this->input->post('type')),
											'message' => $this->input->post('email_message'),
											'year' => date('Y'),
											'senderusertypeID' => $this->session->userdata('usertypeID'),
											'senderID' => $this->session->userdata('loginuserID')
										);
										$this->mailandsms_m->insert_mailandsms($array);
										redirect(base_url('mailandsms/index'));
									} else {
										$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
										redirect(base_url('mailandsms/add'));
									}
								} else {
									/* Single school Year Student */
									$message = $this->input->post('email_message');
									$singleSchoolYear = $this->input->post('email_schoolyear');
									$singleSchoolYearStudents = $this->studentrelation_m->general_get_order_by_student(array('srschoolyearID' => $singleSchoolYear), TRUE);
									if(customCompute($singleSchoolYearStudents)) {
										$countusers = '';
										foreach ($singleSchoolYearStudents as $key => $singleSchoolYearStudent) {
											$this->userConfigEmail($message, $singleSchoolYearStudent, $usertypeID, $schoolyearID);
											$countusers .= $singleSchoolYearStudent->srname .' ,';
										}
										$array = array(
											'usertypeID' => $usertypeID,
											'users' => $countusers,
											'type' => ucfirst($this->input->post('type')),
											'message' => $this->input->post('email_message'),
											'year' => date('Y'),
											'senderusertypeID' => $this->session->userdata('usertypeID'),
											'senderID' => $this->session->userdata('loginuserID')
										);
										$this->mailandsms_m->insert_mailandsms($array);
										redirect(base_url('mailandsms/index'));
									} else {
										$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
										redirect(base_url('mailandsms/add'));
									}
								}
							} else {
								/* Single Class Student */
								$message = $this->input->post('email_message');
								$singleClass = $this->input->post('email_class');
								$singleSection = $this->input->post('email_section');
								if((int)$singleSection){
                                    $singleClassStudents = $this->studentrelation_m->general_get_order_by_student(array('srclassesID' => $singleClass,'srsectionID' => $singleSection, 'srschoolyearID' => $schoolyearID), TRUE);

                                }else {
                                    $singleClassStudents = $this->studentrelation_m->general_get_order_by_student(array('srclassesID' => $singleClass, 'srschoolyearID' => $schoolyearID), TRUE);
                                }

								if(customCompute($singleClassStudents)) {
									$countusers = '';
									foreach ($singleClassStudents as $key => $singleClassStudent) {
										$this->userConfigEmail($message, $singleClassStudent, $usertypeID, $schoolyearID);
										$countusers .= $singleClassStudent->srname .' ,';
									}
									$array = array(
										'usertypeID' => $usertypeID,
										'users' => $countusers,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('email_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
									redirect(base_url('mailandsms/add'));
								}
							}
						} else {
							/* Single Student */
							$message = $this->input->post('email_message');
							$singlestudent = $this->studentrelation_m->general_get_single_student(array('srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID), TRUE);
							if(customCompute($singlestudent)) {
								$this->userConfigEmail($message, $singlestudent, $usertypeID, $schoolyearID);
								$array = array(
									'usertypeID' => $usertypeID,
									'users' => $singlestudent->srname,
									'type' => ucfirst($this->input->post('type')),
									'message' => $this->input->post('email_message'),
									'year' => date('Y'),
									'senderusertypeID' => $this->session->userdata('usertypeID'),
									'senderID' => $this->session->userdata('loginuserID')
								);

								$this->mailandsms_m->insert_mailandsms($array);
								redirect(base_url('mailandsms/index'));
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					} elseif($usertypeID == 4) { /* FOR PARENTS */
						$parentsID = $this->input->post('email_users');
						if($parentsID == 'select') {
							$message = $this->input->post('email_message');
							$multiparents = $this->parents_m->get_parents();
							if(customCompute($multiparents)) {
								$countusers = '';
								foreach ($multiparents as $key => $multiparent) {
									$this->userConfigEmail($message, $multiparent, $usertypeID);
									$countusers .= $multiparent->name .' ,';
								}
								$array = array(
									'usertypeID' => $usertypeID,
									'users' => $countusers,
									'type' => ucfirst($this->input->post('type')),
									'message' => $this->input->post('email_message'),
									'year' => date('Y'),
									'senderusertypeID' => $this->session->userdata('usertypeID'),
									'senderID' => $this->session->userdata('loginuserID')
								);
								$this->mailandsms_m->insert_mailandsms($array);
								redirect(base_url('mailandsms/index'));
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						} else {
							$message = $this->input->post('email_message');
							$singleparent = $this->parents_m->get_parents($parentsID);
							if(customCompute($singleparent)) {
								$this->userConfigEmail($message, $singleparent, $usertypeID);
								$array = array(
									'usertypeID' => $usertypeID,
									'users' => $singleparent->name,
									'type' => ucfirst($this->input->post('type')),
									'message' => $this->input->post('email_message'),
									'year' => date('Y'),
									'senderusertypeID' => $this->session->userdata('usertypeID'),
									'senderID' => $this->session->userdata('loginuserID')
								);
								$this->mailandsms_m->insert_mailandsms($array);
								redirect(base_url('mailandsms/index'));
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					} else { /* FOR ALL USERS */
						$userID = $this->input->post('email_users');
						if($userID == 'select') {
							$message = $this->input->post('email_message');
							$multiusers = $this->user_m->get_order_by_user(array('usertypeID' => $usertypeID));
							if(customCompute($multiusers)) {
								$countusers = '';
								foreach ($multiusers as $key => $multiuser) {
									$this->userConfigEmail($message, $multiuser, $usertypeID);
									$countusers .= $multiuser->name .' ,';
								}
								$array = array(
									'usertypeID' => $usertypeID,
									'users' => $countusers,
									'type' => ucfirst($this->input->post('type')),
									'message' => $this->input->post('email_message'),
									'year' => date('Y'),
									'senderusertypeID' => $this->session->userdata('usertypeID'),
									'senderID' => $this->session->userdata('loginuserID')
								);
								$this->mailandsms_m->insert_mailandsms($array);
								redirect(base_url('mailandsms/index'));
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						} else {
							$message = $this->input->post('email_message');
							$singleuser = $this->user_m->get_user($userID);
							if(customCompute($singleuser)) {
								$this->userConfigEmail($message, $singleuser, $usertypeID);
								$array = array(
									'usertypeID' => $usertypeID,
									'users' => $singleuser->name,
									'type' => ucfirst($this->input->post('type')),
									'message' => $this->input->post('email_message'),
									'year' => date('Y'),
									'senderusertypeID' => $this->session->userdata('usertypeID'),
									'senderID' => $this->session->userdata('loginuserID')
								);
								$this->mailandsms_m->insert_mailandsms($array);
								redirect(base_url('mailandsms/index'));
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					}
				}
			}  elseif($this->input->post('type') == "whatsapp") {
				// echo "<pre>";print_r($_POST);die;
				 
					$usertypeID = $this->input->post('whatsapp_usertypeID');
					$schoolyearID = $this->input->post('whatsapp_schoolyear');

					//if($usertypeID == 1) { /* FOR ADMIN */
						$systemadminID = $this->input->post('sms_users');
						// if($systemadminID == 'select') {
							$countusers = '';
							$retval = 1;
							$retmess = '';
							if(!empty($_POST['whatsapp_users'])){
								$u_ids = implode(",",$_POST['whatsapp_users']);
								$array = array('u_ids' => $u_ids,'usertypeId' => $usertypeID);
								$multisystemadmins = $this->systemadmin_m->get_systemadmin_in($array);
							}
							$message = $this->input->post('whatsapp_message');
							
							// echo "<pre>";PRINT_R($multisystemadmins);die;
							if(customCompute($multisystemadmins)) {
								foreach ($multisystemadmins as $key => $multisystemadmin) {
									$countusers .= $multisystemadmin->name .' ,';
									$whatsapp_numbers[]= $multisystemadmin->phone;
								}
							}
								
								$whatsapp_numbers[] = $this->input->post('others');
								 $numbers = implode(",",$whatsapp_numbers);
								 
								 $countusers .=$numbers;
								//  $numbers = '91'.$numbers;
								 //echo $numbers;die;
								
								//send whatsapp message - srinu
								$media_path = $this->input->post('dynamic_file1_path');
								$params = array('whatsapp_message' => $message,'whatsapp_numbers' =>$numbers,'media_path' => $media_path);
								$status = $this->send_whatsapp_msg($params);
								
								if($retval == 1) {
									$array = array(
										'usertypeID' => $usertypeID,
										'users' => $countusers,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('whatsapp_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}
							// } else {
							// 	$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
							// 	redirect(base_url('mailandsms/add'));
							// }
						 
					//} 
				//}
			} elseif($this->input->post('type') == "sms") {
				
				$rules = $this->rules_sms();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data['smsUserID'] = $this->input->post('sms_users');
					$this->data['smsTemplateID'] = $this->input->post('sms_template');

					$this->data['allStudents'] = $this->studentrelation_m->get_order_by_student(array('srschoolyearID' => $this->input->post('sms_schoolyear'), 'srclassesID' => $this->input->post('sms_class')));

					$this->data['emailUserID'] = 0;
					$this->data['emailTemplateID'] = 0;

					$this->data["email"] = 0;
					$this->data["sms"] = 1;
					$this->data["otheremail"] = 0;
					$this->data["othersms"] = 0;

					$this->data["subview"] = "mailandsms/add";
					$this->load->view('_layout_main', $this->data);
				} else {
					$getway = $this->input->post('sms_getway');
					$usertypeID = $this->input->post('sms_usertypeID');
					$schoolyearID = $this->input->post('sms_schoolyear');

					if($usertypeID == 1) { /* FOR ADMIN */
						$systemadminID = $this->input->post('sms_users');
						if($systemadminID == 'select') {
							$countusers = '';
							$retval = 1;
							$retmess = '';

							$message = $this->input->post('sms_message');
							$multisystemadmins = $this->systemadmin_m->get_systemadmin();
							if(customCompute($multisystemadmins)) {

								foreach ($multisystemadmins as $key => $multisystemadmin) {
									$status = $this->userConfigSMS($message, $multisystemadmin, $usertypeID, $getway);
									$countusers .= $multisystemadmin->name .' ,';

									if($status['check'] == FALSE) {
										$retval = 0;
										$retmess = $status['message'];
										break;
									}

								}
								if($retval == 1) {
									$array = array(
										'campid' => $status['campid'],
										'usertypeID' => $usertypeID,
										'users' => $countusers,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('sms_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						} else {
							$retval = 1;
							$retmess = '';
							$message = $this->input->post('sms_message');
							$singlesystemadmin = $this->systemadmin_m->get_systemadmin($systemadminID);
							if(customCompute($singlesystemadmin)) {
								$status = $this->userConfigSMS($message, $singlesystemadmin, $usertypeID, $getway);
								if($status['check'] == FALSE) {
									$retval = 0;
									$retmess = $status['message'];
								}

								if($retval == 1) {
									$array = array(
										'campid' => $status['campid'],
										'usertypeID' => $usertypeID,
										'users' => $singlesystemadmin->name,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('sms_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					} elseif($usertypeID == 2) { /* FOR TEACHER */
						$teacherID = $this->input->post('sms_users');
						if($teacherID == 'select') {
							$message = $this->input->post('sms_message');
							$multiteachers = $this->teacher_m->general_get_teacher();
							if(customCompute($multiteachers)) {
								$countusers = '';
								$retval = 1;
								$retmess = '';
								foreach ($multiteachers as $key => $multiteacher) {
									$status = $this->userConfigSMS($message, $multiteacher, $usertypeID, $getway);
									$countusers .= $multiteacher->name .' ,';

									if($status['check'] == FALSE) {
										$retval = 0;
										$retmess = $status['message'];
										break;
									}

								}
								if($retval == 1) {
									$array = array(
										'campid' => $status['campid'],
										'usertypeID' => $usertypeID,
										'users' => $countusers,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('sms_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						} else {
							$retval = 1;
							$retmess = '';
							$message = $this->input->post('sms_message');
							$singleteacher = $this->teacher_m->general_get_teacher($teacherID);
							if(customCompute($singleteacher)) {
								$status = $this->userConfigSMS($message, $singleteacher, $usertypeID, $getway);
								if($status['check'] == FALSE) {
									$retval = 0;
									$retmess = $status['message'];
								}

								if($retval == 1) {
									$array = array(
										'campid' => $status['campid'],
										'usertypeID' => $usertypeID,
										'users' => $singleteacher->name,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('sms_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					} elseif($usertypeID == 3) { /* FOR STUDENT */

						$studentID = $this->input->post('sms_users');
						if($studentID == 'select') {
							$class = $this->input->post('sms_class');
							if($class == 'select') {
								/* Multi School Year */
								$countusers = '';
								$retval = 1;
								$retmess = '';

								$schoolyear = $this->input->post('sms_schoolyear');
								if($schoolyear == 'select') {
									$message = $this->input->post('sms_message');
									$auto_manual = $this->input->post('auto_manual');
									$multiSchoolYearStudents = $this->studentrelation_m->general_get_student(TRUE);
									if(customCompute($multiSchoolYearStudents)) {
										foreach ($multiSchoolYearStudents as $key => $multiSchoolYearStudent) {
											$status = $this->userConfigSMS($message, $multiSchoolYearStudent, $usertypeID, $getway, $multiSchoolYearStudent->srschoolyearID,$auto_manual);
											$countusers .= $multiSchoolYearStudent->srname .' ,';
											if($status['check'] == FALSE) {
												$retval = 0;
												$retmess = $status['message'];
												break;
											}
										}

										if($retval == 1) {
											$array = array(
												'campid' => $status['campid'],
												'usertypeID' => $usertypeID,
												'users' => $countusers,
												'type' => ucfirst($this->input->post('type')),
												'message' => $this->input->post('sms_message'),
												'year' => date('Y'),
												'senderusertypeID' => $this->session->userdata('usertypeID'),
												'senderID' => $this->session->userdata('loginuserID')
											);
											$this->mailandsms_m->insert_mailandsms($array);
											redirect(base_url('mailandsms/index'));
										} else {
											$this->session->set_flashdata('error', $retmess);
											redirect(base_url('mailandsms/add'));
										}
									} else {
										$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
										redirect(base_url('mailandsms/add'));
									}
								} else {
									/* Single school Year Student */
									$countusers = '';
									$retval = 1;
									$retmess = '';
									$message = $this->input->post('sms_message');
									$singleSchoolYear = $this->input->post('sms_schoolyear');
									$singleSchoolYearStudents = $this->studentrelation_m->general_get_order_by_student(array('srschoolyearID' => $singleSchoolYear), TRUE);
									if(customCompute($singleSchoolYearStudents)) {
										foreach ($singleSchoolYearStudents as $key => $singleSchoolYearStudent) {
											$status = $this->userConfigSMS($message, $singleSchoolYearStudent, $usertypeID, $getway, $schoolyearID);
											$countusers .= $singleSchoolYearStudent->srname .' ,';
											if($status['check'] == FALSE) {
												$retval = 0;
												$retmess = $status['message'];
												break;
											}
										}
										if($retval == 1) {
											$array = array(
												'campid' => $status['campid'],
												'usertypeID' => $usertypeID,
												'users' => $countusers,
												'type' => ucfirst($this->input->post('type')),
												'message' => $this->input->post('sms_message'),
												'year' => date('Y'),
												'senderusertypeID' => $this->session->userdata('usertypeID'),
												'senderID' => $this->session->userdata('loginuserID')
											);
											$this->mailandsms_m->insert_mailandsms($array);
											redirect(base_url('mailandsms/index'));
										} else {
											$this->session->set_flashdata('error', $retmess);
											redirect(base_url("mailandsms/add"));
										}
									} else {
										$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
										redirect(base_url('mailandsms/add'));
									}
								}
							} else {
								/* Single Class Student */
								$countusers = '';
								$retval = 1;
								$retmess = '';

								$message = $this->input->post('sms_message');
								$singleClass = $this->input->post('sms_class');
                                $singleSection = $this->input->post('sms_section');
                                if((int)$singleSection){
                                    $singleClassStudents = $this->studentrelation_m->general_get_order_by_student(array('srclassesID' => $singleClass,'srsectionID' => $singleSection, 'srschoolyearID' => $schoolyearID), TRUE);

                                }else {
                                    $singleClassStudents = $this->studentrelation_m->general_get_order_by_student(array('srclassesID' => $singleClass, 'srschoolyearID' => $schoolyearID), TRUE);
                                }
								if(customCompute($singleClassStudents)) {
									$countusers = '';
									foreach ($singleClassStudents as $key => $singleClassStudent) {
										$status = $this->userConfigSMS($message, $singleClassStudent, $usertypeID, $getway, $schoolyearID);
										$countusers .= $singleClassStudent->srname .' ,';
										if($status['check'] == FALSE) {
											$retval = 0;
											$retmess = $status['message'];
											break;
										}
									}

									if($retval == 1) {
										$array = array(
											'campid' => $status['campid'],
											'usertypeID' => $usertypeID,
											'users' => $countusers,
											'type' => ucfirst($this->input->post('type')),
											'message' => $this->input->post('sms_message'),
											'year' => date('Y'),
											'senderusertypeID' => $this->session->userdata('usertypeID'),
											'senderID' => $this->session->userdata('loginuserID')
										);
										$this->mailandsms_m->insert_mailandsms($array);
										redirect(base_url('mailandsms/index'));
									} else {
										$this->session->set_flashdata('error', $retmess);
										redirect(base_url("mailandsms/add"));
									}
								} else {
									$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
									redirect(base_url('mailandsms/add'));
								}
							}
						} else {
							/* Single Student */
							$retval = 1;
							$retmess = '';

							$message = $this->input->post('sms_message');
							$singlestudent = $this->studentrelation_m->general_get_single_student(array('srstudentID' => $studentID, 'srschoolyearID' => $schoolyearID), TRUE);
							if(customCompute($singlestudent)) {
								$status = $this->userConfigSMS($message, $singlestudent, $usertypeID, $getway, $schoolyearID);
								if($status['check'] == FALSE) {
									$retval = 0;
									$retmess = $status['message'];
								}
								if($retval == 1) {
									$array = array(
										'campid' => $status['campid'],
										'usertypeID' => $usertypeID,
										'users' =>  $singlestudent->srname,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('sms_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					} elseif($usertypeID == 4) { /* FOR PARENTS */
						$parentsID = $this->input->post('sms_users');
						if($parentsID == 'select') {
							$countusers = '';
							$retval = 1;
							$retmess = '';

							$message = $this->input->post('sms_message');
							$multiparents = $this->parents_m->get_parents();
							if(customCompute($multiparents)) {

								foreach ($multiparents as $key => $multiparent) {
									$status = $this->userConfigSMS($message, $multiparent, $usertypeID, $getway);
									$countusers .= $multiparent->name .' ,';

									if($status['check'] == FALSE) {
										$retval = 0;
										$retmess = $status['message'];
										break;
									}
								}

								if($retval == 1) {
									$array = array(
										'campid' => $status['campid'],
										'usertypeID' => $usertypeID,
										'users' => $countusers,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('sms_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						} else {
							$retval = 1;
							$retmess = '';

							$message = $this->input->post('sms_message');
							$singleparent = $this->parents_m->get_parents($parentsID);
							if(customCompute($singleparent)) {
								$status = $this->userConfigSMS($message, $singleparent, $usertypeID, $getway);
								if($status['check'] == FALSE) {
									$retval = 0;
									$retmess = $status['message'];

								}

								if($retval == 1) {
									$array = array(
										'campid' => $status['campid'],
										'usertypeID' => $usertypeID,
										'users' => $singleparent->name,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('sms_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}

							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					} else { /* FOR ALL USERS */
						$userID = $this->input->post('sms_users');
						if($userID == 'select') {
							$countusers = '';
							$retval = 1;
							$retmess = '';
							$message = $this->input->post('sms_message');
							$multiusers = $this->user_m->get_order_by_user(array('usertypeID' => $usertypeID));
							if(customCompute($multiusers)) {
								foreach ($multiusers as $key => $multiuser) {
									$status = $this->userConfigSMS($message, $multiuser, $usertypeID, $getway);
									$countusers .= $multiuser->name .' ,';

									if($status['check'] == FALSE) {
										$retval = 0;
										$retmess = $status['message'];
										break;
									}
								}

								if($retval == 1) {
									$array = array(
										'campid' => $status['campid'],
										'usertypeID' => $usertypeID,
										'users' => $countusers,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('sms_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						} else {
							$retval = 1;
							$retmess = '';
							$message = $this->input->post('sms_message');
							$singleuser = $this->user_m->get_user($userID);
							if(customCompute($singleuser)) {
								$status = $this->userConfigSMS($message, $singleuser, $usertypeID, $getway);
								if($status['check'] == FALSE) {
									$retval = 0;
									$retmess = $status['message'];
								}

								if($retval == 1) {
									$array = array(
										'campid' => $status['campid'],
										'usertypeID' => $usertypeID,
										'users' => $singleuser->name,
										'type' => ucfirst($this->input->post('type')),
										'message' => $this->input->post('sms_message'),
										'year' => date('Y'),
										'senderusertypeID' => $this->session->userdata('usertypeID'),
										'senderID' => $this->session->userdata('loginuserID')
									);
									$this->mailandsms_m->insert_mailandsms($array);
									redirect(base_url('mailandsms/index'));
								} else {
									$this->session->set_flashdata('error', $retmess);
									redirect(base_url("mailandsms/add"));
								}
							} else {
								$this->session->set_flashdata('error', $this->lang->line('mailandsms_notfound_error'));
								redirect(base_url('mailandsms/add'));
							}
						}
					}
				}
			} elseif($this->input->post('type') == "otheremail") {
				$rules = $this->rules_otheremail();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					
					$this->data['emailUserID'] = 0;
					$this->data['emailTemplateID'] = 0;
					$this->data['allStudents'] = [];
					$this->data['smsUserID'] = 0;
					$this->data['smsTemplateID'] = 0;

					$this->data["email"] = 0;
					$this->data["sms"] = 0;
					$this->data["otheremail"] = 1;
					$this->data["othersms"] = 0;

					$this->data["subview"] = "mailandsms/add";
					$this->load->view('_layout_main', $this->data);
				} else {
					$email   = $this->input->post('otheremail_email');
					$subject = $this->input->post('otheremail_subject');
					$message = $this->input->post('otheremail_message');

					$result  = $this->inilabs->sendMailSystem($email, $subject, $message);
					if($result) {
						$array = array(
							'usertypeID' => '0',
							'users' => $this->input->post('otheremail_name'),
							'type' => ucfirst($this->lang->line('mailandsms_otheremail')),
							'message' => $this->input->post('otheremail_message'),
							'year' => date('Y'),
							'senderusertypeID' => $this->session->userdata('usertypeID'),
							'senderID' => $this->session->userdata('loginuserID')
						);
						$this->mailandsms_m->insert_mailandsms($array);
						$this->session->set_flashdata('success', $this->lang->line('mail_success'));
						redirect(base_url('mailandsms/index'));
					} else {
						$this->session->set_flashdata('error', $this->lang->line('mail_error'));
						redirect(base_url("mailandsms/add"));
					}
				}
			} elseif($this->input->post('type') == "othersms") {
				$rules = $this->rules_othersms();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {

					$this->data['emailUserID'] = 0;
					$this->data['emailTemplateID'] = 0;
					$this->data['allStudents'] = [];
					$this->data['smsUserID'] = 0;
					$this->data['smsTemplateID'] = 0;

					$this->data["email"] = 0;
					$this->data["sms"] = 0;
					$this->data["otheremail"] = 0;
					$this->data["othersms"] = 1;

					$this->data["subview"] = "mailandsms/add";
					$this->load->view('_layout_main', $this->data);
				} else {
					$to = $this->input->post('othersms_phone');
					$getway = $this->input->post('sms_getway');
					$message = $this->input->post('othersms_message');

					$result = $this->allgetway_send_message($getway, $to, $message);
					if($result['check']) {
						$array = array(
							'usertypeID' => '0',
							'users' => $this->input->post('othersms_name'),
							'type' => ucfirst($this->lang->line('mailandsms_othersms')),
							'message' => $this->input->post('othersms_message'),
							'year' => date('Y'),
							'senderusertypeID' => $this->session->userdata('usertypeID'),
							'senderID' => $this->session->userdata('loginuserID')
						);
						$this->mailandsms_m->insert_mailandsms($array);
						redirect(base_url('mailandsms/index'));
					} else {
						$retmess = isset($result['message']) ? $result['message'] : $this->lang->line('mailandsms_error');
						$this->session->set_flashdata('error', $retmess);
						redirect(base_url("mailandsms/add"));
					}
				}
			} elseif($this->input->post('type') == "voice") {
				$rules = $this->rules_voice();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$this->data['emailUserID'] = 0;
					$this->data['emailTemplateID'] = 0;
					$this->data['allStudents'] = [];
					$this->data['smsUserID'] = 0;
					$this->data['smsTemplateID'] = 0;

					$this->data["email"] = 0;
					$this->data["sms"] = 0;
					$this->data["otheremail"] = 0;
					$this->data["othersms"] = 1;

					$this->data["subview"] = "mailandsms/add";
					$this->load->view('_layout_main', $this->data);
				} else {
					$from_call = $this->input->post('from_call');
                    $voice_file = $this->input->post('voice_file');
                    $usertypeID = $this->input->post('voice_usertypeID');
                    $user_array = array();
                    if($usertypeID==2)
                    {
                        $multiteachers = $this->teacher_m->general_get_teacher();
                        foreach ($multiteachers as $key => $multiteacher)
                        {
                            if(strlen(trim($multiteacher->phone))==10)
                            {
                                $user_array[] = array("user_id"=>'',"email"=>'',"mobileno"=>trim($multiteacher->phone));
                            }
                        }
                    }
                    elseif($usertypeID==3)
                    {
                        $schoolyear = $this->input->post('voice_schoolyear');
						$class = $this->input->post('voice_class');
						
						if($class == 'select') {
						    $singleSchoolYearStudents = $this->studentrelation_m->general_get_order_by_student(array('srschoolyearID' => $schoolyear), TRUE);
							if(customCompute($singleSchoolYearStudents))
							{
								foreach ($singleSchoolYearStudents as $key => $singleSchoolYearStudent)
								{
								    if(strlen(trim($singleSchoolYearStudent->phone))==10)
                                    {
                                        $user_array[] = array("user_id"=>'',"email"=>'',"mobileno"=>trim($singleSchoolYearStudent->phone));
                                    }
								}
							}
						}
						else
						{
                            $singleClassStudents = $this->studentrelation_m->general_get_order_by_student(array('srclassesID' => $class,'srschoolyearID' => $schoolyear), TRUE);
                            if(customCompute($singleClassStudents))
                            {
								foreach ($singleClassStudents as $key => $singleClassStudent)
								{
								    if(strlen(trim($singleClassStudent->phone))==10)
                                    {
                                        $user_array[] = array("user_id"=>'',"email"=>'',"mobileno"=>trim($singleClassStudent->phone));
                                    }
								}
                            }
						}
                    }
                    $mobile_nos = explode(",",str_replace("\n",",",str_replace(" ","",$this->input->post('to_numbers'))));
                    foreach($mobile_nos as $key => $custom_mobile)
                    {
                        if(strlen(trim($custom_mobile))==10)
                        {
                            $user_array[] = array("user_id"=>'',"email"=>'',"mobileno"=>trim($custom_mobile));
                        }
                    }
                    $sent = 0;
                    if (!empty($user_array)) {
                        foreach ($user_array as $user_mail_key => $user_mail_value) {
                            if ($user_mail_value['mobileno'] != "") {
                                $response = $this->send_voice_call($from_call,$voice_file,$user_mail_value['mobileno']);
                                $sent++;
                            }
                        }
                    }
					if($sent) {
						$array = array(
							'usertypeID' => '0',
							'users' => "",
							'type' => ucfirst("Voice"),
							'message' => $voice_file,
							'year' => date('Y'),
							'senderusertypeID' => $this->session->userdata('usertypeID'),
							'senderID' => $this->session->userdata('loginuserID')
						);
						$this->mailandsms_m->insert_mailandsms($array);
						redirect(base_url('mailandsms/index'));
					} else {
						$retmess = isset($result['message']) ? $result['message'] : $this->lang->line('mailandsms_error');
						$this->session->set_flashdata('error', $retmess);
						redirect(base_url("mailandsms/add"));
					}
				}
			} else {
				redirect('mainandsms/add');
			}
		} else {
			$this->data['emailUserID'] = 0;
			$this->data['emailTemplateID'] = 0;

			$this->data['smsUserID'] = 0;
			$this->data['smsTemplateID'] = 0;

			$this->data["email"] = 0;
			$this->data["sms"] = 1;
			$this->data["otheremail"] = 0;
			$this->data["othersms"] = 0;
			$this->data['submittype'] = 'none';

			$this->data['allStudents'] = array();
			$this->data["subview"] = "mailandsms/add";
			$this->load->view('_layout_main', $this->data);
		}
	}

	private function userConfigEmail($message, $user, $usertypeID, $schoolyearID = 1) {
		if($user && $usertypeID) {
			$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => $usertypeID));

			if($usertypeID == 2) {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 2));
			} elseif($usertypeID == 3) {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 3));
			} elseif($usertypeID == 4) {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 4));
			} else {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 1));
			}

			$message = $this->tagConvertor($userTags, $user, $message, 'email', $schoolyearID);

			if($user->email) {
				$subject = $this->input->post('email_subject');
				$email = $user->email;

				$emailsetting = $this->emailsetting_m->get_emailsetting();
				$this->email->set_mailtype("html");
				if(customCompute($emailsetting)) {
					if($emailsetting->email_engine == 'smtp') {
						$config = array(
						    'protocol'  => 'smtp',
						    'smtp_host' => $emailsetting->smtp_server,
						    'smtp_port' => $emailsetting->smtp_port,
						    'smtp_user' => $emailsetting->smtp_username,
						    'smtp_pass' => $emailsetting->smtp_password,
						    'mailtype'  => 'html',
						    'charset'   => 'utf-8'
						);
						$this->email->initialize($config);
						$this->email->set_newline("\r\n");
					}

					$this->email->to($email);
					$this->email->from($this->data['siteinfos']->email, $this->data['siteinfos']->sname);
					$this->email->subject($subject);
					$this->email->message($message);
					if($this->email->send()) {
						$this->session->set_flashdata('success', $this->lang->line('mail_success'));
					} else {
						$this->session->set_flashdata('error', $this->lang->line('mail_error'));
					}
				}
			}
		}
	}

	private function userConfigSMS($message, $user, $usertypeID, $getway, $schoolyearID = 1,$auto_manual="") {
	    $template_id = 0;
	    if(isset($_POST['sms_template']) && $_POST['sms_template']!='')
	    {
	        $template = $this->mailandsmstemplate_m->get_mailandsmstemplate($_POST['sms_template']);
	        $template_id = $template->templ_id;
	    }
		if($user && $usertypeID) {
			$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => $usertypeID));

			if($usertypeID == 2) {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 2));
			} elseif($usertypeID == 3) {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 3));
			} elseif($usertypeID == 4) {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 4));
			} else {
				$userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 1));
			}

			if($auto_manual != 'manual'){
				$message = $this->tagConvertor($userTags, $user, $message, 'SMS', $schoolyearID);
			}
			
			if($user->phone) {
				$send = $this->allgetway_send_message($getway, $user->phone, $message, $template_id);
				return $send;
			} else {
				$send = array('check' => TRUE);
				return $send;
			}
		}
	}

	private function tagConvertor($userTags, $user, $message, $sendType, $schoolyearID) {

		$this->data['setting'] = $this->Setting_m->get_setting();
		$school_name = (isset($this->data['setting']->sname)) ? $this->data['setting']->sname : "";
		$website = (isset($this->data['setting']->website)) ? $this->data['setting']->website : "";
		

		if(customCompute($userTags)) {
			foreach ($userTags as $key => $userTag) {
				if($userTag->tagname == '[name]') {
					if($user->name) {
						$message = str_replace('[name]', $user->name, $message);
					} else {
						$message = str_replace('[name]', ' ', $message);
					}
				} elseif($userTag->tagname == '[designation]') {
					if($user->designation) {
						$message = str_replace('[designation]', $user->designation, $message);
					} else {
						$message = str_replace('[designation]', ' ', $message);
					}
				} elseif($userTag->tagname == '[dob]') {
					if($user->dob) {
						$dob =  date("d M Y", strtotime($user->dob));
						$message = str_replace('[dob]', $dob, $message);
					} else {
						$message = str_replace('[dob]', ' ', $message);
					}
				} elseif($userTag->tagname == '[gender]') {
					if($user->sex) {
						$message = str_replace('[gender]', $user->sex, $message);
					} else {
						$message = str_replace('[gender]', ' ', $message);
					}
				} elseif($userTag->tagname == '[religion]') {
					if($user->religion) {
						$message = str_replace('[religion]', $user->religion, $message);
					} else {
						$message = str_replace('[religion]', ' ', $message);
					}
				} elseif($userTag->tagname == '[email]') {
					if($user->email) {
						$message = str_replace('[email]', $user->email, $message);
					} else {
						$message = str_replace('[email]', ' ', $message);
					}
				} elseif($userTag->tagname == '[phone]') {
					if($user->phone) {
						$message = str_replace('[phone]', $user->phone, $message);
					} else {
						$message = str_replace('[phone]', ' ', $message);
					}
				} elseif($userTag->tagname == '[address]') {
					if($user->address) {
						$message = str_replace('[address]', $user->address, $message);
					} else {
						$message = str_replace('[address]', ' ', $message);
					}
				} elseif($userTag->tagname == '[jod]') {
					if($user->jod) {
						$jod =  date("d M Y", strtotime($user->jod));
						$message = str_replace('[jod]', $jod, $message);
					} else {
						$message = str_replace('[jod]', ' ', $message);
					}
				} elseif($userTag->tagname == '[username]') {
					if($user->username) {
						$message = str_replace('[username]', $user->username, $message);
					} else {
						$message = str_replace('[username]', ' ', $message);
					}
				} elseif($userTag->tagname == "[father's_name]") {
					if($user->father_name) {
						$message = str_replace("[father's_name]", $user->father_name, $message);
					} else {
						$message = str_replace("[father's_name]", ' ', $message);
					}
				} elseif($userTag->tagname == "[mother's_name]") {
					if($user->mother_name) {
						$message = str_replace("[mother's_name]", $user->mother_name, $message);
					} else {
						$message = str_replace("[mother's_name]", ' ', $message);
					}
				} elseif($userTag->tagname == "[father's_profession]") {
					if($user->father_profession) {
						$message = str_replace("[father's_profession]", $user->father_profession, $message);
					} else {
						$message = str_replace("[father's_profession]", ' ', $message);
					}
				} elseif($userTag->tagname == "[mother's_profession]") {
					if($user->mother_profession) {
						$message = str_replace("[mother's_profession]", $user->mother_profession, $message);
					} else {
						$message = str_replace("[mother's_profession]", ' ', $message);
					}
				} elseif($userTag->tagname == '[class]') {
					$classes = $this->classes_m->general_get_classes($user->srclassesID);
					if(customCompute($classes)) {
						$message = str_replace('[class]', $classes->classes, $message);
					} else {
						$message = str_replace('[class]', ' ', $message);
					}
				} elseif($userTag->tagname == '[roll]') {
					if($user->srroll) {
						$message = str_replace("[roll]", $user->srroll, $message);
					} else {
						$message = str_replace("[roll]", ' ', $message);
					}
				} elseif($userTag->tagname == '[country]') {
					if($user->country) {
						if(isset($this->data['allcountry'][$user->country])) {
							$message = str_replace("[country]", $this->data['allcountry'][$user->country], $message);
						} else {
							$message = str_replace("[country]", ' ', $message);
						}
					} else {
						$message = str_replace("[country]", ' ', $message);
					}
				} elseif($userTag->tagname == '[state]') {
					if($user->state) {
						$message = str_replace("[state]", $user->state, $message);
					} else {
						$message = str_replace("[state]", ' ', $message);
					}
				} elseif($userTag->tagname == '[register_no]') {
					if($user->srregisterNO) {
						$message = str_replace("[register_no]", $user->srregisterNO, $message);
					} else {
						$message = str_replace("[register_no]", ' ', $message);
					}
				} elseif($userTag->tagname == '[section]') {
					if($user->srsectionID) {
						$section = $this->section_m->general_get_section($user->srsectionID);
						if(customCompute($section)) {
							$message = str_replace('[section]', $section->section, $message);
						} else {
							$message = str_replace('[section]',' ', $message);
						}
					} else {
						$message = str_replace("[section]", ' ', $message);
					}
				} elseif($userTag->tagname == '[blood_group]') {
					if($user->bloodgroup && $user->bloodgroup != '0') {
						$message = str_replace("[blood_group]", $user->bloodgroup, $message);
					} else {
						$message = str_replace("[blood_group]", ' ', $message);
					}
				} elseif($userTag->tagname == '[group]') {
					if($user->srstudentgroupID && $user->srstudentgroupID != 0) {
						$group = $this->studentgroup_m->get_studentgroup($user->srstudentgroupID);
						if(customCompute($group)) {
							$message = str_replace('[group]', $group->group, $message);
						} else {
							$message = str_replace('[group]',' ', $message);
						}
					} else {
						$message = str_replace('[group]',' ', $message);
					}
				} elseif($userTag->tagname == '[optional_subject]') {
					if($user->sroptionalsubjectID && $user->sroptionalsubjectID != 0) {
						$subject = $this->subject_m->general_get_single_subject(array('subjectID' => $user->sroptionalsubjectID));
						if(customCompute($subject)) {
							$message = str_replace('[optional_subject]', $subject->subject, $message);
						} else {
							$message = str_replace('[optional_subject]',' ', $message);
						}
					} else {
						$message = str_replace('[optional_subject]',' ', $message);
					}
				} elseif($userTag->tagname == '[extra_curricular_activities]') {
					if($user->extracurricularactivities) {
						$message = str_replace("[extra_curricular_activities]", $user->extracurricularactivities, $message);
					} else {
						$message = str_replace("[extra_curricular_activities]", ' ', $message);
					}
				} elseif($userTag->tagname == '[remarks]') {
					if($user->remarks) {
						$message = str_replace("[remarks]", $user->remarks, $message);
					} else {
						$message = str_replace("[remarks]", ' ', $message);
					}
				} elseif($userTag->tagname == '[date]') {
					$message = str_replace("[date]", (date("d M Y")), $message);
				} elseif($userTag->tagname == '[result_table]') {
					if($sendType == 'email') {
						if($user->usertypeID == 3) {
							$this->load->library('mark', ['studentID'=> $user->srstudentID, 'classesID'=> $user->srclassesID, 'schoolyearID'=> $schoolyearID, 'data'=> $this->data['siteinfos']]);
							$result = $this->mark->mail();
						} else {
							$result = '';
						}
						$message = str_replace("[result_table]", $result, $message);
					} elseif($sendType == 'SMS') {
						if($user->usertypeID == 3) {
							$this->load->library('mark', ['studentID'=> $user->srstudentID, 'classesID'=> $user->srclassesID, 'schoolyearID'=> $schoolyearID, 'data'=> $this->data['siteinfos']]);
							$result = $this->mark->sms();
						} else {
							$result = '';
						}
						$message = str_replace("[result_table]", $result, $message);
					}
				}
				elseif($userTag->tagname == '{{student_name}}') {
					if($user->name) {
						$message = str_replace("{{student_name}}", $user->name, $message);
					} else {
						$message = str_replace("{{student_name}}", ' ', $message);
					}
				}
				elseif($userTag->tagname == '{{roll_no}}') {
					if($user->roll) {
						$message = str_replace("{{roll_no}}", $user->srroll, $message);
					} else {
						$message = str_replace("{{roll_no}}", ' ', $message);
					}
				}
				elseif($userTag->tagname == '{{absent_date}}') {
					$message = str_replace("{{absent_date}}", date("Y-m-d"), $message);
				}
				elseif($userTag->tagname == '{{school_name}}') {
					$message = str_replace("{{school_name}}", " ", $message);
				}
				elseif($userTag->tagname == '{{url}}') {
						$message = str_replace("{{url}}", $website, $message);
				}
				elseif($userTag->tagname == '{{username}}') {
					if($user->username) {
						$message = str_replace("{{username}}", $user->username, $message);
					} else {
						$message = str_replace("{{username}}", ' ', $message);
					}
				}
				elseif($userTag->tagname == '{{password}}') {
					if($user->username) {
						$message = str_replace("{{password}}", "123456", $message);
					} else {
						$message = str_replace("{{password}}", ' ', $message);
					}
				}
			}
		}
		return $message;
	}

	public function alltemplate() {
		if($this->input->post('usertypeID') == 'select') {
			echo '<option value="select">'.$this->lang->line('mailandsms_select_template').'</option>';
		} else {
			$usertypeID = $this->input->post('usertypeID');
			$type = $this->input->post('type');

			$templates = $this->mailandsmstemplate_m->get_order_by_mailandsmstemplate(array('usertypeID' => $usertypeID, 'type' => $type));
			echo '<option value="select">'.$this->lang->line('mailandsms_select_template').'</option>';
			if(customCompute($templates)) {
				foreach ($templates as $key => $template) {
					echo '<option value="'.$template->mailandsmstemplateID.'">'. $template->name  .'</option>';
				}
			}
		}
	}

	public function allusers() {
		if($this->input->post('usertypeID') == 'select') {
			echo '<option value="select">'.$this->lang->line('mailandsms_all_users').'</option>';
		} else {
			$usertypeID = $this->input->post('usertypeID');
			$userID = $this->input->post('userID');

			if($usertypeID == 1) {
				$systemadmins = $this->systemadmin_m->get_systemadmin();
				if(customCompute($systemadmins)) {
					echo "<option value='select'>".$this->lang->line('mailandsms_all_users')."</option>";
					foreach ($systemadmins as $key => $systemadmin) {
						echo "<option value='".$systemadmin->systemadminID."'>".$systemadmin->name.'</option>';
					}
				} else {
					echo '<option value="select">'.$this->lang->line('mailandsms_all_users').'</option>';
				}
			} elseif($usertypeID == 2) {
				$teachers = $this->teacher_m->general_get_teacher();
				if(customCompute($teachers)) {
					echo "<option value='select'>".$this->lang->line('mailandsms_all_users')."</option>";
					foreach ($teachers as $key => $teacher) {
						echo "<option value='".$teacher->teacherID."'>".$teacher->name.'</option>';
					}
				} else {
					echo '<option value="select">'.$this->lang->line('mailandsms_all_users').'</option>';
				}
			} elseif($usertypeID == 3) {
				$classes = $this->classes_m->general_get_classes();
				if(customCompute($classes)) {
					echo "<option value='select'>".$this->lang->line('mailandsms_all_class')."</option>";
					foreach ($classes as $key => $classm) {
						echo "<option value='".$classm->classesID."'>".$classm->classes.'</option>';
					}
				} else {
					echo '<option value="select">'.$this->lang->line('mailandsms_all_class').'</option>';
				}
			} elseif($usertypeID == 4) {
				$parents = $this->parents_m->get_parents();
				if(customCompute($parents)) {
					echo "<option value='select'>".$this->lang->line('mailandsms_all_users')."</option>";
					foreach ($parents as $key => $parent) {
						echo "<option value='".$parent->parentsID."'>".$parent->name.'</option>';
					}
				} else {
					echo '<option value="select">'.$this->lang->line('mailandsms_all_users').'</option>';
				}
			} else {
				$users = $this->user_m->get_order_by_user(array('usertypeID' => $usertypeID));
				if(customCompute($users)) {
					echo "<option value='select'>".$this->lang->line('mailandsms_all_users')."</option>";
					foreach ($users as $key => $user) {
						echo "<option value='".$user->userID."'>".$user->name.'</option>';
					}
				} else {
					echo '<option value="select">'.$this->lang->line('mailandsms_all_users').'</option>';
				}
			}
		}
	}

	public function allstudent() {
		$schoolyearID = $this->input->post('schoolyear');
		$classesID = $this->input->post('classes');
		$sectionID = $this->input->post('section');
		$hostel_transport = $this->input->post('hostel_transport');
		if((int)$schoolyearID && (int)$classesID) {
		    if ((int)$sectionID){
                $students = $this->studentrelation_m->get_order_by_student(array('srschoolyearID' => $schoolyearID,'srsectionID' => $sectionID, 'srclassesID' => $classesID));
            }else if ((int)$hostel_transport){

				if($hostel_transport == 1){	//hostel
					$students = $this->studentrelation_m->get_order_by_student(array('srschoolyearID' => $schoolyearID,'srsectionID' => $sectionID, 'srclassesID' => $classesID,'hostel' => 1));
				}else if($hostel_transport == 2){	//transport
					$students = $this->studentrelation_m->get_order_by_student(array('srschoolyearID' => $schoolyearID,'srsectionID' => $sectionID, 'srclassesID' => $classesID,'transport' => 1));
				}

              
            }else {
                $students = $this->studentrelation_m->get_order_by_student(array('srschoolyearID' => $schoolyearID, 'srclassesID' => $classesID));
            }
			if(customCompute($students)) {
				echo '<option value="select">'.$this->lang->line('mailandsms_all_users').'</option>';
				foreach ($students as $key => $student) {
					echo '<option value="'.$student->srstudentID.'">'.$student->srname.'</option>';
				}
			} else {
				echo '<option value="select">'.$this->lang->line('mailandsms_all_users').'</option>';
			}
		} else {
			echo '<option value="select">'.$this->lang->line('mailandsms_all_users').'</option>';
		}
	}

    public function allsection() {
        $classesID = $this->input->post('classes');
        if((int)$classesID) {
            $allsection = $this->section_m->general_get_order_by_section(array('classesID' => $classesID));
            echo "<option value='select'>", $this->lang->line("mailandsms_all_section"),"</option>";
            foreach ($allsection as $value) {
                echo "<option value=\"$value->sectionID\">",$value->section,"</option>";
            }
        }
    }

	public function check_email_usertypeID() {
		if($this->input->post('email_usertypeID') == 'select') {
			$this->form_validation->set_message("check_email_usertypeID", "The %s field is required");
	     	return FALSE;
		} else {
			return TRUE;
		}
	}

	public function alltemplatedesign() {
		if((int)$this->input->post('templateID')) {
			$templateID = $this->input->post('templateID');
			$templates = $this->mailandsmstemplate_m->get_mailandsmstemplate($templateID);
			if(customCompute($templates)) {
				 echo $templates->template;

			}
		} else {
			echo '';
		}
	}

	public function alltemplatedesign_sms() {
		if((int)$this->input->post('templateID')) {
			$templateID = $this->input->post('templateID');
			$templates = $this->mailandsmstemplate_m->get_mailandsmstemplate($templateID);
			if(customCompute($templates)) {

				if($this->input->post('auto_manual') == 'auto'){
					echo $templates->template;
				}else{
					$a = $this->replace_all_text_between($templates->template,"{","}","<span contenteditable='true' class='common_input'></span>");
					echo $a .= '<button class="btn btn-primary f-r" type="button" id="generate">Preview</button>';
				} 

				 
				

			}
		} else {
			echo '';
		}
	}

	function replace_all_text_between($str, $start, $end, $replacement) {

		$replacement = $start . $replacement . $end;
	
		$start = preg_quote($start, '/');
		$end = preg_quote($end, '/');
		$regex = "/({$start})(.*?)({$end})/";
	
		$a = preg_replace($regex,$replacement,$str);
		$a =  str_replace("{","",$a);
		return $a =  str_replace("}","",$a);
	}

	public function check_sms_usertypeID() {
		if($this->input->post('sms_usertypeID') == 'select') {
			$this->form_validation->set_message("check_sms_usertypeID", "The %s field is required");
	     	return FALSE;
		} else {
			return TRUE;
		}
	}

	public function check_getway() {
		if($this->input->post('sms_getway') == 'select') {
			$this->form_validation->set_message("check_getway", "The %s field is required");
	     	return FALSE;
		} else {

			$getway = $this->input->post('sms_getway');
			$arrgetway = array('clickatell', 'twilio', 'bulk', 'msg91');
			if(in_array($getway, $arrgetway)) {
				if($getway == "clickatell") {
					if($this->clickatell->ping() == TRUE) {
						return TRUE;
					} else {
						$this->form_validation->set_message("check_getway", 'Setup Your clickatell Account');
	     				return FALSE;
					}
					return TRUE;
				} elseif($getway == 'twilio') {
					$get = $this->twilio->get_twilio();
					$ApiVersion = $get['version'];
					$AccountSid = $get['accountSID'];
					$check = $this->twilio->request("/$ApiVersion/Accounts/$AccountSid/Calls");

					if($check->IsError) {
						$this->form_validation->set_message("check_getway", $check->ErrorMessage);
	     				return FALSE;
					}
					return TRUE;
				} elseif($getway == 'bulk') {
					if($this->bulk->ping() == TRUE) {
						return TRUE;
					} else {
						$this->form_validation->set_message("check_getway", 'Invalid Username or Password');
	     				return FALSE;
					}
				} elseif($getway == 'msg91') {
                    return true;
				}
			} else {
				$this->form_validation->set_message("check_getway", "The %s field is required");
	     		return FALSE;
			}
		}
	}

	private function allgetway_send_message($getway, $to, $message, $template_id=0) {
		$result = [];
		if($getway == "clickatell") {
			if($to) {
				$this->clickatell->send_message($to, $message);
				$result['check'] = TRUE;
				return $result;
			}
		} elseif($getway == 'twilio') {
			$get = $this->twilio->get_twilio();
			$from = $get['number'];
			if($to) {
				$response = $this->twilio->sms($from, $to, $message);
				if($response->IsError) {
					$result['check'] = FALSE;
					$result['message'] = $response->ErrorMessage;
					return $result;
				} else {
					$result['check'] = TRUE;
					return $result;
				}

			}
		} elseif($getway == 'bulk') {
			if($to) {
				if($this->bulk->send($to, $message) == TRUE)  {
					$result['check'] = TRUE;
					return $result;
				} else {
					$result['check'] = FALSE;
					$result['message'] = "Check your bulk account";
					return $result;
				}
			}
		} elseif($getway == 'msg91') {
			if($to) {
				
				$obj = $this->msg91->send($to, $message, $template_id); 
				$campid = explode(":",$obj); 
				 $campid = rtrim($campid[1],'}"');
				 $campid = trim($campid,"'");
				
				if ($campid) {
				//if($this->msg91->send($to, $message, $template_id) == TRUE)  {
					$result['check'] = TRUE;
					$result['campid'] = $campid;
					return $result;
				} else {
					$result['check'] = FALSE;
					$result['message'] = "Check your msg91 account";
					return $result;
				}
			}
		}
	}

	public function view() {
		$id = htmlentities(escapeString($this->uri->segment(3)));
		$campid = htmlentities(escapeString($this->uri->segment(4)));
		if((int)$id) {

			
			if($campid){
				$sms_status = json_decode($this->msg91->getSmsReport($campid),true);
				$sms_status = explode("-",$sms_status); 
				 $sms_status = rtrim($sms_status[1],'}"');
				 $sms_status = trim($sms_status,"'");
				 $this->data["sms_status"] =$sms_status;
			}

			$this->data['mailandsms'] = $this->mailandsms_m->get_mailandsms($id);
			if($this->data['mailandsms']) {
				$this->data["subview"] = "mailandsms/view";
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

	public function unique_data($data) {
		if($data != "") {
			if($data == "select") {
				$this->form_validation->set_message('unique_data', 'The %s field is required.');
				return FALSE;
			}
		} 
		return TRUE;
	}
	
	private function send_voice_call($caller_id,$voice_file,$mobile_no)
    {
        $bearer = $this->bearer;//"gjK_1igpeGdsHGJwvN1u_c7-QZ7RFyGKFOClGgYMmdVxi7yrkysOK-0nRUFb9-cE";
        $curl = curl_init();
        curl_setopt_array($curl, array( CURLOPT_URL => "https://voice.vignosoft.com/api_v2/voice-call/campaign",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "caller_id=".$caller_id."&voice_file=".$voice_file."&mobile_no=".$mobile_no."&re_schedule_status=NoAnswer%2CFailed%2CBusy%2CRING_TIMEOUT%2CRejected%2CCompleted&voice_interval=5",
        CURLOPT_HTTPHEADER => array(
        "authorization: Bearer ".$bearer,
        "cache-control: no-cache",
        "content-type: application/x-www-form-urlencoded"
        ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
		// start  code for error log saving
		$resp = json_decode($response,true);
		$status = $resp['success'];		
		$url = "https://voice.vignosoft.com/api_v2/voice-call/campaign?caller_id=".$caller_id."&voice_file=".$voice_file."&mobile_no=".$mobile_no."&re_schedule_status=NoAnswer%2CFailed%2CBusy%2CRING_TIMEOUT%2CRejected%2CCompleted&voice_interval=5";

		$data = array(
            'request_url' => $url,
            'api_response' => $response,
            'created_on' => date("Y-m-d H:i:s"),
            'type' => "BULK VOICE CALL",
        ); 
		 //print_r($resp['error']);
        if ($status == false) {  
            $this->db->insert('sms_error_logs',$data);
        }  

		// end code for error log saving
        curl_close($curl);
        return $response;
    }
    
    public function upload_voice()
    {
        $bearer = $this->bearer;//"gjK_1igpeGdsHGJwvN1u_c7-QZ7RFyGKFOClGgYMmdVxi7yrkysOK-0nRUFb9-cE";
        $new_file_path = '';;
        if((!$_FILES['individual_attachment']['error']) && ($_FILES['individual_attachment'] !=''))
		{
			$new_image=rand(1,10000)."_".$_FILES['individual_attachment']['name'];
			move_uploaded_file($_FILES['individual_attachment']['tmp_name'],"uploads/media/".$new_image);
			$new_file_path = base_url()."uploads/media/".$new_image;
		}
		else
		{
		    echo json_encode(array("success"=>false,"error"=>"Files not uploaded"));exit;
		}
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_URL => "https://voice.vignosoft.com/api_v2/voice-call/voice_file_upload",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "voice_title=".date('YmdHis')."&voice_file=".$new_file_path,
            CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$bearer,
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;exit;
    }

	//code for send whatsapp msg -srinu
	public function send_whatsapp_msg($array=""){

		 $field_values = $this->getWhatsappconfig();
			// echo "<pre>";print_r($array);die;


		$whatsapp_link_number = "91".$field_values[1]['field_values'];
		// echo "<pre>";print_r($whatsapp_link_number);die;
		 $numbers = $array["whatsapp_numbers"];
		 $msg = $array["whatsapp_message"];
		$media = $array["media_path"];
		$this->bearer = $field_values[0]["field_values"];
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://voice.vignosoft.com/api_v2/whats-app/send',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => 'https://voice.vignosoft.com/api_v2/whats-app/send?api_key='.$field_values[0]["field_values"].'&type=broadcast&country_code=IN&wa_number='.$whatsapp_link_number.'&mobile_numbers='.$numbers.'&content='.$msg.'&media_url_1='.$media,
		// CURLOPT_POSTFIELDS => 'type=broadcast&country_code=IN&wa_number='.$whatsapp_link_number.'&mobile_numbers='.$numbers.'&content='.$msg.'&media_url_1='.$media,
		CURLOPT_HTTPHEADER => array(
			// 'AuthorizationKey: Bearer ZB8VAHSaD8EAWO1_VhuFyRZ5t0n0kxAHaqzvSw69QXX7Fcz-7-pG27aoxE2BxaVa',
			//  'AuthorizationKey: Bearer "'.$field_values[0]["field_values"].'"',
			"Authorization: Bearer ".$this->bearer,
			'Content-Type: application/x-www-form-urlencoded'
		),
		));

		$response = curl_exec($curl);

		
		$resp = json_decode($response,true);
		$status = $resp['success'];
		// echo "<pre>";print_r($response); die;
        // $url = 'https://voice.vignosoft.com/api_v2/whats-app/send?api_key='.$field_values[0]["field_values"].'&type=broadcast&country_code=IN&wa_number='.$whatsapp_link_number.'&mobile_numbers='.$array["whatsapp_number"].'&content='.$array["whatsapp_msg"].'&media_url_1='.$array["media_path"];
        $url = 'https://voice.vignosoft.com/api_v2/whats-app/send?api_key='.$field_values[0]["field_values"].'&type=broadcast&country_code=IN&wa_number='.$whatsapp_link_number.'&mobile_numbers='.$numbers.'&content='.$msg.'&media_url_1='.$media;
		// echo $url;die;
        $data = array(
            'request_url' => $url,
            'api_response' => $response,
            'created_on' => date("Y-m-d H:i:s"),
            'type' => "WHATSAPP",
        ); 
		// print_r($data);
        if ($status == false) {  
            $this->db->insert('sms_error_logs',$data);
        }  

		// echo 'request=';
		// echo '><br/>';
		// echo 'https://voice.vignosoft.com/api_v2/whats-app/send?api_key='.$field_values.'&type=broadcast&country_code=IN&wa_number=917569996118&mobile_numbers='.$array["whatsapp_numbers"].'&content='.$array["whatsapp_message"].'&media_url_1='.$array["media_path"];
		// print_r($response);die;
		curl_close($curl);
		return $response;
	}

	public function upload_file()
    {
		
		// print_r($_POST);DIE;
		// error_reporting(E_ALL);
		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
	 
        $bearer = $this->bearer;//"gjK_1igpeGdsHGJwvN1u_c7-QZ7RFyGKFOClGgYMmdVxi7yrkysOK-0nRUFb9-cE";
        $new_file_path = '';;
        if((!$_FILES['individual_attachment1']['error']) && ($_FILES['individual_attachment1'] !=''))
		{
			$new_image=rand(1,10000)."_".$_FILES['individual_attachment1']['name'];
			move_uploaded_file($_FILES['individual_attachment1']['tmp_name'],"uploads/media/".$new_image);
			$new_file_path = base_url()."uploads/media/".$new_image;
			echo json_encode(array("success"=>true,"msg"=>"Files uploaded","new_file_path"=>$new_file_path));exit;
		}
		else
		{
		    echo json_encode(array("success"=>false,"error"=>"Files not uploaded"));exit;
		}
	}


}
