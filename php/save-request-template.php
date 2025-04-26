<?php
require_once('config.php');

try {
    // Get form data
    $templateId = isset($_POST['template_id']) ? $_POST['template_id'] : null;
    $title = $_POST['templateTitle'];
    $description = $_POST['templateDescription'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $academies = isset($_POST['academies']) ? $_POST['academies'] : [];
    $departments = isset($_POST['departments']) ? $_POST['departments'] : [];
    $courses = isset($_POST['courses']) ? $_POST['courses'] : [];

    // Start transaction
    $pdo->beginTransaction();

    if ($templateId) {
        // Update existing template
        $sql = "UPDATE request_templates 
                SET title = :title, 
                    description = :description, 
                    date_start = :start_date, 
                    date_end = :end_date 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $templateId,
            'title' => $title,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        // Delete existing relationships
        $tables = [
            'request_template_academy',
            'request_template_department',
            'request_template_course'
        ];

        foreach ($tables as $table) {
            $sql = "DELETE FROM $table WHERE template_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$templateId]);
        }
    } else {
        // Insert new template
        $sql = "INSERT INTO request_templates (title, description, date_start, date_end) 
                VALUES (:title, :description, :start_date, :end_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        $templateId = $pdo->lastInsertId();
    }

    // Insert academies
    if (!empty($academies)) {
        $sql = "INSERT INTO request_template_academy (template_id, academy_id) 
               VALUES (:template_id, :academy_id)";
        $stmt = $pdo->prepare($sql);
        foreach ($academies as $academyId) {
            if (!empty($academyId)) {
                $stmt->execute([
                    'template_id' => $templateId,
                    'academy_id' => $academyId
                ]);
            }
        }
    }

    // Insert departments
    if (!empty($departments)) {
        $sql = "INSERT INTO request_template_department (template_id, department_id) 
               VALUES (:template_id, :department_id)";
        $stmt = $pdo->prepare($sql);
        foreach ($departments as $departmentId) {
            if (!empty($departmentId)) {
                $stmt->execute([
                    'template_id' => $templateId,
                    'department_id' => $departmentId
                ]);
            }
        }
    }

    // Insert courses
    if (!empty($courses)) {
        $sql = "INSERT INTO request_template_course (template_id, course_id) 
               VALUES (:template_id, :course_id)";
        $stmt = $pdo->prepare($sql);
        foreach ($courses as $courseId) {
            if (!empty($courseId)) {
                $stmt->execute([
                    'template_id' => $templateId,
                    'course_id' => $courseId
                ]);
            }
        }
    }

    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => $templateId ? 'Template updated successfully' : 'Template created successfully',
        'template_id' => $templateId
    ]);
} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error saving template: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 