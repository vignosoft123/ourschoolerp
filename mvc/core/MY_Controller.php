<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property document_m $document_m
 * @property email_m $email_m
 * @property error_m $error_m
 */
class MY_Controller extends CI_Controller {
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
    public $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->config('iniconfig');
        $this->data['errors'] = [];
        $this->load->library('session');

         // 1. Get subdomain
         /*$subdomain = get_subdomain(); // defined in helper below
         if (!$subdomain) {
             show_error("Invalid subdomain.");
         }

           // Store it in session (only if not already set)
        if (!$this->session->userdata('subdomain')) {
            $this->session->set_userdata('subdomain', $subdomain);
        }

        // for gettin session 
        // $subdomain = $this->session->userdata('subdomain');
 
        //  echo $subdomain;die;
         // 2. Load fixed master DB to get credentials
         $this->load->database('default'); // defined in database.php
         $setting = $this->db->get_where('subdomain_settings',  ['subdomain' => $subdomain, 'status' => 'active'])->row();
         
 
         if (!$setting) {
             show_error("Subdomain settings not found or License Expired.");
         }
 
         // 3. Dynamically override $this->db
         $db_config = array(
             'hostname' => $setting->db_host,
             'username' => $setting->db_user,
             'password' => $setting->db_pass,
             'database' => $setting->db_name,
             'dbdriver' => 'mysqli',
             'dbprefix' => '',
             'pconnect' => FALSE,
             'db_debug' => TRUE,
         );
 
         // 🔁 This replaces the default DB connection globally
         $this->db = $this->load->database($db_config, TRUE);*/


        if ( !$this->config->config_install() ) {
            redirect(site_url('install'));
        }
    }
}

