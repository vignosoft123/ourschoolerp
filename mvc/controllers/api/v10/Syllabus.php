<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Syllabus extends Api_Controller 
{
    public $upload_data = array();

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('classes_m');
        $this->load->model('syllabus_m');
    }

    public function index_get($id = null) 
    {
        if($this->session->userdata('usertypeID') == 3) {
            $id = $this->data['myclass'];
        }

        if((int)$id) {
            $this->retdata['classesID'] = $id;
            $schoolyearID = $this->session->userdata('defaultschoolyearID');
            $syllabuss = $this->syllabus_m->get_order_by_syllabus(array('schoolyearID' => $schoolyearID, 'classesID' => $id));
            
            $this->retdata['syllabuss'] = [];
            if(customCompute($syllabuss)) {
                foreach($syllabuss as $syllabus) {
                    $this->retdata['syllabuss'][] = [
                        'syllabusID' => $syllabus->syllabusID,
                        'title' => $syllabus->title,
                        'description' => $syllabus->description,
                        'file' => $syllabus->file,
                        'originalfile' => $syllabus->originalfile,
                        'classesID' => $syllabus->classesID,
                        'date' => $syllabus->date,
                        'file_url' => base_url('uploads/images/'.$syllabus->file)
                    ];
                }
            }
        } else {
            $this->retdata['classesID'] = 0;
            $this->retdata['syllabuss'] = []; 
        }

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);
    }

    public function view_get($id = null)
    {
        if(!(int)$id) {
            $this->response(['status' => false, 'message' => 'Invalid ID'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $syllabus = $this->syllabus_m->get_single_syllabus(array('syllabusID' => $id, 'schoolyearID' => $schoolyearID));
        
        if(customCompute($syllabus)) {
            $this->retdata['syllabus'] = [
                'syllabusID' => $syllabus->syllabusID,
                'title' => $syllabus->title,
                'description' => $syllabus->description,
                'file' => $syllabus->file,
                'originalfile' => $syllabus->originalfile,
                'classesID' => $syllabus->classesID,
                'date' => $syllabus->date,
                'file_url' => base_url('uploads/images/'.$syllabus->file)
            ];
            $this->response(['status' => true, 'message' => 'Success', 'data' => $this->retdata], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Syllabus not found'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function add_post()
    {
        $rules = $this->rules();
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            $this->response(['status' => false, 'message' => $this->form_validation->error_array()], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $array = array(
                "title" => $this->input->post("title"),
                "description" => $this->input->post("description"),
                "date" => date('Y-m-d'),
                "usertypeID" => $this->session->userdata('usertypeID'),
                "userID" => $this->session->userdata('loginuserID'),
                "classesID" => $this->input->post("classesID"),
                "schoolyearID" => $this->session->userdata('defaultschoolyearID'),
            );

            $array['file'] = "";
            $array['originalfile'] = "";
            if(isset($this->upload_data['file']['file_name'])) {
                $array['file'] = $this->upload_data['file']['file_name'];
                $array['originalfile'] = $this->upload_data['file']['original_file_name'];
            }

            $this->syllabus_m->insert_syllabus($array);
            $this->response(['status' => true, 'message' => 'Success'], REST_Controller::HTTP_OK);
        }
    }

    public function edit_post()
    {
        $id = $this->input->post('syllabusID');
        if(!(int)$id) {
            $this->response(['status' => false, 'message' => 'Invalid ID'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $rules = $this->rules();
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            $this->response(['status' => false, 'message' => $this->form_validation->error_array()], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $schoolyearID = $this->session->userdata('defaultschoolyearID');
            $syllabus = $this->syllabus_m->get_single_syllabus(array('syllabusID' => $id, 'schoolyearID' => $schoolyearID));
            if(customCompute($syllabus)) {
                $array = array(
                    "title" => $this->input->post("title"),
                    "description" => $this->input->post("description"),
                    "date" => date('Y-m-d'),
                    "usertypeID" => $this->session->userdata('usertypeID'),
                    "userID" => $this->session->userdata('loginuserID'),
                    "classesID" => $this->input->post("classesID")
                );

                if($_FILES["file"]['name'] !="") {
                    $array['file'] = $this->upload_data['file']['file_name'];
                    $array['originalfile'] = $this->upload_data['file']['original_file_name'];
                    // Unlink old file
                    if(file_exists(FCPATH.'uploads/images/'.$syllabus->file)) {
                        unlink(FCPATH.'uploads/images/'.$syllabus->file);
                    }
                }

                $this->syllabus_m->update_syllabus($array, $id);
                $this->response(['status' => true, 'message' => 'Success'], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => false, 'message' => 'Syllabus not found'], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    public function delete_post()
    {
        $id = $this->input->post('syllabusID');
        if(!(int)$id) {
            $this->response(['status' => false, 'message' => 'Invalid ID'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $syllabus = $this->syllabus_m->get_single_syllabus(array('syllabusID' => $id, 'schoolyearID' => $schoolyearID));
        if(customCompute($syllabus)) {
            if(file_exists(FCPATH.'uploads/images/'.$syllabus->file)) {
                unlink(FCPATH.'uploads/images/'.$syllabus->file);
            }
            $this->syllabus_m->delete_syllabus($id);
            $this->response(['status' => true, 'message' => 'Success'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Syllabus not found'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function get_form_data_post()
    {
        $this->retdata['classes'] = pluck($this->classes_m->get_classes(), 'classes', 'classesID');
        $this->response(['status' => true, 'message' => 'Success', 'data' => $this->retdata], REST_Controller::HTTP_OK);
    }

    protected function rules() {
        $rules = array(
            array(
                'field' => 'title', 
                'label' => 'Title', 
                'rules' => 'trim|required|xss_clean|max_length[128]'
            ), 
            array(
                'field' => 'description', 
                'label' => 'Description',
                'rules' => 'trim|required|xss_clean'
            ), 
            array(
                'field' => 'classesID', 
                'label' => 'Class',
                'rules' => 'trim|required|numeric|max_length[11]|xss_clean'
            ),
            array(
                'field' => 'file', 
                'label' => 'File', 
                'rules' => 'trim|max_length[512]|xss_clean|callback_fileupload'
            )
        );
        return $rules;
    }

    public function fileupload() {
        $id = $this->input->post('syllabusID');
        $syllabus = array();
        if((int)$id) {
            $syllabus = $this->syllabus_m->get_single_syllabus(array('syllabusID' => $id));    
        }
        
        $new_file = "";
        $original_file_name = '';
        if($_FILES["file"]['name'] !="") {
            $file_name = $_FILES["file"]['name'];
            $original_file_name = $file_name;
            $random = random19();
            $makeRandom = hash('sha512', $random . $this->input->post('title') . config_item("encryption_key"));
            $file_name_rename = $makeRandom;
            $explode = explode('.', $file_name);
            if(customCompute($explode) >= 2) {
                $new_file = $file_name_rename.'.'.end($explode);
                $config['upload_path'] = "./uploads/images";
                $config['allowed_types'] = "gif|jpg|png|jpeg|pdf|doc|xml|docx|xls|xlsx|txt|ppt|csv";
                $config['file_name'] = $new_file;
                $config['max_size'] = '100024';
                $config['max_width'] = '3000';
                $config['max_height'] = '3000';
                $this->load->library('upload', $config);
                if(!$this->upload->do_upload("file")) {
                    $this->form_validation->set_message("fileupload", $this->upload->display_errors());
                    return FALSE;
                } else {
                    $this->upload_data['file'] =  $this->upload->data();
                    $this->upload_data['file']['original_file_name'] = $original_file_name;
                    return TRUE;
                }
            } else {
                $this->form_validation->set_message("fileupload", "Invalid file");
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }
}

/**
 * API Endpoints & Sample Payloads (cURL):
 * 
 * 1. Get Syllabus List (by Class)
 *    curl --location --request GET 'http://staging.ourschoolerp.localhost/api/v10/syllabus/index/1' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 2. Get Single Syllabus Detail
 *    curl --location --request GET 'http://staging.ourschoolerp.localhost/api/v10/syllabus/view/5' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 3. Fetch Form Data (Classes)
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/syllabus/get_form_data' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 4. Add Syllabus
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/syllabus/add' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --form 'title="Quarterly Syllabus"' \
 *    --form 'description="Syllabus for the first quarter."' \
 *    --form 'classesID="1"' \
 *    --form 'file=@"/path/to/syllabus.pdf"'
 * 
 * 5. Update Syllabus
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/syllabus/edit' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --form 'syllabusID="5"' \
 *    --form 'title="Updated Quarterly Syllabus"' \
 *    --form 'description="Updated description."' \
 *    --form 'classesID="1"' \
 *    --form 'file=@"/path/to/new_syllabus.pdf"'
 * 
 * 6. Delete Syllabus
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/syllabus/delete' \
 *    --header 'Content-Type: application/json' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --data '{ "syllabusID": "5" }'
 */
