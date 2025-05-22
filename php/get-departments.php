<?php
require_once('config.php');

// Debug: See what is being received
if (!isset($_GET['academy_ids'])) {
    echo "No academy_ids parameter received";
    exit;
}
var_dump($_GET['academy_ids']);

try {
    $academyIds = isset($_GET['academy_ids']) ? $_GET['academy_ids'] : [];
    
    if (!is_array($academyIds)) {
        $academyIds = [$academyIds];
    }
    
    if (!empty($academyIds)) {
        $placeholders = str_repeat('?,', count($academyIds) - 1) . '?';
        
        $sql = "SELECT DISTINCT d.id, CONCAT(d.department_name, ' (', d.department_code, ')') as department_display 
                FROM departments d 
                WHERE d.academy_id IN ($placeholders)
                ORDER BY d.department_name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($academyIds);
        
        $html = '<option value="">Select Department</option>';
        while ($row = $stmt->fetch()) {
            $html .= '<option value="' . htmlspecialchars($row['id']) . '">' . 
                    htmlspecialchars($row['department_display']) . '</option>';
        }
        echo $html;
    } else {
        echo '<option value="">Select Academy First</option>';
    }
} catch (PDOException $e) {
    echo '<option value="">Error loading departments</option>';
}
?> 