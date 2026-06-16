<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Delete_account_request extends Api_Controller {

    public $upload_data = array();

    public function __construct() {
        parent::__construct();
        $this->load->model('delete_account_request_m');
    }

    /**
     * POST api/v10/delete_account_request/request
     * Content-Type: application/json
     *
     * Body (JSON):
     *   { "type": "student", "id": 42, "reason": "optional text" }
     */
    public function request_post() {
        // Parse JSON body
        $body   = json_decode($this->input->raw_input_stream, true);
        $type   = trim((string)($body['type']   ?? ''));
        $userId = (int)($body['id']             ?? 0);
        $reason = trim((string)($body['reason'] ?? ''));

        $allowedTypes = ['student', 'teacher', 'user'];

        if (!in_array($type, $allowedTypes) || !$userId) {
            $this->response([
                'status'  => false,
                'message' => 'Invalid or missing type / id. type must be student, teacher, or user.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Prevent duplicate pending requests
        if ($this->delete_account_request_m->request_exists($type, $userId)) {
            $this->response([
                'status'  => false,
                'message' => 'A delete account request is already pending for this account.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $name = $this->session->userdata('name') ?: null;

        $data = [
            'type'         => $type,
            'user_id'      => $userId,
            'user_name'    => $name,
            'reason'       => $reason ?: null,
            'status'       => 'pending',
            'requested_at' => date('Y-m-d H:i:s'),
        ];

        $insertId = $this->delete_account_request_m->insert_request($data);

        if ($insertId) {
            $this->response([
                'status'  => true,
                'message' => 'Delete account request submitted successfully.',
                'data'    => ['request_id' => $insertId]
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status'  => false,
                'message' => 'Failed to submit request. Please try again.'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}

/*
|--------------------------------------------------------------------------
| API Endpoints — Delete Account Request
|--------------------------------------------------------------------------
|
| POST   api/v10/delete_account_request/request
|        Submit a delete-account request from the mobile app.
|
| Headers:
|   Authorization : Bearer <JWT>
|   Content-Type  : application/json
|
| Request Body (JSON):
|   {
|       "type"   : "student",     // required — "student" | "teacher" | "user"
|       "id"     : 42,            // required — the user's own ID
|       "reason" : "No longer studying here"   // optional
|   }
|
| cURL example:
|   curl -X POST https://yourdomain.com/api/v10/delete_account_request/request \
|        -H "Authorization: Bearer <JWT>" \
|        -H "Content-Type: application/json" \
|        -d '{"type":"student","id":42,"reason":"No longer studying here"}'
|
| Response — success (200):
|   {
|       "status"  : true,
|       "message" : "Delete account request submitted successfully.",
|       "data"    : { "request_id": 5 }
|   }
|
| Response — duplicate pending (400):
|   {
|       "status"  : false,
|       "message" : "A delete account request is already pending for this account."
|   }
|
| Response — invalid payload (400):
|   {
|       "status"  : false,
|       "message" : "Invalid or missing type / id. type must be student, teacher, or user."
|   }
|
*/
