<?php
/**
 * Bootstrap Copy Script
 * Upload this file to the ROOT of each dummy server:
 *   dummy1.ourcollegeerp.com/bootstrap_copy.php
 *   dummy1.ourschoolerp.com/bootstrap_copy.php  (etc.)
 *
 * Also upload alongside it:
 *   mvcdeploy.php
 *   Cssupdate.php
 *   css_update_config.php
 *
 * Python calls this to copy those files to any subdomain on the same server.
 */

define('API_KEY', '65b03a8eb7cdf6799815bfd17ba7367d1113b642e12dec28');

header('Content-Type: application/json');

// ── GET: mvc deploy — MUST be before POST auth check ──────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $k = $_GET['k'] ?? '';
    $s = basename($_GET['s'] ?? '');
    $d = $_GET['d'] ?? '';
    if ($k !== API_KEY || !$s || !$d) {
        die(json_encode(['success' => false, 'message' => 'Bad GET params']));
    }
    $pub  = dirname(__DIR__);
    $tgt  = $pub . '/' . $s . $d . '/';
    $src  = __DIR__ . '/mvc.zip';
    if (!is_dir($tgt)) die(json_encode(['success' => false, 'message' => "Target not found: $tgt"]));
    if (!file_exists($src)) die(json_encode(['success' => false, 'message' => 'mvc.zip not on dummy server']));

    @set_time_limit(300);
    $cfgs = ['mvc/config/development/database.php', 'mvc/config/css_update_config.php'];
    $bak = [];
    foreach ($cfgs as $r) { $f = $tgt . $r; if (file_exists($f)) $bak[$r] = file_get_contents($f); }
    $z = new ZipArchive;
    if ($z->open($src) !== TRUE) die(json_encode(['success' => false, 'message' => 'Failed to open zip']));
    $z->extractTo($tgt); $z->close();
    foreach ($bak as $r => $c) { $p = $tgt . $r; if (!is_dir(dirname($p))) mkdir(dirname($p),0755,true); file_put_contents($p,$c); }
    die(json_encode(['success' => true, 'message' => "MVC deployed to {$s}"]));
}

// ── POST: bootstrap (copy files to subdomain) ─────────────────────────────────

// POST auth check
if (($_POST['api_key'] ?? '') !== API_KEY) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$subdomain     = basename($_POST['subdomain']     ?? '');
$domain_suffix = $_POST['domain_suffix']          ?? '';

if (!$subdomain || !$domain_suffix) {
    die(json_encode(['success' => false, 'message' => 'Missing subdomain or domain_suffix']));
}

$public_html = dirname(__DIR__);
$target      = $public_html . '/' . $subdomain . $domain_suffix . '/';

if (!is_dir($target)) {
    die(json_encode(['success' => false, 'message' => "Target directory not found: $target"]));
}

$source = __DIR__ . '/';
$files  = [
    'mvcdeploy.php'         => 'mvc/controllers/Mvcdeploy.php',
    'Cssupdate.php'         => 'mvc/controllers/Cssupdate.php',
    'css_update_config.php' => 'mvc/config/css_update_config.php',
];

$copied = [];
$failed = [];

foreach ($files as $src_filename => $dst_rel) {
    $src     = $source . $src_filename;
    $dst     = $target . $dst_rel;
    $dst_dir = dirname($dst);

    if (!file_exists($src)) {
        $failed[] = basename($dst) . ' (not found on dummy server)';
        continue;
    }
    if (!is_dir($dst_dir)) mkdir($dst_dir, 0755, true);
    if (copy($src, $dst)) {
        $copied[] = $dst_rel;
    } else {
        $failed[] = $dst . ' (copy failed)';
    }
}

echo json_encode([
    'success' => !empty($copied) && empty($failed),
    'message' => 'Copied: ' . implode(', ', $copied) . (empty($failed) ? '' : ' | Failed: ' . implode(', ', $failed)),
    'copied'  => $copied,
    'failed'  => $failed,
]);
