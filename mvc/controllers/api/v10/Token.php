<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Token extends Api_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('student_m');
    }

    public function store_token_post() {
        $studentID    = $this->post('studentID');
        $device_token = $this->post('device_token');
        $platform     = $this->post('platform'); // optional

        if (!$studentID || !$device_token) {
            $this->response([
                'status'  => false,
                'message' => 'Missing studentID or device_token'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Update student record with device token
        $this->student_m->update_student([
            'device_token' => $device_token,
            'platform' => $platform
        ],$studentID);

        $this->response([
            'status'  => true,
            'message' => 'Token saved successfully'
        ], REST_Controller::HTTP_OK);
    }
}
