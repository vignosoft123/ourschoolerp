<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Common_Controller extends MY_Controller { //MY_Controller
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

    private $_backendTheme = '';
    private $_backendThemePath = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->model("signin_m");
        $this->load->model("permission_m");
        $this->load->model("site_m");
        $this->load->model("holiday_m");
        $this->load->model("schoolyear_m");
        $this->load->model("alert_m");
        $this->load->library("session");
        $this->load->helper('language');
        $this->load->helper('date');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('classes_m');
        $this->load->model("menu_m");
        $this->lang->load('topbar_menu', $this->session->userdata('lang'));

        $module            = $this->uri->segment(1);
        $action            = $this->uri->segment(2);
        $siteInfo          = $this->site_m->get_site();
        $frontendManager   = $this->_frontendManager($siteInfo);
        $permissionManager = $this->_permissionManager($module, $action);
        //    echo "<pre>"; print_r($_SESSION);die;


                                $url = $_SERVER['HTTP_HOST'];
                                $encrypted_site_key = $this->encryption($url);
                                
                                $this->db->where('fieldoption','site_key');
                                $query = $this->db->get('setting');
                                $check_site = $query->num_rows();
                                $setting = $query->row(); 

                                // echo $url."<br/>";
                                // echo $encrypted_site_key."<br/>";
                                // echo $setting->value."<br/>";die;

                                if($setting->value != $encrypted_site_key){ 

                                        $this->_my_settings();
                                }


        if ( !empty($frontendManager) ) {
            redirect($frontendManager);
        } elseif ( !empty($permissionManager) ) {
            redirect($permissionManager);
        }

        $userTypeID              = $this->session->userdata('usertypeID');
        $this->_backendTheme     = strtolower($siteInfo->backend_theme);
        $this->_backendThemePath = 'assets/inilabs/themes/' . strtolower($siteInfo->backend_theme);

        $this->data["siteinfos"]            = $siteInfo;
        $this->data['backendTheme']         = $this->_backendTheme;
        $this->data['backendThemePath']     = $this->_backendThemePath;
        $this->data['allcountry']           = $this->getAllCountry();
        $this->data['allbloodgroup']        = $this->_bloodGroup();
        $this->data['myclass']              = $this->_classManager($userTypeID);
        $this->data['schoolyearobj']        = $this->schoolyear_m->get_obj_schoolyear($siteInfo->school_year);
        $this->data['schoolyearsessionobj'] = $this->schoolyear_m->get_obj_schoolyear($this->session->userdata('defaultschoolyearID'));
        $this->data['topbarschoolyears']    = $this->schoolyear_m->get_order_by_schoolyear([ 'schooltype' => 'classbase' ]);

    } 


}