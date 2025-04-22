<?php
require_once('config.php');

try {
    $departmentIds = isset($_GET['department_id']) ? $_GET['department_id'] : [];
    
    if (!is_array($departmentIds)) {
        $departmentIds = [$departmentIds];
    }
    
    if (!empty($departmentIds)) {
        $placeholders = str_repeat('?,', count($departmentIds) - 1) . '?';
        
        $sql = "SELECT DISTINCT c.id, c.course_name, c.course_code, d.department_name, d.department_code
                FROM course c
                JOIN departments d ON c.department_id = d.id
                WHERE c.department_id IN ($placeholders)
                ORDER BY d.department_name, c.course_name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($departmentIds);
        
        $html = '';
        $currentDepartment = '';
        
        while ($row = $stmt->fetch()) {
            if ($currentDepartment != $row['department_name']) {
                if ($currentDepartment != '') {
                    $html .= '</div>';
                }
                $currentDepartment = $row['department_name'];
                $html .= '<div class="department-section mb-3" data-department-id="' . htmlspecialchars($row['department_code']) . '">';
                $html .= '<h6 class="department-header">' . htmlspecialchars($row['department_name']) . 
                        ' (' . htmlspecialchars($row['department_code']) . ')</h6>';
            }
            
            $html .= '<div class="form-check">';
            $html .= '<input type="checkbox" class="form-check-input course-checkbox" name="courses[]" ' .
                    'value="' . htmlspecialchars($row['id']) . '" ' .
                    'id="course_' . htmlspecialchars($row['id']) . '" ' .
                    'data-department="' . htmlspecialchars($row['department_code']) . '">';
            $html .= '<label class="form-check-label" for="course_' . htmlspecialchars($row['id']) . '">' .
                    htmlspecialchars($row['course_name']) . ' (' . htmlspecialchars($row['course_code']) . ')</label>';
            $html .= '</div>';
        }
        
        if ($currentDepartment != '') {
            $html .= '</div>';
        }
        
        if (empty($html)) {
            echo '<p class="text-muted">No courses found for selected departments</p>';
        } else {
            echo $html;
        }
    } else {
        echo '<p class="text-muted">Please select at least one department</p>';
    }
} catch (PDOException $e) {
    echo '<p class="text-danger">Error loading courses</p>';
}
?> 