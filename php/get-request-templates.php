<?php
require_once('config.php');

try {
    $sql = "SELECT rt.id, rt.title, rt.description, rt.date_start, rt.date_end, 
                   GROUP_CONCAT(DISTINCT CONCAT(a.academy_name, ' (', a.academy_code, ')')) as academies,
                   GROUP_CONCAT(DISTINCT CONCAT(d.department_name, ' (', d.department_code, ')')) as departments,
                   GROUP_CONCAT(DISTINCT CONCAT(c.course_name, ' (', c.course_code, ')')) as courses
            FROM request_templates rt
            LEFT JOIN request_template_academy rta ON rt.id = rta.template_id
            LEFT JOIN academies a ON rta.academy_id = a.id
            LEFT JOIN request_template_department rtd ON rt.id = rtd.template_id
            LEFT JOIN departments d ON rtd.department_id = d.id
            LEFT JOIN request_template_course rtc ON rt.id = rtc.template_id
            LEFT JOIN course c ON rtc.course_id = c.id
            GROUP BY rt.id, rt.title, rt.description, rt.date_start, rt.date_end
            ORDER BY rt.id DESC";

    $stmt = $pdo->query($sql);
    
    $html = '';
    if ($stmt) {
        $results = $stmt->fetchAll();
        if (count($results) > 0) {
            $counter = 1;
            foreach($results as $row) {
                $html .= '<tr data-id="' . htmlspecialchars($row['id']) . '">';
                $html .= '<td class="text-center">' . $counter++ . '</td>';
                $html .= '<td>' . htmlspecialchars($row['title']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['description']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['date_start']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['date_end']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['academies'] ?? 'N/A') . '</td>';
                $html .= '<td>' . htmlspecialchars($row['departments'] ?? 'N/A') . '</td>';
                $html .= '<td>' . htmlspecialchars($row['courses'] ?? 'N/A') . '</td>';
                $html .= '<td class="text-center">
                            <div class="d-flex justify-content-center">
                                <button class="btn text-info edit-template" data-id="' . $row['id'] . '">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn text-danger delete-template" data-id="' . $row['id'] . '">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                          </td>';
                $html .= '</tr>';
            }
        } else {
            $html = '<tr><td colspan="9" class="text-center">No requests found</td></tr>';
        }
    }

    echo $html;
} catch (PDOException $e) {
    echo '<tr><td colspan="9" class="text-center">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}
?> 