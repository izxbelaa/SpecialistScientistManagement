<?php
// Check if user is logged in and is an admin
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Διαχειριστής') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Backup directory
$backupDir = __DIR__ . '/usr/bin/';

// Get all backup files
$files = glob($backupDir . '*.sql');

if (empty($files)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Δεν βρέθηκαν αρχεία backup.']);
    exit;
}

// Create a temporary ZIP file
$zipFile = tempnam(sys_get_temp_dir(), 'backup_');
$zip = new ZipArchive();

if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Δεν ήταν δυνατή η δημιουργία του αρχείου ZIP.']);
    exit;
}

// Add each backup file to the ZIP
foreach ($files as $file) {
    if (file_exists($file) && is_readable($file)) {
        $zip->addFile($file, basename($file));
    }
}

$zip->close();

// Verify ZIP file was created and has content
if (!file_exists($zipFile) || filesize($zipFile) === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Το αρχείο ZIP είναι κενό.']);
    exit;
}

// Clean the output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Set headers for ZIP file download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="backups_' . date('Y-m-d_H-i-s') . '.zip"');
header('Content-Length: ' . filesize($zipFile));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output ZIP file content
readfile($zipFile);

// Clean up the temporary ZIP file
unlink($zipFile);
exit;
?> 