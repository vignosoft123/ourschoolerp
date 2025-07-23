<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Youtube extends Api_Controller 
{
     public function __construct() {
        parent::__construct();
        $this->load->database(); // load DB without model
    }

    public function links_get() {
        $schoolyearID = $this->get('schoolyear_id');
        $classID = $this->get('class_id');

        $this->db->select('*');
        $this->db->from('youtube_links');
        $this->db->where('status', 1);

        if ($schoolyearID) {
            $this->db->where('school_year_id', $schoolyearID);
        }

        if ($classID) {
            $this->db->where('class_id', $classID);
        }

        $query = $this->db->get();
        $results = $query->result();

        $this->response([
            'status' => true,
            'message' => 'Success',
            'data' => $results
        ], REST_Controller::HTTP_OK);
    }
}
