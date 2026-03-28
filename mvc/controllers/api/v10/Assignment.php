<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Assignment extends Api_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('section_m');
        $this->load->model('classes_m');
        $this->load->model('assignment_m');
        $this->load->model('assignmentanswer_m');
        $this->load->model('subject_m');
        $this->load->model('studentrelation_m');
        $this->load->model('whatsapp_m');
    }

    public function index_get($classID = 0, $sectionID = 0, $subjectID = 0, $date = 0) 
    {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        if($this->session->userdata('usertypeID') == 3) {
            $classID = $this->data['myclass'];
        }

        $this->retdata['classes'] = $this->classes_m->get_classes();
        if((int)$classID) {
            $fetchClasses = pluck($this->retdata['classes'], 'classesID', 'classesID');
            if(isset($fetchClasses[$classID])) {
                $this->retdata['classesID'] = $classID;
                $this->retdata['sections'] = pluck($this->section_m->general_get_order_by_section(['classesID' => $classID]), 'section', 'sectionID');

                $assignments = $this->assignment_m->join_get_assignment($classID, $schoolyearID, $sectionID, $subjectID, $date);

                foreach ($assignments as &$assignment) {
                    // Decode sectionID JSON string
                    $sectionIDs = json_decode($assignment->sectionID, true);
                    $sectionNames = [];
                    if (is_array($sectionIDs)) {
                        foreach ($sectionIDs as $sID) {
                            if (isset($this->retdata['sections'][$sID])) {
                                $sectionNames[] = $this->retdata['sections'][$sID];
                            }
                        }
                    }
                    $assignment->section_names = $sectionNames;
                    
                    // Remove unneccessary documentation data
                    unset($assignment->create_date);
                    unset($assignment->modify_date);
                    unset($assignment->create_userID);
                    unset($assignment->create_usertypeID);
                }
                $this->retdata['homework'] = $assignments;
            } else {
                $this->retdata['classesID'] = 0;
                $this->retdata['homework'] = [];
            }
        } else {
            $this->retdata['classesID'] = 0;
            $this->retdata['homework'] = []; 
        }

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);
    }

    public function view_get($id = 0, $url = 0)
    {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        if((int)$id && (int)($url)) {
            $this->retdata['classesID'] = $url;
            $fetchClasses = pluck($this->classes_m->get_classes(), 'classesID', 'classesID');
            if(isset($fetchClasses[$url])) {
                $assignment = $this->assignment_m->get_single_assignment(array('assignmentID' => $id, 'classesID' => $url, 'schoolyearID' => $schoolyearID));
                if(customCompute($assignment)) {
                    $this->retdata['assignment'] = $assignment;
                    $this->retdata['assignmentanswers'] = $this->assignmentanswer_m->join_get_assignmentanswer($id, $schoolyearID);
                } else {
                    $this->retdata['assignment'] = null;
                    $this->retdata['assignmentanswers'] = [];
                }
            } else {
                $this->retdata['assignment'] = null;
                $this->retdata['assignmentanswers'] = [];
            }
        } else {
            $this->retdata['classesID'] = $url;
            $this->retdata['assignment'] = null;
            $this->retdata['assignmentanswers'] = [];
        }

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);
    }

    public function add_post() 
    {
        if(($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
            $classesID = $this->post('classesID');
            $sectionID = $this->post('sectionID'); // Array of sectionIDs
            $deadlinedate = $this->post('deadlinedate');
            $rows = $this->post('rows'); // Array of items: [{title, description, subjectID}]

            if (!$classesID || !customCompute($sectionID) || !$deadlinedate || !customCompute($rows)) {
                $this->response(['status' => false, 'message' => 'Missing required fields'], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            foreach ($rows as $key => $row) {
                $data = array(
                    "title"         => $row['title'],
                    "description"   => $row['description'],
                    "deadlinedate"  => date("Y-m-d", strtotime($deadlinedate)),
                    'subjectID'     => $row['subjectID'],
                    "usertypeID"    => $this->session->userdata('usertypeID'),
                    "userID"        => $this->session->userdata('loginuserID'),
                    "classesID"     => $classesID,
                    "schoolyearID"  => $this->session->userdata('defaultschoolyearID'),
                    'assignusertypeID' => 0,
                    'assignuserID'  => 0,
                    'sectionID'     => json_encode(array_map('strval', $sectionID))
                );

                // File handling (assuming one file can be sent or we handle files properly per row in mobile)
                // For simplicity, we match the web logic where the first row gets the file if provided
                if ($key == 0 && isset($_FILES['file'])) {
                    if($this->fileupload()) {
                        $data['originalfile'] = $this->upload_data['file']['original_file_name'];
                        $data['file'] = $this->upload_data['file']['file_name'];
                    }
                }

                $this->assignment_m->insert_assignment($data);
            }

            $this->response(['status' => true, 'message' => 'Homework added successfully'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Unauthorized or invalid school year'], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function edit_post() 
    {
        $id = $this->post('assignmentID');
        if(($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
            if(!(int)$id) {
                $this->response(['status' => false, 'message' => 'Assignment ID is required'], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            $assignment = $this->assignment_m->get_single_assignment(array('assignmentID' => $id));
            if(!customCompute($assignment)) {
                $this->response(['status' => false, 'message' => 'Assignment not found'], REST_Controller::HTTP_NOT_FOUND);
                return;
            }

            $array = array(
                "title"         => $this->post("title") ? $this->post("title") : $assignment->title,
                "description"   => $this->post("description") ? $this->post("description") : $assignment->description,
                "deadlinedate"  => $this->post("deadlinedate") ? date("Y-m-d", strtotime($this->post("deadlinedate"))) : $assignment->deadlinedate,
                "subjectID"     => $this->post("subjectID") ? $this->post("subjectID") : $assignment->subjectID,
                "classesID"     => $this->post("classesID") ? $this->post("classesID") : $assignment->classesID,
            );

            if($this->post('sectionID')) {
                $array['sectionID'] = json_encode(array_map('strval', $this->post('sectionID')));
            }

            if(isset($_FILES['file'])) {
                if($this->fileupload()) {
                    $array['originalfile'] = $this->upload_data['file']['original_file_name'];
                    $array['file'] = $this->upload_data['file']['file_name'];
                }
            }

            $this->assignment_m->update_assignment($array, $id);
            $this->response(['status' => true, 'message' => 'Homework updated successfully'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Unauthorized'], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function delete_post() 
    {
        $id = $this->post('assignmentID');
        if(($this->data['siteinfos']->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
            if(!(int)$id) {
                $this->response(['status' => false, 'message' => 'Assignment ID is required'], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            $assignment = $this->assignment_m->get_single_assignment(array('assignmentID' => $id));
            if(customCompute($assignment)) {
                if($assignment->file != '') {
                    if(file_exists(FCPATH.'uploads/images/'.$assignment->file)) {
                        unlink(FCPATH.'uploads/images/'.$assignment->file);
                    }
                }
                $this->assignment_m->delete_assignment($id);
                $this->response(['status' => true, 'message' => 'Homework deleted successfully'], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => false, 'message' => 'Assignment not found'], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['status' => false, 'message' => 'Unauthorized'], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function get_form_data_post() 
    {
        $classesID = $this->post('classesID');
        $this->retdata['classes'] = pluck($this->classes_m->get_classes(), 'classes', 'classesID');
        
        if($classesID) {
            $this->retdata['sections'] = pluck($this->section_m->general_get_order_by_section(['classesID' => $classesID]), 'section', 'sectionID');
            $this->retdata['subjects'] = pluck($this->subject_m->general_get_order_by_subject(['classesID' => $classesID]), 'subject', 'subjectID');
        } else {
            $this->retdata['sections'] = [];
            $this->retdata['subjects'] = [];
        }

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);
    }

    public function send_whatsapp_post() 
    {
        $classesID = $this->post('classesID');
        $sectionID = $this->post('sectionID');
        $deadlinedate = $this->post('deadlinedate');

        if (empty($classesID) || empty($sectionID)) {
            $this->response(['status' => false, 'message' => 'Class and Section are required.'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $students = $this->studentrelation_m->get_order_by_student(['srclassesID' => $classesID, 'srsectionID' => $sectionID, 'srschoolyearID' => $schoolyearID]);
        $assignments = $this->assignment_m->join_get_assignment($classesID, $schoolyearID, $sectionID, 0, $deadlinedate);
        $section = $this->section_m->get_section($sectionID);
        $class = $this->classes_m->get_classes($classesID);

        if (customCompute($students) && customCompute($assignments)) {
            $mediaUrl = '';
            if (isset($_FILES['homework_file']) && $_FILES['homework_file']['error'] == UPLOAD_ERR_OK) {
                if ($this->web_homework_fileupload()) {
                    $mediaUrl = base_url($this->upload_data['homework_file']['full_path_relative']);
                }
            }

            $homeworkDetails = "";
            foreach ($assignments as $assignment) {
                $homeworkDetails .= $assignment->subject . ': ' . $assignment->description . ' ';
            }

            $dataBatch = array_map(function($student) use ($class, $section, $deadlinedate, $homeworkDetails, $mediaUrl) {
                return [
                    'phone' => $student->phone,
                    'message' => "{$class->classes},{$section->section},{$deadlinedate},{$homeworkDetails}",
                    'url' => $mediaUrl, // Standardized key for media URL
                    'media' => $mediaUrl // Keep for compatibility if needed
                ];
            }, $students);

            $short_name = !empty($mediaUrl) ? 'HOMEWORK_PDF' : 'HOMEWORK';
            $template = $this->db->query("SELECT params, template_name FROM whatapp_templates WHERE short_name LIKE ?", [$short_name])->row_array();

            if (!$template || empty($template['template_name'])) {
                $this->response(['status' => false, 'message' => "Template $short_name not configured"], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                return;
            }

            $templateName = $template['template_name'];
            $sentCount = $this->whatsapp_m->send_homework_whatsapp($dataBatch, $templateName);

            $this->response([
                'status' => true, 
                'message' => "WhatsApp messages sent successfully to {$sentCount} recipients."
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'No students or assignments found for the selected criteria.'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    private function web_homework_fileupload() 
    {
        if (!file_exists('uploads/homework')) {
            mkdir('uploads/homework', 0777, true);
        }

        $config['upload_path'] = "./uploads/homework";
        $config['allowed_types'] = "pdf|doc|docx|jpg|jpeg|png";
        $config['max_size'] = '10240'; // 10MB
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload("homework_file")) {
            return FALSE;
        } else {
            $this->upload_data['homework_file'] = $this->upload->data();
            $this->upload_data['homework_file']['full_path_relative'] = 'uploads/homework/' . $this->upload_data['homework_file']['file_name'];
            return TRUE;
        }
    }

    private function fileupload() 
    {
        $new_file = "";
        $original_file_name = '';
        if($_FILES["file"]['name'] !="") {
            $file_name = $_FILES["file"]['name'];
            $original_file_name = $file_name;
            $random = random19();
            $makeRandom = hash('sha512', $random.$this->post('title') . config_item("encryption_key"));
            $file_name_rename = $makeRandom;
            $explode = explode('.', $file_name);
            if(customCompute($explode) >= 2) {
                $new_file = $file_name_rename.'.'.end($explode);
                $config['upload_path'] = "./uploads/images";
                $config['allowed_types'] = "gif|jpg|png|jpeg|pdf|doc|xml|docx|GIF|JPG|PNG|JPEG|PDF|DOC|XML|DOCX|xls|xlsx|txt|ppt|csv";
                $config['file_name'] = $new_file;
                $config['max_size'] = '100024';
                $config['max_width'] = '3000';
                $config['max_height'] = '3000';
                $this->load->library('upload', $config);
                if(!$this->upload->do_upload("file")) {
                    return FALSE;
                } else {
                    $this->upload_data['file'] =  $this->upload->data();
                    $this->upload_data['file']['original_file_name'] = $original_file_name;
                    return TRUE;
                }
            } else {
                return FALSE;
            }

        }
        return FALSE;
    }
}

/**
 * API Endpoints & Sample Payloads (cURL):
 * 
 * 1. Get Homework List (with optional filters)
 *    URL Pattern: api/v10/assignment/index/<classID>/<sectionID>/<subjectID>/<date>
 *    curl --location --request GET 'http://staging.ourschoolerp.localhost/api/v10/assignment/index/1/1/10/2026-03-28' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 2. Get Single Assignment Detail & Answers
 *    URL Pattern: api/v10/assignment/view/<assignmentID>/<classID>
 *    curl --location --request GET 'http://staging.ourschoolerp.localhost/api/v10/assignment/view/5/1' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 3. Fetch Form Data (Classes, Sections, Subjects)
 *    Input: { "classesID": "<optional classID>" }
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/assignment/get_form_data' \
 *    --header 'Content-Type: application/json' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --data '{ "classesID": "1" }'
 * 
 * 4. Add Homework (Bulk Support)
 *    Inputs:
 *    - classesID (string): Shared classID for the batch
 *    - sectionID[] (array): Shared sectionIDs JSON-encoded
 *    - deadlinedate (string): Shared deadline in YYYY-MM-DD
 *    - rows[0][title] (string): Row-specific title
 *    - rows[0][description] (string): Row-specific description
 *    - rows[0][subjectID] (string): Row-specific subjectID
 *    - file (binary): Single file attachment for the first row
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/assignment/add' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --form 'classesID="1"' \
 *    --form 'sectionID[]="1"' \
 *    --form 'sectionID[]="2"' \
 *    --form 'deadlinedate="2026-04-15"' \
 *    --form 'rows[0][title]="Math Homework"' \
 *    --form 'rows[0][description]="Solve exercises 1-5"' \
 *    --form 'rows[0][subjectID]="10"' \
 *    --form 'file=@/path/to/homework.pdf'
 * 
 * 5. Update Homework
 *    Inputs: assignmentID (required), plus optional updates for title, description, deadlinedate, subjectID, classesID, sectionID[], file
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/assignment/edit' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --form 'assignmentID="5"' \
 *    --form 'title="Updated Math Homework"' \
 *    --form 'sectionID[]="1"'
 * 
 * 6. Delete Homework
 *    Input: { "assignmentID": "<required assignmentID>" }
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/assignment/delete' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --form 'assignmentID="5"'
 * 
 * 7. Send Homework Alerts via WhatsApp
 *    Inputs: classesID, sectionID, deadlinedate (YYYY-MM-DD), homework_file (optional binary)
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/assignment/send_whatsapp' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --form 'classesID="1"' \
 *    --form 'sectionID="1"' \
 *    --form 'deadlinedate="2026-03-28"' \
 *    --form 'homework_file=@/path/to/attachment.pdf'
 */
