<?php
require_once 'config.php';

try {
    if (isset($_GET['template_id'])) {
        $templateId = $_GET['template_id'];
        
        // Get basic template info
        $sql = "SELECT id, title, description, date_start, date_end 
                FROM request_templates 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$templateId]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($template) {
            // Get academies
            $sql = "SELECT a.id, a.academy_name, a.academy_code
                    FROM academies a
                    JOIN request_template_academy rta ON a.id = rta.academy_id
                    WHERE rta.template_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$templateId]);
            $academies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $template['academy_ids'] = array_column($academies, 'id');
            
            // Get departments
            $sql = "SELECT d.id, d.department_name, d.department_code
                    FROM departments d
                    JOIN request_template_department rtd ON d.id = rtd.department_id
                    WHERE rtd.template_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$templateId]);
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $template['department_ids'] = array_column($departments, 'id');
            
            // Get courses
            $sql = "SELECT c.id, c.course_name, c.course_code
                    FROM course c
                    JOIN request_template_course rtc ON c.id = rtc.course_id
                    WHERE rtc.template_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$templateId]);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $template['course_ids'] = array_column($courses, 'id');
            
            echo json_encode($template);
        } else {
            echo json_encode(['error' => 'Template not found']);
        }
    } else {
        echo json_encode(['error' => 'No template ID provided']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
?> 