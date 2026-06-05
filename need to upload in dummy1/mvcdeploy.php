<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mvcdeploy extends CI_Controller {

    public function check() {
        header('Content-Type: application/json');
        echo json_encode(['exists' => true]);
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

        if (empty($_FILES['mvc_zip']['tmp_name'])) {
            echo json_encode(['success' => false, 'message' => 'No mvc.zip file received']);
            return;
        }

        if ($_FILES['mvc_zip']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Upload error code: ' . $_FILES['mvc_zip']['error']]);
            return;
        }

        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        $root = rtrim(FCPATH, '/') . '/';

        if (file_exists($root . 'mvc.zip')) unlink($root . 'mvc.zip');
        if (is_dir($root . 'mvc1'))         $this->_rmdir_recursive($root . 'mvc1');

        if (is_dir($root . 'mvc')) {
            if (!rename($root . 'mvc', $root . 'mvc1')) {
                echo json_encode(['success' => false, 'message' => 'Failed to rename mvc → mvc1. Check permissions.']);
                return;
            }
        }

        if (!move_uploaded_file($_FILES['mvc_zip']['tmp_name'], $root . 'mvc.zip')) {
            if (is_dir($root . 'mvc1')) rename($root . 'mvc1', $root . 'mvc');
            echo json_encode(['success' => false, 'message' => 'Failed to save mvc.zip. Check write permissions.']);
            return;
        }

        $zip = new ZipArchive;
        if ($zip->open($root . 'mvc.zip') !== TRUE) {
            echo json_encode(['success' => false, 'message' => 'Failed to open zip']);
            return;
        }
        $zip->extractTo($root);
        $zip->close();

        if (!is_dir($root . 'mvc')) {
            echo json_encode(['success' => false, 'message' => 'Unzip done but mvc/ folder missing. Check zip structure.']);
            return;
        }

        // Copy server-specific config files from mvc1 → mvc (never overwrite with zip defaults)
        $copy_back = [
            'mvc1/config/development/database.php' => 'mvc/config/development/database.php',
            'mvc1/config/css_update_config.php'    => 'mvc/config/css_update_config.php',
        ];
        foreach ($copy_back as $src_rel => $dst_rel) {
            $src = $root . $src_rel;
            $dst = $root . $dst_rel;
            if (file_exists($src)) {
                if (!is_dir(dirname($dst))) mkdir(dirname($dst), 0755, true);
                copy($src, $dst);
            }
        }

        if (file_exists($root . 'mvc.zip')) unlink($root . 'mvc.zip');

        echo json_encode(['success' => true, 'message' => 'MVC deployed successfully']);
    }

    private function _rmdir_recursive($dir) {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->_rmdir_recursive($path) : unlink($path);
        }
        rmdir($dir);
    }
}
