<?php
header('Content-Type: application/json');
include 'config.php';

$defaults = [
    'site_color' => '#06BBCC',
    'light_color' => '#F0FBFC',
    'dark_color' => '#181d38'
];

try {
    $stmt = $pdo->query("SELECT site_color, light_color, dark_color FROM colors LIMIT 1");
    $colors = $stmt->fetch();

    echo json_encode([
        'site_color' => $colors['site_color'] ?? $defaults['site_color'],
        'light_color' => $colors['light_color'] ?? $defaults['light_color'],
        'dark_color' => $colors['dark_color'] ?? $defaults['dark_color'],
        'logo_path' => file_exists('../assets/img/logo.png') ? '../assets/img/logo.png' : '',
        'logocut_path' => file_exists('../assets/img/logocut.png') ? '../assets/img/logocut.png' : ''
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB error: ' . $e->getMessage()]);
}
?>