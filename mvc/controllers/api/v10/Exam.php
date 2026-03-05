<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Exam extends Api_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('exam_m');
    }

    public function index_get() 
    {
        $academic_year = $this->get('academic_year');
        $query_params = [];
        if($academic_year) {
            $query_params['academic_year'] = $academic_year;
        }

        $this->retdata['exams'] = $this->exam_m->get_order_by_exam($query_params);

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);       
    }

    public function index_post() 
    {
        $academic_year = $this->post('academic_year');
        $query_params = [];
        if($academic_year) {
            $query_params['academic_year'] = $academic_year;
        }

        $this->retdata['exams'] = $this->exam_m->get_order_by_exam($query_params);

        $this->response([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $this->retdata
        ], REST_Controller::HTTP_OK);       
    }
}
