<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Signin extends REST_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('user_m');
        $this->load->model('setting_m');
        $this->load->model('usertype_m');
        $this->load->model('permission_m');
        $this->load->model('schoolyear_m');
    }

    public function index_post()
    {
    	$username 	= inputCall('username');
    	$password 	= inputCall('password');
    	if ($username && $password) {
    		$userInfo = $this->userInfo(inputCall());
    		if(is_array($userInfo)) {
                $tokenArray['iat']   	= time();
                $tokenArray['userdata']	= (array) $userInfo;
                $token                  = $this->jwt_encode($tokenArray);
                $correspondent_phone = '';
                $teacher_phone = '';
                $teacher_name = ''; 

                $c_phone_res = $this->db->query('select value from setting where fieldoption="phone" ')->row_array();
                if(!empty($c_phone_res)){
                    $correspondent_phone = $c_phone_res['value'];
                }
                $this->retdata['token'] = $token;
                $this->retdata['profile'] = (array) $userInfo;
                 
                if($userInfo['usertypeID'] == 3){
                    $studentID = $userInfo['loginuserID'];
                    $this->db->select('t.phone,t.name as teacher_name');
                    $this->db->from('student s');
                    $this->db->join('classes c', 's.classesID = c.classesID', 'left');
                    $this->db->join('teacher t', 'c.teacherID = t.teacherID', 'left');
                    $this->db->where('s.studentID', $studentID);
                    $query = $this->db->get();
                    // echo $this->db->last_query();die;
                    $result = $query->row_array();
                    $teacher_phone =  $result['phone'];
                    $teacher_name =  $result['teacher_name'];
                }
                
                $schoolyear = $this->schoolyear_m->get_single_schoolyear(['schoolyearID' => $userInfo['defaultschoolyearID']]);
                $this->retdata['academic_year']       = $schoolyear ? $schoolyear->schoolyear : '';
                $this->retdata['teacher_phone']       = $teacher_phone;
                $this->retdata['teacher_name']        = $teacher_name;
                $this->retdata['correspondent_phone'] = $correspondent_phone;

                $this->response([
                    'status'    => true,
                    'message'   => 'Success',
                    'data'      => $this->retdata
                ], REST_Controller::HTTP_OK);
            } else {
    			$this->response([
                	'status' 	=> false,
	                'message' 	=> 'Invalid username or password'
	            ], REST_Controller::HTTP_UNAUTHORIZED);	
    		}
    	} else {
    		$this->response([
                'status' 	=> false,
                'message' 	=> 'Invalid username or password'
            ], REST_Controller::HTTP_UNAUTHORIZED);
    	}
    }

    private function userInfo($array)
    {
    	$username = $array['username'];
    	$password = $this->user_m->hash($array['password']);
    	$tables   = [
            'student'     => 'student',
            'parents'     => 'parents',
            'teacher'     => 'teacher',
            'user'        => 'user',
            'systemadmin' => 'systemadmin',
        ];

        $setting 		= $this->setting_m->get_setting();
       	$userFoundInfo 	= [];
       	$tableID 		= 0;

       	foreach ($tables as $table) {
            $user 				= $this->db->get_where($table, ["username" => $username, "password" => $password, 'active' => 1]);
            $userInfo 			= $user->row();
            if(customCompute($userInfo)) {
            	$tableID 		= $table . 'ID';
            	$userFoundInfo 	= $userInfo; 
            }
        }

        if(customCompute($userFoundInfo)) {
        	$usertype 		= $this->usertype_m->get_single_usertype(array('usertypeID' => $userFoundInfo->usertypeID));
        	$sessionArray 	= [
                'loginuserID'         	=> $userFoundInfo->$tableID,
                'name'                	=> $userFoundInfo->name,
                'email'               	=> $userFoundInfo->email,
                'usertypeID'          	=> $userFoundInfo->usertypeID,
                'usertype'            	=> $usertype->usertype,
                'username'              => $userFoundInfo->username,
                'password'           	=> $password,
                'photo'               	=> $userFoundInfo->photo,
                'lang'               	=> $setting->language,
                'defaultschoolyearID' 	=> $setting->school_year,
                "loggedin"            	=> true,
                "varifyvaliduser"       => true,
            ];

            $this->session->unset_userdata('master_permission_set');
            $this->session->set_userdata($sessionArray);
            
            $permissionSet  = [];
            $session        = $this->session->userdata;
            if($this->session->userdata('usertypeID') == 1 && $this->session->userdata('loginuserID') == 1) {
                if(isset($session['loginuserID'])) {
                    $features   = $this->permission_m->get_permission();
                    if(customCompute($features)) {
                        foreach ($features as $featureKey => $feature) {
                            $permissionSet['master_permission_set'][trim($feature->name)] = $feature->active;
                        }
                        $permissionSet['master_permission_set']['take_exam'] = 'yes';
                        $this->session->set_userdata($permissionSet);
                    }
                }
            } else {
                if(isset($session['loginuserID'])) {
                    $features   = $this->permission_m->get_modules_with_permission($session['usertypeID']);
                    foreach ($features as $feature) {
                        $permissionSet['master_permission_set'][$feature->name] = $feature->active;
                    }

                    if($session['usertypeID'] == 3) {
                        $permissionSet['master_permission_set']['take_exam'] = 'yes';
                    }
                    $this->session->set_userdata($permissionSet);
                }
            }

            return $sessionArray;
        } else {
        	return false;
        }
    }
}