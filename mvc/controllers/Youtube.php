<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Youtube extends Admin_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('classes_m');
        $this->load->model('section_m');
        $this->load->model('subject_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('youtube', $language);
    }

    public function index() {
        $schoolyearID = $this->session->userdata('defaultschoolyearID');

        $this->db->select('yl.*, c.classes as class_name, s.subject as subject_name, sec.section as section_name');
        $this->db->from('youtube_links yl');
        $this->db->join('classes c',   'c.classesID = yl.class_id',     'left');
        $this->db->join('subject s',   's.subjectID = yl.subject_id',   'left');
        $this->db->join('section sec', 'sec.sectionID = yl.section_id', 'left');
        $this->db->where('yl.school_year_id', $schoolyearID);
        $this->db->order_by('yl.sort_order', 'ASC');
        $this->db->order_by('yl.id', 'DESC');
        $links = $this->db->get()->result();

        foreach ($links as &$row) {
            preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|v\/|shorts\/))([a-zA-Z0-9_-]{11})/', $row->link, $m);
            $row->video_id  = isset($m[1]) ? $m[1] : '';
            $row->thumbnail = $row->video_id ? 'https://img.youtube.com/vi/' . $row->video_id . '/mqdefault.jpg' : '';
        }

        $this->data['youtube_links'] = $links;
        $this->data['subview'] = 'youtube/index';
        $this->load->view('_layout_main', $this->data);
    }

    public function add() {
        $this->data['headerassets'] = [
            'css' => ['assets/select2/css/select2.css', 'assets/select2/css/select2-bootstrap.css'],
            'js'  => ['assets/select2/select2.js']
        ];
        $this->data['classes'] = $this->classes_m->general_get_classes();

        if ($_POST) {
            $this->form_validation->set_rules($this->rules());
            if ($this->form_validation->run() == FALSE) {
                $this->data['subview'] = 'youtube/add';
                $this->load->view('_layout_main', $this->data);
            } else {
                $array = [
                    'link'                => $this->input->post('link'),
                    'title'               => $this->input->post('title'),
                    'description'         => $this->input->post('description'),
                    'school_year_id'      => $this->session->userdata('defaultschoolyearID'),
                    'class_id'            => (int)$this->input->post('class_id'),
                    'section_id'          => (int)$this->input->post('section_id'),
                    'subject_id'          => (int)$this->input->post('subject_id'),
                    'sort_order'          => (int)$this->input->post('sort_order'),
                    'created_by'          => $this->session->userdata('loginuserID'),
                    'created_by_usertype' => $this->session->userdata('usertypeID'),
                    'status'              => 1,
                ];
                $this->db->insert('youtube_links', $array);
                $this->session->set_flashdata('success', $this->lang->line('youtube_add_success'));
                redirect(base_url('youtube/index'));
            }
        } else {
            $this->data['subview'] = 'youtube/add';
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function edit() {
        $id = (int)$this->uri->segment(3);
        $this->data['youtube'] = $this->db->get_where('youtube_links', ['id' => $id])->row();

        if (!$this->data['youtube']) {
            $this->session->set_flashdata('error', 'Record not found.');
            redirect(base_url('youtube/index'));
        }

        $this->data['headerassets'] = [
            'css' => ['assets/select2/css/select2.css', 'assets/select2/css/select2-bootstrap.css'],
            'js'  => ['assets/select2/select2.js']
        ];
        $this->data['classes'] = $this->classes_m->general_get_classes();

        if ($_POST) {
            $this->form_validation->set_rules($this->rules());
            if ($this->form_validation->run() == FALSE) {
                $this->data['subview'] = 'youtube/edit';
                $this->load->view('_layout_main', $this->data);
            } else {
                $array = [
                    'link'        => $this->input->post('link'),
                    'title'       => $this->input->post('title'),
                    'description' => $this->input->post('description'),
                    'class_id'    => (int)$this->input->post('class_id'),
                    'section_id'  => (int)$this->input->post('section_id'),
                    'subject_id'  => (int)$this->input->post('subject_id'),
                    'sort_order'  => (int)$this->input->post('sort_order'),
                    'status'      => (int)$this->input->post('status'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
                $this->db->where('id', $id)->update('youtube_links', $array);
                $this->session->set_flashdata('success', $this->lang->line('youtube_update_success'));
                redirect(base_url('youtube/index'));
            }
        } else {
            $this->data['subview'] = 'youtube/edit';
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function delete() {
        $id = (int)$this->uri->segment(3);
        if ($id && $this->db->get_where('youtube_links', ['id' => $id])->row()) {
            $this->db->delete('youtube_links', ['id' => $id]);
            $this->session->set_flashdata('success', $this->lang->line('youtube_delete_success'));
        }
        redirect(base_url('youtube/index'));
    }

    public function getSection() {
        $classesID = $this->input->post('classesID');
        if ((int)$classesID) {
            $sections = $this->section_m->general_get_order_by_section(['classesID' => $classesID]);
            echo "<option value='0'>Please Select</option>";
            if (customCompute($sections))
                foreach ($sections as $s)
                    echo "<option value='{$s->sectionID}'>{$s->section}</option>";
        }
    }

    public function getSubject() {
        $classesID = $this->input->post('classesID');
        if ((int)$classesID) {
            $subjects = $this->subject_m->general_get_order_by_subject(['classesID' => $classesID]);
            echo "<option value='0'>Please Select</option>";
            if (customCompute($subjects))
                foreach ($subjects as $s)
                    echo "<option value='{$s->subjectID}'>{$s->subject}</option>";
        }
    }

    protected function rules() {
        return [
            ['field' => 'title',    'label' => 'Title',        'rules' => 'required|max_length[255]'],
            ['field' => 'link',     'label' => 'YouTube Link', 'rules' => 'required|callback_valid_youtube_url'],
            ['field' => 'class_id', 'label' => 'Class',        'rules' => 'required|callback_unique_data'],
        ];
    }

    public function unique_data($data) {
        if ($data === "0" || $data === "" || $data === null) {
            $this->form_validation->set_message('unique_data', 'The %s field is required.');
            return FALSE;
        }
        return TRUE;
    }

    public function valid_youtube_url($url) {
        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|v\/|shorts\/))([a-zA-Z0-9_-]{11})/', $url, $m);
        if (empty($m[1])) {
            $this->form_validation->set_message('valid_youtube_url', 'Please enter a valid YouTube URL.');
            return FALSE;
        }
        return TRUE;
    }
}
