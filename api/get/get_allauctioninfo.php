<?php
require_once '../../config.php';
admin_token_check();
header('Content-Type: application/json');

try {
    $sql = "
        SELECT 
            a.id AS auction_id,
            a.start_price,
            a.current_highest_bid,
            a.auction_location,
            a.start_time,
            a.end_time,
            a.auction_status,
            a.created_at,
            c.title AS car_title,
            c.image_url AS car_image,
            c.make,
            c.model,
            c.year,
            u.full_name AS seller_name,
            u.phone_number AS seller_phone,
            u.location AS seller_location
        FROM auctions a
        JOIN cars c ON a.car_id = c.id
        JOIN users u ON a.user_id = u.id
        ORDER BY a.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['auctions' => $auctions]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
