<?php
    // get_highest_bid.php
    session_start();
    include '../../config.php';

    header('Content-Type: application/json');

    if (!isset($_GET['auction_id'])) {
        echo json_encode(["highest_bid" => 0]);
        exit;
    }

    $auction_id = (int) $_GET['auction_id'];

    $stmt = $pdo->prepare("SELECT current_highest_bid as highest_bid FROM auctions WHERE id = :id");
    $stmt->execute(['id' => $auction_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["highest_bid" => $result['highest_bid'] ?? 0]);
