<?php
require_once '../php_classes/Course.php';

$course = new Course($pdo);

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'fetch_departments':
        echo json_encode($course->getDepartments());
        break;

    case 'fetch_courses':
        echo json_encode($course->getCourses());
        break;

    case 'add_course':
        $deptName = $_POST['departmentname'];
        $deptId = $course->getDepartmentIdByName($deptName);
        $name = $_POST['name'];
        $code = $_POST['code'];

        if ($deptId && $name && $code) {
            $result = $course->addCourse($deptId, $name, $code);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
        }
        break;

    case 'delete_course':
        $id = $_POST['id'];
        echo json_encode(['success' => $course->deleteCourse($id)]);
        break;

    case 'update_course':
        $id = $_POST['id'];
        $deptName = $_POST['departmentname'];
        $deptId = $course->getDepartmentIdByName($deptName);
        $name = $_POST['name'];
        $code = $_POST['code'];

        if ($deptId && $name && $code) {
            $result = $course->updateCourse($id, $deptId, $name, $code);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
        }
        break;
}
?>
