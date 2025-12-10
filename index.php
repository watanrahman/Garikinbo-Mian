<?php
    session_start();
    include 'config.php';
    include __DIR__ . '/comp/card/AuctionPostCard.php';

    $now = date('Y-m-d H:i:s');

    // Fetch All Auctions at once
    $stmt = $pdo->prepare("SELECT a.*, u.profile_status, u.user_name, u.profile_pic_url, c.title, c.mileage, c.t_type, c.image_url 
        FROM auctions a 
        JOIN cars c ON a.car_id = c.id 
        JOIN users u ON c.user_id = u.id");
    $stmt->execute();
    $allAuctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Separate into categories
    $liveAuctions = [];
    $upcomingAuctions = [];
    $archivedAuctions = [];

    foreach ($allAuctions as $auction) {
        if ($auction['start_time'] <= $now && $auction['end_time'] >= $now) {
            $liveAuctions[] = $auction;
        } elseif ($auction['start_time'] > $now) {
            $upcomingAuctions[] = $auction;
        } elseif ($auction['end_time'] < $now && $auction['buyer_id'] != null) {
            $archivedAuctions[] = $auction;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Home</title>

    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
</head>

<style>
    html, body {
        overflow-x: hidden !important;
        width: 100%;
    }
</style>
<body>
    <?php include __DIR__ . '/comp/nav/MainNavbar.php'; ?>
    <div class="">

        <section class="">
            <div class="container pb-3">
                <?php include __DIR__ . '/comp/banner/HomepageBanner.php'; ?>
            </div>
        </section>
        
        <section class="pt-4 pb-3 our-ser-section">
            <?php include __DIR__ . '/comp/section/Service.php'; ?>
        </section>  

        
        <?php if (!empty($liveAuctions)) { ?>
            <section class="pt-3 pb-3">
                <div class="container">
                    <div class="row">
                        
                            <h3 class="mb-4">Live</h3>
                                <div class="owl-carousel owl-theme">
                                    <?php foreach ($liveAuctions as $auction): ?>
                                        <div class="item"> 
                                            <?php defaultPostCard($auction, $main_url, "live",); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        
                    </div>    
                </div> 
            </section>
        <?php } ?>
        <?php if (!empty($upcomingAuctions)) { ?>
            <section class="pt-3 pb-3">
                <div class="container">
                    <div class="row">
                        
                            <h3 class="mb-4">Upcoming</h3>
                                <div class="owl-carousel owl-theme">
                                    <?php foreach ($upcomingAuctions as $auction): ?>
                                        <div class="item">
                                            <?php defaultPostCard($auction, $main_url, "upcoming",); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        
                    </div>    
                </div> 
            </section>
        <?php } ?>
        <?php if (!empty($archivedAuctions)) { ?>
            <section class="pt-3 pb-3">
                <div class="container">
                    <div class="row">
                        
                            <h3 class="mb-4">Archive</h3>
                                <div class="owl-carousel owl-theme">
                                    <?php foreach ($archivedAuctions as $auction): ?>
                                        <div class="item">
                                            <?php defaultPostCard($auction, $main_url, "ended",); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        
                    </div>    
                </div> 
            </section>
        <?php } ?>
    </div>
    <div class="">
        <?php include __DIR__ . '/comp/section/Newsletter.php'; ?>
    </div>
    <?php include __DIR__ . '/comp/nav/MainFooter.php'; ?>
</body>





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
</html>
