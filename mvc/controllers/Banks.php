<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Banks extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('banks_m');
    }

    public function index() {
        $this->data['banks']   = $this->banks_m->get_banks();
        $this->data['subview'] = 'banks/index';
        $this->load->view('_layout_main', $this->data);
    }

    public function add() {
        if ($_POST) {
            $bank_name = trim($this->input->post('bank_name'));
            if (empty($bank_name)) {
                $this->session->set_flashdata('error', 'Bank name is required');
                redirect(base_url('banks/add'));
            }
            $existing = $this->banks_m->get_order_by_banks(['bank_name' => $bank_name]);
            if (customCompute($existing)) {
                $this->session->set_flashdata('error', 'Bank already exists');
                redirect(base_url('banks/add'));
            }
            $this->banks_m->insert_bank(['bank_name' => $bank_name, 'status' => 1]);
            $this->session->set_flashdata('success', 'Bank added successfully');
            redirect(base_url('banks/index'));
        } else {
            $this->data['subview'] = 'banks/add';
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function edit() {
        $id = (int) $this->uri->segment(3);
        if (!$id) { redirect(base_url('banks/index')); }

        $bank = $this->banks_m->get_single_bank(['banksID' => $id]);
        if (!$bank) { redirect(base_url('banks/index')); }

        if ($_POST) {
            $bank_name = trim($this->input->post('bank_name'));
            if (empty($bank_name)) {
                $this->session->set_flashdata('error', 'Bank name is required');
                redirect(base_url('banks/edit/' . $id));
            }
            $existing = $this->banks_m->get_order_by_banks(['bank_name' => $bank_name, 'banksID !=' => $id]);
            if (customCompute($existing)) {
                $this->session->set_flashdata('error', 'Bank already exists');
                redirect(base_url('banks/edit/' . $id));
            }
            $this->banks_m->update_bank(['bank_name' => $bank_name], $id);
            $this->session->set_flashdata('success', 'Bank updated successfully');
            redirect(base_url('banks/index'));
        } else {
            $this->data['bank']    = $bank;
            $this->data['subview'] = 'banks/edit';
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function delete() {
        $id = (int) $this->uri->segment(3);
        if ($id) {
            $this->banks_m->delete_bank($id);
            $this->session->set_flashdata('success', 'Bank deleted successfully');
        }
        redirect(base_url('banks/index'));
    }

    public function getBanksList() {
        $banks = $this->banks_m->get_active_banks();
        echo json_encode(['status' => TRUE, 'banks' => $banks]);
        exit;
    }

    public function addBankAjax() {
        $bank_name = trim($this->input->post('bank_name'));
        if (empty($bank_name)) {
            echo json_encode(['status' => FALSE, 'msg' => 'Bank name is required']);
            exit;
        }
        $existing = $this->banks_m->get_order_by_banks(['bank_name' => $bank_name]);
        if (customCompute($existing)) {
            echo json_encode(['status' => FALSE, 'msg' => 'Bank already exists']);
            exit;
        }
        $id = $this->banks_m->insert_bank(['bank_name' => $bank_name, 'status' => 1]);
        echo json_encode(['status' => TRUE, 'banksID' => $id, 'bank_name' => $bank_name]);
        exit;
    }
}
