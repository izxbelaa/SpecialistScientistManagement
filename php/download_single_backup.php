<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Διαχειριστής') {
    http_response_code(403);
    exit('Unauthorized');
}

$backupDir = __DIR__ . '/../backups/';
$db = isset($_GET['db']) ? $_GET['db'] : '';
if (!in_array($db, ['cei326omada2', 'moodle_omada2'])) {
    http_response_code(400);
    exit('Invalid database selection.');
}

$pattern = $backupDir . $db . '_*.sql';
$files = glob($pattern);
if (!$files) {
    http_response_code(404);
    exit('Δεν βρέθηκε αντίγραφο ασφαλείας για τη βάση ' . htmlspecialchars($db) . '.');
}

// Sort files by modification time, descending
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});
$latestFile = $files[0];
$filename = basename($latestFile);

header('Content-Description: File Transfer');
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($latestFile));
flush();
readfile($latestFile);
exit; 