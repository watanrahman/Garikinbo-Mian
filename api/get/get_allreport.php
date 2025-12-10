<?php
require_once '../../config.php';
admin_token_check();
header('Content-Type: application/json');

try {
    $sql = "
        SELECT 
            r.id AS report_id,
            r.to_id,
            r.from_id,
            r.report_type,
            r.message,
            r.created_at
        FROM user_reports r
        ORDER BY r.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['reports' => $reports]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
