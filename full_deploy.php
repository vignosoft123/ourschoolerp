<?php
/**
 * Full Deploy Script — New Subdomain Setup
 * Upload this file to the ROOT of each dummy server alongside the zip files:
 *   dummy1.ourschoolerp.com/full_deploy.php  (etc.)
 *
 * Required zip files on dummy server root:
 *   assets.zip, frontend.zip, main2.zip, mvc.zip,
 *   others.zip, uploads.zip, vendor.zip
 *
 * Python calls this when creating a new subdomain.
 * Extracts all zips to the target subdomain, then writes database.php with live credentials.
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
$db_user       = $_POST['db_user']                ?? '';
$db_name       = $_POST['db_name']                ?? '';
$db_pass       = $_POST['db_pass']                ?? '';

if (!$subdomain || !$domain_suffix) {
    die(json_encode(['success' => false, 'message' => 'Missing subdomain or domain_suffix']));
}

// 3. Build target path
$public_html = dirname(__DIR__);
$target      = $public_html . '/' . $subdomain . $domain_suffix . '/';

if (!is_dir($target)) {
    die(json_encode(['success' => false, 'message' => "Target folder not found: $target"]));
}

// 4. Extract all zip files
$zip_files = [
    'assets.zip',
    'frontend.zip',
    'main2.zip',
    'mvc.zip',
    'others.zip',
    'uploads.zip',
    'vendor.zip',
];

$source        = __DIR__ . '/';
$results       = [];
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

        if (file_exists($target . $zip_file)) {
            unlink($target . $zip_file);
        }

        $results[]     = ['file' => $zip_file, 'success' => true, 'message' => "Extracted → $subdomain"];
        $success_count++;
    } else {
        $results[] = ['file' => $zip_file, 'success' => false, 'message' => 'Failed to open zip'];
    }
}

// 5. Write database.php with live credentials (if db_user and db_name provided)
$db_written  = false;
$db_message  = '';

if ($db_user && $db_name) {
    $db_config_path = $target . 'mvc/config/development/database.php';
    $db_config_dir  = dirname($db_config_path);

    if (!is_dir($db_config_dir)) {
        mkdir($db_config_dir, 0755, true);
    }

    // Escape values for use in single-quoted PHP strings
    $safe_user = addcslashes($db_user, "\\'");
    $safe_name = addcslashes($db_name, "\\'");
    $safe_pass = addcslashes($db_pass, "\\'");

    $db_content = <<<PHP
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

\$active_group  = 'default';
\$active_record = TRUE;

\$db['default']['dsn']          = '';
\$db['default']['hostname']     = 'localhost';
\$db['default']['username']     = '{$safe_user}';
\$db['default']['password']     = '{$safe_pass}';
\$db['default']['database']     = '{$safe_name}';
\$db['default']['dbdriver']     = 'mysqli';
\$db['default']['dbprefix']     = '';
\$db['default']['pconnect']     = FALSE;
\$db['default']['db_debug']     = FALSE;
\$db['default']['cache_on']     = FALSE;
\$db['default']['cachedir']     = '';
\$db['default']['char_set']     = 'utf8';
\$db['default']['dbcollat']     = 'utf8_general_ci';
\$db['default']['swap_pre']     = '';
\$db['default']['encrypt']      = FALSE;
\$db['default']['compress']     = FALSE;
\$db['default']['autoinit']     = FALSE;
\$db['default']['stricton']     = FALSE;
\$db['default']['failover']     = array();
\$db['default']['save_queries'] = TRUE;
PHP;

    if (file_put_contents($db_config_path, $db_content) !== false) {
        $db_written = true;
        $db_message = "database.php written (user: $db_user, db: $db_name)";
    } else {
        $db_message = "database.php write failed — check folder permissions on $db_config_dir";
    }
} else {
    $db_message = 'database.php not written — db_user or db_name not provided';
}

echo json_encode([
    'success'       => $success_count > 0,
    'total'         => count($zip_files),
    'success_count' => $success_count,
    'db_written'    => $db_written,
    'message'       => "Extracted $success_count/" . count($zip_files) . " zip files to $subdomain. $db_message",
    'details'       => $results,
]);
