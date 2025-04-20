<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Whatsapp_m extends MY_Model {


    public function __construct()
    {  
        $this->load->model('smssettings_m');
        $this->load->model('mailandsmstemplatetag_m');
        $msg91_bind = [];
        $get_msg91s = $this->smssettings_m->get_order_by_whatsapp();
        foreach ( $get_msg91s as $key => $get_msg91 ) {
            $msg91_bind[ $get_msg91->field_names ] = $get_msg91->field_values;
        }
        // $this->authKey  = "8c79d04588b945d2083b";//$msg91_bind['msg91_authKey'];
        $this->senderID = $msg91_bind['whatsapp_sender'];
        $this->username = $msg91_bind['whatsapp_user'];
        $this->password = $msg91_bind['whatsapp_password']; 
    }

	  public function get_whatsapp_credits()
        {
             $get_msg91s = $this->smssettings_m->get_order_by_whatsapp(); 
            if(isset($get_msg91s[1]->field_values) && isset($get_msg91s[2]->field_values))
            {
                $user_name = $get_msg91s[1]->field_values;
                $password = $get_msg91s[2]->field_values; 
               $url = "http://bwa.mindwhile.com/api/checkbalance.php?user=$user_name&pass=$password";
               $result = file_get_contents($url); 
                if(!empty($result))
                {
                    return $result;
                }
            }
            return 0;
        }
  
		function url_get_contents ($url) {
            // echo $url;
                        
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url);

            // 3. set cURL to return as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // 4. execute cURL and store the result
            $output = curl_exec($ch);

            if (curl_errno($ch)) {
               echo $error_msg = curl_error($ch);die;
            }

            // 5. close cURL after use
            curl_close($ch);
 
            print_r($output) ;die;
        }

	function sendWhatsapp($to, $message, $template_name = '') {  //send marks &
        // URL encode message and template name
        $msg = $message;
        $message = urlencode($message);
        $template_name = urlencode($template_name);
    
        // Construct API URL
        $url = "http://bwa.mindwhile.com/api/sendmsg.php?user={$this->username}&pass={$this->password}&sender={$this->senderID}&phone={$to}&text={$template_name}&priority=wa&stype=normal&Params={$message}";
    
        // Initialize cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ]);
    
        // Execute request
        $output = curl_exec($ch);
    
        // Handle cURL errors
        if (curl_errno($ch)) {
            log_message('error', 'CURL Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }
    
        curl_close($ch);
    
        // Prepare log data
        $data = [
            'request_url'   => $url,
            'api_response'  => $output,
            'created_on'    => date("Y-m-d H:i:s"),
            'type'          => "whatsapp",
            'message'       => $msg,
            'template_name' => $template_name
        ];
    
        // Log only successful messages
        // if ($output && strpos($output, 'campid') !== false) { 
            $this->db->insert('whatsapp_logs', $data);
        // } else {
        //     log_message('error', 'WhatsApp API Error: ' . $output);
        // }
    
        return $output ?: false;
    }


   
	function whatsapp_config_send($user) {  //paid fee

        $template_sql = "select params,template_name from whatapp_templates where template_name like '%FEE_PAID%' ";
		$whatsapp_params = $this->db->query($template_sql)->row_array();
        $params = $whatsapp_params['params'];
        $template_name = $whatsapp_params['template_name'];

        if(!empty($user->phone) && !empty($whatsapp_params['template_name']) && !empty($whatsapp_params['params'])){
            $userTags = $this->mailandsmstemplatetag_m->get_order_by_mailandsmstemplatetag(array('usertypeID' => 3));
            $message = $this->tagConvertor($userTags, $user, $params, 'SMS');         
             $this->sendWhatsapp($user->phone, $message, $template_name);
             return 1;
        }else{
           return 0;
        }
    }

    private function tagConvertor($userTags, $user, $message)
	{
		if (customCompute($userTags)) {
			foreach ($userTags as $key => $userTag) {
				if ($userTag->tagname == '[name]') {
					if ($user->name) {
						$message = str_replace('[name]', $user->name, $message);
					} else {
						$message = str_replace('[name]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[designation]') {
					if ($user->designation) {
						$message = str_replace('[designation]', $user->designation, $message);
					} else {
						$message = str_replace('[designation]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[dob]') {
					if ($user->dob) {
						$dob =  date("d M Y", strtotime($user->dob));
						$message = str_replace('[dob]', $dob, $message);
					} else {
						$message = str_replace('[dob]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[gender]') {
					if ($user->sex) {
						$message = str_replace('[gender]', $user->sex, $message);
					} else {
						$message = str_replace('[gender]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[religion]') {
					if ($user->religion) {
						$message = str_replace('[religion]', $user->religion, $message);
					} else {
						$message = str_replace('[religion]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[email]') {
					if ($user->email) {
						$message = str_replace('[email]', $user->email, $message);
					} else {
						$message = str_replace('[email]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[phone]') {
					if ($user->phone) {
						$message = str_replace('[phone]', $user->phone, $message);
					} else {
						$message = str_replace('[phone]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[address]') {
					if ($user->address) {
						$message = str_replace('[address]', $user->address, $message);
					} else {
						$message = str_replace('[address]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[jod]') {
					if ($user->jod) {
						$jod =  date("d M Y", strtotime($user->jod));
						$message = str_replace('[jod]', $jod, $message);
					} else {
						$message = str_replace('[jod]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[username]') {
					if ($user->username) {
						$message = str_replace('[username]', $user->username, $message);
					} else {
						$message = str_replace('[username]', ' ', $message);
					}
				} elseif ($userTag->tagname == "[father's_name]") {
					if ($user->father_name) {
						$message = str_replace("[father's_name]", $user->father_name, $message);
					} else {
						$message = str_replace("[father's_name]", ' ', $message);
					}
				} elseif ($userTag->tagname == "[mother's_name]") {
					if ($user->mother_name) {
						$message = str_replace("[mother's_name]", $user->mother_name, $message);
					} else {
						$message = str_replace("[mother's_name]", ' ', $message);
					}
				} elseif ($userTag->tagname == "[father's_profession]") {
					if ($user->father_profession) {
						$message = str_replace("[father's_profession]", $user->father_profession, $message);
					} else {
						$message = str_replace("[father's_profession]", ' ', $message);
					}
				} elseif ($userTag->tagname == "[mother's_profession]") {
					if ($user->mother_profession) {
						$message = str_replace("[mother's_profession]", $user->mother_profession, $message);
					} else {
						$message = str_replace("[mother's_profession]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[class]') {
					$classes = $this->classes_m->get_classes($user->srclassesID);
					if (customCompute($classes)) {
						$message = str_replace('[class]', $classes->classes, $message);
					} else {
						$message = str_replace('[class]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[roll]') {
					if ($user->srroll) {
						$message = str_replace("[roll]", $user->srroll, $message);
					} else {
						$message = str_replace("[roll]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[country]') {
					if ($user->country) {
						$message = str_replace("[country]", $this->data['allcountry'][$user->country], $message);
					} else {
						$message = str_replace("[country]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[state]') {
					if ($user->state) {
						$message = str_replace("[state]", $user->state, $message);
					} else {
						$message = str_replace("[state]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[register_no]') {
					if ($user->srregisterNO) {
						$message = str_replace("[register_no]", $user->srregisterNO, $message);
					} else {
						$message = str_replace("[register_no]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[section]') {
					if ($user->srsectionID) {
						$section = $this->section_m->get_section($user->srsectionID);
						if (customCompute($section)) {
							$message = str_replace('[section]', $section->section, $message);
						} else {
							$message = str_replace('[section]', ' ', $message);
						}
					} else {
						$message = str_replace("[section]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[blood_group]') {
					if ($user->bloodgroup && $user->bloodgroup != '0') {
						$message = str_replace("[blood_group]", $user->bloodgroup, $message);
					} else {
						$message = str_replace("[blood_group]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[group]') {
					if ($user->srstudentgroupID && $user->srstudentgroupID != 0) {
						$group = $this->studentgroup_m->get_studentgroup($user->srstudentgroupID);
						if (customCompute($group)) {
							$message = str_replace('[group]', $group->group, $message);
						} else {
							$message = str_replace('[group]', ' ', $message);
						}
					} else {
						$message = str_replace('[group]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[optional_subject]') {
					if ($user->sroptionalsubjectID && $user->sroptionalsubjectID != 0) {
						$subject = $this->subject_m->get_single_subject(array('subjectID' => $user->sroptionalsubjectID));
						if (customCompute($subject)) {
							$message = str_replace('[optional_subject]', $subject->subject, $message);
						} else {
							$message = str_replace('[optional_subject]', ' ', $message);
						}
					} else {
						$message = str_replace('[optional_subject]', ' ', $message);
					}
				} elseif ($userTag->tagname == '[extra_curricular_activities]') {
					if ($user->extracurricularactivities) {
						$message = str_replace("[extra_curricular_activities]", $user->extracurricularactivities, $message);
					} else {
						$message = str_replace("[extra_curricular_activities]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[remarks]') {
					if ($user->remarks) {
						$message = str_replace("[remarks]", $user->remarks, $message);
					} else {
						$message = str_replace("[remarks]", ' ', $message);
					}
				} elseif ($userTag->tagname == '[result_table]') {
					// 	if($sendType == 'email') {
					// 		if($user->usertypeID == 3) {
					// 			$result = $this->resultTableEmail($user->srstudentID, $user->srclassesID, $schoolyearID);
					// 		} else {
					// 			$result = '';
					// 		}
					// 		$message = str_replace("[result_table]", $result, $message);
					// 	} elseif($sendType == 'SMS') {
					// 		if($user->usertypeID == 3) {
					// 			$result = $this->resultTableSMS($user->srstudentID, $user->srclassesID, $schoolyearID);
					// 		} else {
					// 			$result = '';
					// 		}
					// 		$message = str_replace("[result_table]", $result, $message);
					// 	}
				} elseif ($userTag->tagname == '{{student_name}}') {
					if ($user->name) {
						$message = str_replace("{{student_name}}", $user->name, $message);
					} else {
						$message = str_replace("{{student_name}}", ' ', $message);
					}
				} elseif ($userTag->tagname == '{{roll_no}}') {
					if ($user->srroll) {
						$message = str_replace("{{roll_no}}", $user->srroll, $message);
					} else {
						$message = str_replace("{{roll_no}}", ' ', $message);
					}
				} elseif ($userTag->tagname == '{{absent_date}}') {
					$message = str_replace("{{absent_date}}", @$_POST['day'] . '-' . @$_POST['monthyear'], $message);
				} elseif ($userTag->tagname == '{{school_name}}') {
					$settings = $this->data['setting'];
					$message = str_replace("{{school_name}}", @$settings->sname, $message);
				}elseif($userTag->tagname == '{{date}}') {
					$message = str_replace("{{date}}",$user->date, $message);
				}elseif($userTag->tagname == '{{category}}') {
					if($user->category) {
						$message = str_replace('{{category}}', $user->category, $message);
					} else {
						$message = str_replace('{{category}}', ' ', $message);
					}
				}elseif($userTag->tagname == '{{paid_amount}}') {
					if($user->paidamount) {
						$message = str_replace('{{paid_amount}}', $user->paidamount, $message);
					} else {
						$message = str_replace('{{paid_amount}}', ' ', $message);
					}
				}
			}
		}
		return $message;
	}
    


}