<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Routine extends Api_Controller 
{
    public $upload_data = array();

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('classes_m');
        $this->load->model('section_m');
        $this->load->model('subject_m');
        $this->load->model('routine_m');
        $this->load->model('teacher_m');
        $this->load->model('subjectteacher_m');
        $this->lang->load('routine', $this->data['language']);
    }

    public function index_get($id = null) 
    {
        if($this->session->userdata('usertypeID') == 3) {
            $id = $this->data['myclass'];
        }
        
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $this->retdata['classes'] = $this->classes_m->get_classes();
        if((int)$id) {
            $this->retdata['classesID'] = $id;
            $routines   = $this->routine_m->get_order_by_routine(array('classesID' => $id, 'schoolyearID' => $schoolyearID));
            $routines   = $this->routineManipulate($routines);
            $subject    = pluck($this->subject_m->general_get_subject(), 'obj', 'subjectID');
            $teacher    = pluck($this->teacher_m->get_select_teacher(), 'obj', 'teacherID');
            $classes    = pluck($this->classes_m->general_get_classes(), 'obj', 'classesID');
            $section    = pluck($this->section_m->general_get_section(), 'obj', 'sectionID');
            $weekend    = $this->weekend();

            $days                  = [
                'SUNDAY' => $this->lang->line('sunday'),
                'MONDAY' => $this->lang->line('monday'),
                'TUESDAY' => $this->lang->line('tuesday'),
                'WEDNESDAY' => $this->lang->line('wednesday'),
                'THURSDAY' => $this->lang->line('thursday'),
                'FRIDAY' => $this->lang->line('friday'),
                'SATURDAY' => $this->lang->line('saturday')
            ];
            $this->retdata['days'] = $days;

            $fetchClass = pluck($this->retdata['classes'], 'classesID', 'classesID');
            if(isset($fetchClass[$id])) {
                $routineArray = [];
                $routineSectionArray = [];
                
                $this->retdata['sections'] = $this->section_m->general_get_order_by_section(array("classesID" => $id));
                $sections                  = $this->retdata['sections'];

                foreach ($days as $dayKey => $day) {
                    foreach ($sections as $sec) {
                        if(isset($routines[$dayKey][$sec->sectionID])) {
                            $rt = $routines[$dayKey][$sec->sectionID];
                            if(customCompute($rt)) {
                                foreach ($rt as  $r) {
                                    $subjectName    = isset($subject[$r->subjectID]) ? $subject[$r->subjectID]->subject : 'None';
                                    $teacherName    = isset($teacher[$r->teacherID]) ? $teacher[$r->teacherID]->name : 'None';
                                    $className      = isset($classes[$r->classesID]) ? $classes[$r->classesID]->classes : 'None';
                                    $sectionName    = isset($section[$r->sectionID]) ? $section[$r->sectionID]->section : 'None';

                                    $routineSectionArray[$sec->sectionID][$dayKey][] = [
                                        'routineID' => $r->routineID,
                                        'time' => $r->start_time.'-'.$r->end_time, 
                                        'start_time' => $r->start_time,
                                        'end_time' => $r->end_time,
                                        'room' => $r->room,
                                        'subject' => $subjectName, 
                                        'subjectID' => $r->subjectID,
                                        'classes' => $className, 
                                        'classesID' => $r->classesID,
                                        'section' => $sectionName, 
                                        'sectionID' => $r->sectionID,
                                        'teacher' => $teacherName,
                                        'teacherID' => $r->teacherID,
                                        'day' => $r->day
                                    ];
                                }
                            }
                        } else {
                            if(!isset($routineSectionArray[$sec->sectionID][$dayKey])) {
                                if(in_array($dayKey, $weekend)) {
                                    $routineSectionArray[$sec->sectionID][$dayKey] = 'Weekend';
                                } else {
                                    $routineSectionArray[$sec->sectionID][$dayKey] = null;
                                }
                            }
                        }
                    }

                    // Build flat routineArray for the class (all sections)
                    foreach ($routines as $dKey => $secRoutines) {
                        if($dKey == $dayKey) {
                            foreach($secRoutines as $sID => $rtList) {
                                foreach($rtList as $r) {
                                    $subjectName    = isset($subject[$r->subjectID]) ? $subject[$r->subjectID]->subject : 'None';
                                    $teacherName    = isset($teacher[$r->teacherID]) ? $teacher[$r->teacherID]->name : 'None';
                                    $className      = isset($classes[$r->classesID]) ? $classes[$r->classesID]->classes : 'None';
                                    $sectionName    = isset($section[$r->sectionID]) ? $section[$r->sectionID]->section : 'None';

                                    $routineArray[$dayKey][] = [
                                        'routineID' => $r->routineID,
                                        'time' => $r->start_time.'-'.$r->end_time, 
                                        'start_time' => $r->start_time,
                                        'end_time' => $r->end_time,
                                        'room' => $r->room,
                                        'subject' => $subjectName, 
                                        'subjectID' => $r->subjectID,
                                        'classes' => $className, 
                                        'classesID' => $r->classesID,
                                        'section' => $sectionName, 
                                        'sectionID' => $r->sectionID,
                                        'teacher' => $teacherName,
                                        'teacherID' => $r->teacherID,
                                        'day' => $r->day
                                    ];
                                }
                            }
                        }
                    }
                    if(!isset($routineArray[$dayKey])) {
                        if(in_array($dayKey, $weekend)) {
                            $routineArray[$dayKey] = 'Weekend';
                        } else {
                            $routineArray[$dayKey] = null;
                        }
                    }
                }

                $this->retdata['routines'] = $routineArray;
                $this->retdata['routinesections'] = $routineSectionArray;
            } else {
                $this->retdata['routines'] = [];
                $this->retdata['routinesections'] = [];
            }
        } else {
            $this->retdata['classesID'] =  $id;
            $this->retdata['routines'] = [];
            $this->retdata['routinesections'] = [];
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

        $routine = $this->routine_m->get_single_routine(array('routineID' => $id));
        if(customCompute($routine)) {
            $this->retdata['routine'] = (array) $routine;
            
            $subject = $this->subject_m->general_get_single_subject(array('subjectID' => $routine->subjectID));
            $teacher = $this->teacher_m->get_single_teacher(array('teacherID' => $routine->teacherID));
            $class = $this->classes_m->general_get_single_classes(array('classesID' => $routine->classesID));
            $section = $this->section_m->general_get_single_section(array('sectionID' => $routine->sectionID));

            $this->retdata['routine']['subject_name'] = $subject ? $subject->subject : 'None';
            $this->retdata['routine']['teacher_name'] = $teacher ? $teacher->name : 'None';
            $this->retdata['routine']['class_name'] = $class ? $class->classes : 'None';
            $this->retdata['routine']['section_name'] = $section ? $section->section : 'None';

            $this->response(['status' => true, 'message' => 'Success', 'data' => $this->retdata], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Routine not found'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function add_post() 
    {
        try {
            $schoolyearID = $this->post('schoolyearID') ? $this->post('schoolyearID') : $this->session->userdata('defaultschoolyearID');
            $classesID = $this->post('classesID');
            $sectionID = $this->post('sectionID');
            $day = $this->post('day');
            $routines = $this->post('routines'); // Preformatted array of objects

            if(!$classesID || !$sectionID || !$day || !customCompute($routines)) {
                $this->response(['status' => false, 'message' => 'Missing required common fields or routines list'], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            $insert_count = 0;
            foreach ($routines as $r) {
                if(isset($r['subjectID']) && isset($r['teacherID']) && isset($r['start_time']) && isset($r['end_time'])) {
                    $array = array(
                        "classesID"     => $classesID,
                        "sectionID"     => $sectionID,
                        "subjectID"     => $r['subjectID'],
                        'schoolyearID'  => $schoolyearID,
                        "day"           => $day,
                        'teacherID'     => $r['teacherID'],
                        "start_time"    => $r['start_time'],
                        "end_time"      => $r['end_time'],
                        "room"          => isset($r['room']) ? $r['room'] : ''
                    );
                    $this->routine_m->insert_routine($array);
                    $insert_count++;
                }
            }

            if($insert_count > 0) {
                $this->response(['status' => true, 'message' => "Successfully added $insert_count routine(s)"], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => false, 'message' => 'No valid records to insert'], REST_Controller::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $this->response(['status' => false, 'message' => $e->getMessage()], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit_post() 
    {
        try {
            $routineID = $this->post('routineID');
            if(!(int)$routineID) {
                $this->response(['status' => false, 'message' => 'Routine ID is required'], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            $routine = $this->routine_m->get_single_routine(array('routineID' => $routineID));
            if(!$routine) {
                $this->response(['status' => false, 'message' => 'Routine not found'], REST_Controller::HTTP_NOT_FOUND);
                return;
            }

            $array = [];
            if($this->post('classesID')) $array['classesID'] = $this->post('classesID');
            if($this->post('sectionID')) $array['sectionID'] = $this->post('sectionID');
            if($this->post('subjectID')) $array['subjectID'] = $this->post('subjectID');
            if($this->post('teacherID')) $array['teacherID'] = $this->post('teacherID');
            if($this->post('day')) $array['day'] = $this->post('day');
            if($this->post('start_time')) $array['start_time'] = $this->post('start_time');
            if($this->post('end_time')) $array['end_time'] = $this->post('end_time');
            if($this->post('room') !== null) $array['room'] = $this->post('room');

            if(customCompute($array)) {
                $this->routine_m->update_routine($array, $routineID);
                $this->response(['status' => true, 'message' => 'Routine updated successfully'], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => false, 'message' => 'No fields to update'], REST_Controller::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $this->response(['status' => false, 'message' => $e->getMessage()], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete_post() 
    {
        $id = $this->post('routineID');
        if(!(int)$id) {
            $this->response(['status' => false, 'message' => 'Routine ID is required'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $routine = $this->routine_m->get_single_routine(array('routineID' => $id));
        if($routine) {
            $this->routine_m->delete_routine($id);
            $this->response(['status' => true, 'message' => 'Routine deleted successfully'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Routine not found'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function get_form_data_post() 
    {
        $classesID = $this->post('classesID');
        $subjectID = $this->post('subjectID');

        $this->retdata['classes'] = pluck($this->classes_m->get_classes(), 'classes', 'classesID');
        $this->retdata['schoolyears'] = pluck($this->schoolyear_m->get_schoolyear(), 'schoolyear', 'schoolyearID');
        $this->retdata['days'] = [
            'SUNDAY' => $this->lang->line('sunday'),
            'MONDAY' => $this->lang->line('monday'),
            'TUESDAY' => $this->lang->line('tuesday'),
            'WEDNESDAY' => $this->lang->line('wednesday'),
            'THURSDAY' => $this->lang->line('thursday'),
            'FRIDAY' => $this->lang->line('friday'),
            'SATURDAY' => $this->lang->line('saturday')
        ];

        if($classesID) {
            $this->retdata['sections'] = pluck($this->section_m->general_get_order_by_section(array('classesID' => $classesID)), 'section', 'sectionID');
            $this->retdata['subjects'] = pluck($this->subject_m->general_get_order_by_subject(array('classesID' => $classesID)), 'subject', 'subjectID');
        }

        if($subjectID) {
            $this->retdata['teachers'] = pluck($this->subjectteacher_m->get_subjectteacher_with_teacher($subjectID), 'name', 'teacherID');
        } else {
            $this->retdata['all_teachers'] = pluck($this->teacher_m->get_teacher(), 'name', 'teacherID');
        }

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);
    }

    private function routineManipulate($routines)
    {
        $routineArray = [];
        if(customCompute($routines)) {
            foreach ($routines as $routine) {
                $routineArray[$routine->day][$routine->sectionID][] = $routine;
            }
        }
        return $routineArray;
    }

    private function weekend()
    {
        $weekends = $this->data['siteinfos']->weekends;
        $weekendsKeys = explode(',', $weekends);
        $weekendsDays = [];
        if(customCompute($weekendsKeys)) {
            foreach($weekendsKeys  as $key => $value) {
                if($value !='') {
                    $weekendsDays[] = $value;
                }
            }
        }
        return $weekendsDays;
    }
}

/**
 * API Endpoints & Sample Payloads (cURL):
 * 
 * 1. Get Routine Index (by Class)
 *    curl --location --request GET 'http://staging.ourschoolerp.localhost/api/v10/routine/index/1' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 2. Get Single Routine Detail
 *    curl --location --request GET 'http://staging.ourschoolerp.localhost/api/v10/routine/view/10' \
 *    --header 'Authorization: Bearer <TOKEN>'
 * 
 * 3. Fetch Form Data (Classes, Days, Sections/Subjects/Teachers based on input)
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/routine/get_form_data' \
 *    --header 'Content-Type: application/json' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --data '{ "classesID": "1", "subjectID": "10" }'
 * 
 * 4. Add Routine (Bulk)
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/routine/add' \
 *    --header 'Content-Type: application/json' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --data '{
 *      "classesID": "1",
 *      "sectionID": "1",
 *      "day": "MONDAY",
 *      "routines": [
 *        {
 *          "subjectID": "10",
 *          "teacherID": "5",
 *          "start_time": "09:00 AM",
 *          "end_time": "09:50 AM",
 *          "room": "101"
 *        },
 *        {
 *          "subjectID": "11",
 *          "teacherID": "6",
 *          "start_time": "10:00 AM",
 *          "end_time": "10:50 AM",
 *          "room": "102"
 *        }
 *      ]
 *    }'
 * 
 * 5. Update Routine
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/routine/edit' \
 *    --header 'Content-Type: application/json' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --data '{
 *      "routineID": "10",
 *      "start_time": "09:15 AM",
 *      "room": "103"
 *    }'
 * 
 * 6. Delete Routine
 *    curl --location --request POST 'http://staging.ourschoolerp.localhost/api/v10/routine/delete' \
 *    --header 'Content-Type: application/json' \
 *    --header 'Authorization: Bearer <TOKEN>' \
 *    --data '{ "routineID": "10" }'
 */
