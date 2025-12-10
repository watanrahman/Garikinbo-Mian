<?php
    session_start();
    include '../config.php';
    auth_check();

    $auction_id = $_GET['id'] ?? 0;

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

    if (!$auction) {
        die("<div class='alert alert-danger text-center mt-5'>Auction not found.</div>");
    }

    // Fetch bid count
    $bidStmt = $pdo->prepare("SELECT COUNT(*) as bid_count FROM bids WHERE auction_id = ?");
    $bidStmt->execute([$auction_id]);
    $bidCount = $bidStmt->fetch(PDO::FETCH_ASSOC)['bid_count'] ?? 0;

    // Fetch buyer (if any)
    $buyer = null;
    if (!empty($auction['buyer_id'])) {
        $buyerStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $buyerStmt->execute([$auction['buyer_id']]);
        $buyer = $buyerStmt->fetch(PDO::FETCH_ASSOC);
    }

    // Determine report target
    $report_to = ($auction['buyer_id'] === $_SESSION['user_id']) ? $auction['user_id'] : $auction['buyer_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Result - <?php echo htmlspecialchars($auction['title']); ?></title>
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
    <style>
        body {
            background: linear-gradient(180deg, #6f42c1 0%, #212529 80%);
            min-height: 100vh;
        }
        .result-banner {
            color: #fff;
            text-align: center;
            padding: 40px 20px;
        }
        .card {
            border-radius: 15px;
        }
        .info-table td {
            padding: 6px 10px;
        }
    </style>
</head>
<body>
<section class="auction-result-section">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">

                <button class="btn btn-secondary mb-4" onclick="history.back();">
                    <i class="bi bi-arrow-left"></i> Back
                </button>

                <!-- Banner -->
                <div class="card border-0 shadow-lg mb-4">
                    <div class="card-body p-0">
                        <?php if ($auction['buyer_id'] === $_SESSION['user_id']): ?>
                            <div class="result-banner bg-success">
                                <i class="bi bi-trophy-fill fs-1 mb-2"></i>
                                <h2 class="fw-bold">Congratulations! You Won!</h2>
                                <p>You are the highest bidder for this auction.</p>
                            </div>
                        <?php elseif ($auction['user_id'] === $_SESSION['user_id']): ?>
                            <div class="result-banner bg-primary">
                                <i class="bi bi-currency-dollar fs-1 mb-2"></i>
                                <h2 class="fw-bold">Auction Sold Successfully!</h2>
                                <p>Your vehicle has been sold to the highest bidder.</p>
                            </div>
                        <?php elseif ($auction['buyer_id']): ?>
                            <div class="result-banner bg-info">
                                <i class="bi bi-info-circle fs-1 mb-2"></i>
                                <h2 class="fw-bold">This Auction Has a Winner</h2>
                                <p>Better luck next time!</p>
                            </div>
                        <?php else: ?>
                            <div class="result-banner bg-warning text-dark">
                                <i class="bi bi-exclamation-triangle fs-1 mb-2"></i>
                                <h2 class="fw-bold">No Winner</h2>
                                <p>This auction ended without any bids.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Vehicle Info & Result -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light fw-bold">
                                <i class="bi bi-car-front me-2"></i> Vehicle Details
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img src="<?php echo $main_url ?>/uploads/thumbnails/<?php echo htmlspecialchars($auction['image_url']); ?>"
                                         alt="Vehicle" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                                <table class="table table-borderless info-table">
                                    <tr><td>Auction ID:</td><td><strong><?php echo $auction['id']; ?></strong></td></tr>
                                    <tr><td>Title:</td><td><strong><?php echo htmlspecialchars($auction['title']); ?></strong></td></tr>
                                    <tr><td>Make/Model:</td><td><?php echo htmlspecialchars($auction['make'] . ' ' . $auction['model']); ?></td></tr>
                                    <tr><td>Year:</td><td><?php echo htmlspecialchars($auction['year']); ?></td></tr>
                                    <tr><td>Body Type:</td><td><?php echo htmlspecialchars($auction['body_type']); ?></td></tr>
                                </table>
                                <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#carInfoModal">
                                    View Full Information
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light fw-bold">
                                <i class="bi bi-graph-up me-2"></i> Auction Results
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <span class="text-muted">Start Price:</span>
                                    <span class="float-end fw-bold text-primary"><?php echo number_format($auction['start_price']); ?> BDT</span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted">Final Price:</span>
                                    <span class="float-end fw-bold text-success"><?php echo number_format($auction['current_highest_bid'] ?? $auction['start_price']); ?> BDT</span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted">Total Bids:</span>
                                    <span class="float-end fw-bold text-info"><?php echo $bidCount; ?> bids</span>
                                </div>
                                <div>
                                    <span class="text-muted">Auction Ended:</span>
                                    <span class="float-end fw-bold text-warning"><?php echo date("M d, Y h:i A", strtotime($auction['end_time'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Section -->
                <?php if ($auction['buyer_id'] || $auction['user_id'] === $_SESSION['user_id']): ?>
                    <div class="card shadow-sm border-0 mt-4">
                        <div class="card-header bg-light fw-bold">
                            <i class="bi bi-person-check me-2"></i> Transaction Details
                        </div>
                        <div class="card-body row">
                            <div class="col-md-6">
                                <?php if ($auction['buyer_id'] === $_SESSION['user_id']): ?>
                                    <h6 class="text-muted">Seller Information</h6>
                                    <p><strong><a href="<?php echo $main_url ?>/user/index.php?u=<?php echo $auction['username']; ?>"><?php echo htmlspecialchars($auction['username']); ?></a></strong></p>
                                    <p>📞 <?php echo htmlspecialchars($auction['phone_number']); ?></p>
                                    <p>✉️ <?php echo htmlspecialchars($auction['email']); ?></p>
                                    <p>📍 <?php echo htmlspecialchars($auction['auction_location']); ?></p>
                                <?php elseif ($auction['user_id'] === $_SESSION['user_id'] && $buyer): ?>
                                    <h6 class="text-muted">Buyer Information</h6>
                                    <p><strong><a href="<?php echo $main_url ?>/user/index.php?u=<?php echo $buyer['user_name']; ?>"><?php echo htmlspecialchars($buyer['user_name']); ?></a></strong></p>
                                    <p>📞 <?php echo htmlspecialchars($buyer['phone_number']); ?></p>
                                    <p>✉️ <?php echo htmlspecialchars($buyer['email']); ?></p>
                                    <p>📍 <?php echo htmlspecialchars($buyer['location']); ?></p>
                                <?php else: ?>
                                    <p class="text-danger">Buyer information not found.</p>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted">Next Steps</h6>
                                <ul class="list-unstyled">
                                    <?php if ($auction['buyer_id'] === $_SESSION['user_id']): ?>
                                        <li>✅ Contact the seller within 24 hours.</li>
                                        <li>🚩 If no response, <a href="<?php echo $main_url ?>/user/report.php?to=<?php echo $report_to; ?>">Report Us</a></li>
                                        <li>💰 Arrange payment and pickup.</li>
                                    <?php elseif ($auction['user_id'] === $_SESSION['user_id']): ?>
                                        <li>✅ Wait for buyer contact.</li>
                                        <li>🚩 If no response, <a href="<?php echo $main_url ?>/user/report.php?to=<?php echo $report_to; ?>">Report Us</a></li>
                                        <li>🚗 Prepare vehicle for handover.</li>
                                    <?php else: ?>
                                        <li>🔍 Explore active auctions.</li>
                                        <li>💡 Improve bidding strategy.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Back Button -->
                <div class="text-center mt-4">
                    <a href="auctions.php" class="btn btn-lg btn-primary">
                        <i class="bi bi-arrow-left me-2"></i> Back to Auctions
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="carInfoModal" tabindex="-1" aria-labelledby="carInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carInfoModalLabel">Full Car Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-center text-muted">Display your extended car details here (same fields as before).</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
