<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cssupdate extends CI_Controller {

    public function receive() {
        header('Content-Type: application/json');
        $this->config->load('css_update_config');

        // Accept JSON body (sent by Python with json= parameter)
        $raw  = file_get_contents('php://input');
        $body = json_decode($raw, true);

        $submitted_key = isset($body['api_key']) ? $body['api_key'] : '';
        if ($submitted_key !== $this->config->item('css_update_api_key')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $files = isset($body['files']) ? $body['files'] : [];
        if (empty($files)) {
            echo json_encode(['success' => false, 'message' => 'No CSS files received']);
            return;
        }

        // Only allow safe filenames — no directory traversal
        $allowed = ['inilabs.css', 'responsive.css', 'combined.css', 'hidetable.css', 'mailandmedia.css', 'custom-overrides.css'];
        $updated = [];
        $failed  = [];

        foreach ($files as $filename => $content) {
            if (!in_array($filename, $allowed)) {
                $failed[] = $filename . ' (not allowed)';
                continue;
            }
            $target = FCPATH . 'assets/inilabs/' . $filename;
            if (file_put_contents($target, $content) !== false) {
                $updated[] = $filename;
            } else {
                $failed[] = $filename . ' (write failed)';
            }
        }

        if (empty($failed)) {
            echo json_encode(['success' => true, 'message' => 'Updated: ' . implode(', ', $updated), 'updated' => $updated]);
        } else {
            echo json_encode(['success' => count($updated) > 0, 'message' => 'Updated: ' . implode(', ', $updated) . '. Failed: ' . implode(', ', $failed), 'updated' => $updated, 'failed' => $failed]);
        }
    }
}
