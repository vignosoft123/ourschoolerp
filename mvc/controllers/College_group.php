<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class College_group extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("college_group_m");
        $language = $this->session->userdata('lang');
        $this->lang->load('college_group', $language);
    }

    public function index() {
        $this->data['college_groups'] = $this->college_group_m->get_order_by_college_group();
        $this->data["subview"] = "college_group/index";
        $this->load->view('_layout_main', $this->data);
    }

    protected function rules() {
        $rules = array(
            array(
                'field' => 'college_name',
                'label' => 'College Name',
                'rules' => 'trim|required|xss_clean|max_length[255]'
            ),
            array(
                'field' => 'college_url',
                'label' => 'College URL',
                'rules' => 'trim|required|xss_clean|max_length[255]|valid_url'
            )
        );
        return $rules;
    }

    public function add() {
        if($_POST) {
            $rules = $this->rules();
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $this->data["subview"] = "college_group/add";
                $this->load->view('_layout_main', $this->data);
            } else {
                $array = array(
                    "college_name" => $this->input->post("college_name"),
                    "college_url" => $this->input->post("college_url"),
                    "status" => 1
                );
                
                $this->college_group_m->insert_college_group($array);
                $this->session->set_flashdata('success', $this->lang->line('menu_success'));
                redirect(base_url("college_group/index"));
            }
        } else {
            $this->data["subview"] = "college_group/add";
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function edit() {
        $id = htmlentities(escapeString($this->uri->segment(3)));
        if((int)$id) {
            $this->data['college_group'] = $this->college_group_m->get_single_college_group(array('collegegroupID' => $id));
            if($this->data['college_group']) {
                if($_POST) {
                    $rules = $this->rules();
                    $this->form_validation->set_rules($rules);
                    if ($this->form_validation->run() == FALSE) {
                        $this->data["subview"] = "college_group/edit";
                        $this->load->view('_layout_main', $this->data);
                    } else {
                        $array = array(
                            "college_name" => $this->input->post("college_name"),
                            "college_url" => $this->input->post("college_url")
                        );

                        $this->college_group_m->update_college_group($array, $id);
                        $this->session->set_flashdata('success', $this->lang->line('menu_success'));
                        redirect(base_url("college_group/index"));
                    }
                } else {
                    $this->data["subview"] = "college_group/edit";
                    $this->load->view('_layout_main', $this->data);
                }
            } else {
                $this->data["subview"] = "error";
                $this->load->view('_layout_main', $this->data);
            }
        } else {
            $this->data["subview"] = "error";
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function delete() {
        $id = htmlentities(escapeString($this->uri->segment(3)));
        if((int)$id) {
            $this->data['college_group'] = $this->college_group_m->get_single_college_group(array('collegegroupID' => $id));
            if($this->data['college_group']) {
                $this->college_group_m->delete_college_group($id);
                $this->session->set_flashdata('success', $this->lang->line('menu_success'));
                redirect(base_url("college_group/index"));
            } else {
                redirect(base_url("college_group/index"));
            }
        } else {
            redirect(base_url("college_group/index"));
        }
    }
}
