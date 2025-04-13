<?php 
class Dummy extends Admin_Controller{	
	public function __construct() {
		parent::__construct();
		$this->load->model('classes_m');
		$this->load->model('feetypes_m');
		$this->load->model('section_m');
		$this->load->model('student_m');
		$this->load->model('schoolyear_m');
		$this->load->model('invoice_m');
		$this->load->model('studentrelation_m');
		$this->load->model('weaverandfine_m');
		$this->load->model('parents_m');
		$this->load->model('payment_m');
		$this->load->model('village_m');

		$language = $this->session->userdata('lang');
		$this->lang->load('balancefeesreport', $language);
	}
 

	public function index() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/datepicker/datepicker.css',
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/datepicker/datepicker.js',
				'assets/select2/select2.js'
			)
		);

		$this->data['date'] = date("d-m-Y");		
		$this->data['feetypes'] = $this->feetypes_m->get_feetypes();
		$this->data['classes'] = $this->classes_m->general_get_classes();
		$this->data['villeges'] = $this->village_m->get_active_villages();
		// print_r($this->data['villeges']);die;
		$this->data["subview"] = "report/balancefees/BalanceFeesReportView";
		$this->load->view('_layout_main', $this->data);
	}

    public function dummy() {
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/datepicker/datepicker.css',
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/datepicker/datepicker.js',
				'assets/select2/select2.js'
			)
		);

		
		$this->data["subview"] = "dummy";
		$this->load->view('_layout_main', $this->data);
	}

}
?>