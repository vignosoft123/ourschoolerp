<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Subject extends Api_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('teacher_m');
        $this->load->model('subject_m');
        $this->load->model('classes_m');
        $this->load->model('subjectteacher_m');
    }

    public function index_get($id = null) 
    {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        if($this->session->userdata('usertypeID') == 3) {
            $id = $this->data['myclass'];
        }

        if((int)$id) {
            $this->retdata['classesID'] = $id;
            $this->retdata['teachers'] = pluck($this->teacher_m->general_get_teacher(), 'name', 'teacherID');
            $this->retdata['classes'] = $this->classes_m->get_classes();
            $fetchClass = pluck($this->retdata['classes'], 'classesID', 'classesID');
            if(isset($fetchClass[$id])) {
                $subjects = $this->subject_m->general_get_order_by_subject(array('classesID' => $id));
                $this->retdata['subjects'] = [];
                if(customCompute($subjects)) {
                    foreach($subjects as $subject) {
                        $this->retdata['subjects'][] = [
                            'subjectID' => $subject->subjectID,
                            'classesID' => $subject->classesID,
                            'type' => $subject->type,
                            'subject' => $subject->subject,
                            'subject_code' => $subject->subject_code
                        ];
                    }
                }
                $this->retdata['subjectteachers'] = pluck_multi_array($this->subjectteacher_m->get_order_by_subjectteacher(array('classesID' => $id)), 'teacherID', 'subjectID');
            } else {
                $this->retdata['classesID'] = 0;
                $this->retdata['subjects'] = [];
                $this->retdata['subjectteachers'] = [];
            }
        } else {
            $this->retdata['classesID'] = 0;
            $this->retdata['subjects'] = [];
            $this->retdata['subjectteachers'] = [];
            $this->retdata['classes'] = $this->classes_m->get_classes();
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

        $subject = $this->subject_m->general_get_single_subject(array('subjectID' => $id));
        if(customCompute($subject)) {
            $this->retdata['subject'] = (array) $subject;
            // Remove unneccessary documentation data
            unset($this->retdata['subject']['create_date']);
            unset($this->retdata['subject']['modify_date']);
            unset($this->retdata['subject']['create_userID']);
            unset($this->retdata['subject']['create_username']);
            unset($this->retdata['subject']['create_usertype']);

            $teachers = $this->subjectteacher_m->get_subjectteacher_with_teacher($id);
            $this->retdata['subjectteachers'] = [];
            if(customCompute($teachers)) {
                foreach($teachers as $teacher) {
                    $this->retdata['subjectteachers'][] = [
                        'subjectteacherID' => $teacher->subjectteacherID,
                        'subjectID' => $teacher->subjectID,
                        'classesID' => $teacher->classesID,
                        'teacherID' => $teacher->teacherID,
                        'name' => $teacher->name,
                        'designation' => $teacher->designation,
                        'photo' => $teacher->photo
                    ];
                }
            }
            $this->response(['status' => true, 'message' => 'Success', 'data' => $this->retdata], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Subject not found'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function add_post() 
    {
        try {
            $classesID = $this->post('classesID');
            $teacherID = $this->post('teacherID'); // Array of teacher IDs
            $type = $this->post('type');
            $subject = $this->post('subject');
            $subject_author = $this->post('subject_author');
            $subject_code = $this->post('subject_code');
            $passmark = $this->post('passmark') ? $this->post('passmark') : 0;
            $finalmark = $this->post('finalmark') ? $this->post('finalmark') : 100;

            if(!$classesID || !$subject || !$subject_code || !$type) {
                $this->response(['status' => false, 'message' => 'Missing required fields'], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            // Get primary teacher name for the subject table
            $primary_teacher_name = "";
            if(is_array($teacherID) && customCompute($teacherID)) {
                $teacher = $this->teacher_m->get_single_teacher(array('teacherID' => $teacherID[0]));
                if($teacher) $primary_teacher_name = $teacher->name;
            } elseif((int)$teacherID) {
                $teacher = $this->teacher_m->get_single_teacher(array('teacherID' => $teacherID));
                if($teacher) $primary_teacher_name = $teacher->name;
            }

            $array = array(
                "classesID" => $classesID,
                "type" => $type,
                "passmark" => $passmark,
                "finalmark" => $finalmark,
                "subject" => $subject,
                "subject_author" => $subject_author,
                "subject_code" => $subject_code,
                "teacher_name" => $primary_teacher_name,
                "create_date" => date("Y-m-d H:i:s"),
                "modify_date" => date("Y-m-d H:i:s"),
                "create_userID" => $this->session->userdata('loginuserID'),
                "create_username" => $this->session->userdata('username'),
                "create_usertype" => $this->session->userdata('usertype')
            );

            // Insert Subject
            $this->subject_m->insert_subject($array);
            $subjectID = $this->db->insert_id();

            // Insert Subject Teachers
            if(is_array($teacherID) && customCompute($teacherID)) {
                $batch = [];
                foreach($teacherID as $tID) {
                    $batch[] = array(
                        "subjectID" => $subjectID,
                        "classesID" => $classesID,
                        "teacherID" => $tID
                    );
                }
                $this->subjectteacher_m->insert_batch_subjectteacher($batch);
            } elseif((int)$teacherID) {
                $this->subjectteacher_m->insert_subjectteacher(array(
                    "subjectID" => $subjectID,
                    "classesID" => $classesID,
                    "teacherID" => $teacherID
                ));
            }

            $this->response(['status' => true, 'message' => 'Subject added successfully', 'data' => ['subjectID' => $subjectID]], REST_Controller::HTTP_OK);
        } catch (Exception $e) {
            $this->response(['status' => false, 'message' => $e->getMessage()], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit_post() 
    {
        try {
            $subjectID = $this->post('subjectID');
            if(!(int)$subjectID) {
                $this->response(['status' => false, 'message' => 'Subject ID is required'], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            $classesID = $this->post('classesID');
            $teacherID = $this->post('teacherID'); // Array of teacher IDs
            $type = $this->post('type');
            $subject = $this->post('subject');
            $subject_author = $this->post('subject_author');
            $subject_code = $this->post('subject_code');
            $passmark = $this->post('passmark');
            $finalmark = $this->post('finalmark');

            $array = array(
                "modify_date" => date("Y-m-d H:i:s")
            );
            if($classesID) $array['classesID'] = $classesID;
            if($type !== null) $array['type'] = $type;
            if($subject) $array['subject'] = $subject;
            if($subject_author !== null) $array['subject_author'] = $subject_author;
            if($subject_code) $array['subject_code'] = $subject_code;
            if($passmark !== null) $array['passmark'] = $passmark;
            if($finalmark !== null) $array['finalmark'] = $finalmark;

            // Update primary teacher name if changed
            if($teacherID) {
                $pID = is_array($teacherID) ? $teacherID[0] : $teacherID;
                $teacher = $this->teacher_m->get_single_teacher(array('teacherID' => $pID));
                if($teacher) $array['teacher_name'] = $teacher->name;
            }

            $this->subject_m->update_subject($array, $subjectID);

            // Update Subject Teachers (Sync)
            if($teacherID) {
                $this->subjectteacher_m->delete_subjectteacher_by_array(array('subjectID' => $subjectID));
                if(is_array($teacherID)) {
                    $batch = [];
                    foreach($teacherID as $tID) {
                        $batch[] = array(
                            "subjectID" => $subjectID,
                            "classesID" => $classesID ? $classesID : $this->subject_m->get_single_subject(array('subjectID' => $subjectID))->classesID,
                            "teacherID" => $tID
                        );
                    }
                    $this->subjectteacher_m->insert_batch_subjectteacher($batch);
                } else {
                    $this->subjectteacher_m->insert_subjectteacher(array(
                        "subjectID" => $subjectID,
                        "classesID" => $classesID ? $classesID : $this->subject_m->get_single_subject(array('subjectID' => $subjectID))->classesID,
                        "teacherID" => $teacherID
                    ));
                }
            }

            $this->response(['status' => true, 'message' => 'Subject updated successfully'], REST_Controller::HTTP_OK);
        } catch (Exception $e) {
            $this->response(['status' => false, 'message' => $e->getMessage()], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete_post() 
    {
        $id = $this->post('subjectID');
        if(!(int)$id) {
            $this->response(['status' => false, 'message' => 'Subject ID is required'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $this->subject_m->delete_subject($id);
        $this->subjectteacher_m->delete_subjectteacher_by_array(array('subjectID' => $id));
        
        $this->response(['status' => true, 'message' => 'Subject deleted successfully'], REST_Controller::HTTP_OK);
    }

    public function get_form_data_post() 
    {
        $this->retdata['classes'] = pluck($this->classes_m->get_classes(), 'classes', 'classesID');
        $this->retdata['teachers'] = pluck($this->teacher_m->get_teacher(), 'name', 'teacherID');
        // Standard types for subjects: usually 1 for Mandatory, 0 for Optional
        $this->retdata['types'] = [
            ['id' => 1, 'name' => 'Mandatory'],
            ['id' => 0, 'name' => 'Optional']
        ];

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);
    }
}

/**
 * API Endpoints & Sample Payloads (cURL):
 * 
 * 1. Get Subjects List (by Class)
 *    curl --location --request GET 'http://staging.ourschoolerp.localhost/api/v10/subject/index/1' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 2. Get Single Subject Detail
 *    curl --location --request GET 'http://staging.ourschoolerp.localhost/api/v10/subject/view/10' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 3. Fetch Form Data (Classes, Teachers, Types)
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/subject/get_form_data' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 4. Add Subject
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/subject/add' \
 *    --header 'Content-Type: application/json' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --data '{
 *      "classesID": "5",
 *      "teacherID": ["1", "3"],
 *      "type": "1",
 *      "subject": "Mathematics",
 *      "subject_author": "Dr. Smith",
 *      "subject_code": "MATH101",
 *      "passmark": "35",
 *      "finalmark": "100"
 *    }'
 * 
 * 5. Update Subject
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/subject/edit' \
 *    --header 'Content-Type: application/json' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --data '{
 *      "subjectID": "10",
 *      "subject": "Advanced Mathematics",
 *      "teacherID": ["1"]
 *    }'
 * 
 * 6. Delete Subject
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/subject/delete' \
 *    --header 'Content-Type: application/json' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --data '{ "subjectID": "10" }'
 */
