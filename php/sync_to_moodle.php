<?php
require_once 'config.php';

// Check if full sync is enabled
$stmt = $pdo->query("SELECT enabled FROM full_sync LIMIT 1");
$row = $stmt->fetch();
if (!$row || $row['enabled'] != 1) {
    exit("Full sync not enabled.\n");
}

// Moodle API Token
$token = '4a422108f9b3d9f1bba55c35bec1e607';
$baseUrl = 'https://cei326-omada2.cut.ac.cy/moodle/webservice/rest/server.php';

$users = $pdo->query("SELECT * FROM users WHERE email IS NOT NULL AND type_of_user = 2")->fetchAll();

foreach ($users as $u) {
    $username = explode('@', $u['email'])[0];
    $email = $u['email'];

    // Step 1: Check if user already exists in Moodle by email
    $checkUrl = "$baseUrl?" . http_build_query([
        'wstoken' => $token,
        'moodlewsrestformat' => 'json',
        'wsfunction' => 'core_user_get_users_by_field',
        'field' => 'email',
        'values[0]' => $email
    ]);

    $checkResp = json_decode(file_get_contents($checkUrl), true);

    if (!empty($checkResp[0]['id'])) {
        // User exists in Moodle → Update info
        $moodleId = $checkResp[0]['id'];

        $updateParams = http_build_query([
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_user_update_users',
            'users[0][id]' => $moodleId,
            'users[0][firstname]' => $u['first_name'],
            'users[0][lastname]' => $u['last_name']
        ]);

        $updateResp = file_get_contents("$baseUrl?$updateParams");
        echo "Updated Moodle user '$email' — Response: $updateResp\n";
    } else {
        // User does not exist → Create new
        $createParams = http_build_query([
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_user_create_users',
            'users[0][username]' => $username,
            'users[0][firstname]' => $u['first_name'],
            'users[0][lastname]' => $u['last_name'],
            'users[0][email]' => $email,
            'users[0][createpassword]' => 1
        ]);

        $createResp = file_get_contents("$baseUrl?$createParams");
        echo "Created new Moodle user '$email' — Response: $createResp\n";
    }
}


// Step 2: Delete disabled users from Moodle
$disabledUsers = $pdo->query("SELECT email FROM users WHERE disabled_user = 1")->fetchAll();

foreach ($disabledUsers as $u) {
    $email = $u['email'];

    // Look up Moodle user by email
    $checkUrl = "$baseUrl?" . http_build_query([
        'wstoken' => $token,
        'moodlewsrestformat' => 'json',
        'wsfunction' => 'core_user_get_users_by_field',
        'field' => 'email',
        'values[0]' => $email
    ]);
    $userData = json_decode(file_get_contents($checkUrl), true);

    if (!empty($userData[0]['id'])) {
        $moodleUserId = $userData[0]['id'];

        // Call delete API
        $deleteParams = http_build_query([
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_user_delete_users',
            'userids[0]' => $moodleUserId
        ]);

        $deleteResp = file_get_contents("$baseUrl?$deleteParams");
        echo "Deleted user '$email' from Moodle — Response: $deleteResp\n";
    } else {
        echo "User '$email' not found in Moodle — skipping deletion.\n";
    }
}


// Step 3: Sync course categories based on departments table
$departments = $pdo->query("SELECT * FROM departments")->fetchAll();
$departmentMap = [];
foreach ($departments as $d) {
    $departmentMap[$d['department_code']] = $d; // use department_code as key
}

// Fetch all existing Moodle categories
$categoryResp = file_get_contents("$baseUrl?" . http_build_query([
    'wstoken' => $token,
    'moodlewsrestformat' => 'json',
    'wsfunction' => 'core_course_get_categories'
]));
$moodleCategories = json_decode($categoryResp, true);

// Track which Moodle categories match the DB
$matchedCategoryIds = [];

foreach ($moodleCategories as $cat) {
    $code = $cat['idnumber'] ?? null; // optionally stored in idnumber
    if ($code && isset($departmentMap[$code])) {
        $dept = $departmentMap[$code];
        $matchedCategoryIds[] = $cat['id'];

        // Update name if different
        if ($cat['name'] !== $dept['department_name']) {
            $updateParams = http_build_query([
                'wstoken' => $token,
                'moodlewsrestformat' => 'json',
                'wsfunction' => 'core_course_update_categories',
                'categories[0][id]' => $cat['id'],
                'categories[0][name]' => $dept['department_name']
            ]);
            file_get_contents("$baseUrl?$updateParams");
            echo "Updated category '{$code}' to '{$dept['department_name']}'\n";
        } else {
            echo "Category '{$code}' already up-to-date\n";
        }
    }
}

