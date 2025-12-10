<?php 
    session_start();
    include '../../config.php';
    auth_check();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["status" => "error", "message" => "Invalid request"]);
        exit;
    }

    if (!isset($_POST['auction_id'], $_POST['bid_amount'])) {
        echo json_encode(["status" => "error", "message" => "Missing data"]);
        exit;
    }

    $auction_id = (int) $_POST['auction_id'];
    $bid_amount = (float) $_POST['bid_amount'];

    // Single query: get start_price, author, highest bid
    $sql = "
        SELECT 
            a.start_price,
            a.user_id AS author_id,
            current_highest_bid as highest_bid
        FROM auctions a
        LEFT JOIN bids b ON a.id = b.auction_id
        WHERE a.id = :id
        GROUP BY a.id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $auction_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["status" => "error", "message" => "Auction not found"]);
        exit;
    }

    $highestBid = $row['highest_bid'] !== null ? (float)$row['highest_bid'] : 0;
    $startPrice = (float)$row['start_price'];
    $authorId   = (int)$row['author_id']; // auction creator user_id
    $new_highestBid = $highestBid == null ? $startPrice : $highestBid;
    $new_bid_amount = $new_highestBid + $bid_amount;

    // Prevent same user attempt
    if ($authorId == $_SESSION['user_id']) {
        echo json_encode(["status" => "error", "message" => "You can not bid in your auctions"]);
        exit;
    }


    if ($new_bid_amount > $startPrice && $new_bid_amount > $highestBid) {
        // Insert the bid
        $stmt = $pdo->prepare("INSERT INTO bids (auction_id, user_id, bid_amount, bid, created_at) VALUES (:auction_id, :user_id, :bid, :bid_amount, NOW())");
        $success = $stmt->execute([
            'auction_id' => $auction_id,
            'user_id'    => $_SESSION['user_id'],
            'bid_amount' => $new_bid_amount,
            'bid' => $bid_amount
        ]);

        if ($success) {
            // Update the current_highest_bid in auctions table
            $updateStmt = $pdo->prepare("UPDATE auctions SET current_highest_bid = :bid_amount WHERE id = :auction_id");
            $updateSuccess = $updateStmt->execute([
                'bid_amount' => $new_bid_amount,
                'auction_id' => $auction_id
            ]);

            if ($updateSuccess) {
                echo json_encode(["status" => "success", "message" => "Bid placed successfully!", "new_highest" => $bid_amount]);
            } else {
                echo json_encode(["status" => "error", "message" => "Bid recorded but failed to update highest bid."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Error placing bid."]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Your bid must be higher than the start price (".number_format($startPrice)." BDT) and current highest bid (".number_format($new_highestBid)." BDT) and your bid is (".number_format($new_bid_amount)." BDT)",
        ]);
    }
?>

