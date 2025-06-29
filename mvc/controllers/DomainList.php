<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DomainList extends CI_Controller {

    public function index() {
        $file_path = FCPATH . 's.txt';
        $domains = [];

        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);
            $lines = explode("<br/>", $content);

            foreach ($lines as $line) {
                $domain = trim(str_replace('www.', '', $line));
                if (!empty($domain) && strpos($domain, '.localhost') === false) {
                    $domains[] = $domain;
                }
            }

            $domains = array_unique($domains);
            sort($domains); // optional: alphabetically sort
        }

        $data['domains'] = $domains;
        $this->load->view('domain_list_view', $data);
    }
}
