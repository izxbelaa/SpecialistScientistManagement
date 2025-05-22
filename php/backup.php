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
    chown($backupDir, 'www-data');
    chgrp($backupDir, 'www-data');
}

// Function to create backup
function createBackup($dbName, $backupDir) {
    global $dbHost, $dbUser, $dbPass;
    
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = $backupDir . $dbName . '_' . $timestamp . '.sql';
    
    // Try to find mysqldump in common locations
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
        return false;
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
        return false;
    }
    
    if ($returnVar !== 0) {
        return false;
    }
    
    if (!file_exists($backupFile)) {
        return false;
    }
    
    // Check if file is empty
    if (filesize($backupFile) === 0) {
        unlink($backupFile); // Delete empty file
        return false;
    }
    
    return $backupFile;
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
    $backupFile1 = createBackup($db1, $backupDir);
    if ($backupFile1) {
        $backupFiles[$db1] = basename($backupFile1);
        
        // Only proceed with second database if first backup was successful
        $db2 = 'moodle_omada2';
        $backupFile2 = createBackup($db2, $backupDir);
        if ($backupFile2) {
            $backupFiles[$db2] = basename($backupFile2);
        } else {
            $errors[] = "Αποτυχία backup για τη βάση $db2";
        }
    } else {
        $errors[] = "Αποτυχία backup για τη βάση $db1";
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