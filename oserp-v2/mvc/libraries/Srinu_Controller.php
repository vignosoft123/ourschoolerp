<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property document_m $document_m
 * @property email_m $email_m
 * @property error_m $error_m
 */
class Srinu_Controller {
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
        // $this->load->config('iniconfig');
        // $this->data['errors'] = [];

        // if ( !$this->config->config_install() ) {
        //     redirect(site_url('install'));
        // }
    }

    public function generateAttachment1($stylesheet=NULL, $data=NULL, $viewpath=NULL, $pagesize = 'a4', $pagetype='portrait'){
        echo 'srinivas123';die;
    }
}

