<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delete_account_request extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('delete_account_request_m');
        $this->load->model('activity_log_m');
        $this->lang->load('delete_account_request', $this->data['language']);
    }

    // Page shell — no GET params needed
    public function index() {
        $this->data['counts']  = $this->delete_account_request_m->get_counts();
        $this->data['subview'] = 'delete_account_request/index';
        $this->load->view('_layout_main', $this->data);
    }

    // POST — tab filter, returns JSON rows
    public function get_list() {
        header('Content-Type: application/json');

        $type         = trim((string)$this->input->post('type'));
        $allowedTypes = ['all', 'student', 'teacher', 'user'];

        if (!in_array($type, $allowedTypes)) {
            echo json_encode(['status' => false, 'message' => 'Invalid type']);
            return;
        }

        $filterType = $type === 'all' ? null : $type;
        $requests   = $this->delete_account_request_m->get_all_requests($filterType);

        $rows = [];
        foreach ($requests as $row) {
            if ($row->type === 'student') {
                $name = $row->student_name ?: $row->user_name;
            } elseif ($row->type === 'teacher') {
                $name = $row->teacher_name ?: $row->user_name;
            } else {
                $name = $row->user_name_join ?: $row->user_name;
            }

            $rows[] = [
                'id'           => $row->id,
                'type'         => $row->type,
                'user_id'      => $row->user_id,
                'user_name'    => $name ?: '—',
                'roll'         => isset($row->student_roll)    ? $row->student_roll    : '',
                'phone'        => isset($row->student_phone)   ? $row->student_phone   : '',
                'class'        => isset($row->student_class)   ? $row->student_class   : '',
                'section'      => isset($row->student_section) ? $row->student_section : '',
                'reason'       => $row->reason ?: '',
                'status'       => $row->status,
                'requested_at' => date('d M Y, h:i A', strtotime($row->requested_at)),
            ];
        }

        echo json_encode(['status' => true, 'data' => $rows]);
    }

    // POST — mark one request as processed and deactivate the user/student/teacher
    public function mark_processed() {
        header('Content-Type: application/json');

        $id = (int)$this->input->post('id');
        if (!$id) {
            echo json_encode(['status' => false, 'message' => 'Invalid request ID']);
            return;
        }

        $request = $this->delete_account_request_m->get_request_by_id($id);
        if (!$request) {
            echo json_encode(['status' => false, 'message' => 'Request not found']);
            return;
        }

        if (!$this->delete_account_request_m->update_status($id, 'processed')) {
            echo json_encode(['status' => false, 'message' => 'Failed to update status']);
            return;
        }

        $this->delete_account_request_m->deactivate_user($request->type, $request->user_id);

        $this->activity_log_m->add([
            'module'      => 'delete_account_request',
            'action'      => 'deactivate',
            'record_id'   => $request->user_id,
            'record_type' => $request->type,
            'old_value'   => ['active' => 1],
            'new_value'   => ['active' => 0],
            'description' => ucfirst($request->type) . ' (ID: ' . $request->user_id . ') deactivated via Delete Account Request #' . $id . '. Reason: ' . ($request->reason ?: 'N/A'),
        ]);

        echo json_encode(['status' => true, 'message' => 'Marked as processed and account deactivated']);
    }

    // POST — permanently remove a request record
    public function remove() {
        header('Content-Type: application/json');

        $id = (int)$this->input->post('id');
        if (!$id) {
            echo json_encode(['status' => false, 'message' => 'Invalid request ID']);
            return;
        }

        if (!$this->delete_account_request_m->get_request_by_id($id)) {
            echo json_encode(['status' => false, 'message' => 'Request not found']);
            return;
        }

        if ($this->delete_account_request_m->delete_request($id)) {
            echo json_encode(['status' => true, 'message' => 'Request deleted successfully']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to delete request']);
        }
    }
}
