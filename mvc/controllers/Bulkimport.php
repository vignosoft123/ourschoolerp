<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bulkimport extends Admin_Controller
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
    function __construct()
    {
        parent::__construct();
        $this->load->model("teacher_m");
        $this->load->model("parents_m");
        $this->load->model("student_m");
        $this->load->model("user_m");
        $this->load->model("book_m");
        $this->load->model("studentrelation_m");
        $this->load->model("section_m");
        $this->load->model("classes_m");
        $this->load->model("studentextend_m");
        $this->load->model("subject_m");
        $this->load->model("studentgroup_m");
        $this->load->model("question_bank_m");
        $this->load->model("question_group_m");
        $this->load->model("question_level_m");
        $this->load->model("question_type_m");
        $this->load->model("question_answer_m");
        $this->load->model("question_option_m");
        $this->load->model("online_exam_question_m");
        $this->load->model("Setting_m");

        $this->load->library('csvimport');

        $language = $this->session->userdata('lang');
        $this->lang->load('bulkimport', $language);
    }

    public function index()
    {
        $this->data["subview"] = "bulkimport/index";
        $this->load->view('_layout_main', $this->data);
    }


    public function teacher_bulkimport()
    {
        if(isset($_FILES["csvFile"])) {
            $config['upload_path']   = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size']      = '2048';
            $config['file_name']     = $_FILES["csvFile"]['name'];
            $config['overwrite']     = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvFile")) {
                $this->session->set_flashdata('error', $this->lang->line('bulkimport_upload_fail'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data      = $this->upload->data();
                $file_path      = './uploads/csv/' . $file_data['file_name'];
                $column_headers = [
                    "Name",
                    "Designation",
                    "Dob",
                    "Gender",
                    "Religion",
                    "Email",
                    "Phone",
                    "Address",
                    "Jod",
                    "Username",
                    "Password"
                ];

                if($csv_array = @$this->csvimport->get_array($file_path, $column_headers)) {
                    if(customCompute($csv_array)) {
                        $msg     = "";
                        $i       = 1;
                        $csv_col = [];
                        foreach($csv_array as $row) {
                            if($i == 1) {
                                $csv_col = array_keys($row);
                            }
                            $match = array_diff($column_headers, $csv_col);
                            if(customCompute($match) <= 0) {
                                $array              = $this->arrayToPost($row);
                                $singleteacherCheck = $this->singleteacherCheck($array);

                                if($singleteacherCheck['status']) {
                                    $insert_data = [
                                        'name'            => $row['Name'],
                                        'designation'     => $row['Designation'],
                                        'dob'             => $this->trim_required_convertdate($row['Dob']),
                                        'sex'             => $row['Gender'],
                                        'religion'        => $row['Religion'],
                                        'email'           => $row['Email'],
                                        'phone'           => $row['Phone'],
                                        'address'         => $row['Address'],
                                        'jod'             => $this->trim_required_convertdate($row['Jod']),
                                        'username'        => $row['Username'],
                                        'password'        => $this->teacher_m->hash($row['Password']),
                                        'usertypeID'      => 2,
                                        'photo'           => 'default.png',
                                        "create_date"     => date("Y-m-d h:i:s"),
                                        "modify_date"     => date("Y-m-d h:i:s"),
                                        "create_userID"   => $this->session->userdata('loginuserID'),
                                        "create_username" => $this->session->userdata('username'),
                                        "create_usertype" => $this->session->userdata('usertype'),
                                        "active"          => 1,
                                    ];
                                    $this->usercreatemail($row['Email'], $row['Username'], $row['Password']);
                                    $this->teacher_m->insert_teacher($insert_data);
                                } else {
                                    $msg .= $i . ". " . $row['Name'] . " is not added! , ";
                                    $msg .= implode(' , ', $singleteacherCheck['error']);
                                    $msg .= ". <br/>";
                                }
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!");
                                redirect(base_url("bulkimport/index"));
                            }
                            $i++;
                        }
                        if($msg != "") {
                            $this->session->set_flashdata('msg', $msg);
                        }
                        $this->session->set_flashdata('success', $this->lang->line('bulkimport_success'));
                        redirect(base_url("bulkimport/index"));
                    } else {
                        $this->session->set_flashdata('error', $this->lang->line('bulkimport_data_not_found'));
                        redirect(base_url("bulkimport/index"));
                    }
                } else {
                    $this->session->set_flashdata('error', "Wrong csv file!");
                    redirect(base_url("bulkimport/index"));
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('bulkimport_select_file'));
            redirect(base_url("bulkimport/index"));
        }
    }

    public function parent_bulkimport()
    {
        if(isset($_FILES["csvParent"])) {
            $config['upload_path']   = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size']      = '2048';
            $config['file_name']     = $_FILES["csvParent"]['name'];
            $config['overwrite']     = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvParent")) {
                $this->session->set_flashdata('error', $this->lang->line('bulkimport_upload_fail'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data      = $this->upload->data();
                $file_path      = './uploads/csv/' . $file_data['file_name'];
                $column_headers = [
                    "Name",
                    "Father Name",
                    "Mother Name",
                    "Father Profession",
                    "Mother Profession",
                    "Email",
                    "Phone",
                    "Address",
                    "Username",
                    "Password"
                ];

                if($csv_array = @$this->csvimport->get_array($file_path, $column_headers)) {
                    if(customCompute($csv_array)) {
                        $i       = 1;
                        $msg     = "";
                        $csv_col = [];
                        foreach($csv_array as $row) {
                            if($i == 1) {
                                $csv_col = array_keys($row);
                            }
                            $match = array_diff($column_headers, $csv_col);
                            if(customCompute($match) <= 0) {
                                $array             = $this->arrayToPost($row);
                                $singleparentCheck = $this->singleparentCheck($array);
                                if($singleparentCheck['status']) {
                                    $insert_data = [
                                        'name'              => $row['Name'],
                                        'father_name'       => $row['Father Name'],
                                        'mother_name'       => $row['Mother Name'],
                                        'father_profession' => $row['Father Profession'],
                                        'mother_profession' => $row['Mother Profession'],
                                        'email'             => $row['Email'],
                                        'phone'             => $row['Phone'],
                                        'photo'             => 'default.png',
                                        'address'           => $row['Address'],
                                        'username'          => $row['Username'],
                                        'password'          => $this->parents_m->hash($row['Password']),
                                        'usertypeID'        => 2,
                                        'photo'             => 'default.png',
                                        "create_date"       => date("Y-m-d h:i:s"),
                                        "modify_date"       => date("Y-m-d h:i:s"),
                                        "create_userID"     => $this->session->userdata('loginuserID'),
                                        "create_username"   => $this->session->userdata('username'),
                                        "create_usertype"   => $this->session->userdata('usertype'),
                                        "active"            => 1,
                                    ];
                                    // For Email
                                    $this->usercreatemail($this->input->post('email'), $this->input->post('username'), $this->input->post('password'));
                                    $this->parents_m->insert_parents($insert_data);
                                } else {
                                    $msg .= $i . ". " . $row['Name'] . " is not added! , ";
                                    $msg .= implode(' , ', $singleparentCheck['error']);
                                    $msg .= ". <br/>";
                                }
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!");
                                redirect(base_url("bulkimport/index"));
                            }
                            $i++;
                        }
                        if($msg != "") {
                            $this->session->set_flashdata('msg', $msg);
                        }
                        $this->session->set_flashdata('success', $this->lang->line('bulkimport_success'));
                        redirect(base_url("bulkimport/index"));
                    } else {
                        $this->session->set_flashdata('error', $this->lang->line('bulkimport_data_not_found'));
                        redirect(base_url("bulkimport/index"));
                    }
                } else {
                    $this->session->set_flashdata('error', "Wrong csv file!");
                    redirect(base_url("bulkimport/index"));
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('bulkimport_select_file'));
            redirect(base_url("bulkimport/index"));
        }
    }

    public function user_bulkimport()
    {
        if(isset($_FILES["csvUser"])) {
            $config['upload_path']   = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size']      = '2048';
            $config['file_name']     = $_FILES["csvUser"]['name'];
            $config['overwrite']     = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvUser")) {
                $this->session->set_flashdata('error', $this->lang->line('bulkimport_upload_fail'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data      = $this->upload->data();
                $file_path      = './uploads/csv/' . $file_data['file_name'];
                $column_headers = [
                    "Name",
                    "Dob",
                    "Gender",
                    "Religion",
                    "Email",
                    "Phone",
                    "Address",
                    "Jod",
                    "Username",
                    "Password",
                    "Usertype"
                ];
                if($csv_array = @$this->csvimport->get_array($file_path, $column_headers)) {
                    if(customCompute($csv_array)) {
                        $i       = 1;
                        $msg     = "";
                        $csv_col = [];
                        foreach($csv_array as $row) {
                            if($i == 1) {
                                $csv_col = array_keys($row);
                            }
                            $match = array_diff($column_headers, $csv_col);
                            if(customCompute($match) <= 0) {
                                $array           = $this->arrayToPost($row);
                                $singleuserCheck = $this->singleuserCheck($array);
                                if($singleuserCheck['status']) {
                                    $dob         = $this->trim_required_convertdate($row['Dob']);
                                    $jod         = $this->trim_required_convertdate($row['Jod']);
                                    $insert_data = [
                                        'name'            => $row['Name'],
                                        'dob'             => $dob,
                                        'sex'             => $row['Gender'],
                                        'religion'        => $row['Religion'],
                                        'email'           => $row['Email'],
                                        'phone'           => $row['Phone'],
                                        'address'         => $row['Address'],
                                        'jod'             => $jod,
                                        'photo'           => 'default.png',
                                        'username'        => $row['Username'],
                                        'password'        => $this->user_m->hash($row['Password']),
                                        'usertypeID'      => $this->trim_check_usertype($row['Usertype']),
                                        "create_date"     => date("Y-m-d h:i:s"),
                                        "modify_date"     => date("Y-m-d h:i:s"),
                                        "create_userID"   => $this->session->userdata('loginuserID'),
                                        "create_username" => $this->session->userdata('username'),
                                        "create_usertype" => $this->session->userdata('usertype'),
                                        "active"          => 1,
                                    ];
                                    $this->user_m->insert_user($insert_data);
                                    $this->usercreatemail($this->input->post('email'), $this->input->post('username'), $this->input->post('password'));
                                } else {
                                    $msg .= $i . ". " . $row['Name'] . " is not added! , ";
                                    $msg .= implode(' , ', $singleuserCheck['error']);
                                    $msg .= ". <br/>";
                                }
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!");
                                redirect(base_url("bulkimport/index"));
                            }
                            $i++;
                        }
                        if($msg != "") {
                            $this->session->set_flashdata('msg', $msg);
                        }
                        $this->session->set_flashdata('success', $this->lang->line('bulkimport_success'));
                        redirect(base_url("bulkimport/index"));
                    } else {
                        $this->session->set_flashdata('error', $this->lang->line('bulkimport_data_not_found'));
                        redirect(base_url("bulkimport/index"));
                    }
                } else {
                    $this->session->set_flashdata('error', "Wrong csv file!");
                    redirect(base_url("bulkimport/index"));
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('bulkimport_select_file'));
            redirect(base_url("bulkimport/index"));
        }
    }

    public function book_bulkimport()
    {
        if(isset($_FILES["csvBook"])) {
            $config['upload_path']   = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size']      = '2048';
            $config['file_name']     = $_FILES["csvBook"]['name'];
            $config['overwrite']     = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvBook")) {
                $this->session->set_flashdata('error', $this->lang->line('bulkimport_upload_fail'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data      = $this->upload->data();
                $file_path      = './uploads/csv/' . $file_data['file_name'];
                $column_headers = ["Book", "Subject code", "Author", "Price", "Quantity", "Rack"];
                if($csv_array = @$this->csvimport->get_array($file_path, $column_headers)) {
                    if(customCompute($csv_array)) {
                        $i       = 1;
                        $msg     = "";
                        $csv_col = [];
                        foreach($csv_array as $row) {
                            if($i == 1) {
                                $csv_col = array_keys($row);
                            }

                            $match = array_diff($column_headers, $csv_col);
                            if(customCompute($match) <= 0) {
                                $array           = $this->arrayToPost($row);
                                $singlebookCheck = $this->singlebookCheck($array);

                                if($singlebookCheck['status']) {
                                    $insert_data = [
                                        'book'         => $row['Book'],
                                        'subject_code' => $row['Subject code'],
                                        'author'       => $row['Author'],
                                        'price'        => $row['Price'],
                                        'quantity'     => $row['Quantity'],
                                        'due_quantity' => 0,
                                        'rack'         => $row['Rack']
                                    ];
                                    $this->book_m->insert_book($insert_data);
                                } else {
                                    $msg .= $i . ". " . $row['Book'] . " is not added! , ";
                                    $msg .= implode(' , ', $singlebookCheck['error']);
                                    $msg .= ". <br/>";
                                }
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!");
                                redirect(base_url("bulkimport/index"));
                            }
                            $i++;
                        }
                        if($msg != "") {
                            $this->session->set_flashdata('msg', $msg);
                        }
                        $this->session->set_flashdata('success', $this->lang->line('bulkimport_success'));
                        redirect(base_url("bulkimport/index"));
                    } else {
                        $this->session->set_flashdata('error', $this->lang->line('bulkimport_data_not_found'));
                        redirect(base_url("bulkimport/index"));
                    }
                } else {
                    $this->session->set_flashdata('error', "Wrong csv file!");
                    redirect(base_url("bulkimport/index"));
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('bulkimport_error'));
            redirect(base_url("bulkimport/index"));
        }
    }

    // Method to clean and normalize CSV data
    private function cleanCsvData($data) {
        if(is_array($data)) {
            $cleaned = [];
            foreach($data as $key => $value) {
                $cleaned[$key] = $this->cleanCsvData($value);
            }
            return $cleaned;
        } else {
            // Remove BOM (Byte Order Mark) if present
            $data = preg_replace('/^\xEF\xBB\xBF/', '', $data);
            // Normalize whitespace - replace multiple spaces/tabs/newlines with single space
            $data = preg_replace('/\s+/', ' ', trim($data));
            // Remove non-printable characters except basic punctuation
            $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\xFF]/', '', $data);
            return trim($data);
        }
    }

    public function student_bulkimport()
    {
        if(isset($_FILES["csvStudent"])) {
            $config['upload_path']   = "./uploads/csv/";
            $config['allowed_types'] = '*';
            // $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size']      = '2048';
            $config['file_name']     = $_FILES["csvStudent"]['name'];
            $config['overwrite']     = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvStudent")) {
                $this->session->set_flashdata('error', $this->lang->line('bulkimport_upload_fail'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data      = $this->upload->data();
                $file_path      = './uploads/csv/' . $file_data['file_name'];
                $column_headers = [
                    "ID Card Name",
                    "Dob",
                    "Gender",
                    "Father Name",
                    "Email",
                    "Phone",
                    "Address",
                    "Village",
                    "Class",
                    "Section",
                    "BloodGroup",
                    "State",
                    "Group",
                    "OptionalSubject",
                    "ration_card",
                    "bank_name",
                    "account_no",
                    "ifsc_code",
                    "branch_name",
                    "admission_no",
                    "pen_number",
                    "child_id",
                    "father_aadhar",
                    "mother_aadhar",
                    "caste",
                    "sub_caste",
                    "mother_toungue",
                    "Religion",
                    "First Name",
                    "Last Name",
                    "Mother Name",
                    "Admission Date",
                    "Medium",
                    "RF ID",
                    "WhatsApp Number",
                    "Alternative Phone 2",
                    "Country",
                    "Aadhar Card Number",
                    "Identifying Mark 1",
                    "Identifying Mark 2",
                    "Remarks",
                    "Student Type",
                    "Joined Class"
                ];
                $file_ext = strtolower(pathinfo($file_data['file_name'], PATHINFO_EXTENSION));
                if ($file_ext === 'xlsx' || $file_ext === 'xls') {
                    $this->load->library('phpspreadsheet');
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_path);
                    $reader->setReadDataOnly(true);
                    $xl = $reader->load($file_path);
                    $sheetRows = $xl->getActiveSheet()->toArray(null, true, true, false);
                    $xlHeaders = array_shift($sheetRows);
                    $csv_array = [];
                    foreach ($sheetRows as $rowData) {
                        $rowAssoc = [];
                        foreach ($xlHeaders as $j => $hdr) {
                            $rowAssoc[trim((string)$hdr)] = isset($rowData[$j]) ? (string)$rowData[$j] : '';
                        }
                        if (array_filter($rowAssoc)) {
                            $csv_array[] = $rowAssoc;
                        }
                    }
                } else {
                    $csv_array = @$this->csvimport->get_array($file_path, $column_headers);
                }
                if($csv_array) {//echo 'if';die;
                    if(customCompute($csv_array)) {
                        $msg     = "";
                        $i       = 1;
                        $csv_col = [];
                        foreach($csv_array as $row) {
                            // Clean all CSV data to remove special characters and normalize whitespace
                            $row = $this->cleanCsvData($row);
                            
                            if($i == 1) {
                                $csv_col = array_keys($row);
                            }
                            $match = array_diff($column_headers, $csv_col);
                            if(customCompute($match) <= 0) {
                                $array              = $this->arrayToPost($row);
                                $singlestudentCheck = $this->singlestudentCheck($array);
                                if($singlestudentCheck['status']) {
                                    $classID         = $this->get_student_class($row['Class']);
                                    $sections        = $this->get_student_section($classID, $row['Section']);
                                    $group           = $this->get_student_group($row['Group']);
                                    $optionalSubject = $this->get_student_optional_subject($classID, $row['OptionalSubject']);
                                    $dob             = $this->trim_required_convertdate($row['Dob']);
                                    $admission_date  = !empty($row['Admission Date']) ? $this->trim_required_convertdate($row['Admission Date']) : date('Y-m-d');
                                    $joinedClassID   = !empty($row['Joined Class']) ? $this->get_student_class($row['Joined Class']) : 0;
                                    $studentTypeMap  = ['transport' => 1, 'hostel' => 2];
                                    $studentTypeText = strtolower(trim($row['Student Type']));
                                    $studentTypeVal  = isset($studentTypeMap[$studentTypeText]) ? $studentTypeMap[$studentTypeText] : 3;

                                    $insert_data = [
                                        'name'              => $row['ID Card Name'],
                                        'father_name'       => $row['Father Name'],
                                        'mother_name'       => $row['Mother Name'],
                                        'email'             => $row['Email'],
                                        'phone'             => $row['Phone'],
                                        'photo'             => 'default.png',
                                        'address'           => $row['Address'],
                                        'username'          => "parent".rand(100000,999999),
                                        'password'          => $this->parents_m->hash(1234567890),
                                        'usertypeID'        => 2,
                                        "create_date"       => date("Y-m-d h:i:s"),
                                        "modify_date"       => date("Y-m-d h:i:s"),
                                        "create_userID"     => $this->session->userdata('loginuserID'),
                                        "create_username"   => $this->session->userdata('username'),
                                        "create_usertype"   => $this->session->userdata('usertype'),
                                        "active"            => 1,
                                        "father_aadhar"     => $row['father_aadhar'],
                                        "mother_aadhar"     => $row['mother_aadhar'],
                                    ];
                                    // $parent_id = $this->parents_m->insert_parents($insert_data);
                                    $parent_id = $this->parents_m->insert_parents_with_id($insert_data);

                                    // $this->db->where('schoolyearID',$this->session->userdata('defaultschoolyearID'));
                                    $this->db->where('classesID',$classID);
                                    $this->db->where('sectionID',$sections->sectionID);
                                    // echo $this->db->last_query();die;;
                                     $cnt = $this->db->get('student')->num_rows();
                                     $auto_roll = $cnt+1;

                                     $settings = $this->Setting_m->get_setting();
                                    $excelAdmissionNo = trim($row['admission_no']);
                                    $randomAdmissionCode = !empty($excelAdmissionNo)
                                        ? $excelAdmissionNo
                                        : $this->getAdmissonNumber($settings);
                                    
                                    $insert_data = [
                                        'name'               => $row['ID Card Name'],
                                        'first_name'         => $row['First Name'],
                                        'last_name'          => $row['Last Name'],
                                        'dob'                => $dob,
                                        'sex'                => $row['Gender'],
                                        'religion'           => $row['Religion'],
                                        'email'              => $row['Email'],
                                        'phone'              => $row['Phone'],
                                        'photo'              => 'default.png',
                                        'address'            => $row['Address'],
                                        "bloodgroup"         => $row['BloodGroup'],
                                        "state"              => $row['State'],
                                        "country"            => !empty($row['Country']) ? $row['Country'] : 'India',
                                        'classesID'          => $classID,
                                        'sectionID'          => $sections->sectionID,
                                        'roll'               => $auto_roll,
                                        'username'           => $randomAdmissionCode,
                                        'password'           => $this->student_m->hash($row['Phone']),
                                        'usertypeID'         => 3,
                                        'parentID'           => $parent_id,
                                        'library'            => 0,
                                        'hostel'             => $studentTypeVal == 2 ? 1 : 0,
                                        'transport'          => $studentTypeVal == 1 ? 1 : 0,
                                        'createschoolyearID' => $this->session->userdata('defaultschoolyearID'),
                                        'schoolyearID'       => $this->session->userdata('defaultschoolyearID'),
                                        "create_date"        => date("Y-m-d h:i:s"),
                                        "modify_date"        => date("Y-m-d h:i:s"),
                                        "create_userID"      => $this->session->userdata('loginuserID'),
                                        "create_username"    => $this->session->userdata('username'),
                                        "create_usertype"    => $this->session->userdata('usertype'),
                                        "active"             => 1,
                                        'admission_date'     => $admission_date,
                                        'medium'             => !empty($row['Medium']) ? $row['Medium'] : 'English',
                                        'rf_id'              => $row['RF ID'],
                                        'alternative_phone1' => $row['WhatsApp Number'],
                                        'alternative_phone2' => $row['Alternative Phone 2'],
                                        'aadharCardNumber'   => $row['Aadhar Card Number'],
                                        'mole1'              => $row['Identifying Mark 1'],
                                        'mole2'              => $row['Identifying Mark 2'],
                                        'studentType'        => $studentTypeVal,
                                        'joined_class'       => $joinedClassID,
                                        'ration_card'        => $row['ration_card'],
                                        'bank_name'          => $row['bank_name'],
                                        'account_no'         => $row['account_no'],
                                        'ifsc_code'          => $row['ifsc_code'],
                                        'branch_name'        => $row['branch_name'],
                                        'villageID'          => $row['Village'],
                                        'registerNO'         => $randomAdmissionCode,
                                        'pen_number'         => $row['pen_number'],
                                        'child_id'           => $row['child_id'],
                                        'caste'              => $row['caste'],
                                        'sub_caste'          => $row['sub_caste'],
                                        'mother_toungue'     => $row['mother_toungue'],
                                    ];

                                    $this->usercreatemail($this->input->post('email'), $this->input->post('username'), $this->input->post('password'));
                                    $this->student_m->insert_student($insert_data);
                                    $studentID = $this->db->insert_id();

                                    $classes = $this->classes_m->general_get_single_classes(['classesID' => $classID]);
                                    $section = $this->section_m->general_get_single_section([
                                        'classesID' => $classID,
                                        'sectionID' => $sections->sectionID
                                    ]);

                                    if(customCompute($classes)) {
                                        $setClasses = $classes->classes;
                                    } else {
                                        $setClasses = NULL;
                                    }

                                    if(customCompute($section)) {
                                        $setSection = $section->section;
                                    } else {
                                        $setSection = NULL;
                                    }

                                    $studentReletion = $this->studentrelation_m->get_order_by_studentrelation([
                                        'srstudentID'    => $studentID,
                                        'srschoolyearID' => $this->session->userdata('defaultschoolyearID')
                                    ]);
                                    if(!customCompute($studentReletion)) {
                                        $arrayStudentRelation = [
                                            'srstudentID'         => $studentID,
                                            'srname'              => $row['ID Card Name'],
                                            'srclassesID'         => $classID,
                                            'srclasses'           => $setClasses,
                                            'srroll'              => $auto_roll,
                                            // 'srroll'              => $row['Roll'],
                                            // 'srregisterNO'        => $row['RegistrationNO'],
                                            'srregisterNO'        => $randomAdmissionCode,
                                            'srsectionID'         => $sections->sectionID,
                                            'srsection'           => $setSection,
                                            'srstudentgroupID'    => @$group->studentgroupID,
                                            'sroptionalsubjectID' => 0,//$optionalSubject->subjectID,
                                            'srschoolyearID'      => $this->session->userdata('defaultschoolyearID')
                                        ];
                                        $this->studentrelation_m->insert_studentrelation($arrayStudentRelation);
                                    } else {
                                        $arrayStudentRelation = [
                                            'srname'              => $row['ID Card Name'],
                                            'srclassesID'         => $classID,
                                            'srclasses'           => $setClasses,
                                            // 'srroll'              => $row['Roll'],
                                            'srroll'              => $auto_roll,
                                            // 'srregisterNO'        => $row['RegistrationNO'],
                                            'srregisterNO'        => $randomAdmissionCode,
                                            'srsectionID'         => $sections->sectionID,
                                            'srsection'           => $setSection,
                                            'srstudentgroupID'    => @$group->studentgroupID,
                                            'sroptionalsubjectID' => 0,//$optionalSubject->subjectID,
                                        ];
                                        $this->studentrelation_m->update_studentrelation_with_multicondition($arrayStudentRelation, [
                                            'srstudentID'    => $studentID,
                                            'srschoolyearID' => $this->session->userdata('defaultschoolyearID')
                                        ]);
                                    }

                                    $studentExtend = $this->studentextend_m->get_single_studentextend(['studentID' => $studentID]);
                                    if(!customCompute($studentExtend)) {
                                        $studentExtendArray = [
                                            'studentID'                 => $studentID,
                                            'studentgroupID'            => @$group->studentgroupID,
                                            'optionalsubjectID'         => $optionalSubject->subjectID,
                                            'extracurricularactivities' => NULL,
                                            'remarks'                   => $row['Remarks'],
                                        ];
                                        $this->studentextend_m->insert_studentextend($studentExtendArray);
                                    } else {
                                        $studentExtendArray = [
                                            'studentID'                 => $studentID,
                                            'studentgroupID'            => @$group->studentgroupID,
                                            'optionalsubjectID'         => $optionalSubject->subjectID,
                                            'extracurricularactivities' => NULL,
                                            'remarks'                   => $row['Remarks'],
                                        ];
                                        $this->studentextend_m->update_studentextend($studentExtendArray, $studentExtend->studentextendID);
                                    }
                                } else {
                                    $msg .= $i . ". " . $row['ID Card Name'] . " is not added! , ";
                                    $msg .= implode(' , ', $singlestudentCheck['error']);
                                    $msg .= ". <br/>";
                                }
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!!");
                                redirect(base_url("bulkimport/index"));
                            }
                            $i++;
                        }
                        if($msg != "") {
                            $this->session->set_flashdata('msg', $msg);
                        }
                        $this->session->set_flashdata('success', 'Success');
                        redirect(base_url("bulkimport/index"));
                    } else {
                        $this->session->set_flashdata('error', $this->lang->line('bulkimport_data_not_found'));
                        redirect(base_url("bulkimport/index"));
                    }
                } else {//echo 'else';die;
                    $this->session->set_flashdata('error', "Wrong csv file..!");
                    redirect(base_url("bulkimport/index"));
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('bulkimport_select_file'));
            redirect(base_url("bulkimport/index"));
        }
    }

    public function question_bulkimport()
    {
        if(isset($_FILES["csvQuestion"])) {
            $config['upload_path']   = "./uploads/csv/";
            $config['allowed_types'] = 'text/plain|text/csv|csv';
            $config['max_size']      = '2048';
            $config['file_name']     = $_FILES["csvQuestion"]['name'];
            $config['overwrite']     = TRUE;
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload("csvQuestion")) {
                $this->session->set_flashdata('error', $this->lang->line('bulkimport_upload_fail'));
                redirect(base_url("bulkimport/index"));
            } else {
                $file_data      = $this->upload->data();
                $file_path      = './uploads/csv/' . $file_data['file_name'];
                $column_headers = [
                    "Question Group",
                    "Difficulty Level",
                    "Question",
                    "Explanation",
                    "Hints",
                    "Mark",
                    "Question Type",
                    "Total Option",
                    "Options",
                    "Correct Answer"
                ];

                if($csv_array = @$this->csvimport->get_array($file_path, $column_headers)) {
                    if(customCompute($csv_array)) {
                        $i       = 1;
                        $msg     = "";
                        $csv_col = [];
                        foreach($csv_array as $row) {
                            if($i == 1) {
                                $csv_col = array_keys($row);
                            }
                            $match = array_diff($column_headers, $csv_col);
                            if(customCompute($match) <= 0) {
                                $array               = $this->arrayToPost($row);
                                $singleQuestionCheck = $this->singleQuestionCheck($array);
                                if($singleQuestionCheck['status']) {
                                    $levelID      = $this->get_id($row['Difficulty Level'], 'level');
                                    $groupID      = $this->get_id($row['Question Group'], 'group');
                                    $typeID       = $this->get_id($row['Question Type'], 'type');
                                    $insert_data  = [
                                        'question'          => $row['Question'],
                                        'explanation'       => $row['Explanation'],
                                        'levelID'           => $levelID,
                                        'groupID'           => $groupID,
                                        'totalOption'       => $row['Total Option'],
                                        'typeNumber'        => $typeID,
                                        'mark'              => $row['Mark'],
                                        'hints'             => $row['Hints'],
                                        "create_date"       => date("Y-m-d h:i:s"),
                                        "modify_date"       => date("Y-m-d h:i:s"),
                                        "create_userID"     => $this->session->userdata('loginuserID'),
                                        "create_usertypeID" => $this->session->userdata('usertypeID'),
                                    ];
                                    $questionID   = $this->question_bank_m->insert_question_bank($insert_data);
                                    $questionType = $this->get_id($row['Question Type'], 'type');
                                    $totalOption  = $row['Total Option'];
                                    $options      = explode(',', $row['Options']);
                                    $totalAnswers = explode(',', $row['Correct Answer']);
                                    $answers      = [];
                                    foreach($options as $key => $option) {
                                        foreach($totalAnswers as $singleAnswer) {
                                            if($singleAnswer == $option) {
                                                $answers[$key] = $singleAnswer;
                                            }
                                        }
                                    }
                                    if($questionType == 1 || $questionType == 2) {

                                        $getQuestionOptions = pluck($this->question_option_m->get_order_by_question_option(['questionID' => $questionID]), 'optionID');

                                        if(!customCompute($getQuestionOptions)) {
                                            foreach(range(1, 10) as $optionID) {
                                                $data                 = [
                                                    'name'       => '',
                                                    'questionID' => $questionID
                                                ];
                                                $getQuestionOptions[] = $this->question_option_m->insert_question_option($data);
                                            }
                                        }

                                        foreach($options as $key => $option) {
                                            if($option == '') {
                                                $totalOption--;
                                                continue;
                                            }

                                            $data = [
                                                'name' => $option,
                                            ];

                                            $this->question_option_m->update_question_option($data, $getQuestionOptions[$key]);

                                            if(array_key_exists($key, $answers)) {
                                                $ansData = [
                                                    'questionID' => $questionID,
                                                    'optionID'   => $getQuestionOptions[$key],
                                                    'typeNumber' => $questionType
                                                ];
                                                $this->question_answer_m->insert_question_answer($ansData);
                                            }
                                        }

                                        if($totalOption != $row['Total Option']) {
                                            $this->question_bank_m->update_question_bank(['totalOption' => $totalOption], $questionID);
                                        }
                                    } elseif($questionType == 3) {
                                        foreach($answers as $answer) {
                                            if(empty($answer)) {
                                                $totalOption--;
                                                continue;
                                            }
                                            $ansData = [
                                                'questionID' => $questionID,
                                                'text'       => $answer,
                                                'typeNumber' => $questionType
                                            ];
                                            $this->question_answer_m->insert_question_answer($ansData);

                                        }
                                        if($totalOption != $row['Total Option']) {
                                            $this->question_bank_m->update_question_bank(['totalOption' => $totalOption], $questionID);
                                        }
                                    }

                                } else {
                                    $msg .= $i . ". " . $row['Question'] . " is not added! , ";
                                    $msg .= implode(' , ', $singleQuestionCheck['error']);
                                    $msg .= ". <br/>";
                                }
                            } else {
                                $this->session->set_flashdata('error', "Wrong csv file!");
                                redirect(base_url("bulkimport/index"));
                            }
                            $i++;
                        }
                        if($msg != "") {
                            $this->session->set_flashdata('msg', $msg);
                            $this->session->set_flashdata('error', $this->lang->line('bulkimport_error'));
                            redirect(base_url("bulkimport/index"));
                        }
                        $this->session->set_flashdata('success', $this->lang->line('bulkimport_success'));
                        redirect(base_url("bulkimport/index"));
                    } else {
                        $this->session->set_flashdata('error', $this->lang->line('bulkimport_data_not_found'));
                        redirect(base_url("bulkimport/index"));
                    }
                } else {
                    $this->session->set_flashdata('error', "Wrong csv file!");
                    redirect(base_url("bulkimport/index"));
                }
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('bulkimport_select_file'));
            redirect(base_url("bulkimport/index"));
        }
    }

    // Single  Validation Check
    private function singleteacherCheck( $array )
    {
        $name        = $this->trim_required_string_maxlength_minlength_Check($array['name'], 60);
        $designation = $this->trim_required_string_maxlength_minlength_Check($array['designation'], 128);
        $dob         = $this->trim_required_date_Check($array['dob']);
        $gender      = $this->trim_required_string_maxlength_minlength_Check($array['gender'], 10);
        $religion    = $this->trim_required_string_maxlength_minlength_Check($array['religion'], 25);
        $email       = $this->trim_check_unique_email($array['email'], 40);
        $phone       = $this->trim_required_string_maxlength_minlength_Check($array['phone'], 25, 5);
        $address     = $this->trim_required_string_maxlength_minlength_Check($array['address'], 200);
        $jod         = $this->trim_required_date_Check($array['jod']);
        $username    = $this->trim_check_unique_username($array['username'], 40);
        $password    = $this->trim_required_string_maxlength_minlength_Check($array['password'], 40);

        $retArray['status'] = TRUE;
        if($name && $designation && $dob && $gender && $religion && $email && $phone && $address && $jod && $username && $password) {
            $retArray['status'] = TRUE;
        } else {
            $retArray['status'] = FALSE;
            if(!$name) {
                $retArray['error']['name'] = 'Invalid Student Name';
            }
            if(!$designation) {
                $retArray['error']['designation'] = 'Invalid Designation';
            }
            if(!$dob) {
                $retArray['error']['dob'] = 'Invalid Date Of Birth';
            }
            if(!$gender) {
                $retArray['error']['gender'] = 'Invalid Gender';
            }
            if(!$religion) {
                $retArray['error']['religion'] = 'Invalid Riligion';
            }
            if(!$email) {
                $retArray['error']['email'] = 'Invalid email address or email address already exists.';
            }
            if(!$phone) {
                $retArray['error']['phone'] = 'Invalid Phone Number';
            }
            if(!$address) {
                $retArray['error']['address'] = 'Invalid Address';
            }
            if(!$jod) {
                $retArray['error']['jod'] = 'Invalid Date Of Birth';
            }
            if(!$username) {
                $retArray['error']['username'] = 'Invalid username or username already exists';
            }
            if(!$password) {
                $retArray['error']['password'] = 'Invalid Password';
            }
        }
        return $retArray;
    }

    private function singleparentCheck( $array )
    {
        $name              = $this->trim_required_string_maxlength_minlength_Check($array['name'], 60);
        $father_name       = $this->trim_required_string_maxlength_minlength_Check($array['father_name'], 60);
        $mother_name       = $this->trim_required_string_maxlength_minlength_Check($array['mother_name'], 40);
        $father_profession = $this->trim_required_string_maxlength_minlength_Check($array['father_profession'], 40);
        $mother_profession = $this->trim_required_string_maxlength_minlength_Check($array['mother_profession'], 40);
        $email             = $this->trim_check_unique_email($array['email'], 40);
        $phone             = $this->trim_required_string_maxlength_minlength_Check($array['phone'], 25, 5);
        $address           = $this->trim_required_string_maxlength_minlength_Check($array['address'], 200);
        $username          = $this->trim_check_unique_username($array['username'], 40, 4);
        $password          = $this->trim_required_string_maxlength_minlength_Check($array['password'], 40, 4);

        $retArray['status'] = TRUE;
        if($name && $father_name && $mother_name && $father_profession && $mother_profession && $email && $phone && $address && $username && $password) {
            $retArray['status'] = TRUE;
        } else {
            $retArray['status'] = FALSE;
            if(!$name) {
                $retArray['error']['name'] = 'Invalid Parent Name';
            }
            if(!$father_name) {
                $retArray['error']['father_name'] = 'Invalid Father Name';
            }
            if(!$mother_name) {
                $retArray['error']['mother_name'] = 'Invalid Mother Name';
            }
            if(!$father_profession) {
                $retArray['error']['father_profession'] = 'Invalid Father Profession';
            }
            if(!$mother_profession) {
                $retArray['error']['mother_profession'] = 'Invalid Mother Profession';
            }
            if(!$email) {
                $retArray['error']['email'] = 'Invalid email address or email address already exists.';
            }
            if(!$phone) {
                $retArray['error']['phone'] = 'Invalid Phone Number';
            }
            if(!$address) {
                $retArray['error']['address'] = 'Invalid Address';
            }
            if(!$username) {
                $retArray['error']['username'] = 'Invalid username or username already exists';
            }
            if(!$password) {
                $retArray['error']['password'] = 'Invalid Password';
            }
        }
        return $retArray;
    }

    private function singleuserCheck( $array )
    {
        $name     = $this->trim_required_string_maxlength_minlength_Check($array['name'], 60);
        $dob      = $this->trim_required_date_Check($array['dob']);
        $gender   = $this->trim_required_string_maxlength_minlength_Check($array['gender'], 10);
        $religion = $this->trim_required_string_maxlength_minlength_Check($array['religion'], 25);
        $email    = $this->trim_check_unique_email($array['email'], 40);
        $phone    = $this->trim_required_string_maxlength_minlength_Check($array['phone'], 25, 5);
        $address  = $this->trim_required_string_maxlength_minlength_Check($array['address'], 200);
        $jod      = $this->trim_required_date_Check($array['jod']);
        $username = $this->trim_check_unique_username($array['username'], 40);
        $password = $this->trim_required_string_maxlength_minlength_Check($array['password'], 40);
        $usertype = $this->trim_check_usertype($array['usertype']);

        $retArray['status'] = TRUE;
        if($name && $dob && $gender && $religion && $email && $phone && $address && $jod && $username && $password && $usertype) {
            $retArray['status'] = TRUE;
        } else {
            $retArray['status'] = FALSE;
            if(!$name) {
                $retArray['error']['name'] = 'Invalid User Name';
            }
            if(!$dob) {
                $retArray['error']['dob'] = 'Invalid Date Of Birth';
            }
            if(!$gender) {
                $retArray['error']['gender'] = 'Invalid Gender';
            }
            if(!$religion) {
                $retArray['error']['religion'] = 'Invalid Riligion';
            }
            if(!$email) {
                $retArray['error']['email'] = 'Invalid email address or email address already exists.';
            }
            if(!$phone) {
                $retArray['error']['phone'] = 'Invalid Phone Number';
            }
            if(!$address) {
                $retArray['error']['address'] = 'Invalid Address';
            }
            if(!$jod) {
                $retArray['error']['jod'] = 'Invalid Date Of Birth';
            }
            if(!$username) {
                $retArray['error']['username'] = 'Invalid username or username already exists';
            }
            if(!$password) {
                $retArray['error']['password'] = 'Invalid Password';
            }
            if(!$usertype) {
                $retArray['error']['usertype'] = 'Invalid Usertype';
            }
        }
        return $retArray;
    }

    private function singlebookCheck( $array )
    {
        $book         = $this->trim_required_string_maxlength_minlength_Check($array['book'], 60);
        $price        = $this->trim_required_int_maxlength_minlength_Check($array['price'], 10);
        $rack         = $this->trim_required_string_maxlength_minlength_Check($array['rack'], 60);
        $author       = $this->trim_required_string_maxlength_minlength_Check($array['author'], 100);
        $quantity     = $this->trim_required_int_maxlength_minlength_Check($array['quantity'], 10);
        $subject_code = $this->trim_required_string_maxlength_minlength_Check($array['subject_code'], 20);

        $retArray['status'] = TRUE;
        if($book && $price && $rack && $author && $quantity && $subject_code) {
            $books = $this->book_m->get_single_book([
                'LOWER(book)'         => strtolower($book),
                'LOWER(author)'       => strtolower($author),
                'LOWER(subject_code)' => strtolower($subject_code)
            ]);
            if(customCompute($books)) {
                $retArray['status']        = FALSE;
                $retArray['error']['book'] = 'Book already exits';
            } else {
                $retArray['status'] = TRUE;
            }
        } else {
            $retArray['status'] = FALSE;
            if(!$book) {
                $retArray['error']['book'] = 'Invalid Book Name';
            }
            if(!$price) {
                $retArray['error']['price'] = 'Price are not valid';
            }
            if(!$rack) {
                $retArray['error']['rack'] = 'Rack are not valid';
            }
            if(!$author) {
                $retArray['error']['author'] = 'Author are not valid';
            }
            if(!$quantity) {
                $retArray['error']['quantity'] = 'Quantity are not valid';
            }
            if(!$subject_code) {
                $retArray['error']['subject_code'] = 'Subject Code are not valid';
            }
        }
        return $retArray;
    }


    public function check_unique_admission_no($admission_no=""){
        if(!empty($admission_no)){
            $this->db->where('registerNO',$admission_no);
            $cnt =  $this->db->get('student')->num_rows();
            if($cnt > 0){
                return false;
            }else{
                return true;
            }
        }
       
    }

    public function singlestudentCheck( $array )
    {
        $name            = $this->trim_required_string_maxlength_minlength_Check($array['name'], 60);
        $dob             = $this->trim_required_date_Check($array['dob']);
        // $admission_no             = $this->check_unique_admission_no($array['admission_no']);
        $gender          = $this->trim_required_string_maxlength_minlength_Check($array['gender'], 10);
        // $religion        = $this->trim_required_string_maxlength_minlength_Check($array['religion'], 25);
        // $email           = $this->trim_check_unique_email($array['email'], 40);
        $phone_exists           = $this->trim_check_unique_phone($array['phone'],$array['name'], 40);
        $phone           = $this->trim_required_string_maxlength_minlength_Check($array['phone'], 25, 5);
        $address         = $this->trim_required_string_maxlength_minlength_Check($array['address'], 200);
        $class           = $this->trim_required_class_Check($array['class']);
        $section         = $this->trim_required_section_Check($array['class'], $array['section']);
        // $username        = $this->trim_check_unique_username($array['username'], 40);
        // $password        = $this->trim_required_string_maxlength_minlength_Check($array['password'], 40);
        $roll            = $this->trim_roll_Check($array);
        // $bloodgroup      = $this->trim_required_string_maxlength_minlength_Check($array['bloodgroup'], 5);
        // $state           = $this->trim_required_string_maxlength_minlength_Check($array['state'], 128);
        // $country         = $this->trim_required_string_maxlength_minlength_Check($array['country'], 128);
        $registrationno  = $this->trim_required_registration_Check($array['class'],$array['registrationno']);
        // $group           = $this->trim_group_Check($array['group'], 40);
        // $optionalsubject = $this->trim_optionalsubject_Check($array['optionalsubject'], $array['class']);

        $checkStudent = $this->trim_check_section_student($array);

        $retArray['status'] = FALSE;
        // if($name && $dob && $gender && $phone && $address && $class && $section && $roll && $registrationno && $checkStudent) {
        if($name && $dob && $gender && $phone && $phone_exists && $address && $class && $section && $checkStudent) {
            $retArray['status'] = TRUE;
        } else {

            // if(!$admission_no) {
            //     $retArray['error']['admission_no'] = 'Admission no already exists';
            // }

            if(!$name) {
                $retArray['error']['name'] = 'Invalid Teacher Name';
            }
            if(!$dob) {
                $retArray['error']['dob'] = 'Invalid Date Of Birth';
            }
            if(!$gender) {
                $retArray['error']['gender'] = 'Invalid Gender';
            }
            // if(!$religion) {
            //     $retArray['error']['religion'] = 'Invalid Riligion';
            // }
            
            // if(!$email) {
            //     $retArray['error']['email'] = 'Invalid email address or email address already exists.';
            // }

            if(!$phone_exists) {
                $retArray['error']['phone'] = 'Invalid Phone Number or phone Number already exists.';
            }

            if(!$phone) {
                $retArray['error']['phone'] = 'Invalid Phone Number';
            }
            if(!$address) {
                $retArray['error']['address'] = 'Invalid Address';
            }
            if(!$class) {
                $retArray['error']['class'] = 'Invalid Class';
            }
            if(!$section) {
                $retArray['error']['section'] = 'Invalid Section';
            }
            // if(!$username) {
            //     $retArray['error']['username'] = 'Invalid username or username already exists';
            // }
            // if(!$password) {
            //     $retArray['error']['password'] = 'Invalid Password';
            // }
            // if(!$roll) {
            //     $retArray['error']['roll'] = 'Invalid roll or roll already exists in class';
            // }
            // if(!$bloodgroup) {
            //     $retArray['error']['bloodgroup'] = 'Invalid bloodgroup';
            // }
            // if(!$state) {
            //     $retArray['error']['state'] = 'Invalid state';
            // }
            // if(!$country) {
            //     $retArray['error']['country'] = 'Invalid country';
            // }
            // if(!$registrationno) {
            //     $retArray['error']['registrationno'] = 'Invalid registration no or registration no already exists';
            // }
            // if(!$group) {
            //     $retArray['error']['group'] = 'Invalid Group';
            // }
            // if(!$optionalsubject) {
            //     $retArray['error']['optionalsubject'] = 'Invalid OptionalSubject Subject';
            // }
            if(!$checkStudent) {
                // Provide more specific error message based on what failed
                $class_check = $this->get_student_class($array['class']);
                if($class_check == 0) {
                    $retArray['error']['checkStudent'] = 'Class not found: "' . $array['class'] . '"';
                } else {
                    $section_check = $this->get_student_section($class_check, $array['section']);
                    if($section_check == 0) {
                        $retArray['error']['checkStudent'] = 'Section not found: "' . $array['section'] . '" in class "' . $array['class'] . '"';
                    } else {
                        $retArray['error']['checkStudent'] = 'Section capacity exceeded for "' . $array['section'] . '" in class "' . $array['class'] . '"';
                    }
                }
            }
        }
        return $retArray;
    }

    private function singleQuestionCheck( $array )
    {
        $groupID            = $this->trim_required_exists_Check($array['question_group'], 'group');
        $levelID            = $this->trim_required_exists_Check($array['difficulty_level'], 'level');
        $question           = $this->trim_required_string_maxlength_minlength_Check($array['question'], 1000);
        $explanation        = $this->trim_string_maxlength_minlength_Check($array['explanation'], 1000);
        $hints              = $this->trim_string_maxlength_minlength_Check($array['hints'], 1000);
        $mark               = $this->trim_required_string_maxlength_minlength_Check($array['mark'], 40);
        $typeNumber         = $this->trim_required_exists_Check($array['question_type'], 'type');
        $totalOption        = $this->trim_int_maxlength_minlength_Check($array['total_option'], 10, 1);
        $options            = $this->trim_unique_required_string_maxlength_minlength_Check($array['options'], $array, 'options', 10, 1);
        $correctAnswers     = $this->trim_unique_required_string_maxlength_minlength_Check($array['correct_answer'], $array, 'answers', 10, 1);
        $retArray['status'] = TRUE;
        if($groupID && $levelID && $question && $explanation && $hints && $mark && $typeNumber && $totalOption && $options && $correctAnswers) {
            $retArray['status'] = TRUE;
        } else {
            $retArray['status'] = FALSE;
            if(!$groupID) {
                $retArray['error']['groupID'] = 'Invalid Group';
            }
            if(!$levelID) {
                $retArray['error']['levelID'] = 'Invalid Level';
            }
            if(!$question) {
                $retArray['error']['question'] = 'Invalid Question';
            }
            if(!$explanation) {
                $retArray['error']['explanation'] = 'Invalid Explanation';
            }
            if(!$hints) {
                $retArray['error']['hints'] = 'Invalid hints';
            }
            if(!$mark) {
                $retArray['error']['mark'] = 'Invalid Marking';
            }
            if(!$typeNumber) {
                $retArray['error']['typeNumber'] = 'Invalid Question Type';
            }
            if(!$totalOption) {
                $retArray['error']['totalOption'] = 'Invalid Total Option';
            }
            if(!$options) {
                $retArray['error']['options'] = 'Invalid Options';
            }
            if(!$correctAnswers) {
                $retArray['error']['correctAnswers'] = 'Invalid Correct Answers';
            }

        }
        return $retArray;
    }

    // Student Valiadtion Check
    private function trim_check_section_student( $array )
    {
        // Normalize class and section names using our improved logic
        $classes = preg_replace('/\s+/', ' ', trim($array['class']));
        $classes = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\xFF]/', '', $classes);
        $classes = strtolower(trim($classes));
        
        $section = preg_replace('/\s+/', ' ', trim($array['section']));
        $section = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\xFF]/', '', $section);
        $section = strtolower(trim($section));

        if($classes && $section) {
            // First try exact match for class
            $class_result = $this->classes_m->general_get_single_classes(['LOWER(classes)' => $classes]);
            
            // If no exact match, try to find similar class names
            if(!customCompute($class_result)) {
                $all_classes = $this->classes_m->general_get_classes();
                if(customCompute($all_classes)) {
                    foreach($all_classes as $class) {
                        $db_class = preg_replace('/\s+/', ' ', trim($class->classes));
                        $db_class = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\xFF]/', '', $db_class);
                        $db_class = strtolower(trim($db_class));
                        
                        if($classes === $db_class) {
                            $class_result = $class;
                            break;
                        }
                    }
                }
            }
            
            if(customCompute($class_result)) {
                // First try exact match for section
                $section_result = $this->section_m->general_get_single_section([
                    'classesID'      => $class_result->classesID,
                    'LOWER(section)' => $section
                ]);
                
                // If no exact match, try to find similar section names
                if(!customCompute($section_result)) {
                    $all_sections = $this->section_m->general_get_order_by_section(['classesID' => $class_result->classesID]);
                    if(customCompute($all_sections)) {
                        foreach($all_sections as $sect) {
                            $db_section = preg_replace('/\s+/', ' ', trim($sect->section));
                            $db_section = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\xFF]/', '', $db_section);
                            $db_section = strtolower(trim($db_section));
                            
                            if($section === $db_section) {
                                $section_result = $sect;
                                break;
                            }
                        }
                    }
                }
                
                if(customCompute($section_result)) {
                    $capacity     = $section_result->capacity;
                    $schoolyearID = $this->session->userdata('defaultschoolyearID');
                    $students     = $this->studentrelation_m->general_get_order_by_student([
                        'srclassesID'    => $section_result->classesID,
                        'srsectionID'    => $section_result->sectionID,
                        'srschoolyearID' => $schoolyearID
                    ]);
                    $totalStudent = customCompute($students);
                    
                    if($totalStudent < $capacity) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                }
            }
        }
        
        return FALSE;
    }

    private function trim_required_registration_Check($classes, $data )
    {
        $classes = strtolower(trim($classes));
        $data = trim($data);
        /*if($data) {
            $student = $this->studentrelation_m->general_get_single_student(["srregisterNO" => $data]);
            if(customCompute($student)) {
                return FALSE;
            } else {
                return $data;
            }
        }
        return FALSE;*/
        if($classes && $data) {
            $result = $this->classes_m->general_get_single_classes(['LOWER(classes)' => $classes]);
            if(customCompute($result)) {
                $student = $this->studentrelation_m->general_get_single_student(["srregisterNO" => $data,"srclassesID"=>$result->classesID]);
                if(customCompute($student)) {
                    return FALSE;
                } else {
                    return $data;
                }
            }
        }
        return FALSE;
    }

    private function trim_roll_Check( $data )
    {
        $roll    = trim($data['roll']);
        $classes = strtolower(trim($data['class']));
        if($roll && $classes) {
            $sections = $this->get_student_section($classes, $data['section']);
            $result = $this->classes_m->general_get_single_classes(['LOWER(classes)' => $classes]);
            if(customCompute($result)) {
                $schoolyearID = $this->session->userdata('defaultschoolyearID');
                $student      = $this->studentrelation_m->general_get_order_by_student([
                    "srroll"         => $roll,
                    "srclassesID"    => $result->classesID,
                    "srschoolyearID" => $schoolyearID,
                    "srsectionID" => $sections->sectionID
                ]);
                if(customCompute($student)) {
                    return FALSE;
                } else {
                    return $roll;
                }
            }
        }
        return FALSE;
    }

    private function trim_optionalsubject_Check( $subject, $classes )
    {
        if($subject == '') {
            $array = [
                'subjectID' => 0,
                'subject'   => ''
            ];
            $array = (object)$array;
            return $array;
        } else {
            $subject = strtolower(trim($subject));
            $classes = strtolower(trim($classes));
            if($subject && $classes) {
                $result = $this->classes_m->general_get_single_classes(['LOWER(classes)' => $classes]);
                if(customCompute($result)) {
                    $result = $this->subject_m->general_get_single_subject([
                        'classesID'      => $result->classesID,
                        'type'           => 0,
                        'LOWER(subject)' => $subject
                    ]);
                    if(customCompute($result)) {
                        return $result;
                    }
                }
            }
            return FALSE;
        }
    }

    private function trim_required_class_Check( $classes )
    {
        // Normalize the class name
        $classes = preg_replace('/\s+/', ' ', trim($classes)); // Replace multiple spaces with single space
        $classes = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $classes); // Remove non-printable characters
        $classes = strtolower(trim($classes));
        
        if($classes) {
            // First try exact match
            $result = $this->classes_m->general_get_single_classes(['LOWER(classes)' => $classes]);
            if(customCompute($result)) {
                return $result;
            }
            
            // If no exact match, try to find similar class names
            $all_classes = $this->classes_m->general_get_classes();
            if(customCompute($all_classes)) {
                foreach($all_classes as $class) {
                    $db_class = preg_replace('/\s+/', ' ', trim($class->classes));
                    $db_class = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $db_class);
                    $db_class = strtolower(trim($db_class));
                    
                    if($classes === $db_class) {
                        return $class;
                    }
                }
            }
        }
        return FALSE;
    }

    private function trim_group_Check( $group )
    {
        $group1 = strtolower(trim($group));
        $group2 = trim($group);
        if($group1 && $group2) {
            $result1 = $this->studentgroup_m->get_single_studentgroup(['group' => $group1]);
            $result2 = $this->studentgroup_m->get_single_studentgroup(['group' => $group2]);
            if(customCompute($result1)) {
                return $result1;
            } elseif(customCompute($result2)) {
                return $result2;
            }
        }
        return FALSE;
    }

    private function trim_required_section_Check( $classes, $section )
    {
        // Normalize class and section names
        $classes = preg_replace('/\s+/', ' ', trim($classes));
        $classes = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $classes);
        $classes = strtolower(trim($classes));
        
        $section = preg_replace('/\s+/', ' ', trim($section));
        $section = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $section);
        $section = strtolower(trim($section));
        
        if($classes && $section) {
            // First find the class
            $class_result = $this->classes_m->general_get_single_classes(['LOWER(classes)' => $classes]);
            
            if(!customCompute($class_result)) {
                // Try to find similar class names
                $all_classes = $this->classes_m->general_get_classes();
                if(customCompute($all_classes)) {
                    foreach($all_classes as $class) {
                        $db_class = preg_replace('/\s+/', ' ', trim($class->classes));
                        $db_class = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $db_class);
                        $db_class = strtolower(trim($db_class));
                        
                        if($classes === $db_class) {
                            $class_result = $class;
                            break;
                        }
                    }
                }
            }
            
            if(customCompute($class_result)) {
                // Now find the section
                $section_result = $this->section_m->general_get_single_section([
                    'classesID'      => $class_result->classesID,
                    'LOWER(section)' => $section
                ]);
                
                if(customCompute($section_result)) {
                    return $section_result;
                }
                
                // Try to find similar section names
                $all_sections = $this->section_m->general_get_order_by_section(['classesID' => $class_result->classesID]);
                if(customCompute($all_sections)) {
                    foreach($all_sections as $sect) {
                        $db_section = preg_replace('/\s+/', ' ', trim($sect->section));
                        $db_section = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $db_section);
                        $db_section = strtolower(trim($db_section));
                        
                        if($section === $db_section) {
                            return $sect;
                        }
                    }
                }
            }
        }
        return FALSE;
    }

    // User Validation Check
    private function trim_check_usertype( $usertype )
    {
        $usertype = strtolower(trim($usertype));
        if($usertype) {
            $result = $this->usertype_m->get_single_usertype(['LOWER(usertype)' => $usertype]);
            if(customCompute($result)) {
                $usertypeID   = $result->usertypeID;
                $blockuserArr = [1, 2, 3, 4];
                if(in_array($usertypeID, $blockuserArr)) {
                    return FALSE;
                } else {
                    return $usertypeID;
                }
            }
        }
        return FALSE;
    }

    // Username and Email Validation Check
    private function trim_check_unique_username( $data )
    {
        $data = (string)trim($data);
        if($data) {
            $tables = ['student', 'parents', 'teacher', 'user', 'systemadmin'];
            $i      = 0;
            $array  = [];
            foreach($tables as $table) {
                $user = $this->student_m->get_username($table, ["username" => $data]);
                if(customCompute($user)) {
                    $array['permition'][$i] = 'no';
                } else {
                    $array['permition'][$i] = 'yes';
                }
                $i++;
            }

            if(in_array('no', $array['permition'])) {
                return FALSE;
            } else {
                return $data;
            }
        }
        return FALSE;
    }

    private function trim_check_unique_email( $data )
    {
        $data = trim($data);
        if(filter_var($data, FILTER_VALIDATE_EMAIL)) {
            $tables = ['student', 'parents', 'teacher', 'user', 'systemadmin'];
            $array  = [];
            $i      = 0;
            foreach($tables as $table) {
                $user = $this->student_m->get_username($table, ["email" => $data]);
                if(customCompute($user)) {
                    $array['permition'][$i] = 'no';
                } else {
                    $array['permition'][$i] = 'yes';
                }
                $i++;
            }
            if(in_array('no', $array['permition'])) {
                return FALSE;
            } else {
                return $data;
            }
        }
        return FALSE;
    }

    //srinu
    private function trim_check_unique_phone( $data,$name="" )
    {
        $data = trim($data);
        // if(filter_var($data, FILTER_VALIDATE_EMAIL)) {
            $tables = ['student', 'parents', 'teacher', 'user', 'systemadmin'];
            $array  = [];
            $i      = 0;
            // foreach($tables as $table) {
                $user = $this->student_m->get_username($table="student", ["phone" => $data, "name" => $name, "schoolyearID" => $this->session->userdata('defaultschoolyearID')]);
                if(customCompute($user)) {
                    $array['permition'][$i] = 'no';
                } else {
                    $array['permition'][$i] = 'yes';
                }
                $i++;
            // }
            if(in_array('no', $array['permition'])) {
                return FALSE;
            } else {
                return $data;
            }
        // }
        return FALSE;
    }

    // Default Function All Import Validation Check
    public function arrayToPost( $data )
    {
        if(is_array($data)) {
            $post = [];
            foreach($data as $key => $item) {
                $key        = preg_replace('/\s+/', '_', $key);
                $key        = strtolower($key);
                $post[$key] = $item;
            }
            return $post;
        }
        return [];
    }

    private function trim_required_string_maxlength_minlength_Check( $data, $maxlength = 10, $minlength = 0 )
    {
        $data       = (string)trim($data);
        $dataLength = strlen($data);

        if(($dataLength == 0) || ($dataLength > $maxlength) || ($dataLength < $minlength)) {
            return FALSE;
        } else {
            if(is_string($data)) {
                return $data;
            }
            return FALSE;
        }
    }

    private function trim_required_int_maxlength_minlength_Check( $data, $maxlength = 10, $minlength = 0 )
    {
        $data       = (int)trim($data);
        $dataLength = strlen($data);

        if(($dataLength == 0) || ($dataLength > $maxlength) || ($dataLength < $minlength)) {
            return FALSE;
        } else {
            if(is_int($data)) {
                return $data;
            }
            return FALSE;
        }
    }

    private function trim_required_date_Check( $date )
    {
        $date = trim($date);
        if($date) {
            $date = str_replace('/', '-', $date);
            return date("Y-m-d", strtotime($date));
        }
        return FALSE;
    }

    private function trim_required_convertdate( $date )
    {
        $date = trim($date);
        if($date) {
            // Excel stores dates as numeric serials (e.g. 33227 = 20/12/1990)
            // This happens when reading xlsx with setReadDataOnly(true) — styles not loaded
            if (is_numeric($date) && (float)$date > 1 && (float)$date < 2958466) {
                $unixTs = ((float)$date - 25569) * 86400;
                return date('Y-m-d', (int)$unixTs);
            }
            $date = str_replace('/', '-', $date);
            return date("Y-m-d", strtotime($date));
        }
        return 0;
    }

    // For Only Student Import Check Query
    public function get_student_class( $classes )
    {
        // Normalize the class name - remove extra spaces, tabs, non-breaking spaces, and special chars
        $classes = preg_replace('/\s+/', ' ', trim($classes)); // Replace multiple spaces with single space
        $classes = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $classes); // Remove non-printable characters
        $classes = strtolower(trim($classes));
        
        if($classes) {
            // First try exact match
            $result = $this->classes_m->general_get_single_classes(['LOWER(classes)' => $classes]);
            if(customCompute($result)) {
                return $result->classesID;
            }
            
            // If no exact match, try to find similar class names
            $all_classes = $this->classes_m->general_get_classes();
            if(customCompute($all_classes)) {
                foreach($all_classes as $class) {
                    $db_class = preg_replace('/\s+/', ' ', trim($class->classes));
                    $db_class = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $db_class);
                    $db_class = strtolower(trim($db_class));
                    
                    if($classes === $db_class) {
                        return $class->classesID;
                    }
                }
            }
        }
        return 0;
    }

    public function get_student_section( $classesID, $section )
    {
        // Normalize the section name - remove extra spaces, tabs, non-breaking spaces, and special chars
        $section = preg_replace('/\s+/', ' ', trim($section)); // Replace multiple spaces with single space
        $section = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $section); // Remove non-printable characters
        $section = strtolower(trim($section));
        
        if($classesID && $section) {
            // First try exact match
            $result = $this->section_m->general_get_single_section([
                'classesID'      => $classesID,
                'LOWER(section)' => $section
            ]);
            if(customCompute($result)) {
                return $result;
            }
            
            // If no exact match, try to find similar section names
            $all_sections = $this->section_m->general_get_order_by_section(['classesID' => $classesID]);
            if(customCompute($all_sections)) {
                foreach($all_sections as $sect) {
                    $db_section = preg_replace('/\s+/', ' ', trim($sect->section));
                    $db_section = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $db_section);
                    $db_section = strtolower(trim($db_section));
                    
                    if($section === $db_section) {
                        return $sect;
                    }
                }
            }
        }
        return 0;
    }

    public function get_student_group( $group )
    {
        $group1 = strtolower(trim($group));
        $group2 = trim($group);
        if($group1 && $group2) {
            $result1 = $this->studentgroup_m->get_single_studentgroup(['group' => $group1]);
            $result2 = $this->studentgroup_m->get_single_studentgroup(['group' => $group2]);
            if(customCompute($result1)) {
                return $result1;
            } elseif(customCompute($result2)) {
                return $result2;
            }
        }
        $array = [
            'studentgroupID' => 0,
            'group'          => ''
        ];
        $array = (object)$array;
        return $array;
    }

    public function get_student_optional_subject( $classesID, $subject )
    {
        $subject = strtolower(trim($subject));
        if($subject) {
            $result = $this->subject_m->general_get_single_subject([
                'classesID'      => $classesID,
                'type'           => 0,
                'LOWER(subject)' => $subject
            ]);
            if(customCompute($result)) {
                return $result;
            }
        }
        $array = [
            'subjectID' => 0,
            'subject'   => ''
        ];
        $array = (object)$array;
        return $array;
    }

    public function get_student_country( $country )
    {
        $countryArr = $this->getAllCountry();
        $key        = array_search($country, $countryArr);
        return ($key) ? $key : 0;
    }

    // Question import only
    private function trim_required_exists_Check( $data, $type )
    {
        $data = trim($data);
        if($data == '') {
            return false;
        }
        return $this->get_id($data, $type);
    }

    private function get_id( $data, $type )
    {
        if($type == 'group') {
            $group = $this->question_group_m->get_single_question_group(['title' => $data]);
            if($group) {
                return $group->questionGroupID;
            }
            return false;
        } elseif($type == 'level') {
            $level = $this->question_level_m->get_single_question_level(['name' => $data]);
            if($level) {
                return $level->questionLevelID;
            }
            return false;

        } else {
            $type = $this->question_type_m->get_single_question_type(['name' => $data]);
            if($type) {
                return $type->typeNumber;
            }
            return false;
        }
    }

    private function trim_string_maxlength_minlength_Check( $data, $maxlength = 10 )
    {
        $data       = (string)trim($data);
        $dataLength = strlen($data);
        if(($dataLength > $maxlength)) {
            return FALSE;
        }
        return true;
    }

    private function trim_int_maxlength_minlength_Check( $data, $maxlength = 10, $minlength = 1 )
    {
        $data       = (int)trim($data);
        $dataLength = strlen($data);

        if(($dataLength > $maxlength) || ($dataLength < $minlength)) {
            return FALSE;
        } else {
            if(is_int($data)) {
                return $data;
            }
            return FALSE;
        }
    }

    private function trim_unique_required_string_maxlength_minlength_Check( $data, $array, $type, $maxlength = 10, $minlength = 1 )
    {

        $data = trim($data);
        if(empty($data)) {
            return false;
        }

        //newArray is either options or answers
        $newArray = explode(',', $data);

        if(customCompute($newArray) > $maxlength) {
            return false;
        }

        $totalOption  = $array['total_option'];
        $questionType = $this->get_id($array['question_type'], 'type');
        if($type == 'options') {
            if((customCompute($newArray) != (int)$totalOption) || (customCompute($newArray) !== customCompute(array_unique($newArray)))) {
                return false;
            }
        } else {
            if($questionType == 1) {
                if(customCompute($newArray) > 1) {
                    return false;
                }
            } elseif($questionType == 2) {
                if((customCompute($newArray) !== customCompute(array_unique($newArray)) || ((customCompute($newArray)) > $totalOption))) {
                    return false;
                }

            } else {
                if(customCompute($newArray) < 1) {
                    return false;
                }
            }
        }
        return $data;
    }

    public function get_village_id(){

    }

    public function download_sample_student()
    {
        $this->load->library('phpspreadsheet');

        $spreadsheet = $this->phpspreadsheet->spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Students');

        $columns = [
            'A'  => 'ID Card Name',        'B'  => 'Dob',                 'C'  => 'Gender',
            'D'  => 'Father Name',         'E'  => 'Email',               'F'  => 'Phone',
            'G'  => 'Address',             'H'  => 'Village',             'I'  => 'Class',
            'J'  => 'Section',             'K'  => 'BloodGroup',          'L'  => 'State',
            'M'  => 'Group',               'N'  => 'OptionalSubject',     'O'  => 'ration_card',
            'P'  => 'bank_name',           'Q'  => 'account_no',          'R'  => 'ifsc_code',
            'S'  => 'branch_name',         'T'  => 'admission_no',        'U'  => 'pen_number',
            'V'  => 'child_id',            'W'  => 'father_aadhar',       'X'  => 'mother_aadhar',
            'Y'  => 'caste',               'Z'  => 'sub_caste',           'AA' => 'mother_toungue',
            'AB' => 'Religion',            'AC' => 'First Name',          'AD' => 'Last Name',
            'AE' => 'Mother Name',         'AF' => 'Admission Date',      'AG' => 'Medium',
            'AH' => 'RF ID',               'AI' => 'WhatsApp Number',     'AJ' => 'Alternative Phone 2',
            'AK' => 'Country',             'AL' => 'Aadhar Card Number',  'AM' => 'Identifying Mark 1',
            'AN' => 'Identifying Mark 2',  'AO' => 'Remarks',             'AP' => 'Student Type',
            'AQ' => 'Joined Class',
        ];

        $headerStyle = [
            'font'    => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'    => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
        ];

        foreach ($columns as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $sheet->getColumnDimension($col)->setWidth(20);
        }

        $sampleValues = [
            'A'  => 'Madhukanth',          'B'  => '20/12/1990',          'C'  => 'Male',
            'D'  => 'John',                'E'  => 'madhu@email.com',     'F'  => '8801676667',
            'G'  => 'Dhaka Road',          'H'  => 'Madhapur',            'I'  => '1ST CLASS',
            'J'  => 'A',                   'K'  => 'A+',                  'L'  => 'Telangana',
            'M'  => 'Arts',                'N'  => 'Bangla',              'O'  => '111222333',
            'P'  => 'SBI',                 'Q'  => '123456789',           'R'  => 'SBIN0001234',
            'S'  => 'Hyderabad Branch',    'T'  => 'abc100',              'U'  => '1234567890',
            'V'  => 'CHILD001',            'W'  => '123456789012',        'X'  => '987654321098',
            'Y'  => 'OC',                  'Z'  => 'BC-B',                'AA' => 'Telugu',
            'AB' => 'Hindu',               'AC' => 'Madhu',               'AD' => 'Kanth',
            'AE' => 'Lakshmi',             'AF' => '13/06/2026',          'AG' => 'Telugu',
            'AH' => 'RF001',               'AI' => '9999999999',          'AJ' => '8888888888',
            'AK' => 'India',               'AL' => '111122223333',        'AM' => 'Mole on left hand',
            'AN' => 'Scar on right knee',  'AO' => 'Good student',        'AP' => 'Day Scholar',
            'AQ' => '1ST CLASS',
        ];

        // Columns that must stay as text (long numbers, IDs, codes)
        $textColumns = ['F', 'Q', 'U', 'V', 'W', 'X', 'AI', 'AJ', 'AL'];
        $textFormat   = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT;
        foreach ($textColumns as $col) {
            $sheet->getStyle($col . '2:' . $col . '1000')
                  ->getNumberFormat()->setFormatCode($textFormat);
        }

        foreach ($sampleValues as $col => $val) {
            if (in_array($col, $textColumns)) {
                $sheet->setCellValueExplicit($col . '2', $val,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            } else {
                $sheet->setCellValue($col . '2', $val);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="sample_student.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function getAdmissonNumber($objSettings)
	{
		$schoolyearID = $this->session->userdata('defaultschoolyearID');
		$count = $this->db->where('srschoolyearID', $schoolyearID)->count_all_results('studentrelation');
		$num = $count + 1;
		return $objSettings->schoolCode . sprintf("%04d", $num);
	}


}