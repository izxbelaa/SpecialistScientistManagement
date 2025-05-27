<?php
// Check if user is logged in and is an admin
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Διαχειριστής') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Set execution time limit to 15 minutes
set_time_limit(900);
ini_set('max_execution_time', 900);

// Database credentials
$dbUser = 'cei326omada2user';
$dbPass = 'Vp5!2BNFh!cHiN!U';
$dbHost = 'localhost';  // This will be overridden by the actual server hostname

// Get the actual database host from the configuration
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
    if (isset($host)) {
        $dbHost = $host;
    }
}

// Backup directory setup
$backupDir = __DIR__ . '/../backups';
if (!file_exists($backupDir)) {
    if (!@mkdir($backupDir, 0775, true)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Δεν ήταν δυνατή η δημιουργία του φακέλου backup. Ελέγξτε τα δικαιώματα.'
        ]);
        exit;
    }
    // Set proper ownership for Ubuntu Apache
    chmod($backupDir, 0775);
    @chown($backupDir, 'www-data');
    @chgrp($backupDir, 'www-data');
}

// Function to create backup
function createBackup($dbName, $backupDir) {
    global $dbHost, $dbUser, $dbPass;
    
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = $backupDir . DIRECTORY_SEPARATOR . $dbName . '_' . $timestamp . '.sql';
    
    // Try to find mysqldump in common Linux locations
    $possiblePaths = [
        '/usr/bin/mysqldump',                    // Ubuntu/Debian default
        '/usr/local/mysql/bin/mysqldump',        // Custom MySQL installation
        '/usr/local/bin/mysqldump',              // Common server location
        '/opt/mysql/bin/mysqldump',              // Alternative server location
        'mysqldump'                              // If in PATH
    ];
    
    $mysqldump = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $mysqldump = $path;
            break;
        }
    }
    
    if (!$mysqldump) {
        error_log('mysqldump not found in any known location.');
        return ['success' => false, 'error' => 'mysqldump not found'];
    }
    
    // Build the command with basic options for maximum compatibility
    $command = sprintf(
        '"%s" --routines --triggers --add-drop-table --quick --no-tablespaces --single-transaction -h%s -u%s -p%s %s > "%s" 2>&1',
        $mysqldump,
        $dbHost,
        $dbUser,
        $dbPass,
        $dbName,
        $backupFile
    );
    
    // Execute the command and capture output
    $output = [];
    $returnVar = 0;
    
    // Use proc_open to capture both stdout and stderr
    $descriptorspec = array(
        0 => array("pipe", "r"),  // stdin
        1 => array("pipe", "w"),  // stdout
        2 => array("pipe", "w")   // stderr
    );
    
    $process = proc_open($command, $descriptorspec, $pipes);
    
    $stdout = '';
    $stderr = '';
    if (is_resource($process)) {
        // Close stdin
        fclose($pipes[0]);
        
        // Read stdout
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        
        // Read stderr
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        
        // Get the return value
        $returnVar = proc_close($process);
    } else {
        return ['success' => false, 'error' => 'proc_open failed'];
    }
    
    if ($returnVar !== 0) {
        if (file_exists($backupFile)) {
            unlink($backupFile);
        }
        return ['success' => false, 'error' => "mysqldump failed: $stderr $stdout", 'command' => $command];
    }
    
    if (!file_exists($backupFile)) {
        return ['success' => false, 'error' => 'Backup file not created', 'command' => $command, 'stderr' => $stderr, 'stdout' => $stdout];
    }
    
    // Check if file is empty
    if (filesize($backupFile) === 0) {
        unlink($backupFile); // Delete empty file
        return ['success' => false, 'error' => 'Backup file is empty', 'command' => $command, 'stderr' => $stderr, 'stdout' => $stdout];
    }
    
    return ['success' => true, 'file' => $backupFile];
}

try {
    $backupFiles = [];
    $errors = [];
    
    // Verify backup directory is writable
    if (!is_writable($backupDir)) {
        throw new Exception("Ο φάκελος backup δεν είναι εγγράψιμος. Ελέγξτε τα δικαιώματα: " . $backupDir);
    }
    
    // Backup first database (cei326omada2)
    $db1 = 'cei326omada2';
    $result1 = createBackup($db1, $backupDir);
    if ($result1['success']) {
        $backupFiles[$db1] = basename($result1['file']);
        
        // Only proceed with second database if first backup was successful
        $db2 = 'moodle_omada2';
        $result2 = createBackup($db2, $backupDir);
        if ($result2['success']) {
            $backupFiles[$db2] = basename($result2['file']);
        } else {
            $errors[] = "Αποτυχία backup για τη βάση $db2: " . ($result2['error'] ?? 'Άγνωστο σφάλμα');
        }
    } else {
        $errors[] = "Αποτυχία backup για τη βάση $db1: " . ($result1['error'] ?? 'Άγνωστο σφάλμα');
    }
    
    if (empty($errors)) {
        $response = [
            'success' => true,
            'message' => "Το backup ολοκληρώθηκε επιτυχώς για όλες τις βάσεις δεδομένων.",
            'files' => $backupFiles,
            'path' => realpath($backupDir)
        ];
    } else {
        throw new Exception(implode("\n", $errors));
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Σφάλμα κατά το backup: " . $e->getMessage()
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 