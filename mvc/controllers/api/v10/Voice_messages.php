<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Voice_messages extends Api_Controller {

    public $upload_data = [];

    public function __construct() {
        parent::__construct();
        $this->load->model('voice_messages_m');
    }

    // GET api/v10/voice_messages/index or api/v10/voice_messages/index/1
    public function index_get($id = null) {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $usertypeID   = (int)$this->session->userdata('usertypeID');
        $loginuserID  = (int)$this->session->userdata('loginuserID');

        if ((int)$id) {
            $this->db->select('vm.*, c.classes as class_name, s.section as section_name');
            $this->db->from('voice_messages vm');
            $this->db->join('classes c', 'c.classesID = vm.class_id', 'left');
            $this->db->join('section s', 's.sectionID = vm.section_id', 'left');
            $this->db->where('vm.id', $id);
            $this->db->where('vm.school_year_id', $schoolyearID);
            $query = $this->db->get();
            $voice = $query ? $query->row() : null;
            if (!$voice) {
                $this->response(['status' => false, 'message' => 'Not found', 'data' => []], REST_Controller::HTTP_NOT_FOUND);
                return;
            }
            $voice->file_url = base_url('uploads/voice_messages/' . $voice->file_name);
            unset($voice->created_by, $voice->created_by_usertype);
            $this->retdata['voice_message'] = $voice;
        } else {
            $this->db->select('vm.*, c.classes as class_name, s.section as section_name');
            $this->db->from('voice_messages vm');
            $this->db->join('classes c', 'c.classesID = vm.class_id', 'left');
            $this->db->join('section s', 's.sectionID = vm.section_id', 'left');
            $this->db->where('vm.school_year_id', $schoolyearID);

            if (in_array($usertypeID, [1, 2])) {
                // Admin/Teacher: optional class_id and section_id filter via query params
                $filterClassID   = (int)$this->get('class_id');
                $filterSectionID = (int)$this->get('section_id');
                if ($filterClassID) {
                    $this->db->where('vm.class_id', $filterClassID);
                    if ($filterSectionID) {
                        $this->db->where('vm.section_id', $filterSectionID);
                    }
                }
            } else {
                // Students: show only messages matching their class/section (or broadcast messages with class_id=0)
                $student = $this->db->get_where('students', ['loginuserID' => $loginuserID])->row();
                if ($student) {
                    $this->db->group_start();
                        $this->db->where('vm.class_id', 0);
                        $this->db->or_group_start();
                            $this->db->where('vm.class_id', $student->classesID);
                            $this->db->group_start();
                                $this->db->where('vm.section_id', 0);
                                $this->db->or_where('vm.section_id', $student->sectionID);
                            $this->db->group_end();
                        $this->db->group_end();
                    $this->db->group_end();
                }
            }

            $this->db->order_by('vm.id', 'DESC');
            $query  = $this->db->get();
            $voices = $query ? $query->result() : [];
            foreach ($voices as &$v) {
                $v->file_url = base_url('uploads/voice_messages/' . $v->file_name);
                unset($v->created_by, $v->created_by_usertype);
            }
            $this->retdata['voice_messages'] = $voices;
        }
        $this->response(['status' => true, 'message' => 'Success', 'data' => $this->retdata], REST_Controller::HTTP_OK);
    }

    private function _requireAdminOrTeacher() {
        $usertypeID = (int)$this->session->userdata('usertypeID');
        if (!in_array($usertypeID, [1, 2])) {
            $this->response(['status' => false, 'message' => 'Unauthorized. Only Admin and Teachers can perform this action.', 'data' => []], REST_Controller::HTTP_UNAUTHORIZED);
            return false;
        }
        return true;
    }

    // POST api/v10/voice_messages/add
    public function add_post() {
        if (!$this->_requireAdminOrTeacher()) return;
        $voiceName = $this->post('voice_name');
        if (empty($voiceName)) {
            $this->response(['status' => false, 'message' => 'voice_name is required', 'data' => []], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $upload = $this->_handleApiAudioUpload();
        if (!$upload['success']) {
            $this->response(['status' => false, 'message' => $upload['error'], 'data' => []], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $id = $this->voice_messages_m->insert([
            'voice_name'          => $voiceName,
            'class_id'            => (int)$this->post('class_id'),
            'section_id'          => (int)$this->post('section_id'),
            'file_name'           => $upload['file_name'],
            'file_original_name'  => $upload['original_name'],
            'file_size'           => $upload['file_size'],
            'school_year_id'      => $this->session->userdata('defaultschoolyearID'),
            'created_by'          => $this->session->userdata('loginuserID'),
            'created_by_usertype' => $this->session->userdata('usertypeID'),
            'status'              => 1,
            'created_at'          => date('Y-m-d H:i:s'),
        ]);
        $this->retdata['id'] = $id;
        $this->response(['status' => true, 'message' => 'Voice message created', 'data' => $this->retdata], REST_Controller::HTTP_OK);
    }

    // POST api/v10/voice_messages/edit
    public function edit_post() {
        if (!$this->_requireAdminOrTeacher()) return;
        $id = (int)$this->post('id');
        if (!$id) {
            $this->response(['status' => false, 'message' => 'id is required', 'data' => []], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $voice = $this->voice_messages_m->get_one(['id' => $id]);
        if (!$voice) {
            $this->response(['status' => false, 'message' => 'Not found', 'data' => []], REST_Controller::HTTP_NOT_FOUND);
            return;
        }
        $array = ['updated_at' => date('Y-m-d H:i:s')];
        if ($this->post('voice_name')) {
            $array['voice_name'] = $this->post('voice_name');
        }
        if ($this->post('class_id') !== null) {
            $array['class_id'] = (int)$this->post('class_id');
        }
        if ($this->post('section_id') !== null) {
            $array['section_id'] = (int)$this->post('section_id');
        }
        if (!empty($_FILES['audio_file']['name'])) {
            $upload = $this->_handleApiAudioUpload();
            if (!$upload['success']) {
                $this->response(['status' => false, 'message' => $upload['error'], 'data' => []], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }
            if ($voice->file_name) {
                @unlink('./uploads/voice_messages/' . $voice->file_name);
            }
            $array['file_name']          = $upload['file_name'];
            $array['file_original_name'] = $upload['original_name'];
            $array['file_size']          = $upload['file_size'];
        }
        $this->voice_messages_m->update($array, $id);
        $this->response(['status' => true, 'message' => 'Voice message updated', 'data' => []], REST_Controller::HTTP_OK);
    }

    // POST api/v10/voice_messages/delete
    public function delete_post() {
        if (!$this->_requireAdminOrTeacher()) return;
        $id = (int)$this->post('id');
        if (!$id) {
            $this->response(['status' => false, 'message' => 'id is required', 'data' => []], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $voice = $this->voice_messages_m->get_one(['id' => $id]);
        if (!$voice) {
            $this->response(['status' => false, 'message' => 'Not found', 'data' => []], REST_Controller::HTTP_NOT_FOUND);
            return;
        }
        if ($voice->file_name) { @unlink('./uploads/voice_messages/' . $voice->file_name); }
        $this->voice_messages_m->delete($id);
        $this->response(['status' => true, 'message' => 'Voice message deleted', 'data' => []], REST_Controller::HTTP_OK);
    }

    private function _handleApiAudioUpload() {
        if (empty($_FILES['audio_file']['name'])) {
            return ['success' => false, 'error' => 'audio_file is required'];
        }
        $ext     = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];
        if (!in_array($ext, $allowed)) {
            return ['success' => false, 'error' => 'Invalid file type. Allowed: mp3, wav, ogg, m4a, aac, webm'];
        }
        $uploadPath = './uploads/voice_messages/';
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0755, true); }
        $newName = md5(uniqid('vm_api_', true)) . '.' . $ext;
        $config  = [
            'upload_path'   => $uploadPath,
            'allowed_types' => 'mp3|wav|ogg|m4a|aac|webm|mpeg',
            'file_name'     => $newName,
            'max_size'      => 10240,
        ];
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('audio_file')) {
            return ['success' => false, 'error' => strip_tags($this->upload->display_errors())];
        }
        $d = $this->upload->data();
        return ['success' => true, 'file_name' => $d['file_name'], 'original_name' => $d['orig_name'], 'file_size' => $d['file_size']];
    }
}

/*
|==========================================================================
| VOICE MESSAGES API ENDPOINTS
|==========================================================================
| Base URL: https://your-domain.com/api/v10/voice_messages
| Auth Header: Authorization: Bearer {jwt_token}
|
| 1. LIST ALL
|    GET  api/v10/voice_messages/index
|    GET  api/v10/voice_messages/index?class_id=3
|    GET  api/v10/voice_messages/index?class_id=3&section_id=2
|    curl -X GET "BASE_URL/index" -H "Authorization: Bearer TOKEN"
|    Response includes: class_name, section_name, file_url
|    Filtering (Admin/Teacher only):
|      - Pass class_id to filter by class
|      - Pass class_id + section_id to filter by class and section
|    Students: auto-filtered to their class/section plus broadcasts (class_id=0).
|
| 2. GET ONE
|    GET  api/v10/voice_messages/index/1
|    curl -X GET "BASE_URL/index/1" -H "Authorization: Bearer TOKEN"
|
| 3. ADD  (Admin/Teacher only)
|    POST api/v10/voice_messages/add
|    curl -X POST "BASE_URL/add" \
|         -H "Authorization: Bearer TOKEN" \
|         -F "voice_name=Good Morning" \
|         -F "class_id=3" \
|         -F "section_id=2" \
|         -F "audio_file=@/path/to/file.mp3"
|    Note: Omit class_id (or send 0) to broadcast to all classes.
|          Omit section_id (or send 0) to broadcast to all sections.
|
| 4. EDIT  (Admin/Teacher only)
|    POST api/v10/voice_messages/edit
|    curl -X POST "BASE_URL/edit" \
|         -H "Authorization: Bearer TOKEN" \
|         -F "id=1" \
|         -F "voice_name=Updated Name" \
|         -F "class_id=3" \
|         -F "section_id=2" \
|         -F "audio_file=@/path/to/new_file.mp3"
|
| 5. DELETE  (Admin/Teacher only)
|    POST api/v10/voice_messages/delete
|    curl -X POST "BASE_URL/delete" \
|         -H "Authorization: Bearer TOKEN" \
|         -F "id=1"
|==========================================================================
*/
