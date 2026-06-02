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
        if ((int)$id) {
            $voice = $this->voice_messages_m->get_one(['id' => $id, 'school_year_id' => $schoolyearID]);
            if (!$voice) {
                $this->response(['status' => false, 'message' => 'Not found', 'data' => []], REST_Controller::HTTP_NOT_FOUND);
                return;
            }
            $voice->file_url = base_url('uploads/voice_messages/' . $voice->file_name);
            unset($voice->created_by, $voice->created_by_usertype);
            $this->retdata['voice_message'] = $voice;
        } else {
            $voices = $this->voice_messages_m->get_all(['school_year_id' => $schoolyearID]);
            if (!$voices) $voices = [];
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
|    curl -X GET "BASE_URL/index" -H "Authorization: Bearer TOKEN"
|
| 2. GET ONE
|    GET  api/v10/voice_messages/index/1
|    curl -X GET "BASE_URL/index/1" -H "Authorization: Bearer TOKEN"
|
| 3. ADD
|    POST api/v10/voice_messages/add
|    curl -X POST "BASE_URL/add" \
|         -H "Authorization: Bearer TOKEN" \
|         -F "voice_name=Good Morning" \
|         -F "audio_file=@/path/to/file.mp3"
|
| 4. EDIT
|    POST api/v10/voice_messages/edit
|    curl -X POST "BASE_URL/edit" \
|         -H "Authorization: Bearer TOKEN" \
|         -F "id=1" \
|         -F "voice_name=Updated Name" \
|         -F "audio_file=@/path/to/new_file.mp3"
|
| 5. DELETE
|    POST api/v10/voice_messages/delete
|    curl -X POST "BASE_URL/delete" \
|         -H "Authorization: Bearer TOKEN" \
|         -F "id=1"
|==========================================================================
*/
