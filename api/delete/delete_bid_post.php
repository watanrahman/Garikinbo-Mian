<?php
    session_start();
    require_once '../../config.php';
    auth_check();

    $user_id = $_SESSION['user_id'];

    if (!isset($_GET['auction_id']) || !is_numeric($_GET['auction_id'])) {
        echo "Invalid or missing auction ID.";
        exit;
    }

    $auction_id = intval($_GET['auction_id']);

    try {
        // Fetch auction and join car to get car_id and image_url
        $stmt = $pdo->prepare("
            SELECT a.id as auction_id, a.user_id, a.car_id, c.image_url 
            FROM auctions a
            INNER JOIN cars c ON a.car_id = c.id
            WHERE a.id = ?
        ");
        $stmt->execute([$auction_id]);
        $auction = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$auction) {
            echo "Auction not found.";
            exit;
        }

        if ($auction['user_id'] != $user_id) {
            echo "Unauthorized: You do not have permission to delete this auction.";
            exit;
        }

        $car_id = $auction['car_id'];
        $thumb_name = $auction['image_url'];

        $pdo->beginTransaction();

        // Fetch and delete car images
        $img_stmt = $pdo->prepare("SELECT file_name FROM car_images WHERE car_id = ?");
        $img_stmt->execute([$car_id]);
        $images = $img_stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($images as $img) {
            $img_path = ROOT_PATH . "uploads/car_images/" . $img['file_name'];
            if (file_exists($img_path)) {
                unlink($img_path);
            }
        }

        // Delete car image DB entries
        $del_img_stmt = $pdo->prepare("DELETE FROM car_images WHERE car_id = ?");
        $del_img_stmt->execute([$car_id]);

        // Delete auction — this will cascade delete bids due to FK
        $del_auction_stmt = $pdo->prepare("DELETE FROM auctions WHERE id = ?");
        $del_auction_stmt->execute([$auction_id]);

        // Delete the car entry
        $del_car_stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
        $del_car_stmt->execute([$car_id]);

        // Delete thumbnail file
        $thumb_path = ROOT_PATH . "uploads/thumbnails/" . $thumb_name;
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }

        $pdo->commit();

        header('Location: ' . $main_url . '/user/index.php?u=' . $user['user_name']);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error during deletion: " . $e->getMessage());
        echo "Failed to delete auction: " . $e->getMessage();
    }
?>
