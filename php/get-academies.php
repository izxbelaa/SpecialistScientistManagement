<?php
require_once('config.php');

try {
    $sql = "SELECT id, academy_name, academy_code 
            FROM academies 
            ORDER BY academy_name";
    $stmt = $pdo->query($sql);
    
    $options = '';
    while($row = $stmt->fetch()) {
        $options .= '<option value="' . $row['id'] . '">' . 
                   htmlspecialchars($row['academy_name']) . 
                   ' (' . htmlspecialchars($row['academy_code']) . ')</option>';
    }
    
    echo $options;
} catch (PDOException $e) {
    echo '<option value="">Error loading academies: ' . htmlspecialchars($e->getMessage()) . '</option>';
}
?> 