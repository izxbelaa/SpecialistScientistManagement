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
/*
// Function to log errors
function logError($message) {
    $logFile = __DIR__ . '/../logs/backup_errors.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        if (!@mkdir($logDir, 0775, true)) {
            // If we can't create the directory, we'll just return silently
            return;
        }
        // Set proper ownership for Ubuntu Apache
        chmod($logDir, 0775);
        chown($logDir, 'www-data');
        chgrp($logDir, 'www-data');
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    
    // Try to write to the log file
    if (!@file_put_contents($logFile, $logMessage, FILE_APPEND)) {
        // If the file doesn't exist or isn't writable, try to create it
        if (!file_exists($logFile)) {
            touch($logFile);
            chmod($logFile, 0664);
            chown($logFile, 'www-data');
            chgrp($logFile, 'www-data');
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
    }
}
*/
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
        logError("mysqldump not found in any of the common locations");
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
    
    // Log the command (without password)
   // logError("Executing command: mysqldump -h $dbHost -u $dbUser -p**** $dbName > \"$backupFile\"");
    
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
        
       /* // Log any output
        if (!empty($stdout)) {
            logError("Command stdout for $dbName: " . $stdout);
        }
        if (!empty($stderr)) {
            logError("Command stderr for $dbName: " . $stderr);
        }
    } else {
        logError("Failed to execute command for $dbName");
        return false;*/
    }/*
    
    if ($returnVar !== 0) {
        logError("Error backing up database $dbName. Return code: $returnVar");
        
        // Check for specific error messages
        $errorOutput = $stderr . "\n" . $stdout;
        if (strpos($errorOutput, "Access denied") !== false) {
            logError("Database access denied. Please check user permissions.");
        }
        if (strpos($errorOutput, "Unknown database") !== false) {
            logError("Database does not exist or is not accessible.");
        }
        if (strpos($errorOutput, "Can't connect") !== false) {
            logError("Cannot connect to MySQL server. Please check if MySQL is running.");
        }
        
        return false;
    }
    
    if (!file_exists($backupFile)) {
        logError("Backup file was not created for $dbName at: $backupFile");
        return false;
    }*/
    
    // Check if file is empty
    if (filesize($backupFile) === 0) {
        logError("Backup file is empty for $dbName at: $backupFile");
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
    logError($e->getMessage());
    $response = [
        'success' => false,
        'message' => "Σφάλμα κατά το backup: " . $e->getMessage()
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 