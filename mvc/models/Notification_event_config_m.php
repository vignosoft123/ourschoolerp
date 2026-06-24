<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification_event_config_m extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_all() {
        return $this->db->order_by('id', 'ASC')->get('notification_event_config')->result();
    }

    public function update_by_key($event_key, $sms_enabled, $whatsapp_enabled) {
        $this->db->where('event_key', $event_key);
        return $this->db->update('notification_event_config', [
            'sms_enabled'       => $sms_enabled       ? 1 : 0,
            'whatsapp_enabled'  => $whatsapp_enabled   ? 1 : 0,
        ]);
    }
}
