<?php
// Prevent any output before intended JSON response
ob_start();

// Enable error reporting for debugging but log to file instead of output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

try {
    header('Content-Type: application/json');
    
    // Include required files
    if (!file_exists('config.php')) {
        throw new Exception('Configuration file not found');
    }
    require_once 'config.php';
    
    if (!file_exists('../php_classes/Academy.php')) {
        throw new Exception('Academy class file not found');
    }
    require_once '../php_classes/Academy.php';
    
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access');
    }

    // Check if PDO connection exists
    if (!isset($pdo)) {
        throw new Exception('Database connection not established');
    }

    // Clear any output buffers before processing
    ob_clean();

    $academy = new Academy($pdo);

    if (!isset($_POST['action'])) {
        throw new Exception('No action specified');
    }

    $action = $_POST['action'];
    error_log("Received action: " . $action);
    error_log("POST data: " . print_r($_POST, true));

    switch ($action) {
        case 'create':
            $name = trim($_POST['name'] ?? '');
            $code = trim($_POST['code'] ?? '');
            
            error_log("Creating academy - Name: $name, Code: $code");
            
            if (empty($name) || empty($code)) {
                throw new Exception('Academy name and code are required');
            }
            
            $newId = $academy->create($name, $code);
            if ($newId) {
                $response = [
                    'success' => true, 
                    'message' => 'Academy created successfully',
                    'data' => ['id' => $newId, 'academy_name' => $name, 'academy_code' => $code]
                ];
                error_log("Success creating academy: " . print_r($response, true));
                echo json_encode($response);
            } else {
                throw new Exception('Failed to create academy - no ID returned');
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $code = trim($_POST['code'] ?? '');
            
            if (empty($id) || empty($name) || empty($code)) {
                throw new Exception('Academy ID, name and code are required');
            }
            
            $result = $academy->update($id, $name, $code);
            echo json_encode(['success' => true, 'message' => 'Academy updated successfully']);
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            
            if (empty($id)) {
                throw new Exception('Academy ID is required');
            }
            
            $result = $academy->delete($id);
            echo json_encode(['success' => true, 'message' => 'Academy deleted successfully']);
            break;
            
        case 'get':
            $id = $_POST['id'] ?? 0;
            
            $result = $id ? $academy->getById($id) : $academy->getAll();
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    error_log("Error in academies.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Ensure a clean response
    ob_clean();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}

// Ensure we send the output
ob_end_flush(); 