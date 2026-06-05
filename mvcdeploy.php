<?php
/**
 * MVC Deploy Receiver
 * Deploy this file to the ROOT of each live subdomain (e.g. veda.ourcollegeerp.com/).
 * Python POSTs mvc.zip here → this script handles rename, unzip, database.php copy.
 *
 * URL: https://{subdomain}.ourcollegeerp.com/mvcdeploy.php
 */

define('API_KEY', 'a3517b0f46b17c8b813d850e8ef65fd035df328d45f0a836');

header('Content-Type: application/json');

// Increase limits for large zip files
@set_time_limit(300);
@ini_set('memory_limit', '256M');

// 1. Authenticate
if (($_POST['api_key'] ?? '') !== API_KEY) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

// 2. Receive uploaded zip
if (empty($_FILES['mvc_zip']['tmp_name'])) {
    die(json_encode(['success' => false, 'message' => 'No mvc.zip file received']));
}

if ($_FILES['mvc_zip']['error'] !== UPLOAD_ERR_OK) {
    die(json_encode(['success' => false, 'message' => 'Upload error code: ' . $_FILES['mvc_zip']['error']]));
}

$root = rtrim(__DIR__, '/') . '/';   // Subdomain webroot — where mvc/ lives

// 3. Delete old mvc.zip if it exists
if (file_exists($root . 'mvc.zip')) {
    unlink($root . 'mvc.zip');
}

// 4. Delete mvc1 folder if it exists
if (is_dir($root . 'mvc1')) {
    _rmdir_recursive($root . 'mvc1');
}

// 5. Rename mvc → mvc1
if (is_dir($root . 'mvc')) {
    if (!rename($root . 'mvc', $root . 'mvc1')) {
        die(json_encode(['success' => false, 'message' => 'Failed to rename mvc → mvc1. Check permissions.']));
    }
}

// 6. Save uploaded zip to root
if (!move_uploaded_file($_FILES['mvc_zip']['tmp_name'], $root . 'mvc.zip')) {
    // Restore mvc from mvc1 on failure
    if (is_dir($root . 'mvc1')) rename($root . 'mvc1', $root . 'mvc');
    die(json_encode(['success' => false, 'message' => 'Failed to save mvc.zip. Check write permissions.']));
}

// 7. Unzip mvc.zip → creates mvc/ folder
$zip = new ZipArchive;
$open_result = $zip->open($root . 'mvc.zip');
if ($open_result !== TRUE) {
    die(json_encode(['success' => false, 'message' => 'Failed to open zip (ZipArchive error: ' . $open_result . ')']));
}
$zip->extractTo($root);
$zip->close();

if (!is_dir($root . 'mvc')) {
    die(json_encode(['success' => false, 'message' => 'Unzip completed but mvc/ folder not found. Check zip structure.']));
}

// 8. Copy database.php from mvc1 → mvc (preserves subdomain-specific DB credentials)
$db_src = $root . 'mvc1/config/development/database.php';
$db_dst = $root . 'mvc/config/development/database.php';
if (file_exists($db_src)) {
    $dst_dir = dirname($db_dst);
    if (!is_dir($dst_dir)) {
        mkdir($dst_dir, 0755, true);
    }
    copy($db_src, $db_dst);
}

// 9. Remove mvc.zip
if (file_exists($root . 'mvc.zip')) {
    unlink($root . 'mvc.zip');
}

echo json_encode(['success' => true, 'message' => 'MVC deployed successfully']);


// ── Helpers ──────────────────────────────────────────────────────────────────

function _rmdir_recursive($dir) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            _rmdir_recursive($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}
