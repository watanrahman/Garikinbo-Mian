<section class="auction-result-section">
    <div class="container py-5" style="margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Main Result Card -->
                <button class="btn btn-app btn-primary btn-dark mb-3" onclick="history.go(-1);"><i class="bi bi-arrow-left"></i> Back</button>

                <div class="card shadow-lg border-0 overflow-hidden">
                    <!-- Header -->

                    <div class="card-body p-0">
                        <!-- Result Banner -->
                        <div class="result-banner p-4 text-center">
                            <?php if ($auction['buyer_id'] === $_SESSION['user_id']): ?>
                                <div class="winner-banner bg-success text-white py-4">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="bi bi-trophy-fill me-3 fs-1"></i>
                                        <h2 class="mb-0 fw-bold">Congratulations! You Won!</h2>
                                    </div>
                                    <p class="mb-0 fs-5">You are the highest bidder for this auction</p>
                                </div>
                            <?php elseif ($auction['user_id'] === $_SESSION['user_id']): ?>
                                <div class="seller-banner bg-primary text-white py-4">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="bi bi-currency-dollar me-3 fs-1"></i>
                                        <h2 class="mb-0 fw-bold">Auction Successfully Sold!</h2>
                                    </div>
                                    <p class="mb-0 fs-5">Your car has been sold to the highest bidder</p>
                                </div>
                            <?php elseif ($auction['buyer_id'] !== null): ?>
                                <div class="loser-banner bg-info text-white py-4">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="bi bi-info-circle me-3 fs-1"></i>
                                        <h2 class="mb-0 fw-bold">Auction Has Winner</h2>
                                    </div>
                                    <p class="mb-0 fs-5">This auction has been won by another bidder</p>
                                </div>
                            <?php else: ?>
                                <div class="no-winner-banner bg-warning text-dark py-4">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <i class="bi bi-exclamation-triangle me-3 fs-1"></i>
                                        <h2 class="mb-0 fw-bold">No Winner</h2>
                                    </div>
                                    <p class="mb-0 fs-5">This auction ended without any bids</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Auction Details -->
                        <div class="p-4">
                            <div class="row">
                                <!-- Car Information -->
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 fw-bold">
                                                <i class="bi bi-car-front me-2"></i>Vehicle Details
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <img src="<?php echo $main_url?>/uploads/thumbnails/<?php echo htmlspecialchars($auction['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($auction['title']); ?>" 
                                                     class="img-fluid rounded" style="max-height: 200px;">
                                            </div>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="text-muted">Auction Id:</td>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($auction['id']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Title:</td>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($auction['title']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Make/Model:</td>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($auction['make'] ?? 'N/A'); ?> <?php echo htmlspecialchars($auction['model'] ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Year:</td>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($auction['year'] ?? 'N/A'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Body Type:</td>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($auction['body_type'] ?? 'N/A'); ?></td>
                                                </tr>
                                            </table>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">View Full Information</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cars Full Information -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Car Information</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
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
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-app btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                                                
                                <!-- Auction Results -->
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 fw-bold">
                                                <i class="bi bi-graph-up me-2"></i>Auction Results
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                                                <span class="text-muted">Start Price:</span>
                                                <span class="fw-bold text-primary fs-5">
                                                    <?php echo number_format($auction['start_price']); ?> BDT
                                                </span>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-success bg-opacity-10 rounded">
                                                <span class="text-muted">Final Price:</span>
                                                <span class="fw-bold text-success fs-5">
                                                    <?php echo number_format($auction['current_highest_bid'] ?? $auction['start_price']); ?> BDT
                                                </span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-info bg-opacity-10 rounded">
                                                <span class="text-muted">Total Bids:</span>
                                                <span class="fw-bold text-info">
                                                    <?php 
                                                        // You'll need to fetch bid count - add this to your query
                                                        $bidStmt = $pdo->prepare("SELECT COUNT(*) as bid_count FROM bids WHERE auction_id = ?");
                                                        $bidStmt->execute([$auction_id]);
                                                        $bidCount = $bidStmt->fetch(PDO::FETCH_ASSOC)['bid_count'];
                                                        echo $bidCount . ' bids';
                                                    ?>
                                                </span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center p-3 bg-warning bg-opacity-10 rounded">
                                                <span class="text-muted">Auction Ended:</span>
                                                <span class="fw-bold text-warning">
                                                    <?php echo date("M d, Y h:i A", strtotime($auction['end_time'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transaction Details Information -->
                            <?php if ($auction['buyer_id'] !== null || $auction['user_id'] === $_SESSION['user_id']): ?>
                                <?php 
                                    if (!empty($auction['buyer_id'])) {
                                        $buyerStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                                        $buyerStmt->execute([$auction['buyer_id']]);
                                        $buyer = $buyerStmt->fetch(PDO::FETCH_ASSOC);
                                    } else {
                                        $buyer = null;
                                    }
                                ?>

                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0 fw-bold">
                                            <i class="bi bi-person-check me-2"></i>
                                            Transaction Details
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php if ($auction['buyer_id'] === $_SESSION['user_id']): ?>
                                                    <h6 class="text-muted mb-3">Seller Information</h6>
                                                    
                                                    <p class="mb-2">
                                                        <i class="bi bi-person me-2"></i>
                                                        <strong>
                                                            <a href="<?php echo $main_url?>/user/index.php?u=<?php echo $auction['username']; ?>">
                                                                <?php echo htmlspecialchars($auction['username']); ?>
                                                            </a>
                                                        </strong>
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="bi bi-telephone me-2"></i>
                                                        <strong><?php echo htmlspecialchars($auction['phone_number']); ?></strong>
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="bi bi-envelope me-2"></i>
                                                        <strong><?php echo htmlspecialchars($auction['email']); ?></strong>
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="bi bi-geo-alt me-2"></i>
                                                        <?php echo htmlspecialchars($auction['auction_location']); ?>
                                                    </p>                                                    
                                                <?php elseif ($auction['user_id'] === $_SESSION['user_id']): ?>
                                                    <h6 class="text-muted mb-3">Buyer Information</h6>

                                                    <p class="mb-2">
                                                        <i class="bi bi-person me-2"></i>
                                                        <strong>
                                                            <a href="<?php echo $main_url?>/user/index.php?u=<?php echo $buyer['user_name']; ?>">
                                                                <?php echo htmlspecialchars($buyer['user_name']); ?> 
                                                            </a>
                                                        </strong>
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="bi bi-telephone me-2"></i>
                                                        <strong><?php echo htmlspecialchars($buyer['phone_number']); ?></strong>
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="bi bi-envelope me-2"></i>
                                                        <strong><?php echo htmlspecialchars($buyer['email']); ?></strong>
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="bi bi-geo-alt me-2"></i>
                                                        <?php echo htmlspecialchars($buyer['location']); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-muted mb-3">Next Steps</h6>
                                                <ul class="list-unstyled">
                                                    <?php if ($auction['buyer_id'] === $_SESSION['user_id']): ?>
                                                        <li class="mb-2">✅ Contact the seller within 24 hours. If He or She not respond <a href="<?php echo $main_url?>/user/report.php?to=<?php echo $auction['buyer_id']; ?>">Report Us</a></li>
                                                        <li class="mb-2">📞 Arrange payment and pickup</li>
                                                        <li class="mb-2">📝 Complete ownership transfer</li>
                                                    <?php elseif ($auction['user_id'] === $_SESSION['user_id']): ?>
                                                        <li class="mb-2">✅ Wait for buyer to contact you. If He or She not respond <a href="<?php echo $main_url?>/user/report.php?to=<?php echo $auction['buyer_id']; ?>">Report Us</a></li>
                                                        <li class="mb-2">💰 Arrange payment method</li>
                                                        <li class="mb-2">🚗 Prepare vehicle for handover</li>
                                                    <?php else: ?>
                                                        <li class="mb-2">🔍 Explore other active auctions</li>
                                                        <li class="mb-2">💡 Improve your bidding strategy</li>
                                                        <li class="mb-2">⏰ Set reminders for future auctions</li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Action Buttons -->
                            <div class="text-center mt-4">
                                <div class="btn-group" role="group">
                                    <a href="auctions.php" class="btn btn-primary btn-lg px-4">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Auctions
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
    body{
        background: linear-gradient(179deg, #7552ff94, #212529 80%);
    }
</style>