<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Push_notification extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('push_notification_m');
        $this->load->model('classes_m');
        $this->load->model('section_m');
        $this->load->helper('fcm_helper');
    }

    public function index() {
        $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';
        $serviceAccountOk   = false;
        if (file_exists($serviceAccountPath)) {
            $sa = json_decode(file_get_contents($serviceAccountPath), true);
            $serviceAccountOk = (json_last_error() === JSON_ERROR_NONE && ($sa['project_id'] ?? '') === 'our-school-erp-cbf37');
        }

        $this->data['headerassets'] = [
            'css' => [
                'assets/select2/css/select2.css',
                'assets/select2/css/select2-bootstrap.css',
            ],
            'js' => [
                'assets/select2/select2.js',
            ],
        ];
        $this->data['classes']            = $this->classes_m->get_classes();
        $this->data['service_account_ok'] = $serviceAccountOk;
        // topbarschoolyears + schoolyearsessionobj are set by Admin_Controller
        $this->data['subview']            = 'push_notification/index';
        $this->load->view('_layout_main', $this->data);
    }

    // AJAX — load students by school year / class / section for the Users multi-select
    public function load_students() {
        header('Content-Type: application/json');
        $schoolyearID = (int) $this->input->get_post('schoolyearID');
        $classesID    = (int) $this->input->get_post('classesID');
        $sectionID    = (int) $this->input->get_post('sectionID');

        $students = $this->push_notification_m->load_students_for_filter(
            $schoolyearID ?: null,
            $classesID    ?: null,
            $sectionID    ?: null
        );

        $output = [];
        foreach ($students as $s) {
            $output[] = ['id' => $s->studentID, 'text' => $s->name];
        }
        echo json_encode($output);
    }

    // AJAX — load sections for a class
    public function load_sections() {
        header('Content-Type: application/json');
        $classesID = (int) $this->input->get_post('classesID');
        if (!$classesID) { echo json_encode([]); return; }
        $sections = $this->section_m->get_join_section($classesID);
        $output   = [];
        if ($sections) {
            foreach ($sections as $s) {
                $output[] = ['sectionID' => $s->sectionID, 'section' => $s->section];
            }
        }
        echo json_encode($output);
    }

    // POST — AJAX send handler
    public function send() {
        header('Content-Type: application/json');

        $schoolyearID      = (int) $this->input->post('schoolyearID');
        $classesID         = (int) $this->input->post('classesID');
        $sectionID         = (int) $this->input->post('sectionID');
        $title             = trim($this->input->post('title'));
        $message           = trim($this->input->post('message'));
        $notification_type = $this->input->post('notification_type') ?: 'general';
        $image_url         = trim($this->input->post('image_url') ?: '');

        if (empty($title) || empty($message)) {
            echo json_encode(['status' => false, 'message' => 'Title and message are required.']);
            return;
        }

        $rawIDs  = $this->input->post('userIDs');
        $userIDs = (!empty($rawIDs) && is_array($rawIDs)) ? array_map('intval', $rawIDs) : [];

        // When specific students are selected, skip class/section filter — the user
        // explicitly chose those students from a token-filtered list, so just send to them.
        // Apply class/section filter only when no explicit selection is made.
        if (!empty($userIDs)) {
            $filterClass   = null;
            $filterSection = null;
        } else {
            $filterClass   = $classesID > 0 ? $classesID : null;
            $filterSection = $sectionID > 0 ? $sectionID : null;
        }

        $students = $this->push_notification_m->get_students_with_tokens(
            $filterClass,
            $filterSection,
            !empty($userIDs) ? $userIDs : null
        );

        if (empty($students)) {
            echo json_encode(['status' => false, 'message' => 'No students with the app installed found for the selected recipients.']);
            return;
        }

        $tokens = array_column((array) $students, 'device_token');
        $result = send_fcm_push_bulk($tokens, $title, $message, ['type' => $notification_type], $image_url ?: null);

        // Resolve names for the log
        $className   = null;
        $sectionName = null;
        if (!empty($students)) {
            $first       = $students[0];
            $className   = $first->classes  ?? null;
            $sectionName = $filterSection ? ($first->section ?? null) : null;
        }

        if ($filterSection) {
            $recipientType = 'section';
        } elseif ($filterClass) {
            $recipientType = 'class';
        } else {
            $recipientType = 'all';
        }

        $logData = [
            'title'             => $title,
            'message'           => $message,
            'notification_type' => $notification_type,
            'recipient_type'    => $recipientType,
            'classesID'         => $filterClass,
            'sectionID'         => $filterSection,
            'class_name'        => $className,
            'section_name'      => $sectionName,
            'total_recipients'  => count($tokens),
            'success_count'     => $result['successCount'] ?? 0,
            'failure_count'     => $result['failureCount'] ?? 0,
            'sent_by_userID'    => $this->session->userdata('loginuserID'),
            'sent_by_name'      => $this->session->userdata('loginname'),
            'sent_at'           => date('Y-m-d H:i:s'),
        ];
        $this->push_notification_m->log_notification($logData);

        if ($result['status']) {
            echo json_encode([
                'status'          => true,
                'message'         => 'Notifications sent successfully.',
                'totalRecipients' => count($tokens),
                'successCount'    => $result['successCount'],
                'failureCount'    => $result['failureCount'],
            ]);
        } else {
            echo json_encode([
                'status'  => false,
                'message' => $result['message'] ?? 'Failed to send notifications.',
            ]);
        }
    }

    public function history() {
        $this->data['logs']    = $this->push_notification_m->get_history(100);
        $this->data['subview'] = 'push_notification/history';
        $this->load->view('_layout_main', $this->data);
    }

    public function setup() {
        $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $jsonContent = trim($this->input->post('service_account_json'));
            $decoded     = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->session->set_flashdata('error', 'Invalid JSON format: ' . json_last_error_msg());
                redirect('Push_notification/setup'); return;
            }

            $required = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
            foreach ($required as $field) {
                if (empty($decoded[$field])) {
                    $this->session->set_flashdata('error', "Missing required field: $field");
                    redirect('Push_notification/setup'); return;
                }
            }

            if (file_exists($serviceAccountPath)) {
                copy($serviceAccountPath, $serviceAccountPath . '.backup.' . date('YmdHis'));
            }

            if (file_put_contents($serviceAccountPath, json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {
                $this->session->set_flashdata('success', 'Service account updated successfully. Project: ' . $decoded['project_id']);
            } else {
                $this->session->set_flashdata('error', 'Failed to write file. Check PHP write permissions for: ' . $serviceAccountPath);
            }
            redirect('Push_notification/setup'); return;
        }

        $this->data['service_account_path'] = $serviceAccountPath;
        $this->data['service_account_info'] = null;
        if (file_exists($serviceAccountPath)) {
            $sa = json_decode(file_get_contents($serviceAccountPath), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->data['service_account_info'] = [
                    'project_id'   => $sa['project_id']   ?? 'Unknown',
                    'client_email' => $sa['client_email'] ?? 'Unknown',
                    'file_size'    => number_format(filesize($serviceAccountPath)) . ' bytes',
                    'is_correct'   => ($sa['project_id'] ?? '') === 'our-school-erp-cbf37',
                ];
            }
        }

        $this->data['subview'] = 'push_notification/setup';
        $this->load->view('_layout_main', $this->data);
    }

    public function verify() {
        $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';
        $checks             = [];

        if (!file_exists($serviceAccountPath)) {
            $checks[] = ['label' => 'Service account file', 'ok' => false, 'detail' => 'File not found: ' . $serviceAccountPath];
            $this->data['verify_checks'] = $checks;
            $this->data['verify_passed'] = false;
            $this->data['subview']       = 'push_notification/setup';
            $this->load->view('_layout_main', $this->data); return;
        }
        $checks[] = ['label' => 'Service account file', 'ok' => true, 'detail' => 'File found'];

        $sa = json_decode(file_get_contents($serviceAccountPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $checks[] = ['label' => 'JSON format', 'ok' => false, 'detail' => json_last_error_msg()];
            $this->data['verify_checks'] = $checks;
            $this->data['verify_passed'] = false;
            $this->data['subview']       = 'push_notification/setup';
            $this->load->view('_layout_main', $this->data); return;
        }
        $checks[] = ['label' => 'JSON format', 'ok' => true, 'detail' => 'Valid JSON'];

        $projectId = $sa['project_id'] ?? '';
        $checks[]  = [
            'label'  => 'Project ID',
            'ok'     => $projectId === 'our-school-erp-cbf37',
            'detail' => $projectId === 'our-school-erp-cbf37'
                ? "Matches: $projectId"
                : "Mismatch — found: $projectId, expected: our-school-erp-cbf37",
        ];

        $required      = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
        $missingFields = array_filter($required, fn($f) => empty($sa[$f]));
        $checks[]      = [
            'label'  => 'Required fields',
            'ok'     => empty($missingFields),
            'detail' => empty($missingFields) ? 'All required fields present' : 'Missing: ' . implode(', ', $missingFields),
        ];

        try {
            require_once FCPATH . 'vendor/autoload.php';
            $factory   = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
            $messaging = $factory->createMessaging();
            $checks[]  = ['label' => 'Firebase SDK', 'ok' => true, 'detail' => 'Initialized successfully — messaging service ready'];
        } catch (\Exception $e) {
            $checks[] = ['label' => 'Firebase SDK', 'ok' => false, 'detail' => $e->getMessage()];
        }

        $allPassed = array_reduce($checks, fn($carry, $c) => $carry && $c['ok'], true);

        $this->data['verify_checks']        = $checks;
        $this->data['verify_passed']        = $allPassed;
        $this->data['service_account_path'] = $serviceAccountPath;
        $this->data['service_account_info'] = [
            'project_id'   => $sa['project_id']   ?? 'Unknown',
            'client_email' => $sa['client_email'] ?? 'Unknown',
            'file_size'    => number_format(filesize($serviceAccountPath)) . ' bytes',
            'is_correct'   => ($sa['project_id'] ?? '') === 'our-school-erp-cbf37',
        ];
        $this->data['subview'] = 'push_notification/setup';
        $this->load->view('_layout_main', $this->data);
    }
}
