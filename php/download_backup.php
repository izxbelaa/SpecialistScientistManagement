<?php
// Check if user is logged in and is an admin
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Διαχειριστής') {
    http_response_code(403);
    exit('Unauthorized');
}

$backupDir = __DIR__ . '/../backups/';
$dbs = ['cei326omada2', 'moodle_omada2'];
$latestFiles = [];

foreach ($dbs as $db) {
    $pattern = $backupDir . $db . '_*.sql';
    $files = glob($pattern);
    if (!$files) {
        http_response_code(404);
        exit('Δεν βρέθηκε αντίγραφο ασφαλείας για τη βάση ' . htmlspecialchars($db) . '.');
    }
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    $latestFiles[$db] = $files[0];
}

// Create ZIP
$zipFile = tempnam(sys_get_temp_dir(), 'backup_');
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    http_response_code(500);
    exit('Δεν ήταν δυνατή η δημιουργία του αρχείου ZIP.');
}

foreach ($latestFiles as $db => $file) {
    $zip->addFile($file, basename($file));
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="latest_backups_' . date('Y-m-d_H-i-s') . '.zip"');
header('Content-Length: ' . filesize($zipFile));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
readfile($zipFile);
unlink($zipFile);
exit;
?> 