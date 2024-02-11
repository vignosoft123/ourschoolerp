<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends MY_Controller {
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
        $this->_my_settings();
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

    Private function _classManager( $userTypeID )
    {
        if ( $userTypeID == 3 ) {
            $this->load->model('studentrelation_m');
            $student = $this->studentrelation_m->get_single_student([
                'srstudentID'    => $this->session->userdata('loginuserID'),
                'srschoolyearID' => $this->session->userdata('defaultschoolyearID')
            ]);
            if ( customCompute($student) ) {
                return $student->srclassesID;
            }
            return 0;
        }
        return 0;
    }

    private function _frontendManager( $siteInfo )
    {
        $url = '';
        $exceptionUris = [
            'signin',
            'signin/index',
            'signin/signout'
        ];

        if ( in_array(uri_string(), $exceptionUris) == false ) {
            if ( $this->signin_m->loggedin() == false ) {
                if ( $siteInfo->frontendorbackend === 'YES' || $siteInfo->frontendorbackend == 1 ) {
                    $this->load->model('fmenu_m');
                    $this->load->model('pages_m');
                    $this->load->model('posts_m');
                    $frontendRedirectURL    = '';
                    $frontendRedirectMethod = 'home';
                    $frontendTopbar         = $this->fmenu_m->get_single_fmenu([ 'topbar' => 1 ]);
                    $homePage               = $this->pages_m->get_one($frontendTopbar);
                    if ( customCompute($homePage) ) {
                        if ( $homePage->menu_typeID == 1 ) {
                            $page = $this->pages_m->get_single_pages([ 'pagesID' => $homePage->menu_pagesID ]);
                            if ( customCompute($page) ) {
                                $frontendRedirectURL    = $page->url;
                                $frontendRedirectMethod = 'page';
                            }
                        } elseif ( $homePage->menu_typeID == 2 ) {
                            $post = $this->posts_m->get_single_posts([ 'postsID' => $homePage->menu_pagesID ]);
                            if ( customCompute($post) ) {
                                $frontendRedirectURL    = $post->url;
                                $frontendRedirectMethod = 'post';
                            }
                        }
                    }
                    $url = base_url('frontend/' . $frontendRedirectMethod . '/' . $frontendRedirectURL);
                } else {
                    $url = base_url("signin/index");
                }
            }
        }
        return $url;
    }

    private function _permissionManager( $module, $action )
    {
        if ( $action == 'index' || $action == false ) {
            $permission = $module;
        } else {
            $permission = $module . '_' . $action;
        }

        $url             = '';
        $permissionArray = [];
        $userdata        = $this->session->userdata;

        if ( $this->session->userdata('usertypeID') == 1 && $this->session->userdata('loginuserID') == 1 ) {
            if ( isset($userdata['loginuserID']) && !isset($userdata['get_permission']) ) {
                $features = $this->permission_m->get_permission();
                if ( customCompute($features) ) {
                    foreach ( $features as $featureKey => $feature ) {
                        $permissionArray['master_permission_set'][ trim($feature->name) ] = $feature->active;
                    }

                    $permissionArray['master_permission_set']['take_exam'] = 'yes';
                    $this->session->set_userdata([ 'get_permission' => true ]);
                    $this->session->set_userdata($permissionArray);
                }
            }
        } else {
            if ( isset($userdata['loginuserID']) && !isset($userdata['get_permission']) ) {
                if ( !$this->session->userdata($permission) ) {
                    $user_permission = $this->permission_m->get_modules_with_permission($userdata['usertypeID']);

                    foreach ( $user_permission as $value ) {
                        $permissionArray['master_permission_set'][ $value->name ] = $value->active;
                    }

                    if ( $userdata['usertypeID'] == 3 ) {
                        $permissionArray['master_permission_set']['take_exam'] = 'yes';
                    }

                    $this->session->set_userdata([ 'get_permission' => true ]);
                    $this->session->set_userdata($permissionArray);
                }
            }
        }

        $sessionPermission     = $this->session->userdata('master_permission_set');
        // echo "<pre>";print_r($sessionPermission);die;
        $dbMenus               = $this->menuTree(json_decode(json_encode(pluck($this->menu_m->get_order_by_menu([ 'status' => 1 ]),
            'obj', 'menuID')), true), $sessionPermission);
        $this->data["dbMenus"] = $dbMenus;

        if ( ( isset($sessionPermission[ $permission ]) && $sessionPermission[ $permission ] == "no" ) ) {
            if ( $permission == 'dashboard' && $sessionPermission[ $permission ] == "no" ) {
                if ( in_array('yes', $sessionPermission) ) {
                    if ( $sessionPermission["dashboard"] == 'no' ) {
                        $url = 'exceptionpage/index';
                        foreach ( $sessionPermission as $key => $value ) {
                            if ( $value == 'yes' ) {
                                $url = $key;
                                break;
                            }
                        }
                    }
                }
            } else {
                $url = base_url('exceptionpage/error');
            }
        }
        return $url;
    }

    public function usercreatemail($email=NULL, $username=NULL, $password=NULL) {
        $this->load->model('emailsetting_m');
        $emailSetting = $this->emailsetting_m->get_emailsetting();
        $this->load->library('email');
        $this->email->set_mailtype("html");

        if(customCompute($emailSetting)) {
            if($emailSetting->email_engine == 'smtp') {
                if ($emailSetting->smtp_security){
                    $config = [
                        'protocol'    => 'smtp',
                        'smtp_host'   => $emailSetting->smtp_server,
                        'smtp_port'   => $emailSetting->smtp_port,
                        'smtp_user'   => $emailSetting->smtp_username,
                        'smtp_pass'   => $emailSetting->smtp_password,
                        'smtp_crypto' => $emailSetting->smtp_security,
                        'mailtype'    => 'html',
                        'charset'     => 'utf-8',
                        'crlf' => "\r\n",
                        'newline' => "\r\n"
                    ];
                } else{
                    $config = [
                        'protocol'    => 'smtp',
                        'smtp_host'   => $emailSetting->smtp_server,
                        'smtp_port'   => $emailSetting->smtp_port,
                        'smtp_user'   => $emailSetting->smtp_username,
                        'smtp_pass'   => $emailSetting->smtp_password,
                        'mailtype'    => 'html',
                        'charset'     => 'utf-8',
                        'crlf' => "\r\n",
                        'newline' => "\r\n"
                    ];
                }
                $this->email->initialize($config);
            }
        }

        if($email) {
            $this->email->from($this->data['siteinfos']->email, $this->data['siteinfos']->sname);
            $this->email->to($email);
            $this->email->subject($this->data['siteinfos']->sname);
            $url = base_url();
            $message = "<h2>Welcome to ".$this->data['siteinfos']->sname."</h2>
	        <p>Please log-in to this website and change the password as soon as possible </p>
	        <p>Website : ".$url."</p>
	        <p>Username: ".$username."</p>
	        <p>Password: ".$password."</p>
	        <br>
	        <p>Once again, thank you for choosing ".$this->data['siteinfos']->sname."</p>
	        <p>Best Wishes,</p>
	        <p>The ".$this->data['siteinfos']->sname." Team</p>";
            $this->email->message($message);
            $this->email->send();
        }
    }

    public function reportPDF($stylesheet=NULL, $data=NULL, $viewpath= NULL, $mode = 'view', $pagesize = 'a4', $pagetype='portrait') {
        $designType = 'LTR';
        $this->data['panel_title'] = $this->lang->line('panel_title');
        $html = $this->load->view($viewpath, $this->data, true);

        $this->load->library('mhtml2pdf');

        $this->mhtml2pdf->folder('uploads/report/');
        $this->mhtml2pdf->filename('Report');
        $this->mhtml2pdf->paper($pagesize, $pagetype);
        $this->mhtml2pdf->html($html);

        if(!empty($stylesheet)) {
            $stylesheet = file_get_contents(base_url('assets/pdf/'.$designType.'/'.$stylesheet));
            return $this->mhtml2pdf->create($mode, $this->data['panel_title'], $stylesheet);
        } else {
            return $this->mhtml2pdf->create($mode, $this->data['panel_title']);
        }
    }

    public function reportSendToMail($stylesheet=NULL, $data=NULL, $viewpath=NULL, $email=NULL, $subject=NULL, $message=NULL, $pagesize = 'a4', $pagetype='portrait') {
        $this->load->model('emailsetting_m');

        $designType = 'LTR';
        $this->load->library('email');
        $this->load->library('mhtml2pdf');
        $this->mhtml2pdf->folder('uploads/report/');
        $rand    = random19() . date('y-m-d h:i:s');
        $sharand = hash('sha512', $rand);

        $this->mhtml2pdf->filename($sharand);
        $this->mhtml2pdf->paper($pagesize, $pagetype);
        $this->data['panel_title'] = $this->lang->line('panel_title');
        $html = $this->load->view($viewpath, $this->data, true);
        $this->mhtml2pdf->html($html);


        if(!empty($stylesheet)) {
            $stylesheet = file_get_contents(base_url('assets/pdf/'.$designType.'/'.$stylesheet));
        }

        $emailsetting = $this->emailsetting_m->get_emailsetting();
        $this->email->set_mailtype("html");

        if(customCompute($emailsetting)) {
            if($path = @$this->mhtml2pdf->create('save',$this->data['panel_title'], $stylesheet)) {
                if($emailsetting->email_engine == 'smtp') {
                    if ($emailsetting->smtp_security){
                        $config = [
                            'protocol'    => 'smtp',
                            'smtp_host'   => $emailsetting->smtp_server,
                            'smtp_port'   => $emailsetting->smtp_port,
                            'smtp_user'   => $emailsetting->smtp_username,
                            'smtp_pass'   => $emailsetting->smtp_password,
                            'smtp_crypto' => $emailsetting->smtp_security,
                            'mailtype'    => 'html',
                            'charset'     => 'utf-8',
                            'crlf' => "\r\n",
                            'newline' => "\r\n"
                        ];
                    } else{
                        $config = [
                            'protocol'    => 'smtp',
                            'smtp_host'   => $emailsetting->smtp_server,
                            'smtp_port'   => $emailsetting->smtp_port,
                            'smtp_user'   => $emailsetting->smtp_username,
                            'smtp_pass'   => $emailsetting->smtp_password,
                            'mailtype'    => 'html',
                            'charset'     => 'utf-8',
                            'crlf' => "\r\n",
                            'newline' => "\r\n"
                        ];
                    }
                    $this->email->initialize($config);
                }

                $fromEmail = $this->data["siteinfos"]->email;
                if($this->session->userdata('email') != '') {
                    $fromEmail = $this->session->userdata('email');
                }

                $this->email->from($fromEmail, $this->data['siteinfos']->sname);
                $this->email->to($email);
                $this->email->subject($subject);
                $this->email->message($message);
                $this->email->attach($path);
                if($this->email->send()) {
                    $this->session->set_flashdata('success', $this->lang->line('mail_success'));
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('mail_error'));
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('mail_error'));
        }
    }

    public function getAllCountry() {
        $country = array(
            "AF" => "Afghanistan",
            "AL" => "Albania",
            "DZ" => "Algeria",
            "AS" => "American Samoa",
            "AD" => "Andorra",
            "AO" => "Angola",
            "AI" => "Anguilla",
            "AQ" => "Antarctica",
            "AG" => "Antigua and Barbuda",
            "AR" => "Argentina",
            "AM" => "Armenia",
            "AW" => "Aruba",
            "AU" => "Australia",
            "AT" => "Austria",
            "AZ" => "Azerbaijan",
            "BS" => "Bahamas",
            "BH" => "Bahrain",
            "BD" => "Bangladesh",
            "BB" => "Barbados",
            "BY" => "Belarus",
            "BE" => "Belgium",
            "BZ" => "Belize",
            "BJ" => "Benin",
            "BM" => "Bermuda",
            "BT" => "Bhutan",
            "BO" => "Bolivia",
            "BA" => "Bosnia and Herzegovina",
            "BW" => "Botswana",
            "BV" => "Bouvet Island",
            "BR" => "Brazil",
            "BQ" => "British Antarctic Territory",
            "IO" => "British Indian Ocean Territory",
            "VG" => "British Virgin Islands",
            "BN" => "Brunei",
            "BG" => "Bulgaria",
            "BF" => "Burkina Faso",
            "BI" => "Burundi",
            "KH" => "Cambodia",
            "CM" => "Cameroon",
            "CA" => "Canada",
            "CT" => "Canton and Enderbury Islands",
            "CV" => "Cape Verde",
            "KY" => "Cayman Islands",
            "CF" => "Central African Republic",
            "TD" => "Chad",
            "CL" => "Chile",
            "CN" => "China",
            "CX" => "Christmas Island",
            "CC" => "Cocos [Keeling] Islands",
            "CO" => "Colombia",
            "KM" => "Comoros",
            "CG" => "Congo - Brazzaville",
            "CD" => "Congo - Kinshasa",
            "CK" => "Cook Islands",
            "CR" => "Costa Rica",
            "HR" => "Croatia",
            "CU" => "Cuba",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "CI" => "Côte d’Ivoire",
            "DK" => "Denmark",
            "DJ" => "Djibouti",
            "DM" => "Dominica",
            "DO" => "Dominican Republic",
            "NQ" => "Dronning Maud Land",
            "DD" => "East Germany",
            "EC" => "Ecuador",
            "EG" => "Egypt",
            "SV" => "El Salvador",
            "GQ" => "Equatorial Guinea",
            "ER" => "Eritrea",
            "EE" => "Estonia",
            "ET" => "Ethiopia",
            "FK" => "Falkland Islands",
            "FO" => "Faroe Islands",
            "FJ" => "Fiji",
            "FI" => "Finland",
            "FR" => "France",
            "GF" => "French Guiana",
            "PF" => "French Polynesia",
            "TF" => "French Southern Territories",
            "FQ" => "French Southern and Antarctic Territories",
            "GA" => "Gabon",
            "GM" => "Gambia",
            "GE" => "Georgia",
            "DE" => "Germany",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GR" => "Greece",
            "GL" => "Greenland",
            "GD" => "Grenada",
            "GP" => "Guadeloupe",
            "GU" => "Guam",
            "GT" => "Guatemala",
            "GG" => "Guernsey",
            "GN" => "Guinea",
            "GW" => "Guinea-Bissau",
            "GY" => "Guyana",
            "HT" => "Haiti",
            "HM" => "Heard Island and McDonald Islands",
            "HN" => "Honduras",
            "HK" => "Hong Kong SAR China",
            "HU" => "Hungary",
            "IS" => "Iceland",
            "IN" => "India",
            "ID" => "Indonesia",
            "IR" => "Iran",
            "IQ" => "Iraq",
            "IE" => "Ireland",
            "IM" => "Isle of Man",
            "IL" => "Israel",
            "IT" => "Italy",
            "JM" => "Jamaica",
            "JP" => "Japan",
            "JE" => "Jersey",
            "JT" => "Johnston Island",
            "JO" => "Jordan",
            "KZ" => "Kazakhstan",
            "KE" => "Kenya",
            "KI" => "Kiribati",
            "KW" => "Kuwait",
            "KG" => "Kyrgyzstan",
            "LA" => "Laos",
            "LV" => "Latvia",
            "LB" => "Lebanon",
            "LS" => "Lesotho",
            "LR" => "Liberia",
            "LY" => "Libya",
            "LI" => "Liechtenstein",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "MO" => "Macau SAR China",
            "MK" => "Macedonia",
            "MG" => "Madagascar",
            "MW" => "Malawi",
            "MY" => "Malaysia",
            "MV" => "Maldives",
            "ML" => "Mali",
            "MT" => "Malta",
            "MH" => "Marshall Islands",
            "MQ" => "Martinique",
            "MR" => "Mauritania",
            "MU" => "Mauritius",
            "YT" => "Mayotte",
            "FX" => "Metropolitan France",
            "MX" => "Mexico",
            "FM" => "Micronesia",
            "MI" => "Midway Islands",
            "MD" => "Moldova",
            "MC" => "Monaco",
            "MN" => "Mongolia",
            "ME" => "Montenegro",
            "MS" => "Montserrat",
            "MA" => "Morocco",
            "MZ" => "Mozambique",
            "MM" => "Myanmar [Burma]",
            "NA" => "Namibia",
            "NR" => "Nauru",
            "NP" => "Nepal",
            "NL" => "Netherlands",
            "AN" => "Netherlands Antilles",
            "NT" => "Neutral Zone",
            "NC" => "New Caledonia",
            "NZ" => "New Zealand",
            "NI" => "Nicaragua",
            "NE" => "Niger",
            "NG" => "Nigeria",
            "NU" => "Niue",
            "NF" => "Norfolk Island",
            "KP" => "North Korea",
            "VD" => "North Vietnam",
            "MP" => "Northern Mariana Islands",
            "NO" => "Norway",
            "OM" => "Oman",
            "PC" => "Pacific Islands Trust Territory",
            "PK" => "Pakistan",
            "PW" => "Palau",
            "PS" => "Palestinian Territories",
            "PA" => "Panama",
            "PZ" => "Panama Canal Zone",
            "PG" => "Papua New Guinea",
            "PY" => "Paraguay",
            "YD" => "People's Democratic Republic of Yemen",
            "PE" => "Peru",
            "PH" => "Philippines",
            "PN" => "Pitcairn Islands",
            "PL" => "Poland",
            "PT" => "Portugal",
            "PR" => "Puerto Rico",
            "QA" => "Qatar",
            "RO" => "Romania",
            "RU" => "Russia",
            "RW" => "Rwanda",
            "RE" => "Réunion",
            "BL" => "Saint Barthélemy",
            "SH" => "Saint Helena",
            "KN" => "Saint Kitts and Nevis",
            "LC" => "Saint Lucia",
            "MF" => "Saint Martin",
            "PM" => "Saint Pierre and Miquelon",
            "VC" => "Saint Vincent and the Grenadines",
            "WS" => "Samoa",
            "SM" => "San Marino",
            "SA" => "Saudi Arabia",
            "SN" => "Senegal",
            "RS" => "Serbia",
            "CS" => "Serbia and Montenegro",
            "SC" => "Seychelles",
            "SL" => "Sierra Leone",
            "SG" => "Singapore",
            "SK" => "Slovakia",
            "SI" => "Slovenia",
            "SB" => "Solomon Islands",
            "SO" => "Somalia",
            "ZA" => "South Africa",
            "GS" => "South Georgia and the South Sandwich Islands",
            "KR" => "South Korea",
            "ES" => "Spain",
            "LK" => "Sri Lanka",
            "SD" => "Sudan",
            "SR" => "Suriname",
            "SJ" => "Svalbard and Jan Mayen",
            "SZ" => "Swaziland",
            "SE" => "Sweden",
            "CH" => "Switzerland",
            "SY" => "Syria",
            "ST" => "São Tomé and Príncipe",
            "TW" => "Taiwan",
            "TJ" => "Tajikistan",
            "TZ" => "Tanzania",
            "TH" => "Thailand",
            "TL" => "Timor-Leste",
            "TG" => "Togo",
            "TK" => "Tokelau",
            "TO" => "Tonga",
            "TT" => "Trinidad and Tobago",
            "TN" => "Tunisia",
            "TR" => "Turkey",
            "TM" => "Turkmenistan",
            "TC" => "Turks and Caicos Islands",
            "TV" => "Tuvalu",
            "UM" => "U.S. Minor Outlying Islands",
            "PU" => "U.S. Miscellaneous Pacific Islands",
            "VI" => "U.S. Virgin Islands",
            "UG" => "Uganda",
            "UA" => "Ukraine",
            "SU" => "Union of Soviet Socialist Republics",
            "AE" => "United Arab Emirates",
            "GB" => "United Kingdom",
            "US" => "United States",
            "ZZ" => "Unknown or Invalid Region",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VU" => "Vanuatu",
            "VA" => "Vatican City",
            "VE" => "Venezuela",
            "VN" => "Vietnam",
            "WK" => "Wake Island",
            "WF" => "Wallis and Futuna",
            "EH" => "Western Sahara",
            "YE" => "Yemen",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe",
            "AX" => "Åland Islands",
        );
        return $country;
    }

    private function _bloodGroup() {
        $bloodgroup = array(
            'A+' => 'A+',
            'A-' => 'A-',
            'B+' => 'B+',
            'B-' => 'B-',
            'O+' => 'O+',
            'O-' => 'O-',
            'AB+' => 'AB+',
            'AB-' => 'AB-'
        );
        return $bloodgroup;
    }

    public function menuTree($dataset, $sessionPermission) {
        $tree = array();
        foreach ($dataset as $id=>&$node) {
            if($node['link'] == '#' || (isset($sessionPermission[$node['link']]) && $sessionPermission[$node['link']] != "no") ) {
                if ($node['parentID'] == 0) {
                    $tree[$id]=&$node;
                } else {
                    if (!isset($dataset[$node['parentID']]['child']))
                        $dataset[$node['parentID']]['child'] = array();

                    $dataset[$node['parentID']]['child'][$id] = &$node;
                }
            }
        }
        return $tree;
    }

    public function getHolidays() {
        $schoolyearID = $this->data['siteinfos']->school_year;
        $holidays = $this->holiday_m->get_order_by_holiday(array('schoolyearID' => $schoolyearID));
        $allHolidayList = array();
        if(customCompute($holidays)) {
            foreach ($holidays as $holiday) {
                $from_date = strtotime($holiday->fdate);
                $to_date   = strtotime($holiday->tdate);
                $oneday    = 60*60*24;
                for($i= $from_date; $i<= $to_date; $i= $i+$oneday) {
                    $allHolidayList[] = date('d-m-Y', $i);
                }
            }
        }

        $uniqueHolidays =  array_unique($allHolidayList);
        if(customCompute($uniqueHolidays)) {
            $uniqueHolidays = implode('","', $uniqueHolidays);
        } else {
            $uniqueHolidays = '';
        }

        return $uniqueHolidays;
    }

    public function getHolidaysSession() {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $holidays = $this->holiday_m->get_order_by_holiday(array('schoolyearID' => $schoolyearID));
        $allHolidayList = array();
        if(customCompute($holidays)) {
            foreach ($holidays as $holiday) {
                $from_date = strtotime($holiday->fdate);
                $to_date   = strtotime($holiday->tdate);
                $oneday    = 60*60*24;
                for($i= $from_date; $i<= $to_date; $i= $i+$oneday) {
                    $allHolidayList[] = date('d-m-Y', $i);
                }
            }
        }

        $uniqueHolidays =  array_unique($allHolidayList);
        if(customCompute($uniqueHolidays)) {
            $uniqueHolidays = implode('","', $uniqueHolidays);
        } else {
            $uniqueHolidays = '';
        }

        return $uniqueHolidays;
    }

    public function getWeekendDays() {
        $date_from = strtotime($this->data['schoolyearobj']->startingdate);
        $date_to = strtotime($this->data['schoolyearobj']->endingdate);
        $oneDay = 60*60*24;

        $allDays = array(
            '0' => 'Sunday',
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday'
        );

        $weekendDay    = $this->data['siteinfos']->weekends;
        $weekendArrays = explode(',', $weekendDay);
        $weekendDateArrays = array();
        for($i= $date_from; $i<= $date_to; $i= $i+$oneDay) {
            if($weekendDay != "") {
                foreach($weekendArrays as $weekendValue) {
                    if($weekendValue >= 0 && $weekendValue <= 6) {
                        if(date('l',$i) == $allDays[$weekendValue]) {
                            $weekendDateArrays[] = date('d-m-Y', $i);
                        }
                    }
                }
            }
        }
        return $weekendDateArrays;
    }

    public function getWeekendDaysSession() {
        $date_from = strtotime($this->data['schoolyearsessionobj']->startingdate);
        $date_to   = strtotime($this->data['schoolyearsessionobj']->endingdate);
        $oneDay    = 60*60*24;

        $allDays = array(
            '0' => 'Sunday',
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday'
        );

        $weekendDay = $this->data['siteinfos']->weekends;
        $weekendArrays = explode(',', $weekendDay);

        $weekendDateArrays = array();

        for($i= $date_from; $i<= $date_to; $i= $i+$oneDay) {
            if($weekendDay != "") {
                foreach($weekendArrays as $weekendValue) {
                    if($weekendValue >= 0 && $weekendValue <= 6) {
                        if(date('l',$i) == $allDays[$weekendValue]) {
                            $weekendDateArrays[] = date('d-m-Y', $i);
                        }
                    }
                }
            }
        }
        return $weekendDateArrays;
    }

    //code start for new functionalites -srinu

    public function generateAttachment($stylesheet=NULL, $data=NULL, $viewpath=NULL, $pagesize = 'a4', $pagetype='portrait'){
        $designType = 'LTR';
        $this->load->library('email');
        $this->load->library('mhtml2pdf');
        $this->mhtml2pdf->folder('uploads/report/');
        $rand    = random19() . date('y-m-d h:i:s');
        $sharand = hash('sha512', $rand);
        
        if(!empty($stylesheet)) {
            $stylesheet = file_get_contents(base_url('assets/pdf/'.$designType.'/'.$stylesheet));
        }

        // http://localhost/school/invoice/print_preview/86
        //$viewpath = 'invoice/print_preview';
        $this->mhtml2pdf->filename($sharand);
        $this->mhtml2pdf->paper($pagesize, $pagetype);
        $this->data['panel_title'] = $this->lang->line('panel_title');
        $html = $this->load->view($viewpath, $this->data, true);
        $this->mhtml2pdf->html($html);
        $path = @$this->mhtml2pdf->create('save',$this->data['panel_title'], $stylesheet);
        return $path;
         

}

public function send_whatsapp_attachment($array=""){
 

    // echo "<pre>";print_r($array);die;
    // echo $array["media_path"];die;
    
    $field_values = $this->getWhatsappconfig();

    $whatsapp_link_number = "91".$field_values[1]['field_values'];
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
		CURLOPT_POSTFIELDS => 'https://voice.vignosoft.com/api_v2/whats-app/send?api_key='.$field_values[0]["field_values"].'&type=broadcast&country_code=IN&wa_number='.$whatsapp_link_number.'&mobile_numbers='.$array["whatsapp_number"].'&content='.$array["whatsapp_msg"].'&media_url_1='.$array["media_path"],
		CURLOPT_HTTPHEADER => array(
			// 'AuthorizationKey: Bearer "'.$field_values[0]["field_values"].'"',
            "Authorization: Bearer ".$this->bearer,
			'Content-Type: application/x-www-form-urlencoded'
		),
		));

		$response = curl_exec($curl);

        $resp = json_decode($response,true);
		$status = $resp['success'];
		// print_r($response); 
        $url = 'https://voice.vignosoft.com/api_v2/whats-app/send?api_key='.$field_values[0]["field_values"].'&type=broadcast&country_code=IN&wa_number='.$whatsapp_link_number.'&mobile_numbers='.$array["whatsapp_number"].'&content='.$array["whatsapp_msg"].'&media_url_1='.$array["media_path"];
        $data = array(
            'request_url' => $url,
            'api_response' => $response,
            'created_on' => date("Y-m-d H:i:s"),
            'type' => "WHATSAPP - progresscard",
        ); 
		// print_r($data);
        if ($status == false) {  
            $this->db->insert('sms_error_logs',$data);
            
            unlink($array["attachment_path"]);
        }  

       

        //echo 'https://voice.vignosoft.com/api_v2/whats-app/send?api_key='.$field_values.'&type=broadcast&country_code=IN&wa_number=8500814626&mobile_numbers='.$array["whatsapp_number"].'&content='.$array["whatsapp_msg"].'&media_url_1='.$array["media_path"];
        //echo 'https://voice.vignosoft.com/api_v2/whats-app/send?api_key='.$field_values[0]["field_values"].'&type=broadcast&country_code=IN&wa_number='.$whatsapp_link_number.'&mobile_numbers='.$array["whatsapp_number"].'&content='.$array["whatsapp_msg"].'&media_url_1='.$array["media_path"];
        // echo "<pre>";print_r($response);die;

		curl_close($curl);
		return $response;
}

