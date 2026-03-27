<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admissionenquiry extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("admission_enquiry_m");
        $this->load->model("classes_m");
        $this->load->model("usertype_m");
        $this->load->model("systemadmin_m");
        $this->load->model("teacher_m");
        $this->load->model("user_m");
        $language = $this->session->userdata('lang');
        $this->lang->load('admissionenquiry', $language);
    }

    private function _permissionManager($module, $action) {
        return null;
    }

    protected function rules() {
        $rules = array(
            array(
                'field' => 'name',
                'label' => $this->lang->line("admissionenquiry_name"),
                'rules' => 'trim|required|xss_clean|max_length[60]'
            ),
            array(
                'field' => 'phone',
                'label' => $this->lang->line("admissionenquiry_phone"),
                'rules' => 'trim|required|xss_clean|max_length[25]'
            ),
            array(
                'field' => 'email',
                'label' => $this->lang->line("admissionenquiry_email"),
                'rules' => 'trim|max_length[40]|valid_email|xss_clean'
            ),
            array(
                'field' => 'address',
                'label' => $this->lang->line("admissionenquiry_address"),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'description',
                'label' => $this->lang->line("admissionenquiry_description"),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'note',
                'label' => $this->lang->line("admissionenquiry_note"),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'date',
                'label' => $this->lang->line("admissionenquiry_date"),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'next_follow_up_date',
                'label' => $this->lang->line("admissionenquiry_next_follow_up_date"),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'assigned_usertypeID',
                'label' => $this->lang->line("admissionenquiry_assigned"),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'assigned_userID',
                'label' => $this->lang->line("admissionenquiry_assigned"),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'reference',
                'label' => $this->lang->line("admissionenquiry_reference"),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'source',
                'label' => $this->lang->line("admissionenquiry_source"),
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'classesID',
                'label' => $this->lang->line("admissionenquiry_class"),
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'num_child',
                'label' => $this->lang->line("admissionenquiry_num_child"),
                'rules' => 'trim|numeric|xss_clean'
            ),
            array(
                'field' => 'fee_particulars',
                'label' => $this->lang->line("admissionenquiry_fee_particulars"),
                'rules' => 'trim|xss_clean'
            )
        );
        return $rules;
    }

    public function index() {
        $this->data['headerassets'] = array(
            'css' => array(
                'assets/select2/css/select2.css',
                'assets/select2/css/select2-bootstrap.css',
                'assets/datepicker/datepicker.css'
            ),
            'js' => array(
                'assets/select2/select2.js',
                'assets/datepicker/datepicker.js'
            )
        );

        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $this->data['admission_enquiries'] = $this->admission_enquiry_m->get_order_by_admission_enquiry(array('schoolyearID' => $schoolyearID));
        $this->data['classes'] = $this->classes_m->get_classes();
        $this->data['usertypes'] = $this->usertype_m->get_usertype();
        
        // Prepare list of users for display in table
        $this->data['all_users'] = $this->getAllUsers();

        if ($_POST) {
            $rules = $this->rules();
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $retArray = $this->form_validation->error_array();
                $retArray['status'] = FALSE;
                header('Content-Type: application/json');
                echo json_encode($retArray);
                exit;
            } else {
                $array = array();
                foreach ($rules as $rule) {
                    $array[$rule['field']] = $this->input->post($rule['field']);
                }

                if ($array['date']) {
                    $array['date'] = date("Y-m-d", strtotime($array['date']));
                }
                if ($array['next_follow_up_date']) {
                    $array['next_follow_up_date'] = date("Y-m-d", strtotime($array['next_follow_up_date']));
                }

                $enquiryID = $this->input->post('enquiryID');
                $result = false;
                if ($enquiryID) {
                    $array['modify_date'] = date("Y-m-d H:i:s");
                    $this->admission_enquiry_m->update_admission_enquiry($array, $enquiryID);
                    $result = true;
                    $this->session->set_flashdata('success', 'Update Success');
                } else {
                    $array['schoolyearID'] = $schoolyearID;
                    $array['create_date'] = date("Y-m-d H:i:s");
                    $array['modify_date'] = date("Y-m-d H:i:s");
                    $array['create_userID'] = $this->session->userdata('loginuserID') ? $this->session->userdata('loginuserID') : 0;
                    $array['create_usertypeID'] = $this->session->userdata('usertypeID') ? $this->session->userdata('usertypeID') : 0;
                    $result = $this->admission_enquiry_m->insert_admission_enquiry($array);
                    if($result) $this->session->set_flashdata('success', 'Add Success');
                }

                header('Content-Type: application/json');
                if ($result) {
                    echo json_encode(['status' => TRUE]);
                } else {
                    $db_error = $this->db->error();
                    echo json_encode([
                        'status' => FALSE, 
                        'error' => 'Database error: ' . $db_error['message'],
                        'query' => $this->db->last_query()
                    ]);
                }
                exit;
            }
        }

        $this->data["subview"] = "admissionenquiry/index";
        $this->load->view('_layout_main', $this->data);
    }

    private function getAllUsers() {
        $mapArray = [];
        $systemadmins = $this->systemadmin_m->get_systemadmin();
        foreach ($systemadmins as $s) $mapArray[1][$s->systemadminID] = $s->name;
        $teachers = $this->teacher_m->get_teacher();
        foreach ($teachers as $t) $mapArray[2][$t->teacherID] = $t->name;
        $users = $this->user_m->get_user();
        foreach ($users as $u) $mapArray[$u->usertypeID][$u->userID] = $u->name;
        return $mapArray;
    }

    public function usercall() {
        $usertypeID = $this->input->post('id');
        echo "<option value='0'>", $this->lang->line("admissionenquiry_select_user"),"</option>";
        if ((int)$usertypeID) {
            if ($usertypeID == 1) {
                $users = $this->systemadmin_m->get_systemadmin();
                foreach ($users as $v) echo "<option value='$v->systemadminID'>$v->name</option>";
            } elseif ($usertypeID == 2) {
                $users = $this->teacher_m->get_teacher();
                foreach ($users as $v) echo "<option value='$v->teacherID'>$v->name</option>";
            } else {
                $users = $this->user_m->get_order_by_user(array('usertypeID' => $usertypeID));
                foreach ($users as $v) echo "<option value='$v->userID'>$v->name</option>";
            }
        }
    }

    public function edit() {
        $id = $this->input->post('id');
        if ((int)$id) {
            $enquiry = $this->admission_enquiry_m->get_single_admission_enquiry(array('enquiryID' => $id));
            if ($enquiry) {
                if ($enquiry->date) $enquiry->date = date("d-m-Y", strtotime($enquiry->date));
                if ($enquiry->next_follow_up_date) $enquiry->next_follow_up_date = date("d-m-Y", strtotime($enquiry->next_follow_up_date));
                echo json_encode(['status' => TRUE, 'enquiry' => $enquiry]);
            } else {
                echo json_encode(['status' => FALSE]);
            }
        } else {
            echo json_encode(['status' => FALSE]);
        }
    }

    public function delete() {
        $id = htmlentities(escapeString($this->uri->segment(3)));
        if ((int)$id) {
            $this->admission_enquiry_m->delete_admission_enquiry($id);
            $this->session->set_flashdata('success', 'Delete Success');
        }
        redirect(base_url("admissionenquiry/index"));
    }

    public function print_preview() {
        $id = htmlentities(escapeString($this->uri->segment(3)));
        if((int)$id) {
            $this->data['admission_enquiry'] = $this->admission_enquiry_m->get_single_admission_enquiry(array('enquiryID' => $id));
            if(customCompute($this->data['admission_enquiry'])) {
                $enquiry = $this->data['admission_enquiry'];
                
                // Get Class Name
                $this->data['class'] = $this->classes_m->get_single_classes(array('classesID' => $enquiry->classesID));
                
                // Get Assigned User Name
                $this->data['assigned_user'] = "";
                if($enquiry->assigned_usertypeID == 1) {
                    $user = $this->systemadmin_m->get_single_systemadmin(array('systemadminID' => $enquiry->assigned_userID));
                    $this->data['assigned_user'] = $user ? $user->name : "";
                } elseif($enquiry->assigned_usertypeID == 2) {
                    $user = $this->teacher_m->get_teacher($enquiry->assigned_userID);
                    $this->data['assigned_user'] = $user ? $user->name : "";
                } elseif($enquiry->assigned_usertypeID == 3) {
                    $user = $this->studentrelation_m->get_single_student(array('srstudentID' => $enquiry->assigned_userID));
                    $this->data['assigned_user'] = $user ? $user->name : "";
                } elseif($enquiry->assigned_usertypeID == 4) {
                    $user = $this->parents_m->get_parents($enquiry->assigned_userID);
                    $this->data['assigned_user'] = $user ? $user->name : "";
                } else {
                    $user = $this->user_m->get_single_user(array('userID' => $enquiry->assigned_userID));
                    $this->data['assigned_user'] = $user ? $user->name : "";
                }

                $this->data['usertypes'] = pluck($this->usertype_m->get_usertype(), 'usertype', 'usertypeID');
                
                $this->load->view('admissionenquiry/print_preview', $this->data);
            } else {
                $this->data["subview"] = "error";
                $this->load->view('_layout_main', $this->data);
            }
        } else {
            $this->data["subview"] = "error";
            $this->load->view('_layout_main', $this->data);
        }
    }
}
