<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Push_notification extends Admin_Controller {
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

    //FCM
    function __construct() {
        parent::__construct();
    // Load student model
    $this->load->model('student_m');
        
    }

public function send_push_to_students()
{
    $studentIDs = [155,349]; // <-- Replace with real student IDs

    
    $students = $this->student_m->get_tokens_by_studentIDs($studentIDs);

    $deviceTokens = [];
    foreach ($students as $student) {
        if (!empty($student->device_token)) {
            $deviceTokens[] = $student->device_token;
        }
    }
        print_r($deviceTokens);
    if (!empty($deviceTokens)) {
        $title = "Exam Reminder";
        $message = "Dear student, your exam starts tomorrow. Please be prepared.";
        $data = [
            'type' => 'exam_alert',
            'screen' => 'exam_schedule'
        ];

        $response = send_fcm_push_bulk($deviceTokens, $title, $message, $data);
        echo "Push sent: <pre>"; print_r($response);
    } else {
        echo "No tokens found.";
    }
}

}