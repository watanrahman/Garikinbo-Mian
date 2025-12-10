<?php
    session_start();
    include '../config.php';
    auth_check();

    // Validate auction ID
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("Invalid auction ID.");
    }

    $auction_id = (int)$_GET['id'];

    // Fetch auction details
    $stmt_cd = $pdo->prepare("
        SELECT a.*, c.*, u.user_name as username, u.phone_number, u.email, c.id as car_id
        FROM auctions a 
        JOIN cars c ON a.car_id = c.id 
        JOIN users u ON c.user_id = u.id 
        WHERE a.id = :id
    ");

    $stmt_cd->execute(['id' => $auction_id]);
    $auction = $stmt_cd->fetch(PDO::FETCH_ASSOC);

    // Fetch car images
    $stmt_ci = $pdo->prepare("
        SELECT * FROM car_images WHERE car_id = :id
    ");
    $stmt_ci->execute(['id' => $auction['car_id']]);
    $car_images = $stmt_ci->fetchAll(PDO::FETCH_ASSOC);

    if ($auction && $auction['auction_status'] === 'ended') {
        header("Location: result.php?id=" . $auction_id);
        exit;
    }
    if (!$auction) {
        die("Auction not found.");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Details - <?php echo htmlspecialchars($auction['title']); ?></title>

    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
</head>
<body>
    <?php include ROOT_PATH . "comp/nav/MainNavbar.php"; ?>
    <div class="page-content">
        <div class="container">
            <div class="row">
                <input 
                    type="text" 
                    name="" 
                    value="<?php echo $auction['start_price']; ?>"
                    id="base_price_input" hidden>

                <!-- Right Column: Bidding Section -->
                <div class="order-1 order-md-1 order-xl-1 order-lg-1 col-xl-3 col-lg-4 col-md-12 col-sm-12 col-12 bidding-section-parent">
                    <div class="bg-dark text-light bidding-section-wrapper card shadow-sm border-0 mb-4">
                        <!-- Bid History -->
                        <div id="bid-record-card" class="card-body p-0">
                            <div class="d-flex p-3 justify-content-between align-items-center m-0">
                                <h6 class="mb-0 fw-bold">Bid History</h6>
                                <span id="bidCount" class="badge bg-primary text-white">0 Bids</span>
                            </div>
                            <div style="min-height: 200px; max-height: 250px; overflow: auto;" class="">
                                <ul id="bidList" class="list-unstyled mb-0 small" id="bidList">
                                    <li class="text-muted">Loading bid history...</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>                            

                <!-- Middle Column: Car Details -->
                <div class="order-3 order-md-3 order-xl-2 order-lg-2 col-xl-6 col-lg-4 col-md-12 col-sm-12 col-12">
                    <!-- Car Image Section -->
                    <div class="mb-4">

                        <!-- Big Display -->
                        <div class="mb-3 text-center">
                            <img 
                                id="mainImage"
                                class="img-fluid rounded"
                                src="<?php echo $main_url; ?>/uploads/thumbnails/<?php echo $auction['image_url']; ?>" 
                                alt="<?php echo htmlspecialchars($auction['title']); ?>"
                                style="max-height:400px; object-fit:contain;"
                            >
                        </div>

                        <!-- Thumbnails Carousel -->
                        <div class="owl-carousel owl-theme">
                            <!-- First image -->
                            <div class="item">
                                <img 
                                    class="img-fluid rounded thumb-img active"
                                    src="<?php echo $main_url; ?>/uploads/thumbnails/<?php echo $auction['image_url']; ?>" 
                                    alt="<?php echo htmlspecialchars($auction['title']); ?>"
                                    style="cursor:pointer; max-height:120px; object-fit:cover;"
                                >
                            </div>

                            <!-- Loop other images -->
                            <?php foreach ($car_images as $car): ?>
                                <div class="item">
                                    <img 
                                        class="img-fluid rounded thumb-img"
                                        src="<?php echo $main_url?>/uploads/car_images/<?php echo $car['file_name']?>" 
                                        alt="Car image"
                                        style="cursor:pointer; max-height:120px; object-fit:cover;"
                                    >
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>



                    <!-- Title -->
                    <div class="mb-3">
                        <h2 class="mb-0"><?php echo htmlspecialchars($auction['title']); ?></h2>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <p class="mb-0 text-muted"><?php echo nl2br(htmlspecialchars($auction['des'])); ?></p>
                    </div>
                    
                    <!-- Car Details Table -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Car Details</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <!-- Basic Information -->
                                        <tr class="table-secondary">
                                            <th colspan="4" class="text-center">Basic Information</th>
                                        </tr>
                                        <tr>
                                            <th width="25%">Make</th>
                                            <td width="25%"><?php echo htmlspecialchars($auction['make'] ?? 'N/A'); ?></td>
                                            <th width="25%">Model</th>
                                            <td width="25%"><?php echo htmlspecialchars($auction['model'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Year</th>
                                            <td><?php echo htmlspecialchars($auction['year'] ?? 'N/A'); ?></td>
                                            <th>Body Type</th>
                                            <td><?php echo htmlspecialchars($auction['body_type'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Transmission</th>
                                            <td><?php echo htmlspecialchars($auction['t_type'] ?? 'N/A'); ?></td>
                                            <th>Fuel Type</th>
                                            <td><?php echo htmlspecialchars($auction['fuel_type'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Engine Capacity</th>
                                            <td><?php echo htmlspecialchars($auction['engine_capacity'] ?? 'N/A'); ?> cc</td>
                                            <th>Mileage</th>
                                            <td><?php echo htmlspecialchars($auction['mileage'] ?? 'N/A'); ?> km</td>
                                        </tr>
                                        
                                        <!-- Registration Details -->
                                        <tr class="table-secondary">
                                            <th colspan="4" class="text-center">Registration Details</th>
                                        </tr>
                                        <tr>
                                            <th>Registration Number</th>
                                            <td><?php echo htmlspecialchars($auction['registration_number'] ?? 'N/A'); ?></td>
                                            <th>Registration Year</th>
                                            <td><?php echo htmlspecialchars($auction['registration_year'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Ownership Status</th>
                                            <td colspan="3"><?php echo htmlspecialchars($auction['ownership_status'] ?? 'N/A'); ?></td>
                                        </tr>
                                        
                                        <!-- Validity Information -->
                                        <tr class="table-secondary">
                                            <th colspan="4" class="text-center">Validity Information</th>
                                        </tr>
                                        <tr>
                                            <th>Tax Token Validity</th>
                                            <td><?php echo $auction['tax_token_validity'] ? date("M d, Y", strtotime($auction['tax_token_validity'])) : 'N/A'; ?></td>
                                            <th>Fitness Validity</th>
                                            <td><?php echo $auction['fitness_validity'] ? date("M d, Y", strtotime($auction['fitness_validity'])) : 'N/A'; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Insurance Validity</th>
                                            <td colspan="3"><?php echo $auction['insurance_validity'] ? date("M d, Y", strtotime($auction['insurance_validity'])) : 'N/A'; ?></td>
                                        </tr>
                                        
                                        <!-- History & Condition -->
                                        <tr class="table-secondary">
                                            <th colspan="4" class="text-center">History & Condition</th>
                                        </tr>
                                        <tr>
                                            <th>Accident History</th>
                                            <td><?php echo htmlspecialchars($auction['accident_history'] ?? 'N/A'); ?></td>
                                            <th>Service History</th>
                                            <td><?php echo htmlspecialchars($auction['service_history'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Car Condition</th>
                                            <td colspan="3"><?php echo htmlspecialchars($auction['car_condition'] ?? 'N/A'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Bidding Section -->
                <div class="order-2 order-md-2 order-xl-3 order-lg-3 col-xl-3 col-lg-4 col-md-12 col-sm-12 col-12 bidding-section-parent">
                    <div class="status text-danger fw-bold mb-2"></div>

                    <div class="bidding-section-wrapper">

                        <!-- Auction Host -->
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-person-circle me-2 text-primary fs-4"></i>
                            <small class="text-muted">
                                Hosted by 
                                <a href="<?php echo $main_url?>/user/index.php?u=<?php echo $auction['username']?>" class="fw-bold text-decoration-none">
                                    @<?php echo htmlspecialchars($auction['username']); ?>
                                </a>
                            </small>
                        </div>

                        <!-- Price Details -->
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Start Price</span>
                                    <span class="fw-semibold"><?php echo number_format($auction['start_price']); ?> BDT</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Current Bid</span>
                                    <span id="highestBid" class="fw-bold text-primary"><?php echo number_format($auction['start_price']); ?> BDT</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Auction Ends</span>
                                    <span class="fw-semibold"><?php echo date("M d, Y h:i A", strtotime($auction['end_time'])); ?></span>
                                </div>
                            </div>
                        </div>

                        <?php 
                        if (isset($_SESSION['auth_token']) || $_SESSION['auth_token'] == ADMIN_TOKEN) {
                            ?>

                            <?php
                        }
                        else if ($user['id'] === $auction['user_id']) { ?>
                            <!-- Manage Button -->
                            <a class="btn btn-primary btn-app w-100 mb-3" 
                                href="<?php echo $main_url ?>/user/auction/manage.php?id=<?php echo $auction_id; ?>">
                                <i class="bi bi-gear me-1"></i> Manage Post
                            </a>
                        <?php } else { ?>
                            <!-- Place Bid -->
                            <div class="card shadow-sm border-0 mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Place Your Bid</h6>

                                <form method="POST" id="bidForm" class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="fs-4 ps-2 me-2 text-success fw-bold border-0">+</span>
                                        <input 
                                            type="number"
                                            class="form-control me-1"
                                            name="bid_amount"
                                            id="bidIncrementInput"
                                            placeholder="Enter Amount"
                                            min="100"
                                        required
                                        >
                                        <button class="btn btn-success px-4 ms-1 btn-app" type="submit">
                                            Place
                                        </button>
                                    </div>
                                </form>



                                <div class="d-flex gap-2 mb-2">
                                    <button type="button" class="btn btn-outline-success btn-app btn-sm quick-add" data-increment="500">+500</button>
                                    <button type="button" class="btn btn-outline-success btn-app btn-sm quick-add" data-increment="1000">+1000</button>
                                    <button type="button" class="btn btn-outline-success btn-app btn-sm quick-add" data-increment="1500">+1500</button>
                                </div>

                                <div id="bidStatus" class="small text-muted"></div>
                            </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>

            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="<?php echo $main_url; ?>/lib/js/bootstrap.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
        
        <script type="text/javascript">
            let auctionId = <?php echo $auction_id; ?>;
            let refreshInterval = 3000; // 3 seconds

            // Quick Add Buttons Functionality
            $('.quick-add').on('click', function() {
                let increment = parseInt($(this).data('increment'));
                let input = $('#bidIncrementInput');
                let currentValue = parseFloat(input.val()) || 0;

                // Add the increment to current value
                let newValue = currentValue + increment;

                // Optional: Set a minimum bid increment (e.g., at least 100)
                if (newValue < 100) newValue = 100;

                input.val(newValue);

                // Optional: Trigger input event so any validations update
                input.trigger('input');
            });

            // Safely format BDT value
            function formatBDT(value) {
                let num = parseFloat(value);
                return isNaN(num) ? "0 BDT" : num.toLocaleString('en-US') + " BDT";
            }

            // Fetch highest bid
            function fetchHighestBid() {
                $.get('<?php echo $main_url; ?>/api/get/get_highest_bid.php', { auction_id: auctionId }, function(data) {
                    if (data.highest_bid !== undefined && data.highest_bid !== null && data.highest_bid !== "") {
                        $('#highestBid').text(formatBDT(data.highest_bid));
                    } else {
                        $('#highestBid').text("Nothing to show");
                    }
                }, 'json').fail(function() {
                    console.log('Failed to fetch highest bid');
                });
            }

            // Fetch bid history dynamically
            function fetchBidRecords() {
                $.get('<?php echo $main_url; ?>/api/get/get_bid_records.php', { auction_id: auctionId }, function(data) {
                    let bidList = $('#bidList');
                    bidList.empty();

                    if (data.status === 'success' && data.bids.length > 0) {
                        $('#bidCount').text(data.bids.length + ' Bids');
                        
                        let basePriceStr = document.getElementById('base_price_input').value.replace(/,/g, '');
                        let basePrice = parseFloat(basePriceStr) || 0;
                        
                        console.log(basePrice);

                        data.bids.forEach(function(bid) {
                            
                            let bidAmountStr = (bid.bid_amount || '').toString().replace(/,/g, '');
                            let bidAmount = parseFloat(bidAmountStr) || 0;
                            let difference = bidAmount - basePrice;

                            bidList.append(`
                                <li class="pe-3 ps-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="diff">+${bidAmountStr}</span>
                                            <span class="user">by ${bid.username || 'Unknown'}</span>
                                        </div>
                                        <div>
                                            <span class="time">${bid.bid_time || 'N/A'}</span>
                                        </div>
                                    </div>
                                </li>
                            `);
                        });
                    } else {
                        $('#bidCount').text('0 Bids');
                        bidList.append('<li class="text-muted">No bids yet.</li>');
                    }
                }, 'json').fail(function() {
                    console.log('Failed to fetch bid records');
                });
            }
            // Handle bid submission
            $('#bidForm').on('submit', function(e) {
                e.preventDefault();
                const bidAmount = parseFloat(document.querySelector('input[name="bid_amount"]').value);
                let submitBtn = $(this).find('button[type="submit"]');

                submitBtn.prop('disabled', true).text('Placing Bid...');
                $.post('<?php echo $main_url; ?>/api/create/create_bid.php', {
                    auction_id: auctionId,
                    bid_amount: bidAmount
                }, function(data) {
                    if (data.status === 'success') {
                        $('input[name="bid_amount"]').val('');
                        fetchHighestBid();
                        fetchBidRecords();
                        $('#bidStatus').html('<p class="text-success fw-bold mb-0">Bid placed successfully!</p>');
                        setTimeout(() => { $('#bidStatus').html(''); }, 3000);
                    } else {
                        $('#bidStatus').html('<p class="text-danger fw-bold mb-0">' + (data.message || 'Bid failed') + '</p>');
                    }
                }, 'json').fail(function() {
                    $('#bidStatus').html();
                }).always(function() {
                    submitBtn.prop('disabled', false).html('Place');
                });
            });

            function checkAuctionStatus() {
                $.get('<?php echo $main_url; ?>/api/get/check_bid_status.php', {
                    auction_id: auctionId
                }, function (data) {
                    if (data.status === 'ended') {
                        location.replace(location.href);
                    }
                }, 'json').fail(function () {
                    console.log('Error checking auction status');
                });
            }

            // Auto-refresh data
            setInterval(() => {
                checkAuctionStatus();
                fetchHighestBid();
                fetchBidRecords();
            }, refreshInterval);

            // Initial fetch
            $(document).ready(function() {
                checkAuctionStatus();
                fetchHighestBid();
                fetchBidRecords();
            });





            $(document).ready(function(){
                // Initialize carousel
                $('.owl-carousel').owlCarousel({
                    loop:false,
                    margin:10,
                    nav:true,
                    dots:false,
                    responsive:{
                        0:{ items:3 },
                        600:{ items:4 },
                        1000:{ items:5 }
                    }
                });

                // Thumbnail click
                $('.thumb-img').on('click', function(){
                    let src = $(this).attr('src');

                    // Fade out main image, then change src, then fade in
                    $('#mainImage').css('opacity', '0');
                    setTimeout(function(){
                        $('#mainImage').attr('src', src).css('opacity', '1');
                    }, 300);

                    // Remove active from all, add to clicked
                    $('.thumb-img').removeClass('active');
                    $(this).addClass('active');
                });
            });






        </script>
    </div>
    <?php include ROOT_PATH . "comp/nav/MainFooter.php"; ?>
</body>
</html>