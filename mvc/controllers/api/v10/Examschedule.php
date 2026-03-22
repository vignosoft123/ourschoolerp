<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Examschedule extends Api_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('classes_m');
        $this->load->model('section_m');
        $this->load->model('examschedule_m');
        $this->load->model('subject_m');
        $this->load->model('exam_m');
        $this->load->model('marksetting_m');
    }

    public function index_get($id = null) 
    {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        if($this->session->userdata('usertypeID') == 3) {
            $id = $this->data['myclass'];
        }

        $this->retdata['classes']       = $this->classes_m->get_classes();
        if((int)$id) {
            $this->retdata['classesID']     = $id;
            $this->retdata['examschedules'] = $this->examschedule_m->get_join_examschedule_with_exam_classes_section_subject(array('classesID' => $id, 'schoolyearID' => $schoolyearID));
            if(customCompute($this->retdata['examschedules'])) {
                $sections = $this->section_m->general_get_order_by_section(array("classesID" => $id));
                $this->retdata['sections'] = $sections;
                if(customCompute($sections)) {
                    foreach ($sections as $key => $section) {
                        $this->retdata['allsection'][$section->section] = $this->examschedule_m->get_join_examschedule_with_exam_classes_section_subject(array('classesID' => $id, 'sectionID' => $section->sectionID, 'schoolyearID' => $schoolyearID));
                    }
                }
            } else {
                $this->retdata['examschedules'] = [];
            }
        } else {
            $this->retdata['classesID'] = 0;
            $this->retdata['examschedules'] = [];
        }

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK); 
    }

    public function index_post() 
    {
        //   post parameters : form data 
        // classesID: 1
            // examID: 13
        
        $classesID = $this->post('classesID');
        $examID = $this->post('examID');
        $sectionID = $this->post('sectionID');
        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        // $this->retdata['classes'] = $this->classes_m->get_classes();
        // echo "<pre>";print_r($classesID);die;
        
        if((int)$classesID) {  
            $this->retdata['classesID'] = $classesID;
            $query_params = array(
                'classesID' => $classesID, 
                'schoolyearID' => $schoolyearID
            );
            
            if($examID) {
                $query_params['examId'] = $examID;
            }
            if($sectionID) {
                $query_params['sectionID'] = $sectionID;
            }
            
            $this->retdata['examschedules'] = $this->examschedule_m->get_join_examschedule_with_exam_classes_section_subject($query_params);
            
            // if(customCompute($this->retdata['examschedules'])) {
            //     $sections = $this->section_m->general_get_order_by_section(array("classesID" => $classesID));
            //     $this->retdata['sections'] = $sections;
            // }
        } else {
            $this->retdata['classesID'] = 0;
            $this->retdata['examschedules'] = [];
        }

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);
    }

    public function add_post() 
    {
        try {
            $schoolyearID = $this->session->userdata('defaultschoolyearID');
            
            // Validate required fields
            $examID = $this->post("examID");
            $classesID = $this->post("classesID");
            $subjectID = $this->post("subjectID");
            $edate = $this->post("date");
            $examfrom = $this->post("examfrom");
            $examto = $this->post("examto");
            $min_mark = $this->post("min_mark");
            $max_mark = $this->post("max_mark");
            $arrSections = $this->post("sectionID");

            if(!$examID || !$classesID || !$subjectID || !$edate || !$examfrom || !$examto || !$min_mark || !$max_mark || !$arrSections) {
                $this->response([
                    'status' => false,
                    'message' => 'Missing required fields'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            $inserted_count = 0;
            $duplicate_count = 0;

            // Handle multiple subjects if provided as array
            if(!is_array($subjectID)) {
                $subjectID = array($subjectID);
                $edate = array($edate);
                $examfrom = array($examfrom);
                $examto = array($examto);
                $min_mark = array($min_mark);
                $max_mark = array($max_mark);
            }

            for($i = 0; $i < count($subjectID); $i++) {
                $array = array(
                    "examID" => $examID,
                    "classesID" => $classesID,
                    "subjectID" => $subjectID[$i],
                    "edate" => date("Y-m-d", strtotime($edate[$i])),
                    "examfrom" => $examfrom[$i],
                    "examto" => $examto[$i],
                    "min_mark" => $min_mark[$i],
                    "max_mark" => $max_mark[$i],
                    "schoolyearID" => $schoolyearID
                );

                foreach($arrSections as $nSection) {
                    $array['sectionID'] = $nSection;

                    // Check for duplicates
                    $this->db->where('examID', $examID);
                    $this->db->where('classesID', $classesID);
                    $this->db->where('sectionID', $nSection);
                    $this->db->where('subjectID', $subjectID[$i]);
                    $this->db->where('schoolyearID', $schoolyearID);
                    $cnt = $this->db->get('examschedule')->num_rows();

                    if($cnt >= 1) {
                        $duplicate_count++;
                    } else {
                        $this->examschedule_m->insert_examschedule($array);
                        $inserted_count++;
                    }
                }
            }

            $this->response([
                'status' => true,
                'message' => "Successfully inserted {$inserted_count} exam schedules. {$duplicate_count} duplicates skipped.",
                'data' => [
                    'inserted' => $inserted_count,
                    'duplicates' => $duplicate_count
                ]
            ], REST_Controller::HTTP_OK);

        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error creating exam schedule: ' . $e->getMessage()
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit_post() 
    {
        try {
            $id = $this->post('examscheduleID');
            $schoolyearID = $this->session->userdata('defaultschoolyearID');
            
            if(!$id) {
                $this->response([
                    'status' => false,
                    'message' => 'Exam schedule ID is required'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            // Check if exam schedule exists
            $examschedule = $this->examschedule_m->get_single_examschedule(array(
                'examscheduleID' => $id, 
                'schoolyearID' => $schoolyearID
            ));

            if(!$examschedule) {
                $this->response([
                    'status' => false,
                    'message' => 'Exam schedule not found'
                ], REST_Controller::HTTP_NOT_FOUND);
                return;
            }

            $array = array(
                "examID" => $this->post("examID"),
                "classesID" => $this->post("classesID"),
                "sectionID" => $this->post("sectionID"),
                "subjectID" => $this->post("subjectID"),
                "edate" => date("Y-m-d", strtotime($this->post("date"))),
                "examfrom" => $this->post("examfrom"),
                "examto" => $this->post("examto"),
                "room" => $this->post("room"),
                "min_mark" => $this->post("min_mark"),
                "max_mark" => $this->post("max_mark")
            );

            $this->examschedule_m->update_examschedule($array, $id);

            $this->response([
                'status' => true,
                'message' => 'Exam schedule updated successfully',
                'data' => $array
            ], REST_Controller::HTTP_OK);

        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error updating exam schedule: ' . $e->getMessage()
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function view_get($id = null) 
    {
        if(!$id) {
            $this->response([
                'status' => false,
                'message' => 'Exam schedule ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $examschedule = $this->examschedule_m->get_single_examschedule(array(
            'examscheduleID' => $id,
            'schoolyearID' => $schoolyearID
        ));

        if(!$examschedule) {
            $this->response([
                'status' => false,
                'message' => 'Exam schedule not found'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $this->response([
            'status' => true,
            'message' => 'Success',
            'data' => $examschedule
        ], REST_Controller::HTTP_OK);
    }

    public function delete_post() 
    {
        try {
            $id = $this->post('examscheduleID');
            $schoolyearID = $this->session->userdata('defaultschoolyearID');

            if(!$id) {
                $this->response([
                    'status' => false,
                    'message' => 'Exam schedule ID is required'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            // Check if exam schedule exists
            $examschedule = $this->examschedule_m->get_single_examschedule(array(
                'examscheduleID' => $id,
                'schoolyearID' => $schoolyearID
            ));

            if(!$examschedule) {
                $this->response([
                    'status' => false,
                    'message' => 'Exam schedule not found'
                ], REST_Controller::HTTP_NOT_FOUND);
                return;
            }

            $this->examschedule_m->delete_examschedule($id);

            $this->response([
                'status' => true,
                'message' => 'Exam schedule deleted successfully'
            ], REST_Controller::HTTP_OK);

        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error deleting exam schedule: ' . $e->getMessage()
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function multi_delete_post() 
    {
        try {
            $selected = $this->post('selected');
            $schoolyearID = $this->session->userdata('defaultschoolyearID');

            if(!is_array($selected) || !customCompute($selected)) {
                $this->response([
                    'status' => false,
                    'message' => 'No items selected or invalid selection'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            // Verify all schedules exist and belong to current school year
            $valid_ids = [];
            foreach($selected as $id) {
                $examschedule = $this->examschedule_m->get_single_examschedule(array(
                    'examscheduleID' => $id,
                    'schoolyearID' => $schoolyearID
                ));
                if($examschedule) {
                    $valid_ids[] = $id;
                }
            }

            if(empty($valid_ids)) {
                $this->response([
                    'status' => false,
                    'message' => 'No valid exam schedules found to delete'
                ], REST_Controller::HTTP_NOT_FOUND);
                return;
            }

            $this->examschedule_m->delete_multiple_examschedule($valid_ids);

            $this->response([
                'status' => true,
                'message' => 'Selected exam schedules deleted successfully',
                'data' => [
                    'deleted_count' => count($valid_ids),
                    'requested_count' => count($selected)
                ]
            ], REST_Controller::HTTP_OK);

        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error deleting exam schedules: ' . $e->getMessage()
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_exams_post() 
    {
        $classesID = $this->post('classesID');
        
        if(!$classesID) {
            $this->response([
                'status' => false,
                'message' => 'Class ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $exams = [];
        if((int)$classesID) {
            $examData = $this->marksetting_m->get_exam($this->data['siteinfos']->marktypeID, $classesID);
            $exams = pluck($examData, 'obj', 'examID');
        }

        $this->response([
            'status' => true,
            'message' => 'Success',
            'data' => $exams ? array_values($exams) : []
        ], REST_Controller::HTTP_OK);
    }

    public function get_subjects_post() 
    {
        $classID = $this->post('classesID');
        
        if(!$classID) {
            $this->response([
                'status' => false,
                'message' => 'Class ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $subjects = [];
        if((int)$classID) {
            $subjects = $this->subject_m->general_get_order_by_subject_only_subjects(array('classesID' => $classID));
        }

        $this->response([
            'status' => true,
            'message' => 'Success',
            'data' => $subjects ? $subjects : []
        ], REST_Controller::HTTP_OK);
    }

    public function get_sections_post() 
    {
        $classID = $this->post('classesID');
        
        if(!$classID) {
            $this->response([
                'status' => false,
                'message' => 'Class ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $sections = [];
        if((int)$classID) {
            $sections = $this->section_m->general_get_order_by_section(array("classesID" => $classID));
        }

        $this->response([
            'status' => true,
            'message' => 'Success',
            'data' => $sections ? $sections : []
        ], REST_Controller::HTTP_OK);
    }

    public function get_form_data_post() 
    {
        $classesID = $this->post('classesID');
        
        $this->retdata['classes'] = $this->classes_m->get_classes();
        
        if((int)$classesID) {
            $this->retdata['subjects'] = $this->subject_m->general_get_order_by_subject(array('classesID' => $classesID));
            $this->retdata['sections'] = $this->section_m->general_get_order_by_section(array("classesID" => $classesID));
            $this->retdata['exams'] = $this->marksetting_m->get_exam($this->data['siteinfos']->marktypeID, $classesID);
        } else {
            $this->retdata['subjects'] = [];
            $this->retdata['sections'] = [];
            $this->retdata['exams'] = [];
        }

        $this->response([
            'status' => true,
            'message' => 'Success',
            'data' => $this->retdata
        ], REST_Controller::HTTP_OK);
    }

    /**
     * Get bearer token from header
     */
    private function getBearerToken() {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            return str_replace('Bearer ', '', $headers['Authorization']);
        }
        return null;
    }

    /**
     * Get token data from session or decode JWT if needed
     */
    private function getTokenData() {
        // Check if token data is stored in session
        $tokenData = $this->session->userdata('token_data');
        if ($tokenData) {
            return $tokenData;
        }

        // If not in session, try to get from bearer token
        $bearerToken = $this->getBearerToken();
        if ($bearerToken) {
            // If it's a JWT token, you might need to decode it
            // For now, just return the token
            return ['bearer_token' => $bearerToken];
        }

        return null;
    }
}

/**
 * API Endpoints & Sample Payloads:
 * 
 * 1. Get Exam Schedule by Class
 *    Endpoint: GET api/v10/examschedule/index/{id}
 * 
 * 2. Filter Exam Schedule
 *    Endpoint: POST api/v10/examschedule/index
 *    Payload:  { "classesID": "1", "examID": "13", "sectionID": "1" }
 * 
 * 3. Add Exam Schedule
 *    Endpoint: POST api/v10/examschedule/add
 *    Payload:
 *    {
 *      "examID": "1",
 *      "classesID": "1",
 *      "sectionID": ["1", "2"],
 *      "subjectID": ["10", "11"],
 *      "date": ["01-04-2026", "02-04-2026"],
 *      "examfrom": ["10:00 AM", "02:00 PM"],
 *      "examto": ["01:00 PM", "05:00 PM"],
 *      "min_mark": ["35", "35"],
 *      "max_mark": ["100", "100"]
 *    }
 * 
 * 4. View Single Exam Schedule
 *    Endpoint: GET api/v10/examschedule/view/{id}
 * 
 * 5. Delete Exam Schedule
 *    Endpoint: POST api/v10/examschedule/delete
 *    Payload: { "examscheduleID": "101" }
 */