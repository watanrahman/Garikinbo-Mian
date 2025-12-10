<?php
    session_start();
    include '../../config.php';

    header('Content-Type: application/json');

    if (!isset($_GET['auction_id']) || !is_numeric($_GET['auction_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid auction ID']);
        exit;
    }

    $auction_id = (int)$_GET['auction_id'];

    // Get auction info
    $stmt = $pdo->prepare("SELECT id, end_time, buyer_id FROM auctions WHERE id = :id");
    $stmt->execute(['id' => $auction_id]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$auction) {
        echo json_encode(['status' => 'error', 'message' => 'Auction not found']);
        exit;
    }

    // Check if auction has ended
    $now = time();
    $end_time = strtotime($auction['end_time']);
    $auctionEnded = $now >= $end_time;

    // If ended and buyer_id not set, determine winner
    if ($auctionEnded && $auction['buyer_id'] == 0) {
        // Find highest bidder
        $stmt = $pdo->prepare("SELECT user_id FROM bids WHERE auction_id = :auction_id ORDER BY bid_amount DESC LIMIT 1");
        $stmt->execute(['auction_id' => $auction_id]);
        $winner = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($winner) {
            // Update auction with buyer_id and mark as ended
            $stmt = $pdo->prepare("UPDATE auctions SET buyer_id = :buyer_id, auction_status = 'ended' WHERE id = :auction_id");
            $stmt->execute([
                'buyer_id' => $winner['user_id'],
                'auction_id' => $auction_id
            ]);
            echo json_encode(['status' => 'ended', 'winner' => true]);
        } else {
            // No bids, but mark as ended
            $stmt = $pdo->prepare("UPDATE auctions SET auction_status = 'ended' WHERE id = :auction_id");
            $stmt->execute(['auction_id' => $auction_id]);
            echo json_encode(['status' => 'ended', 'winner' => false]);
        }
    } else {
        echo json_encode([
            'status' => $auctionEnded ? 'ended' : 'active',
            'winner' => $auction['buyer_id'] ? true : false
        ]);
    }
?>