public function getWhatsappconfig(){
    $this->db->where('types','whatsapp');
   return $this->db->get('smssettings ')->result_array();
}


//srinivas

public function encryption($url=""){
      
    
    // Store a string into the variable which
    // need to be Encrypted
    $simple_string = $url;
     
     
    // Store the cipher method
    $ciphering = "AES-128-CTR";
    
    // Use OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    
    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '1234567891011121';
    
    // Store the encryption key
    $encryption_key = "srinivas";
    
    // Use openssl_encrypt() function to encrypt the data
    $encryption = openssl_encrypt($simple_string, $ciphering,
    			$encryption_key, $options, $encryption_iv);
    
    // Display the encrypted string
    // echo "Encrypted String: " . $encryption . "\n";
    return $encryption;
    
    }
    
    public function decryption($url=""){
      
     
    // Store a string into the variable which
    // need to be Encrypted
    $encryption = $url;
     
     
    // Store the cipher method
    $ciphering = "AES-128-CTR";
    
    // Use OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    
    // Store the encryption key
    $encryption_key = "srinivas"; 
    
    // Non-NULL Initialization Vector for decryption
    $decryption_iv = '1234567891011121';
    
    // Store the decryption key
    $decryption_key = "srinivas";
    
    // Use openssl_decrypt() function to decrypt the data
    $decryption=openssl_decrypt ($encryption, $ciphering,
    		$decryption_key, $options, $decryption_iv);
    
    // Display the decrypted string
    echo "Decrypted String: " . $decryption;
    
     
    }


