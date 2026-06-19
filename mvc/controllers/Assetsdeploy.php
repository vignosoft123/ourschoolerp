<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @deploy-doc mvc-controller
 * CI controller for Assets deployment on live subdomains (GoDaddy).
 * URL: /{subdomain}.ourcollegeerp.com/assetsdeploy/
 *   check()   GET  /assetsdeploy/check   — returns {exists:true}
 *   receive() POST /assetsdeploy/receive — accepts assets_zip upload, extracts to webroot
 * API key: from mvc/config/css_update_config.php → css_update_api_key
 * No config file preservation needed (assets/ contains only static files).
 * @deploy-doc-end
 */

class Assetsdeploy extends CI_Controller {

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

        if (!file_exists($root . 'assets.zip')) {
            echo json_encode(['success' => false, 'message' => 'assets.zip not found in webroot. Upload it first.']);
            return;
        }

        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        $zip = new ZipArchive;
        if ($zip->open($root . 'assets.zip') !== TRUE) {
            echo json_encode(['success' => false, 'message' => 'Failed to open zip']);
            return;
        }
        $zip->extractTo($root);
        $zip->close();

        if (file_exists($root . 'assets.zip')) unlink($root . 'assets.zip');

        echo json_encode(['success' => true, 'message' => 'Assets deployed successfully via trigger']);
    }

    public function receive() {
        header('Content-Type: application/json');
        $this->config->load('css_update_config');

        $submitted_key = $this->input->post('api_key');
        if ($submitted_key !== $this->config->item('css_update_api_key')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if (empty($_FILES['assets_zip']['tmp_name']) || $_FILES['assets_zip']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No valid assets_zip received']);
            return;
        }

        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        $root = rtrim(FCPATH, '/') . '/';

        if (file_exists($root . 'assets.zip')) unlink($root . 'assets.zip');

        if (!move_uploaded_file($_FILES['assets_zip']['tmp_name'], $root . 'assets.zip')) {
            echo json_encode(['success' => false, 'message' => 'Failed to save assets.zip. Check write permissions.']);
            return;
        }

        $zip = new ZipArchive;
        if ($zip->open($root . 'assets.zip') !== TRUE) {
            echo json_encode(['success' => false, 'message' => 'Failed to open zip']);
            return;
        }
        $zip->extractTo($root);
        $zip->close();

        if (file_exists($root . 'assets.zip')) unlink($root . 'assets.zip');

        echo json_encode(['success' => true, 'message' => 'Assets deployed successfully']);
    }
}
