<?php
    session_start();
    include '../../config.php';
    auth_check();

    $auction_id = $_GET['id'] ?? 0;

    // Fetch car + auction info
    $stmt = $pdo->prepare("
        SELECT c.*, a.start_price, a.car_id, a.auction_location, a.start_time, a.end_time, a.id as auction_id
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
        <title>Edit Auction</title>
        
        <!-- Css Files -->
        <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
    </head>
<body>

    <?php
        include ROOT_PATH . "comp/nav/MainNavbar.php"; 
    ?>
    <div class="container page-content">
        <div class="card p-3">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12">
                    <img class="img-fluid" src="<?php echo $main_url?>/uploads/thumbnails/<?php echo $auction['image_url']?>" alt="">
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12">
                    <p class="mb-0 mt-3 fs-4"><?php echo $auction['title']; ?></p>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-4 col-sm-12 col-12 text-end">
                    <a href="<?php echo $main_url?>/user/auction/edit.php?id=<?php echo $auction_id; ?>" class="btn btn-primary btn-app">Edit</a> 
                    <a href="<?php echo $main_url?>/user/auction/delete.php?id=<?php echo $auction_id; ?>" class="btn btn-danger btn-app">Delete</a> 
                </div>
            </div>
        </div>
    </div>
    <?php
        include ROOT_PATH . "comp/nav/MainFooter.php"; 
    ?>


</body>
</html>
