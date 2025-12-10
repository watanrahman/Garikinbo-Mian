<?php
    session_start();
    include '../../config.php';
    auth_check();

    $auction_id = $_GET['id'] ?? 0;

    // Fetch car + auction info
    $stmt = $pdo->prepare("
        SELECT c.*, a.start_price, a.auction_location, a.start_time, a.end_time, a.id as auction_id
        FROM cars c 
        JOIN auctions a ON c.id = a.car_id
        WHERE a.id = ?
    ");
    $stmt->execute([$auction_id]);
    $auction = $stmt->fetch();

    if (!$auction || $auction['user_id'] !== $user['id']) {
        exit("Something went wrong");
    }

    $img_stmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id=?");
    $img_stmt->execute([$auction['id']]);
    $car_images = $img_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete Auction</title>

        <!-- Css Files -->
        <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
    </head>
<body>

    <?php
        include ROOT_PATH . "comp/nav/MainNavbar.php"; 
    ?>

    <div class="container page-content">    
        <button class="btn btn-app btn-primary border mb-3" onclick="history.go(-1);"><i class="bi bi-arrow-left"></i> No, Cancel</button>

        <p class="fs-5">
            <b><?php echo $user['full_name']?></b> Are you sure to Delete <b>"<?php echo $auction['title']?>"</b> post ? <a href="<?php echo $main_url?>/api/delete/delete_bid_post.php?auction_id=<?php echo $auction['auction_id']; ?>" class="btn btn-danger btn-app">Yes, Delete</a>
        </p>
    </div>

    <?php
        include ROOT_PATH . "comp/nav/MainFooter.php"; 
    ?>
</body>
</html>
