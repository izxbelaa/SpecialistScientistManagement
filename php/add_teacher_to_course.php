<?php
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Not authorized.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['user_id'], $data['course_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$user_id = (int)$data['user_id'];
$course_id = (int)$data['course_id'];

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// Moodle API setup
$token = '4a422108f9b3d9f1bba55c35bec1e607';
$baseUrl = 'https://cei326-omada2.cut.ac.cy/moodle/webservice/rest/server.php';

// Find Moodle user by email
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

$moodleUser = moodle_get_user_by_email($user['email'], $token, $baseUrl);
if (!$moodleUser) {
    echo json_encode(['success' => false, 'message' => 'User not found in Moodle.']);
    exit;
}

// Enroll as teacher (editingteacher roleid=3)
$url = $baseUrl . '?wstoken=' . $token . '&moodlewsrestformat=json&wsfunction=enrol_manual_enrol_users';
$data = [
    'enrolments' => [
        [
            'roleid' => 3, // editingteacher
            'userid' => $moodleUser['id'],
            'courseid' => $course_id
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

if ($result && strpos($result, 'exception') === false) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Moodle API error.', 'api_response' => $result]);
} 