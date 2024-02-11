<?php if ( !defined('BASEPATH') ) {
    exit('No direct script access allowed');
}

class Msg91
{
    protected $authKey;
    protected $senderID;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->model('smssettings_m');
        $this->ci->load->database();

        $msg91_bind = [];
        $get_msg91s = $this->ci->smssettings_m->get_order_by_msg91();
        foreach ( $get_msg91s as $key => $get_msg91 ) {
            $msg91_bind[ $get_msg91->field_names ] = $get_msg91->field_values;
        }
        // $this->authKey  = "8c79d04588b945d2083b";//$msg91_bind['msg91_authKey'];
        $this->senderID = $msg91_bind['msg91_senderID'];
        $this->username = $msg91_bind['msg91_username'];
        $this->password = $msg91_bind['msg91_password'];
        $this->PEID = $msg91_bind['msg91_PEID'];
    }

    public function send( $to, $message, $template_id=0 )
    {
       
    
        //Your message to send, Add URL encoding here.
        $message = urlencode($message);

        //Define route
        // $route   = 4;
        // $country = 0;
        //Prepare you post parameters
        // $postData = [
            // 'mobiles' => $to,
            // 'message' => $message,
            // 'apikey' => $this->authKey,
            // 'sender'  => $this->senderID
            // 'route'   => $route,
            // 'country' => $country
        // ];

        // $url = "http://api.msg91.com/api/sendhttp.php";
        // $url = "http://182.18.170.201/api.php?username=".$this->username."&password=".$this->password."&to=".$to."&from=".$this->senderID."&message=".$message."&PEID=".$this->PEID."&templateid=".$template_id;
        //$url = "https://smslogin.co/v3/api.php?username=Demoschool123&apikey=7687263fe67c116e34b6&senderid=VGNSSP&mobile=8500814626,9494022475&message=".$message; //sending static message
        $url = "https://smslogin.co/v3/api.php?username=".$this->username."&apikey=".$this->password."&senderid=".$this->senderID."&mobile=".$to."&message=".$message; //sendin dynamic message

        // error_log($url);exit;
        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'GET'
            // CURLOPT_POSTFIELDS     => $postData
            //,CURLOPT_FOLLOWLOCATION => true
        ]);

        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);
       

        //Print error if any
        if ( curl_errno($ch) ) {
            echo 'error:' . curl_error($ch);
        }
        //  echo $url;
        // print_r($output);die;

        $data = array(
            'request_url' => $url,
            'api_response' => $output,
            'created_on' => date("Y-m-d H:i:s"),
            'type' => "SMS",
        ); 
        if (strpos($output, 'campid') == false) { 
            $this->ci->db->insert('sms_error_logs',$data);
        } 

       
        curl_close($ch);
        if ( $output ) {
            // return true;
            // $output1 = json_decode($output,true);
            return ($output);
        }
        return false;
    }

    public function getSmsReport( $campid )
    {
       
    
        //Your message to send, Add URL encoding here.
        $message = urlencode($message);
        $url = "https://smslogin.co/v3/api.php?username=".$this->username."&apikey=".$this->password."&campid=".$campid;  
 
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'GET'
            // CURLOPT_POSTFIELDS     => $postData
            //,CURLOPT_FOLLOWLOCATION => true
        ]);

        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);
        //Print error if any
        if ( curl_errno($ch) ) {
            echo 'error:' . curl_error($ch);
        }
        // print_r($output);die;
        curl_close($ch);
        if ( $output ) {
            // return true;
            return $output;
        }
        return false;
    }

}