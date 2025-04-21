<?php
require_once '../php/config.php';

class Course {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getDepartments() {
        $stmt = $this->pdo->query("SELECT id, department_name FROM departments ORDER BY department_name");
        return $stmt->fetchAll();
    }

    public function getCourses() {
        $stmt = $this->pdo->query("
            SELECT c.id, c.course_name, c.course_code, d.department_name 
            FROM course c 
            JOIN departments d ON c.department_id = d.id 
            ORDER BY c.id DESC
        ");
        return $stmt->fetchAll();
    }

    public function addCourse($departmentId, $courseName, $courseCode) {
        $stmt = $this->pdo->prepare("INSERT INTO course (department_id, course_name, course_code) VALUES (?, ?, ?)");
        return $stmt->execute([$departmentId, $courseName, $courseCode]);
    }

    public function deleteCourse($id) {
        $stmt = $this->pdo->prepare("DELETE FROM course WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateCourse($id, $departmentId, $courseName, $courseCode) {
        $stmt = $this->pdo->prepare("UPDATE course SET department_id = ?, course_name = ?, course_code = ? WHERE id = ?");
        return $stmt->execute([$departmentId, $courseName, $courseCode, $id]);
    }

    public function getDepartmentIdByName($departmentName) {
        $stmt = $this->pdo->prepare("SELECT id FROM departments WHERE department_name = ?");
        $stmt->execute([$departmentName]);
        return $stmt->fetchColumn();
    }
}
?>
