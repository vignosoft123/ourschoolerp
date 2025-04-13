<?php if ( !defined('BASEPATH') ) {
    exit('No direct script access allowed');
}

class WhatsappLibrary
{
    protected $authKey;
    protected $senderID;

    public function __construct()
    {  
        $this->ci =& get_instance();
        $this->ci->load->model('smssettings_m');
        $this->ci->load->database();

        $msg91_bind = [];
        $get_msg91s = $this->ci->smssettings_m->get_order_by_whatsapp();
        foreach ( $get_msg91s as $key => $get_msg91 ) {
            $msg91_bind[ $get_msg91->field_names ] = $get_msg91->field_values;
        }
        // $this->authKey  = "8c79d04588b945d2083b";//$msg91_bind['msg91_authKey'];
        $this->senderID = $msg91_bind['whatsapp_sender'];
        $this->username = $msg91_bind['whatsapp_user'];
        $this->password = $msg91_bind['whatsapp_password']; 
    }

    public function sendWhatsapp($to, $message, $template_name='')
    { echo 'dddd';die;
        //Your message to send, Add URL encoding here.
        $msg = $message;
        $message = urlencode($message);

       
       echo $url = " http://bwa.mindwhile.com/api/sendmsg.php?user=$this->username&pass=$this->password&sender=$this->senderID&phone=$to&text=$template_name&priority=wa&stype=normal&Params=$message";die;

        // echo ($url);exit;
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

        // $data = array(
        //     'request_url' => $url,
        //     'api_response' => $output,
        //     'created_on' => date("Y-m-d H:i:s"),
        //     'type' => "SMS",
        //     'message'=>$msg
        // ); 
        // if (strpos($output, 'campid') == false) { 
        //     $this->ci->db->insert('sms_error_logs',$data);
        // } 

       
        curl_close($ch);
        if ( $output ) {
            
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