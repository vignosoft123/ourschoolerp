<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Whatsapp_m extends MY_Model {


    public function __construct()
    {  
        $this->load->model('smssettings_m');
        $msg91_bind = [];
        $get_msg91s = $this->smssettings_m->get_order_by_whatsapp();
        foreach ( $get_msg91s as $key => $get_msg91 ) {
            $msg91_bind[ $get_msg91->field_names ] = $get_msg91->field_values;
        }
        // $this->authKey  = "8c79d04588b945d2083b";//$msg91_bind['msg91_authKey'];
        $this->senderID = $msg91_bind['whatsapp_sender'];
        $this->username = $msg91_bind['whatsapp_user'];
        $this->password = $msg91_bind['whatsapp_password']; 
    }
  

	function sendWhatsapp($to, $message, $template_name = '') {  
        // URL encode message and template name
        $msg = $message;
        $message = urlencode($message);
        $template_name = urlencode($template_name);
    
        // Construct API URL
        $url = "http://bwa.mindwhile.com/api/sendmsg.php?user={$this->username}&pass={$this->password}&sender={$this->senderID}&phone={$to}&text={$template_name}&priority=wa&stype=normal&Params={$message}";
    
        // Initialize cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ]);
    
        // Execute request
        $output = curl_exec($ch);
    
        // Handle cURL errors
        if (curl_errno($ch)) {
            log_message('error', 'CURL Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }
    
        curl_close($ch);
    
        // Prepare log data
        $data = [
            'request_url'   => $url,
            'api_response'  => $output,
            'created_on'    => date("Y-m-d H:i:s"),
            'type'          => "whatsapp",
            'message'       => $msg,
            'template_name' => $template_name
        ];
    
        // Log only successful messages
        // if ($output && strpos($output, 'campid') !== false) { 
            $this->db->insert('whatsapp_logs', $data);
        // } else {
        //     log_message('error', 'WhatsApp API Error: ' . $output);
        // }
    
        return $output ?: false;
    }
    
}