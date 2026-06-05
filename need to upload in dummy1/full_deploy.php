<?php
/**
 * Full Deploy Script — New Subdomain Setup
 * Upload this file to the ROOT of each dummy server alongside the zip files:
 *   dummy1.ourcollegeerp.com/full_deploy.php
 *
 * Required zip files on dummy server root:
 *   assets.zip, frontend.zip, main2.zip, mvc.zip,
 *   others.zip, uploads.zip, vendor.zip
 *
 * Python calls this when creating a new subdomain.
 * It extracts all zips to the target subdomain and deletes each zip after extraction.
 */

define('API_KEY', '65b03a8eb7cdf6799815bfd17ba7367d1113b642e12dec28');

header('Content-Type: application/json');
@set_time_limit(300);
@ini_set('memory_limit', '512M');

// 1. Auth
if (($_POST['api_key'] ?? '') !== API_KEY) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

// 2. Inputs
$subdomain     = basename($_POST['subdomain']     ?? '');
$domain_suffix = $_POST['domain_suffix']          ?? '';

if (!$subdomain || !$domain_suffix) {
    die(json_encode(['success' => false, 'message' => 'Missing subdomain or domain_suffix']));
}

// 3. Build target path (same server — sibling directory under public_html)
$public_html = dirname(__DIR__);
$target      = $public_html . '/' . $subdomain . $domain_suffix . '/';

if (!is_dir($target)) {
    die(json_encode(['success' => false, 'message' => "Target folder not found: $target"]));
}

// 4. Zip files to extract (all must exist on dummy server root)
$zip_files = [
    'assets.zip',
    'frontend.zip',
    'main2.zip',
    'mvc.zip',
    'others.zip',
    'uploads.zip',
    'vendor.zip',
];

$source  = __DIR__ . '/';
$results = [];
$success_count = 0;

foreach ($zip_files as $zip_file) {
    $zip_path = $source . $zip_file;

    if (!file_exists($zip_path)) {
        $results[] = ['file' => $zip_file, 'success' => false, 'message' => 'Not found on dummy server'];
        continue;
    }

    $zip = new ZipArchive;
    if ($zip->open($zip_path) === TRUE) {
        $zip->extractTo($target);
        $zip->close();

        // Delete zip from target subdomain if it was accidentally copied there
        if (file_exists($target . $zip_file)) {
            unlink($target . $zip_file);
        }

        $results[]     = ['file' => $zip_file, 'success' => true, 'message' => "Extracted → $subdomain"];
        $success_count++;
    } else {
        $results[] = ['file' => $zip_file, 'success' => false, 'message' => 'Failed to open zip'];
    }
}

echo json_encode([
    'success'       => $success_count > 0,
    'total'         => count($zip_files),
    'success_count' => $success_count,
    'message'       => "Extracted $success_count/" . count($zip_files) . " zip files to $subdomain",
    'details'       => $results,
]);
