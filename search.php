<?php
    session_start();
    include 'config.php';
    include __DIR__ . '/comp/card/AuctionPostCard.php';

    $now = date('Y-m-d H:i:s');

    // Get search & filter inputs
    $q = isset($_GET['q']) ? trim($_GET['q']) : "";
    $make = isset($_GET['make']) ? trim($_GET['make']) : "";
    $t_type = isset($_GET['t_type']) ? trim($_GET['t_type']) : "";
    $fuel_type = isset($_GET['fuel_type']) ? trim($_GET['fuel_type']) : "";
    $min_price = isset($_GET['min_price']) ? trim($_GET['min_price']) : "";
    $max_price = isset($_GET['max_price']) ? trim($_GET['max_price']) : "";
    $status = isset($_GET['status']) ? trim($_GET['status']) : "";
    $u_status = isset($_GET['u_status']) ? trim($_GET['u_status']) : "";


    // Base SQL query
    $sql = "
        SELECT a.*, u.profile_status, u.user_name, u.profile_pic_url, 
            c.title, c.mileage, c.t_type, c.image_url, c.make, c.model, c.des, c.fuel_type, a.start_price
        FROM auctions a
        JOIN cars c ON a.car_id = c.id
        JOIN users u ON c.user_id = u.id
        WHERE 1=1
    ";

    // Dynamic conditions
    $params = [];

    if ($q !== "") {
        $sql .= " AND (c.title LIKE :q OR c.des LIKE :q OR c.make LIKE :q OR c.model LIKE :q)";
        $params['q'] = "%$q%";
    }

    if ($make !== "") {
        $sql .= " AND c.make = :make";
        $params['make'] = $make;
    }

    if ($t_type !== "") {
        $sql .= " AND c.t_type = :t_type";
        $params['t_type'] = $t_type;
    }

    if ($fuel_type !== "") {
        $sql .= " AND c.fuel_type = :fuel_type";
        $params['fuel_type'] = $fuel_type;
    }

    if ($min_price !== "" && is_numeric($min_price)) {
        $sql .= " AND a.starting_price >= :min_price";
        $params['min_price'] = $min_price;
    }

    if ($max_price !== "" && is_numeric($max_price)) {
        $sql .= " AND a.starting_price <= :max_price";
        $params['max_price'] = $max_price;
    }

    if ($status === "live") {
        $sql .= " AND a.start_time <= NOW() AND a.end_time >= NOW()";
    }

    if ($status === "upcoming") {
        $sql .= " AND a.start_time > NOW()";
    }

    if ($u_status === "verified") {
        $sql .= " AND u.profile_status = 'verified'";
    }

    $sql .= " ORDER BY a.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?php echo htmlspecialchars($q); ?></title>
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
</head>
<body>
    <?php include __DIR__ . '/comp/nav/MainNavbar.php'; ?>
    
    <div class="container page-content">
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <p class="mb-0 fs-5">Search results for: <?php echo htmlspecialchars($q); ?></p>
        </div>
        
        <div class="row">
            <!-- FILTER SIDEBAR -->
            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
                <div class="card mb-3 p-2">
                    <form method="get" action="search.php" class="p-3">
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
                            <input type="hidden" name="u_status" value="<?php echo htmlspecialchars($u_status); ?>">                    
                        <!-- Auction Status -->
                        <div class="mb-3">
                            <label class="form-label">Auction Status</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php
                                    $statuses = ['all' => 'All', 'live' => 'Live', 'upcoming' => 'Upcoming'];
                                    foreach ($statuses as $key => $label):
                                ?>
                                    <a 
                                        class="border btn btn-app <?php echo ($status == $key) ? 'btn-dark' : ''; ?>"
                                        href="search.php?status=<?php echo $key; ?>&u_status=<?php echo urlencode($u_status); ?>&q=<?php echo urlencode($q); ?>"
                                    ><?php echo $label; ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>


                        <!-- User Type -->
                        <div class="mb-3">
                            <label class="form-label">User Type</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php
                                    $u_statuses = ['all' => 'All', 'verified' => 'Verified'];
                                    foreach ($u_statuses as $key => $label):
                                ?>
                                    <a 
                                        class="border btn btn-app <?php echo ($u_status == $key) ? 'btn-dark' : ''; ?>"
                                        href="search.php?u_status=<?php echo $key; ?>&status=<?php echo urlencode($status); ?>&q=<?php echo urlencode($q); ?>"
                                    ><?php echo $label; ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>



                        <!-- Make -->
                        <div class="mb-3">
                            <label for="make" class="form-label">Make</label>
                            <select name="make" id="make" class="form-select">
                                <option value="">Any</option>
                                <option value="Toyota" <?php if($make=="Toyota") echo "selected"; ?>>Toyota</option>
                                <option value="Honda" <?php if($make=="Honda") echo "selected"; ?>>Honda</option>
                                <option value="Nissan" <?php if($make=="Nissan") echo "selected"; ?>>Nissan</option>
                            </select>
                        </div>

                        <!-- Transmission -->
                        <div class="mb-3">
                            <label for="t_type" class="form-label">Transmission</label>
                            <select name="t_type" id="t_type" class="form-select">
                                <option value="">Any</option>
                                <option value="Automatic" <?php if($t_type=="Automatic") echo "selected"; ?>>Automatic</option>
                                <option value="Manual" <?php if($t_type=="Manual") echo "selected"; ?>>Manual</option>
                            </select>
                        </div>

                        <!-- Fuel Type -->
                        <div class="mb-3">
                            <label for="fuel_type" class="form-label">Fuel Type</label>
                            <select name="fuel_type" id="fuel_type" class="form-select">
                                <option value="">Any</option>
                                <option value="Petrol" <?php if($fuel_type=="Petrol") echo "selected"; ?>>Petrol</option>
                                <option value="Diesel" <?php if($fuel_type=="Diesel") echo "selected"; ?>>Diesel</option>
                                <option value="CNG" <?php if($fuel_type=="CNG") echo "selected"; ?>>CNG</option>
                                <option value="Hybrid" <?php if($fuel_type=="Hybrid") echo "selected"; ?>>Hybrid</option>
                            </select>
                        </div>

                        <!-- Min/Max Price -->
                        <div class="mb-3">
                            <label for="min_price" class="form-label">Min Price</label>
                            <input
                                type="number" 
                                class="form-control" 
                                name="min_price" 
                                id="min_price" 
                                placeholder="e.g 100000" 
                                value="<?php echo htmlspecialchars($min_price); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="max_price" class="form-label">Max Price</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                name="max_price" 
                                id="max_price" 
                                placeholder="e.g 500000" 
                                value="<?php echo htmlspecialchars($max_price); ?>">
                        </div>

                        <!-- Submit -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-app btn-primary btn-app-primary">
                                <i class="bi bi-search"></i> Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RESULTS -->
            <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
                <div class="row">
                    <?php if (!empty($auctions)): ?>
                        <?php foreach ($auctions as $auction): ?>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <?php 
                                    if ($auction['start_time'] <= $now && $auction['end_time'] >= $now) {
                                        defaultPostCard($auction, $main_url, "live");
                                    } elseif ($auction['start_time'] > $now) {
                                        defaultPostCard($auction, $main_url, "upcoming");
                                    } elseif ($auction['end_time'] < $now && $auction['buyer_id'] != null) {
                                        defaultPostCard($auction, $main_url, "ended");
                                    }
                                ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No auctions found for your search.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/comp/nav/MainFooter.php'; ?>
</body>
<script src="<?php echo $main_url;?>/lib/js/bootstrap.js"></script>
</html>
