<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @deploy-doc mvc-controller
 * CI controller for Frontend deployment on live subdomains (GoDaddy).
 * URL: /{subdomain}.ourcollegeerp.com/frontenddeploy/
 *   check()   GET /frontenddeploy/check           — returns {exists:true}
 *   trigger() GET /frontenddeploy/trigger?api_key — reads frontend.zip from webroot, extracts, deletes zip
 * frontend.zip is uploaded to webroot via cPanel Fileman API (avoids PHP post_max_size limit).
 * No config file preservation needed (frontend/ contains only view/static files).
 * @deploy-doc-end
 */

class Frontenddeploy extends CI_Controller {

    public function check() {
        header('Content-Type: application/json');
        echo json_encode(['exists' => true]);
    }

    public function trigger() {
        header('Content-Type: application/json');
        $this->config->load('css_update_config');

        $api_key = $this->input->get('api_key') ?: $this->input->post('api_key');
        if ($api_key !== $this->config->item('css_update_api_key')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $root = rtrim(FCPATH, '/') . '/';

        if (!file_exists($root . 'frontend.zip')) {
            echo json_encode(['success' => false, 'message' => 'frontend.zip not found in webroot. Upload it first.']);
            return;
        }

        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        $zip = new ZipArchive;
        if ($zip->open($root . 'frontend.zip') !== TRUE) {
            echo json_encode(['success' => false, 'message' => 'Failed to open zip']);
            return;
        }
        $zip->extractTo($root);
        $zip->close();

        if (file_exists($root . 'frontend.zip')) unlink($root . 'frontend.zip');

        echo json_encode(['success' => true, 'message' => 'Frontend deployed successfully via trigger']);
    }
}
