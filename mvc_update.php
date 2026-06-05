<?php
/**
 * MVC Update — runs on the dummy server
 * Receives mvc.zip (already uploaded to dummy server root via cPanel API)
 * Saves server-specific configs, extracts mvc.zip to target subdomain, restores configs
 * Upload to: dummy1.ourschoolerp.com/mvc_update.php
 */

define('API_KEY', '65b03a8eb7cdf6799815bfd17ba7367d1113b642e12dec28');

header('Content-Type: application/json');
@set_time_limit(300);
@ini_set('memory_limit', '256M');

// Auth
if (($_POST['api_key'] ?? '') !== API_KEY) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$subdomain     = basename($_POST['subdomain']     ?? '');
$domain_suffix = $_POST['domain_suffix']          ?? '';

if (!$subdomain || !$domain_suffix) {
    die(json_encode(['success' => false, 'message' => 'Missing subdomain or domain_suffix']));
}

// Paths
$dummy_root  = __DIR__ . '/';
$public_html = dirname(__DIR__);
$target      = $public_html . '/' . $subdomain . $domain_suffix . '/';

if (!is_dir($target)) {
    die(json_encode(['success' => false, 'message' => "Target not found: {$target}"]));
}

$mvc_zip = $dummy_root . 'mvc.zip';
if (!file_exists($mvc_zip)) {
    die(json_encode(['success' => false, 'message' => 'mvc.zip not found on dummy server']));
}

// Step 1: Save server-specific config files before overwriting
$configs = [
    'mvc/config/development/database.php',
    'mvc/config/css_update_config.php',
];
$saved = [];
foreach ($configs as $rel) {
    $path = $target . $rel;
    if (file_exists($path)) {
        $saved[$rel] = file_get_contents($path);
    }
}

// Step 2: Extract mvc.zip to target subdomain
$zip = new ZipArchive;
if ($zip->open($mvc_zip) !== TRUE) {
    die(json_encode(['success' => false, 'message' => 'Failed to open mvc.zip on dummy server']));
}
$zip->extractTo($target);
$zip->close();

// Step 3: Restore server-specific config files
foreach ($saved as $rel => $content) {
    $dst = $target . $rel;
    if (!is_dir(dirname($dst))) mkdir(dirname($dst), 0755, true);
    file_put_contents($dst, $content);
}

echo json_encode(['success' => true, 'message' => "MVC deployed to {$subdomain} (configs preserved)"]);
