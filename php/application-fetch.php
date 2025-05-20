<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if (isset($_GET['fetch']) && $_GET['fetch'] === 'templates') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM request_templates WHERE date_end >= CURDATE()");
$stmt->execute();

        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($templates as &$template) {
            $tid = $template['id'];

            // Fetch associated course IDs
            $stmtC = $pdo->prepare("SELECT course_id FROM request_template_course WHERE template_id = ?");
            $stmtC->execute([$tid]);
            $template['courses'] = array_column($stmtC->fetchAll(PDO::FETCH_ASSOC), 'course_id');

            // Fetch associated academy IDs
            $stmtA = $pdo->prepare("SELECT academy_id FROM request_template_academy WHERE template_id = ?");
            $stmtA->execute([$tid]);
            $template['academies'] = array_column($stmtA->fetchAll(PDO::FETCH_ASSOC), 'academy_id');
        }

        echo json_encode([
            'success' => true,
            'templates' => $templates
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error fetching templates: ' . $e->getMessage()
        ]);
    }
    exit;
}

// If no valid fetch parameter, return error
echo json_encode([
    'success' => false,
    'message' => 'Invalid request'
]);
exit;
