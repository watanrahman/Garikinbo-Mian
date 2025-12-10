<?php
    error_reporting(E_ERROR | E_PARSE);
    session_start();
    include '../config.php';
    include ROOT_PATH . 'comp/card/AuctionPostCard.php';
    
    auth_check();

    // Capture IDs
    $to_id = isset($_GET['to']) ? intval($_GET['to']) : 0;
    $from_id = $_SESSION['user_id'] ?? 0;

    // Handle submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $report_type = $_POST['report_type'];
        $message = trim($_POST['message']);

        try {
            // ✅ FIXED: use $pdo instead of $pod
            $stmt = $pdo->prepare("
                INSERT INTO user_reports (to_id, from_id, message, report_type, created_at) 
                VALUES (:to_id, :from_id, :message, :report_type, NOW())
            ");
            
            $success = $stmt->execute([
                'to_id' => $to_id,
                'from_id' => $from_id,
                'message' => $message,
                'report_type' => $report_type
            ]);

            if ($success) {
                echo "<div class='alert alert-success text-center mt-3'>Report submitted successfully.</div>";
            } else {
                echo "<div class='alert alert-danger text-center mt-3'>Failed to submit report.</div>";
            }

        } catch (Throwable $th) {
            echo "<div class='alert alert-danger text-center mt-3'>Error: " . htmlspecialchars($th->getMessage()) . "</div>";
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
    <div class="container mt-5">
    <button class="btn btn-dark btn-app mb-3" onclick="history.go(-1);"><i class="bi bi-arrow-left"></i> Back</button>
    <div class="card shadow p-4">
        <h4 class="mb-3 text-center">Submit a Report</h4>
        <form method="POST">
        <div class="mb-3">
            <label for="report_type" class="form-label">Report Type</label>
            <select class="form-select" name="report_type" required>
            <option value="">Select reason</option>
            <option value="false information">False Information</option>
            <option value="not respond">Not Responding</option>
            <option value="rouge behaviour">Rogue Behaviour</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Describe the issue</label>
            <textarea class="form-control" name="message" rows="4" required placeholder="Write details here..."></textarea>
        </div>

        <input type="hidden" name="to_id" value="<?= htmlspecialchars($to_id) ?>">
        <input type="hidden" name="from_id" value="<?= htmlspecialchars($from_id) ?>">

        <div class="text-center">
            <button type="submit" class="btn btn-danger">Submit Report</button>
        </div>
        </form>
    </div>
    </div>
</body>
    <script src="<?php echo $main_url;?>/lib/js/bootstrap.js"></script>

</html>