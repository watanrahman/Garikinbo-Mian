<?php

    require_once '../../config.php'; // connect to DB
    // only admin access 
    admin_token_check();
    header('Content-Type: application/json');


    try {
        // Fetch main stats
        $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $total_cars = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
        $active_auctions = $pdo->query("SELECT COUNT(*) FROM auctions WHERE auction_status = 'active'")->fetchColumn();
        $ended_auctions = $pdo->query("SELECT COUNT(*) FROM auctions WHERE auction_status = 'ended'")->fetchColumn();

        echo json_encode([
            'total_users' => (int)$total_users,
            'total_cars' => (int)$total_cars,
            'active_auctions' => (int)$active_auctions,
            'ended_auctions' => (int)$ended_auctions,
            'last_update' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed', 'details' => $e->getMessage()]);
    }
?>
