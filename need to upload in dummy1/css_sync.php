<?php
/**
 * CSS Sync — for servers where mod_security blocks direct Cssupdate.php access
 * Upload to dummy server root: dummy1.ourschoolerp.com/css_sync.php
 * Python POSTs CSS files here → this script writes them directly to target subdomain filesystem
 */

define('API_KEY', '65b03a8eb7cdf6799815bfd17ba7367d1113b642e12dec28');

header('Content-Type: application/json');

// GET request — simple connectivity test
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    die(json_encode(['status' => 'ok', 'file' => 'css_sync.php', 'method' => 'GET']));
}

// 1. Auth — read from POST form data (not JSON body, to avoid mod_security blocks)
$submitted_key = $_POST['api_key'] ?? '';
if ($submitted_key !== API_KEY) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

// 2. Inputs
$subdomain     = basename($_POST['subdomain']     ?? '');
$domain_suffix = $_POST['domain_suffix']          ?? '';
$files_json    = $_POST['files']                  ?? '{}';
$files         = json_decode($files_json, true) ?: [];

if (!$subdomain || !$domain_suffix || empty($files)) {
    die(json_encode(['success' => false, 'message' => 'Missing: subdomain, domain_suffix, or files']));
}

// 3. Target path (sibling directory on same cPanel account)
$public_html = dirname(__DIR__);
$target_css  = $public_html . '/' . $subdomain . $domain_suffix . '/assets/inilabs/';

if (!is_dir($target_css)) {
    die(json_encode(['success' => false, 'message' => "Target not found: {$target_css}"]));
}

// 4. Write CSS files
$allowed  = ['inilabs.css', 'responsive.css', 'combined.css', 'hidetable.css', 'mailandmedia.css', 'custom-overrides.css'];
$is_encoded = ($_POST['encoded'] ?? '0') === '1';
$updated  = [];
$failed   = [];

foreach ($files as $filename => $content) {
    if (!in_array($filename, $allowed)) {
        $failed[] = $filename . ' (not allowed)';
        continue;
    }
    // Decode base64 if flagged (used to bypass mod_security CSS character blocking)
    $data = $is_encoded ? base64_decode($content) : $content;
    if (file_put_contents($target_css . $filename, $data) !== false) {
        $updated[] = $filename;
    } else {
        $failed[] = $filename . ' (write failed — check permissions)';
    }
}

echo json_encode([
    'success' => !empty($updated) && empty($failed),
    'message' => 'Updated: ' . implode(', ', $updated) . (empty($failed) ? '' : ' | Failed: ' . implode(', ', $failed)),
    'updated' => $updated,
    'failed'  => $failed,
]);
