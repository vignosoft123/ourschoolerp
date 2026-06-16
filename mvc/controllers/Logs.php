<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logs extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('activity_log_m');
        $this->lang->load('logs', $this->data['language']);
    }

    public function index() {
        $limit  = 25;
        $page   = max(1, (int)$this->input->get('page'));
        $offset = ($page - 1) * $limit;

        $filters = [
            'module'      => $this->input->get('module'),
            'action'      => $this->input->get('action'),
            'record_type' => $this->input->get('record_type'),
            'date_from'   => $this->input->get('date_from'),
            'date_to'     => $this->input->get('date_to'),
            'search'      => $this->input->get('search'),
        ];

        $total = $this->activity_log_m->count_logs($filters);
        $logs  = $this->activity_log_m->get_logs($filters, $limit, $offset);

        $this->data['logs']       = $logs;
        $this->data['total']      = $total;
        $this->data['page']       = $page;
        $this->data['limit']      = $limit;
        $this->data['last_page']  = (int)ceil($total / $limit);
        $this->data['filters']    = $filters;
        $this->data['subview']    = 'logs/index';
        $this->load->view('_layout_main', $this->data);
    }
}
