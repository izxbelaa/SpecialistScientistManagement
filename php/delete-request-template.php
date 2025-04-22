<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template_id'])) {
    try {
        $templateId = $_POST['template_id'];

        // Start transaction
        $pdo->beginTransaction();

        // Delete associated records in junction tables
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

        // Delete the template
        $sql = "DELETE FROM request_templates WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$templateId]);

        // Commit transaction
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error deleting template: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?> 