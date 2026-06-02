<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voice_messages extends Admin_Controller {

    public $upload_data  = [];
    public $upload_path  = '';
    public $temp_path    = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('voice_messages_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('voice_messages', $language);

        $this->upload_path = FCPATH . 'uploads/voice_messages/';
        $this->temp_path   = FCPATH . 'uploads/voice_messages/temp/';
        if (!is_dir($this->upload_path)) { @mkdir($this->upload_path, 0755, true); }
        if (!is_dir($this->temp_path))   { @mkdir($this->temp_path,   0755, true); }
    }

    public function index() {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');
        $this->data['voices'] = $this->voice_messages_m->get_all(['school_year_id' => $schoolyearID]);
        $this->data['subview'] = 'voice_messages/index';
        $this->load->view('_layout_main', $this->data);
    }

    public function add() {
        if ($_POST) {
            $this->form_validation->set_rules('voice_name', 'Voice Name', 'trim|required|max_length[255]');
            if ($this->form_validation->run() === FALSE) {
                $this->data['subview'] = 'voice_messages/add';
                $this->load->view('_layout_main', $this->data);
                return;
            }
            $upload = $this->_handleAudioUpload(null);
            if (!$upload['success']) {
                $this->data['upload_error'] = $upload['error'];
                $this->data['subview'] = 'voice_messages/add';
                $this->load->view('_layout_main', $this->data);
                return;
            }
            $this->voice_messages_m->insert([
                'voice_name'          => $this->input->post('voice_name'),
                'file_name'           => $upload['file_name'],
                'file_original_name'  => $upload['original_name'],
                'file_size'           => $upload['file_size'],
                'school_year_id'      => $this->session->userdata('defaultschoolyearID'),
                'created_by'          => $this->session->userdata('loginuserID'),
                'created_by_usertype' => $this->session->userdata('usertypeID'),
                'status'              => 1,
                'created_at'          => date('Y-m-d H:i:s'),
            ]);
            $this->session->set_flashdata('success', $this->lang->line('voice_add_success'));
            redirect(base_url('voice_messages/index'));
        } else {
            $this->data['subview'] = 'voice_messages/add';
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function edit() {
        $id    = (int)$this->uri->segment(3);
        $voice = $this->voice_messages_m->get_one(['id' => $id]);
        if (!$voice) {
            $this->session->set_flashdata('error', 'Record not found.');
            redirect(base_url('voice_messages/index'));
        }
        $this->data['voice'] = $voice;
        if ($_POST) {
            $this->form_validation->set_rules('voice_name', 'Voice Name', 'trim|required|max_length[255]');
            if ($this->form_validation->run() === FALSE) {
                $this->data['subview'] = 'voice_messages/edit';
                $this->load->view('_layout_main', $this->data);
                return;
            }
            $array = [
                'voice_name' => $this->input->post('voice_name'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            // Replace audio only if a new file/recording is provided
            $audioSource = $this->input->post('audio_source');
            $hasNewFile  = !empty($_FILES['audio_file']['name']);
            $hasRecorded = (bool)$this->input->post('recorded_audio_data');
            if ($audioSource && ($hasNewFile || $hasRecorded)) {
                $upload = $this->_handleAudioUpload($voice);
                if (!$upload['success']) {
                    $this->data['upload_error'] = $upload['error'];
                    $this->data['subview'] = 'voice_messages/edit';
                    $this->load->view('_layout_main', $this->data);
                    return;
                }
                $array['file_name']          = $upload['file_name'];
                $array['file_original_name'] = $upload['original_name'];
                $array['file_size']          = $upload['file_size'];
            }
            $this->voice_messages_m->update($array, $id);
            $this->session->set_flashdata('success', $this->lang->line('voice_update_success'));
            redirect(base_url('voice_messages/index'));
        } else {
            $this->data['subview'] = 'voice_messages/edit';
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function upload_temp() {
        header('Content-Type: application/json');
        if (empty($_FILES['audio_file']['name'])) {
            echo json_encode(['success' => false, 'error' => 'No file received']);
            return;
        }
        $ext     = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];
        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
            return;
        }
        $newName  = md5(uniqid('vm_tmp_', true)) . '.' . $ext;
        $fullPath = $this->temp_path . $newName;
        if (!move_uploaded_file($_FILES['audio_file']['tmp_name'], $fullPath)) {
            echo json_encode(['success' => false, 'error' => 'Failed to save temporary file']);
            return;
        }
        // Convert WebM/OGG to MP3 for iOS compatibility — silently skip if FFmpeg unavailable
        if (in_array($ext, ['webm', 'ogg']) && function_exists('exec')) {
            $mp3Name = md5(uniqid('vm_tmp_', true)) . '.mp3';
            $mp3Path = $this->temp_path . $mp3Name;
            @exec('ffmpeg -y -i ' . escapeshellarg($fullPath) . ' -acodec libmp3lame -ab 128k ' . escapeshellarg($mp3Path) . ' 2>/dev/null', $out, $code);
            if ($code === 0 && file_exists($mp3Path) && filesize($mp3Path) > 0) {
                @unlink($fullPath);
                $newName = $mp3Name;
            }
        }
        echo json_encode(['success' => true, 'file_name' => $newName]);
    }

    public function delete() {
        $id    = (int)$this->uri->segment(3);
        $voice = $this->voice_messages_m->get_one(['id' => $id]);
        if ($voice) {
            $filePath = $this->upload_path . $voice->file_name;
            if ($voice->file_name && file_exists($filePath)) {
                @unlink($filePath);
            }
            $this->voice_messages_m->delete($id);
            $this->session->set_flashdata('success', $this->lang->line('voice_delete_success'));
        }
        redirect(base_url('voice_messages/index'));
    }

    private function _handleAudioUpload($existingVoice = null) {
        $audioSource = $this->input->post('audio_source');

        if ($audioSource === 'upload') {
            if (empty($_FILES['audio_file']['name'])) {
                return ['success' => false, 'error' => 'Please select an audio file to upload.'];
            }
            $ext         = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
            $allowed     = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
            if (!in_array($ext, $allowed)) {
                return ['success' => false, 'error' => 'Invalid file type. Allowed: MP3, WAV, OGG, M4A, AAC.'];
            }
            $newName = md5(uniqid('vm_', true)) . '.' . $ext;
            $config  = [
                'upload_path'   => $this->upload_path,
                'allowed_types' => 'mp3|wav|ogg|m4a|aac|mpeg',
                'file_name'     => $newName,
                'max_size'      => 10240,
            ];
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('audio_file')) {
                return ['success' => false, 'error' => strip_tags($this->upload->display_errors())];
            }
            if ($existingVoice && $existingVoice->file_name) {
                @unlink($this->upload_path . $existingVoice->file_name);
            }
            $d = $this->upload->data();
            return ['success' => true, 'file_name' => $d['file_name'], 'original_name' => $d['orig_name'], 'file_size' => $d['file_size']];

        } elseif ($audioSource === 'record') {
            $tempFile = basename($this->input->post('temp_audio_file'));
            if (empty($tempFile)) {
                return ['success' => false, 'error' => 'No recording found. Please record your voice.'];
            }
            $tempPath = $this->temp_path . $tempFile;
            if (!file_exists($tempPath)) {
                return ['success' => false, 'error' => 'Temporary recording not found. Please re-record.'];
            }
            $ext     = strtolower(pathinfo($tempFile, PATHINFO_EXTENSION));
            $newName = md5(uniqid('vm_rec_', true)) . '.' . $ext;
            if (!rename($tempPath, $this->upload_path . $newName)) {
                return ['success' => false, 'error' => 'Failed to save recording.'];
            }
            if ($existingVoice && $existingVoice->file_name) {
                @unlink($this->upload_path . $existingVoice->file_name);
            }
            return ['success' => true, 'file_name' => $newName, 'original_name' => 'voice_recording.' . $ext, 'file_size' => filesize($this->upload_path . $newName)];

        } else {
            return ['success' => false, 'error' => 'Please upload an audio file or record your voice.'];
        }
    }
}
