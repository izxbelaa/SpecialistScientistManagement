<?php
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Not authorized.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['user_id'], $data['enable'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$user_id = (int)$data['user_id'];
$enable = (bool)$data['enable'];

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// Moodle API setup
$token = '4a422108f9b3d9f1bba55c35bec1e607';
$baseUrl = 'http://localhost/SpecialistScientistManagement/moodle/webservice/rest/server.php';

function moodle_get_user_by_email($email, $token, $baseUrl) {
    $url = $baseUrl . '?' . http_build_query([
        'wstoken' => $token,
        'moodlewsrestformat' => 'json',
        'wsfunction' => 'core_user_get_users_by_field',
        'field' => 'email',
        'values[0]' => $email
    ]);
    $resp = json_decode(file_get_contents($url), true);
    return !empty($resp[0]['id']) ? $resp[0] : false;
}

function moodle_suspend_user($moodle_user_id, $token, $baseUrl, &$apiResponse = null) {
    $url = $baseUrl . '?wstoken=' . $token . '&moodlewsrestformat=json&wsfunction=core_user_update_users';
    $data = [
        'users' => [
            [
                'id' => $moodle_user_id,
                'suspended' => 1
            ]
        ]
    ];
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $apiResponse = $result;
    return $result && strpos($result, 'exception') === false;
}

function moodle_unsuspend_or_create_user($user, $token, $baseUrl, &$apiResponse = null) {
    $moodleUser = moodle_get_user_by_email($user['email'], $token, $baseUrl);
    if ($moodleUser) {
        // Unsuspend
        $url = $baseUrl . '?wstoken=' . $token . '&moodlewsrestformat=json&wsfunction=core_user_update_users';
        $data = [
            'users' => [
                [
                    'id' => $moodleUser['id'],
                    'suspended' => 0
                ]
            ]
        ];
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $apiResponse = $result;
        return $result && strpos($result, 'exception') === false;
    } else {
        // Create
        $url = $baseUrl . '?wstoken=' . $token . '&moodlewsrestformat=json&wsfunction=core_user_create_users';
        $data = [
            'users' => [
                [
                    'username' => $user['email'],
                    'email' => $user['email'],
                    'firstname' => $user['first_name'],
                    'lastname' => $user['last_name'],
                    'password' => 'TempPass!123',
                    'auth' => 'manual',
                    'suspended' => 0
                ]
            ]
        ];
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $apiResponse = $result;
        return $result && strpos($result, 'exception') === false;
    }
}

$success = false;
$apiResponse = null;
if ($enable) {
    $success = moodle_unsuspend_or_create_user($user, $token, $baseUrl, $apiResponse);
} else {
    $moodleUser = moodle_get_user_by_email($user['email'], $token, $baseUrl);
    if ($moodleUser) {
        $success = moodle_suspend_user($moodleUser['id'], $token, $baseUrl, $apiResponse);
    } else {
        $success = true; // Already disabled
    }
}

if ($success) {
    echo json_encode(['success' => true, 'enabled' => $enable]);
} else {
    echo json_encode(['success' => false, 'message' => 'Moodle API error.', 'api_response' => $apiResponse]);
} 