public function encryption_decryption($url=""){
      
     
    // Store a string into the variable which
    // need to be Encrypted
    $simple_string = $url;
     
     
    // Store the cipher method
    $ciphering = "AES-128-CTR";
    
    // Use OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    
    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '1234567891011121';
    
    // Store the encryption key
    $encryption_key = "srinivas";
    
    // Use openssl_encrypt() function to encrypt the data
    $encryption = openssl_encrypt($simple_string, $ciphering,
    			$encryption_key, $options, $encryption_iv);
    
    // Display the encrypted string
    echo "Encrypted String: " . $encryption . "<br/>";
  
    
    
    
    // Non-NULL Initialization Vector for decryption
    $decryption_iv = '1234567891011121';
    
    // Store the decryption key
    $decryption_key = "srinivas";
    
    // Use openssl_decrypt() function to decrypt the data
    $decryption=openssl_decrypt ($encryption, $ciphering,
    		$decryption_key, $options, $decryption_iv);
    
    // Display the decrypted string
    echo "Decrypted String: " . $decryption;
    
     
}
    
    //  private function _my_settings(){    //don't delete,if deleted site not worked properly
    //     $url = $_SERVER['HTTP_HOST'];
    //     $setting      = $this->setting_m->get_setting();
    //     $encrypted_site_key = $this->encryption($url);
    //     if($setting->site_key != $encrypted_site_key){
    //       echo $data['info'] =   $encrypted_site_key .'^^^^d=^'. $setting->site_key ;
    //         $this->data["subview"] = "errorpermission";
    //         $this->load->view('_layout_main', $this->data);
    //     } 
    // }

    private function _my_settings(){    //don't delete,if deleted site not worked properly
         $url = $_SERVER['HTTP_HOST'];
         $encrypted_site_key = $this->encryption($url);
        
        $this->db->where('fieldoption','site_key');
        $query = $this->db->get('setting');
        $check_site = $query->num_rows();

         $check_appi = $this->check_aapi($url);

        if($check_site > 0 && $check_appi > 0){ 
            
            $setting = $query->row();    
            //  echo 'setting_value='.$setting->value;die;
             
             $this->db->where('fieldoption','site_key_active');
            $active_check = $this->db->get('setting');
             if( ($active_check->num_rows()>0) && ($active_check->row()->value == 1) ){
             
            if($setting->value != $encrypted_site_key){   
                
                $sql = $this->db->query("select * from setting where fieldoption='defined'");
                $user_defined = $sql->num_rows();
                if($user_defined > 0){
                    echo $data['info'] =  $sql->row()->value ;
                }else{
                    echo $data['info'] =  rtrim($setting->fieldoption,"_key") ;
                }
                
                $this->data["subview"] = "errorpermission";
                $this->load->view('_layout_main', $this->data);
            } 
             }
        }else{
            // echo '$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$';

            // Create a new cURL resource
            // $ch = curl_init('https://college.ggsdarsi.org/Main1/save_track');
            $ch = curl_init('https://ourschoolerp.com/Main1/save_track');
            
            // Setup request to send json via POST
            $data = array(
                'url' => $url
            );
            $payload = json_encode($data);
            
            // Attach encoded JSON string to the POST fields
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            
            // Set the content type to application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            
            // Return response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // Execute the POST request
            $result = curl_exec($ch);
            
            // Close cURL resource
            curl_close($ch);
            $response = json_decode($result,true);
            if($response['status'] == 1){
                $insert = array(
                    'fieldoption' => 'site_key',
                    'value' => $encrypted_site_key
                    );
                $this->db->insert('setting',$insert);
            }
        }

        // if($response['status'] == 1){

        //     $setting      = $this->setting_m->get_setting();        
        //     if($setting->site_key != $encrypted_site_key){
        //         echo $data['info'] =  $encrypted_site_key .'^^^^d=^'. $setting->site_key ;
        //         $this->data["subview"] = "errorpermission";
        //         $this->load->view('_layout_main', $this->data);
        //     } 

        // }else{
        //     $insert = array(
        //         'fieldoption' => 'site_key',
        //         'value' => $encrypted_site_key
        //         );
        //     $this->db->insert('setting',$insert);
        // }
        
        
    }

    private function check_aapi($url=""){
        // $ch = curl_init('https://college.ggsdarsi.org/Main1/check_school'); 
        $ch = curl_init('https://ourschoolerp.com/Main1/check_school'); 
        
        $data = array(
            'url' => $url
        );
        $payload = json_encode($data);
        
        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        
        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        
        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the POST request
        $result = curl_exec($ch);
        
        // Close cURL resource
        curl_close($ch);
        return $result;
            
    }

    public function get_settings($fieldoption=""){
        $return = 0;
        if(!empty($fieldoption)){
            $this->db->where('fieldoption',$fieldoption);
            $res = $this->db->get('setting')->row();
            //    echo "<pre>";print_r($res);die;
            if($res){
                $return = $res->value;
            }
            
        }
        return $return;
        
    }

    public function run_migrations(){
        
        $migrations = $this->get_migrations();
        $response = json_decode($migrations,true);
        // echo "<pre>";print_r($response);die;
        $final_result = array();
        if($response['status']==1){
            foreach($response['data'] as $val){
                $query = $val['sql_query'];
               $queries =  explode(";",$query); 
            //    echo "<pre>";print_r($queries);
                for($i=0;$i<count($queries)-1;$i++){
                    if(!empty($queries[$i])){

                        if (! $this->db->query($queries[$i])) {
                            $error = $this->db->error(); // Has keys 'code' and 'message'
                        }else{
                            $error = 0;
                        }
                    // echo $this->db->last_query()."<br/>";
                      
                        if($error){
                            $final_result[$val['id']][$i]['status'] = 'fail';
                            $final_result[$val['id']][$i]['msg'] = $error['message'];
                            $final_result[$val['id']][$i]['is_completed'] = 1;
                            $final_result[$val['id']][$i]['domain'] = $_SERVER['HTTP_HOST']; 
                        }else{
                            $final_result[$val['id']][$i]['status'] = 'success';
                            $final_result[$val['id'][$i]]['msg'] = '';
                            $final_result[$val['id']][$i]['is_completed'] = 1;
                            $final_result[$val['id']][$i]['domain'] = $_SERVER['HTTP_HOST'];
                        }
                    }
                }

            }

            // echo "<pre>";print_r($final_result);die;
            $this->save_migration_log($final_result);
        }
        
        // echo "<pre>";print_r($final_result);die;
    }

    public function get_migrations(){
         
        $domain = $_SERVER['HTTP_HOST'];
        $ch = curl_init('https://ourschoolerp.com/General/get_migrations'); 
        
        $data = array(
            'domain' => $domain
        );
        $payload = json_encode($data);
        
        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        
        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        
        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the POST request
        $result = curl_exec($ch);
        
        // Close cURL resource
        curl_close($ch);
        return $result;
    }

   
    public function save_migration_log($final_result){
         
        
        $ch = curl_init('https://ourschoolerp.com/General/save_migration_log'); 
        
        $data = array(
            'final_result' => $final_result
        );
        $payload = json_encode($data);
        
        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        
        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        
        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the POST request
        $result = curl_exec($ch);
        
        // Close cURL resource
        curl_close($ch);
        echo $result;
    }


    //new logic for database migration start
    public function runDataMigration($domain_id=""){


        // error_reporting(E_ALL);
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        
        $migrations = $this->get_migrations();

        // dd($migrations);

        // $p_id = $this->session->userdata['propdata']['pid'];
        // $data['p_id'] = $p_id;
        // $this->db->where('did',$domain_id);
        // $domainres = $this->db->get('domains')->row_array(); 
        // if(!empty($domainres['did'])){
            // $domain_id = $data['domain_id'] = $domain_id;
           
            // $sql = "SELECT * from sql_data_query where  status =0 and date(created_on) > (select date(data_updated_till) from domains where did = $domain_id)";
            // $rows_list = $this->db->query($sql)->result_array();
            $rows_list = $data['rows_list'] = json_decode($migrations,true);  

            if(empty($rows_list)){
                $strlog = "<table><tbody><tr><td>All data queries are already migrated</td></tr></tbody></table>";
            }else{ 
                //$domain_name = "tdb";
                // $domain_name = $domainres['domain_name'];
                // $dbsettings = check_database_subdomain($domain_name);
                
                // # MySQL with PDO_MYSQL 
                // $mysql_host = $dbsettings["hostname"];
                // $mysql_database = $dbsettings["database"];
                // $mysql_user = $dbsettings["username"];
                // $mysql_password = $dbsettings["password"];
                
                // $domain_db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
                // $domain_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // echo "<pre>";print_r($data['rows_list']);die;
                $strlog= "";
                if(isset($_POST['run']) || true) { //die('if'); 
                  //echo 'afs';  print_r($data['rows_list']); die;
                    $strlog= "<table width='100%' cellpadding=4 cellspacing=5 border=1>";
                    foreach($data['rows_list']['data']  as $key => $value){ //echo ('foreach');die;
                        $strlog .= "<tr>";
                        try { //die('try');
                            
                        //   $stmt = exec($value['query']);
                         $stmt = $this->db->query($value['query']);
                            // echo 'ffff'.$this->db->last_query();die;
                            // $strlog .= "<td style='color:darkgreen'>";
                            // $strlog .="Ok <br/> ".$value['query'];
                            // $strlog .= "</td>";
                            if($this->db->affected_rows() >=0 ){
                                $strlog .= "<td style='color:darkgreen'>";
                                $strlog .="success";
                                $strlog .= "</td>";
                            }
                            $db_error = $this->db->error();
                            if (!empty($db_error)) {
                                throw new Exception('Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                                return false; // unreachable retrun statement !!!
                            } 
                            return TRUE;
                        } catch (Exception $e) {
                            // this will not catch DB related errors. But it will include them, because this is more general. 
                            //  log_message('error: ',$e->getMessage());
                            $strlog .= "<td style='color:red'>";
                             $strlog .= $value['id']."<br/>".$value['query']."<br/>Error: ".$e->getMessage();//$this->db->errorInfo()[2];
                            $strlog .= "</td>";
                            // return;
                        }

                        // }catch(Exception $e){//die('catch');
                        //     $strlog .= "<td style='color:red'>";
                        //     $strlog .= $value['id']."<br/>".$value['query']."<br/>Error: ".$this->db->errorInfo()[2];
                        //     $strlog .= "</td>";
                        // }
                        // catch (PDOException $e) {
                        //     // bad connection
                        //     echo "\nPDOEXCEPTION not run for ".$mysql_database."\n";
                        //     print_r($this->db->errorInfo());   
                        // }

                        
                        $strlog .= "</tr>";
                    }
                    $strlog .= "</table>";        
                }else{
                    // echo 'else';die;
                }
            }
        // }

        //create an api later for domain status updatation
        
        // $domain = $_SERVER['HTTP_HOST'];
        // $update_db['status'] = 1;
        // $this->db->where('domain',$domain);
        // $this->db->update('domains',$update_db);
  
        echo $data["strlog"] = $strlog;  
        // echo $strlog;die;
        // $this->load->view('header',$data);
        // $this->load->view('run_create_tables_migration');
        // $this->load->view('footer');
    }
    //database migration end

}
