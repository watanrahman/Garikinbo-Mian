<?php
    function defaultPostCard($auction, $main_url, $status) {
        ?>
        <div class="item position-relative">
            <div class="card shadow-sm border-0 app-card mb-3">
                <?php 
                    if ($status === "live") {
                        ?>
                           <span class="status-tag status-live">
                                <i class="bi bi-broadcast"></i> Live
                            </span>
                        <?php
                    }
                    if ($status === "upcoming") {
                        ?>
                           <span class="status-tag status-up">Upcoming</span>
                        <?php
                    }
                    if ($status === "ended") {
                        ?>
                           <span class="status-tag status-ended">Ended</span>
                        <?php
                    }
                ?>
                <!-- <p class="mb-0 fw-bold text-center"><?php // echo $status; ?></p> -->
                <!-- Car Image -->
                <img src="<?php echo $main_url; ?>/uploads/thumbnails/<?php echo $auction['image_url']; ?>" 
                    class="card-img-top" 
                    alt="<?= htmlspecialchars($auction['title']) ?>">

                <div class="card-body fs-5">
                    <!-- Hosted By -->
                    <div>
                        <a class="text-dark d-flex mb-2 align-items-center" 
                        href="<?php echo $main_url; ?>/user/index.php?u=<?php echo $auction['user_name']; ?>">
                            <div class="me-2">
                                <div class="profile-pic-container profile-pic-sm">
                                    <img 
                                        class="img-fluid"
                                        src="<?php echo $main_url?>/uploads/users/<?php echo $auction['profile_pic_url']; ?>" 
                                        alt="">
                                    
                                    <?php 
                                        if ($auction['profile_status'] === 'verified') {
                                            ?>
                                                <div class="profile-badge" style="border: 6px solid #fff;">
                                                    <i class="bi bi-check-lg"></i>
                                                </div>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                            <small><?php echo $auction['user_name']?></small>                                                        
                        </a>
                    </div>
                
                    <!-- Title -->
                    <h5 class="card-title fw-bold mb-2">
                        <?= htmlspecialchars($auction['title']) ?>
                    </h5>

                    <!-- Auction Details with Icons -->
                    <ul class="list-unstyled small text-muted mb-3">
                        <li class="mb-1"><i class="bi bi-clock me-1 text-warning"></i> Ends: <?= $auction['end_time'] ?></li>
                        <li class="mb-1"><i class="bi bi-cash-coin me-1 text-success"></i> 
                            Current Bid: <?= $auction['current_highest_bid'] ?? $auction['start_price'] ?> BDT
                        </li>
                        <li class="mb-1"><i class="bi bi-speedometer2 me-1 text-info"></i> Mileage: <?= $auction['mileage'] ?> KM</li>
                        <li class="mb-1"><i class="bi bi-gear-wide-connected me-1 text-primary"></i> Transmission: <?= $auction['t_type'] ?></li>
                    </ul>

                    <!-- Button -->
                    <div class="text-end">
                        <?php 
                            if (isset($_SESSION['user_id']) && $auction['user_id'] === $_SESSION['user_id']) {
                                ?>
                                    <div class="d-flex">
                                        <a href="<?php echo $main_url; ?>/user/auction/manage.php?id=<?php echo $auction['id']; ?>" class="btn btn-primary btn-app w-50 me-1" >
                                            <i class="bi <?php echo $buttonIcon; ?> me-1"></i> Manage
                                        </a>
                                        <a href="<?php echo $main_url; ?>/auction/index.php?id=<?php echo $auction['id']; ?>" class="ms-1 btn btn-app w-50 border" >
                                            <i class="bi <?php echo $buttonIcon; ?> me-1"></i> View <i class="bi bi-arrow-return-right"></i>
                                        </a>
                                    </div>
                                <?php
                            }else{
                                if ($status === "live") {
                                    ?>
                                        <a href="<?php echo $main_url; ?>/auction/index.php?id=<?php echo $auction['id']; ?>" class="w-100 btn btn-dark btn-app" >
                                            <i class="bi <?php echo $buttonIcon; ?> me-1"></i> Join Auction
                                        </a>
                                    <?php
                                }
                                if ($status === "upcoming") {
                                    ?>
                                        <a href="<?php echo $main_url; ?>/auction/index.php?id=<?php echo $auction['id']; ?>" class="w-100 border btn btn-app" >
                                            <i class="bi <?php echo $buttonIcon; ?> me-1"></i> View
                                        </a>
                                    <?php
                                }
                                if ($status === "ended") {
                                    ?>
                                        <a href="<?php echo $main_url; ?>/auction/index.php?id=<?php echo $auction['id']; ?>" class="w-100 btn border btn-app" >
                                            <i class="bi <?php echo $buttonIcon; ?> me-1"></i> View
                                        </a>
                                    <?php
                                }
                            }
                        ?>

                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    function searchPostCard ($auction, $main_url, $status) {
        ?>
        <div class="item position-relative">
            <div class="card mb-3">
                <!-- <p class="mb-0 fw-bold text-center"><?php // echo $status; ?></p> -->
                <!-- Car Image -->
                <div class="row">
                    <div class="col-4">
                        <img src="<?php echo $main_url; ?>/uploads/thumbnails/<?php echo $auction['image_url']; ?>" 
                        class="card-img-top" 
                        alt="<?= htmlspecialchars($auction['title']) ?>">
                    </div>

                    <div class="col-8">
                        <div class="card-body fs-5">
                            <!-- Hosted By -->
                            <div>
                                <a class="text-dark d-flex mb-2 align-items-center" 
                                href="<?php echo $main_url; ?>/user/index.php?u=<?php echo $auction['user_name']; ?>">
                                    <div class="me-2">
                                        <img style="width: 35px; height: 35px" 
                                            src="<?php echo $main_url?>/uploads/users/<?php echo $auction['profile_pic_url']?>" 
                                            alt="" 
                                            class="border profile-pic profile-pic-sm">
                                    </div>
                                    <small><?php echo $auction['user_name']?></small>                                                        
                                </a>
                            </div>
                        
                            <!-- Title -->
                            <h5 class="card-title fw-bold mb-3">
                                <?= htmlspecialchars($auction['title']) ?>
                            </h5>

                            <!-- Auction Details with Icons -->
                            <ul class="list-unstyled small text-muted mb-3">
                                <li class="mb-1"><i class="bi bi-clock me-1 text-warning"></i> Ends: <?= $auction['end_time'] ?></li>
                                <li class="mb-1"><i class="bi bi-cash-coin me-1 text-success"></i> 
                                    Current Bid: <?= $auction['current_highest_bid'] ?? $auction['start_price'] ?> BDT
                                </li>
                                <li class="mb-1"><i class="bi bi-speedometer2 me-1 text-info"></i> Mileage: <?= $auction['mileage'] ?> KM</li>
                                <li class="mb-1"><i class="bi bi-gear-wide-connected me-1 text-primary"></i> Transmission: <?= $auction['t_type'] ?></li>
                            </ul>

                            <!-- Button -->
                            <div class="text-end">
                                <?php 
                                    if (isset($_SESSION['user_id']) && $auction['user_id'] === $_SESSION['user_id']) {
                                        ?>
                                            <a href="<?php echo $main_url; ?>/user/auction/manage.php?id=<?php echo $auction['id']; ?>" class="btn btn-primary btn-app" >
                                                <i class="bi <?php echo $buttonIcon; ?> me-1"></i> Manage
                                            </a>
                                            <a href="<?php echo $main_url; ?>/auction/index.php?id=<?php echo $auction['id']; ?>" class="btn" >
                                                <i class="bi <?php echo $buttonIcon; ?> me-1"></i> View
                                            </a>
                                        <?php
                                    }else{
                                        if ($status === "live") {
                                            ?>
                                                <a href="<?php echo $main_url; ?>/auction/index.php?id=<?php echo $auction['id']; ?>" class="btn btn-dark btn-app" >
                                                    <i class="bi <?php echo $buttonIcon; ?> me-1"></i> Join
                                                </a>
                                            <?php
                                        }
                                        if ($status === "upcoming") {
                                            ?>
                                                <a href="<?php echo $main_url; ?>/auction/index.php?id=<?php echo $auction['id']; ?>" class="btn btn-app" >
                                                    <i class="bi <?php echo $buttonIcon; ?> me-1"></i> View
                                                </a>
                                            <?php
                                        }
                                        if ($status === "ended") {
                                            ?>
                                                <a href="<?php echo $main_url; ?>/auction/index.php?id=<?php echo $auction['id']; ?>" class="btn btn-app" >
                                                    <i class="bi <?php echo $buttonIcon; ?> me-1"></i> View
                                                </a>
                                            <?php
                                        }
                                    }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
?>
