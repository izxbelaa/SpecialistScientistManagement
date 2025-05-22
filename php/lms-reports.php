<?php
require_once 'config.php';

// Moodle API Token and Base URL
$token = '4a422108f9b3d9f1bba55c35bec1e607';
$baseUrl = 'https://cei326-omada2.cut.ac.cy/moodle/webservice/rest/server.php';

// Determine which report to generate based on the 'report' GET parameter
$report = $_GET['report'] ?? '';

switch ($report) {
    case 'ee_stats':
        // Pull all user emails from the users table (type_of_user = 2)
        $stmt = $pdo->query("SELECT email, first_name, last_name FROM users WHERE email IS NOT NULL AND type_of_user = 2");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $withAccess = 0;
        $withoutAccess = 0;
        $ee_users = [];
        foreach ($users as $u) {
            $checkUrl = "$baseUrl?" . http_build_query([
                'wstoken' => $token,
                'moodlewsrestformat' => 'json',
                'wsfunction' => 'core_user_get_users_by_field',
                'field' => 'email',
                'values[0]' => $u['email']
            ]);
            $checkResp = json_decode(@file_get_contents($checkUrl), true);
            if (!empty($checkResp[0]['id'])) {
                $withAccess++;
                $ee_users[] = [
                    'email' => $u['email'],
                    'first_name' => $u['first_name'],
                    'last_name' => $u['last_name'],
                    'access' => 'Με πρόσβαση'
                ];
            } else {
                $withoutAccess++;
                $ee_users[] = [
                    'email' => $u['email'],
                    'first_name' => $u['first_name'],
                    'last_name' => $u['last_name'],
                    'access' => 'Χωρίς πρόσβαση'
                ];
            }
        }
        echo json_encode(['ee_stats' => ['with_access' => $withAccess, 'without_access' => $withoutAccess], 'ee_users' => $ee_users]);
        break;
    case 'courses_no_instructor':
        // Fetch courses without instructor from Moodle
        $coursesNoInstructorUrl = "$baseUrl?" . http_build_query([
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_course_get_courses'
        ]);
        $coursesResp = json_decode(file_get_contents($coursesNoInstructorUrl), true);
        $coursesNoInstructor = [];
        foreach ($coursesResp as $course) {
            if (empty($course['teachers'])) {
                $coursesNoInstructor[] = ['course_name' => $course['fullname'], 'course_code' => $course['idnumber']];
            }
        }
        echo json_encode(['courses_no_instructor' => $coursesNoInstructor]);
        break;
    case 'courses_with_instructor':
        // Fetch courses with instructor from Moodle
        $coursesWithInstructorUrl = "$baseUrl?" . http_build_query([
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_course_get_courses'
        ]);
        $coursesResp = json_decode(file_get_contents($coursesWithInstructorUrl), true);
        $coursesWithInstructor = [];
        foreach ($coursesResp as $course) {
            if (!empty($course['teachers'])) {
                $coursesWithInstructor[] = ['course_name' => $course['fullname'], 'course_code' => $course['idnumber']];
            }
        }
        echo json_encode(['courses_with_instructor' => $coursesWithInstructor]);
        break;
    default:
        echo json_encode(['error' => 'Invalid report type']);
        break;
} 