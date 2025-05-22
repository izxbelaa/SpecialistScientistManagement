<?php
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized.']);
    exit;
}

// Moodle API setup
$token = '4a422108f9b3d9f1bba55c35bec1e607';
$baseUrl = 'http://localhost/SpecialistScientistManagement/moodle/webservice/rest/server.php';

// Fetch categories
$catUrl = $baseUrl . '?' . http_build_query([
    'wstoken' => $token,
    'moodlewsrestformat' => 'json',
    'wsfunction' => 'core_course_get_categories'
]);
$catResponse = file_get_contents($catUrl);
$categories = json_decode($catResponse, true);

// Enhanced error handling for categories
if (isset($categories['exception'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Moodle API exception when fetching categories.',
        'error' => [
            'type' => $categories['exception'],
            'errorcode' => isset($categories['errorcode']) ? $categories['errorcode'] : null,
            'message' => isset($categories['message']) ? $categories['message'] : null,
            'debuginfo' => isset($categories['debuginfo']) ? $categories['debuginfo'] : null,
            'raw_response' => $catResponse
        ]
    ]);
    exit;
}

// Fetch courses
$courseUrl = $baseUrl . '?' . http_build_query([
    'wstoken' => $token,
    'moodlewsrestformat' => 'json',
    'wsfunction' => 'core_course_get_courses'
]);
$courseResponse = file_get_contents($courseUrl);
$allCourses = json_decode($courseResponse, true);

if (!is_array($categories) || !is_array($allCourses)) {
    echo json_encode(['success' => false, 'message' => 'Moodle API error.', 'api_response' => [$catResponse, $courseResponse]]);
    exit;
}

if (!is_array($allCourses)) {
    echo json_encode(['success' => false, 'message' => 'Moodle API error (courses).', 'api_response' => $courseResponse]);
    exit;
}

// Map courses to categories
$catMap = [];
foreach ($categories as $cat) {
    if (!is_array($cat) || !isset($cat['id'], $cat['name'])) continue;
    $catMap[$cat['id']] = [
        'id' => $cat['id'],
        'name' => $cat['name'],
        'courses' => []
    ];
}
foreach ($allCourses as $course) {
    if (!is_array($course) || !isset($course['id'], $course['categoryid'], $course['fullname'])) continue;
    if ($course['id'] == 1) continue; // skip site course
    $catid = $course['categoryid'];
    if (isset($catMap[$catid])) {
        $catMap[$catid]['courses'][] = [
            'id' => $course['id'],
            'fullname' => $course['fullname']
        ];
    }
}

// Only return categories with courses
$catList = array_values($catMap);

echo json_encode([
    'success' => true,
    'categories' => $catList,
    'debug' => [
        'categories_type' => gettype($categories),
        'allCourses_type' => gettype($allCourses),
        'categories_sample' => is_array($categories) ? array_slice($categories, 0, 1) : $categories,
        'allCourses_sample' => is_array($allCourses) ? array_slice($allCourses, 0, 1) : $allCourses
    ]
]); 