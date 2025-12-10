<?php
    error_reporting(E_ERROR | E_PARSE);
    session_start();
    include '../config.php';
    include ROOT_PATH . 'comp/card/AuctionPostCard.php';
    
    auth_check();

    $user_id = $_SESSION['user_id'];
    $now = date('Y-m-d H:i:s');

    // Get the requested username from URL parameter
    $requested_username = isset($_GET['u']) ? trim($_GET['u']) : '';

    // If no username provided, redirect to own profile
    if (empty($requested_username)) {
        header("Location: ?user=" . $_SESSION['user_name']);
        exit;
    }

    // Fetch user profile information
    $stmt = $pdo->prepare("
        SELECT * 
        FROM users 
        WHERE user_name = ?
    ");
    $stmt->execute([$requested_username]);
    $user_profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_profile) {
        die("User not found.");
    }

    $profile_user_id = $user_profile['id'];
    $is_own_profile = ($profile_user_id == $user_id);

    // Fetch user's auctions (posts)
    // Fetch All Auctions at once
    $stmt = $pdo->prepare("SELECT a.*, u.profile_status, u.user_name, u.profile_pic_url, c.title, c.mileage, c.t_type, c.image_url 
        FROM auctions a
        JOIN cars c ON a.car_id = c.id 
        JOIN users u ON c.user_id = u.id
        WHERE a.user_id = :id
    ");
    $stmt->execute(['id' => $user_profile['id']]);
    $allAuctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Separate into categories
    $liveAuctions = [];
    $upcomingAuctions = [];
    $endedAuctions = [];

    foreach ($allAuctions as $auction) {
        if ($auction['start_time'] <= $now && $auction['end_time'] >= $now) {
            $liveAuctions[] = $auction;
        } elseif ($auction['start_time'] > $now) {
            $upcomingAuctions[] = $auction;
        }else {
            $endedAuctions[] = $auction;
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user_profile['full_name']); ?> - Profile</title>

    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
    
</head>
<body>
    <?php include ROOT_PATH . "comp/nav/MainNavbar.php"; ?>

    <div class="page-content">
        <div class="mb-3">
            <div class="container">
                <?php include ROOT_PATH . "comp/section/UserProfileTop.php"; ?>
            </div>
        </div>

        <div class="mb-4">
            <div class="container">
                <div class="card border-0 p-2">
                    <div class="d-flex align-items-center">
                        <button class="btn border btn-app btn-primary me-2">Posts</button>
                        <button class="btn border btn-app me-2">Auction History</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="">
            <!-- User Auction Posts Carousel -->
            <?php if (!empty($allAuctions)) {  ?>
                <section class=""  >
                    <div class="container">
                        <div class="mb-3">
                            <h4>Auction posts</h4>
                        </div>
                        <div class="row">
                            <?php if (!empty($liveAuctions)) { ?>
                                <?php foreach ($liveAuctions as $auction): ?>
                                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
                                        <?php defaultPostCard($auction, $main_url, "live"); ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php } ?>

                            <?php if (!empty($upcomingAuctions)) { ?>
                                <?php foreach ($upcomingAuctions as $auction): ?>
                                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
                                        <?php defaultPostCard($auction, $main_url, "upcoming"); ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php } ?>

                            <?php if (!empty($endedAuctions)) { ?>
                                <?php foreach ($endedAuctions as $auction): ?>
                                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
                                        <?php defaultPostCard($auction, $main_url, "upcoming"); ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php } ?>
                        </div>

                    </div>
                </section>

            <?php } else { ?>
                <div class="container">
                <?php 
                    if ($user_profile['id'] === $_SESSION['user_id']) {
                        ?>
                            <div class="alert fs-5 text-center">
                                Hello <b><?php echo $user['full_name']; ?></b> You don't have any post to show <a class="ms-3 btn btn-app btn-success" href="auction/create.php">Create Post</a>
                            </div>
                        <?php
                    }else {
                        ?>
                            <div class="alert alert-info fs-5 text-center">
                                This user have no post to currently
                            </div>
                        <?php
                    }
                ?>
                </div>
            <?php } ?>            
        </div>
    

    </div>
        <?php include ROOT_PATH . "comp/nav/MainFooter.php"; ?>

    <script src="<?php echo $main_url;?>/lib/js/bootstrap.js"></script>

    <!-- OwlCarousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <!-- Carousel Init -->
    <script>
    $(document).ready(function(){
        $(".owl-carousel").owlCarousel({
            loop: false,
            margin: 20,
            dots:false,
            nav: true,
            navText: [
                '<span class="nav-left-button"><i class="bi bi-caret-left-fill fs-3"></i></span>',
                '<span class="nav-right-button"><i class="bi bi-caret-right-fill fs-3"></i></span>'
            ],
            responsive:{
                0:{ items:1 },
                768:{ items:2 },
                1200:{ items:3 }
            }
        });
    });
    </script>
</body>
</html>