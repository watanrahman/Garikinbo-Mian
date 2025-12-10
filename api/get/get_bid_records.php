<?php
    header('Content-Type: application/json');
    include '../../config.php';

    if (!isset($_GET['auction_id']) || !is_numeric($_GET['auction_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid auction ID']);
        exit;
    }

    $auction_id = (int) $_GET['auction_id'];

    // Fetch bid records
    $stmt = $pdo->prepare("
        SELECT b.bid_amount, b.created_at AS bid_time, u.user_name as username
        FROM bids b
        JOIN users u ON b.user_id = u.id
        WHERE b.auction_id = :auction_id
        ORDER BY b.created_at DESC
    ");
    $stmt->execute(['auction_id' => $auction_id]);
    $bid_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the bid records
    $formatted_bids = [];
    foreach ($bid_records as $bid) {
        $formatted_bids[] = [
            'username' => htmlspecialchars($bid['username']),
            'bid_amount' => number_format($bid['bid_amount'], 2, '.', ','),
            'bid_time' => date("h:i A", strtotime($bid['bid_time']))
        ];
    }

    echo json_encode([
        'status' => 'success',
        'bids' => $formatted_bids
    ]);
?>