// Create new categories
foreach ($departments as $d) {
    $code = $d['department_code'];
    $exists = false;
    foreach ($moodleCategories as $cat) {
        if (($cat['idnumber'] ?? '') === $code) {
            $exists = true;
            break;
        }
    }
    if (!$exists) {
        $createParams = http_build_query([
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_course_create_categories',
            'categories[0][name]' => $d['department_name'],
            'categories[0][parent]' => 0,
            'categories[0][idnumber]' => $code // this will track it uniquely
        ]);
        $resp = file_get_contents("$baseUrl?$createParams");
        echo "Created category '{$d['department_name']}' ($code) — $resp\n";
    }
}

// Delete categories not found in department table
foreach ($moodleCategories as $cat) {
    $code = $cat['idnumber'] ?? '';
    if ($code && !isset($departmentMap[$code])) {
        $deleteParams = http_build_query([
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_course_delete_categories',
            'categories[0]' => $cat['id']
        ]);
        file_get_contents("$baseUrl?$deleteParams");
        echo "Deleted obsolete category '{$cat['name']}' ($code)\n";
    }
}


// Step 4: Sync courses based on `course` table
$courses = $pdo->query("
    SELECT c.*, d.department_code 
    FROM course c 
    JOIN departments d ON c.department_id = d.id
")->fetchAll();

// Step 4.1: Get all existing Moodle courses
$courseResp = file_get_contents("$baseUrl?" . http_build_query([
    'wstoken' => $token,
    'moodlewsrestformat' => 'json',
    'wsfunction' => 'core_course_get_courses'
]));
$moodleCourses = json_decode($courseResp, true);
$courseMap = []; // key = idnumber (course_code)

foreach ($moodleCourses as $mc) {
    if (!empty($mc['idnumber'])) {
        $courseMap[$mc['idnumber']] = $mc;
    }
}

// Step 4.2: Get Moodle category map (name → ID)
$categoryResp = file_get_contents("$baseUrl?" . http_build_query([
    'wstoken' => $token,
    'moodlewsrestformat' => 'json',
    'wsfunction' => 'core_course_get_categories'
]));
$categories = json_decode($categoryResp, true);
$categoryMap = []; // key = department_code

foreach ($categories as $cat) {
    if (!empty($cat['idnumber'])) {
        $categoryMap[$cat['idnumber']] = $cat['id'];
    }
}

// Step 4.3: Sync create/update
$foundCourseCodes = [];

foreach ($courses as $c) {
    $code = $c['course_code'];
    $deptCode = $c['department_code'];
    $foundCourseCodes[] = $code;

    $fullname = $c['course_name'];
    $shortname = $code;
    $categoryId = $categoryMap[$deptCode] ?? 1; // fallback to default category

    if (isset($courseMap[$code])) {
        // Exists → update if needed
        $existing = $courseMap[$code];
        if ($existing['fullname'] !== $fullname || $existing['shortname'] !== $shortname || $existing['categoryid'] !== $categoryId) {
            $update = http_build_query([
                'wstoken' => $token,
                'moodlewsrestformat' => 'json',
                'wsfunction' => 'core_course_update_courses',
                'courses[0][id]' => $existing['id'],
                'courses[0][fullname]' => $fullname,
                'courses[0][shortname]' => $shortname,
                'courses[0][categoryid]' => $categoryId
            ]);
            file_get_contents("$baseUrl?$update");
            echo "Updated Moodle course '$fullname' ($code)\n";
        } else {
            echo "Course '$fullname' ($code) is up-to-date\n";
        }
    } else {
        // Not in Moodle → create
        $create = http_build_query([
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_course_create_courses',
            'courses[0][fullname]' => $fullname,
            'courses[0][shortname]' => $shortname,
            'courses[0][categoryid]' => $categoryId,
            'courses[0][idnumber]' => $code,
            'courses[0][visible]' => 1
        ]);
        $resp = file_get_contents("$baseUrl?$create");
        echo "Created course '$fullname' ($code) — $resp\n";
    }
}

// Step 4.4: Delete Moodle courses not in DB
foreach ($courseMap as $code => $mc) {
    if (!in_array($code, $foundCourseCodes)) {
        $delete = http_build_query([
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_course_delete_courses',
            'courseids[0]' => $mc['id']
        ]);
        file_get_contents("$baseUrl?$delete");
        echo "Deleted obsolete Moodle course '{$mc['fullname']}' ($code)\n";
    }
}

// Step 5: Sync role assignments based on accepted requests

// First, get all Moodle courses and create a mapping of course codes to Moodle course IDs
$courseResp = file_get_contents("$baseUrl?" . http_build_query([
    'wstoken' => $token,
    'moodlewsrestformat' => 'json',
    'wsfunction' => 'core_course_get_courses'
]));
$moodleCourses = json_decode($courseResp, true);
$moodleCourseMap = [];
foreach ($moodleCourses as $course) {
    if (!empty($course['idnumber'])) {
        $moodleCourseMap[$course['idnumber']] = $course['id'];
    }
}

// Get all accepted requests and their course mappings
$requests = $pdo->query("
    SELECT cu.user_id, cu.request_id, cu.status, rc.course_id, u.email, c.course_code 
    FROM candidate_users cu 
    JOIN request_course rc ON cu.request_id = rc.request_id 
    JOIN users u ON cu.user_id = u.id 
    JOIN course c ON rc.course_id = c.id
    WHERE cu.status = 1
")->fetchAll();

foreach ($requests as $request) {
    // Get Moodle user ID
    $userCheckUrl = "$baseUrl?" . http_build_query([
        'wstoken' => $token,
        'moodlewsrestformat' => 'json',
        'wsfunction' => 'core_user_get_users_by_field',
        'field' => 'email',
        'values[0]' => $request['email']
    ]);
    $userResp = json_decode(file_get_contents($userCheckUrl), true);
    
    if (empty($userResp[0]['id'])) {
        echo "Warning: User {$request['email']} not found in Moodle\n";
        continue;
    }
    
    $moodleUserId = $userResp[0]['id'];
    
    // Get Moodle course ID using the course code
    $moodleCourseId = $moodleCourseMap[$request['course_code']] ?? null;
    if (!$moodleCourseId) {
        echo "Warning: Course {$request['course_code']} not found in Moodle\n";
        continue;
    }
    
    // Calculate the correct context ID for the course
    $contextId = 3 * $moodleCourseId;
    // Assign role (3 is the teacher role ID in Moodle)
    $assignParams = http_build_query([
        'wstoken' => $token,
        'moodlewsrestformat' => 'json',
        'wsfunction' => 'core_role_assign_roles',
        'assignments[0][roleid]' => 3,  // Teacher role
        'assignments[0][userid]' => $moodleUserId,
        'assignments[0][contextid]' => $contextId
    ]);
    $assignResp = file_get_contents("$baseUrl?$assignParams");
    echo "Assigned user {$request['email']} as teacher to course {$request['course_code']} — Response: $assignResp\n";
}

// Handle role unassignments for rejected/cancelled requests
$removedRequests = $pdo->query("
    SELECT cu.user_id, cu.request_id, rc.course_id, u.email, c.course_code 
    FROM candidate_users cu 
    JOIN request_course rc ON cu.request_id = rc.request_id 
    JOIN users u ON cu.user_id = u.id 
    JOIN course c ON rc.course_id = c.id
    WHERE cu.status != 1
")->fetchAll();

foreach ($removedRequests as $request) {
    // Get Moodle user ID
    $userCheckUrl = "$baseUrl?" . http_build_query([
        'wstoken' => $token,
        'moodlewsrestformat' => 'json',
        'wsfunction' => 'core_user_get_users_by_field',
        'field' => 'email',
        'values[0]' => $request['email']
    ]);
    $userResp = json_decode(file_get_contents($userCheckUrl), true);
    
    if (empty($userResp[0]['id'])) continue;
    
    $moodleUserId = $userResp[0]['id'];
    
    // Get Moodle course ID using the course code
    $moodleCourseId = $moodleCourseMap[$request['course_code']] ?? null;
    if (!$moodleCourseId) continue;
    
    // Calculate the correct context ID for the course
    $contextId = 3 * $moodleCourseId;
    // Unassign all roles for this user in this course
    $unassignParams = http_build_query([
        'wstoken' => $token,
        'moodlewsrestformat' => 'json',
        'wsfunction' => 'core_role_unassign_roles',
        'unassignments[0][roleid]' => 3,  // Teacher role
        'unassignments[0][userid]' => $moodleUserId,
        'unassignments[0][contextid]' => $contextId
    ]);
    $unassignResp = file_get_contents("$baseUrl?$unassignParams");
    echo "Removed user {$request['email']} from course {$request['course_code']} — Response: $unassignResp\n";
}

echo "Role sync completed.\n";
