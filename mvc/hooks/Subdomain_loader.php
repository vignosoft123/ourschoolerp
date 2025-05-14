<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subdomain_loader {

    public function initialize()
    {
        $CI =& get_instance();

        // Detect subdomain
        $host = $_SERVER['HTTP_HOST'];
        $subdomain = explode('.', $host)[0];

        // Load basic database config to connect to "settings" DB
        $CI->load->database();  // This database only holds "subdomain_settings" table.

        // Query subdomain settings
        $query = $CI->db->get_where('subdomain_settings', ['subdomain' => $subdomain, 'status' => 'active']);
        
        if ($query->num_rows() > 0) {
            $settings = $query->row_array();
print_r($settings);die;
            // Save settings to config or session
            $CI->config->set_item('subdomain_settings', $settings);
            $CI->session->set_userdata('subdomain_settings', $settings);

            // Dynamically change main database config
            $db_config = array(
                'dsn'      => '',
                'hostname' => 'localhost',
                'username' => $settings['db_user'],
                'password' => $settings['db_pass'],
                'database' => $settings['db_name'],
                'dbdriver' => 'mysqli',
                'dbprefix' => '',
                'pconnect' => FALSE,
                'db_debug' => (ENVIRONMENT !== 'production'),
                'cache_on' => FALSE,
                'cachedir' => '',
                'char_set' => 'utf8',
                'dbcollat' => 'utf8_general_ci',
                'swap_pre' => '',
                'encrypt'  => FALSE,
                'compress' => FALSE,
                'stricton' => FALSE,
                'failover' => array(),
                'save_queries' => TRUE
            );

            // Re-initialize database connection
            $CI->db = $CI->load->database($db_config, TRUE);

        } else {
            show_error('Subdomain settings not found or inactive.');
        }
    }
}
