<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please log in to create a bid post.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all form data
$title              = $_POST['title'] ?? '';
$des                = $_POST['des'] ?? '';
$make               = $_POST['make'] ?? '';
$model              = $_POST['model'] ?? '';
$year               = $_POST['year'] ?? null;
$body_type          = $_POST['body_type'] ?? '';
$t_type             = $_POST['t_type'] ?? '';
$fuel_type          = $_POST['fuel_type'] ?? '';
$engine_capacity    = $_POST['engine_capacity'] ?? null;
$mileage            = $_POST['mileage'] ?? null;
$registration_number= $_POST['registration_number'] ?? '';
$registration_year  = $_POST['registration_year'] ?? null;
$ownership_status   = $_POST['ownership_status'] ?? '';
$tax_token_validity = $_POST['tax_token_validity'] ?? null;
$fitness_validity   = $_POST['fitness_validity'] ?? null;
$insurance_validity = $_POST['insurance_validity'] ?? null;
$accident_history   = $_POST['accident_history'] ?? '';
$service_history    = $_POST['service_history'] ?? '';
$car_condition      = $_POST['condition'] ?? '';

// Auction info
$start_price        = $_POST['start_price'] ?? 0;
$auction_location   = $_POST['auction_location'] ?? '';
$start_time         = $_POST['start_time'] ?? date("Y-m-d H:i:s");
$end_time           = $_POST['end_time'] ?? null;

// Debug: Log $_FILES
error_log(print_r($_FILES, true));

// Handle Thumbnail upload
if (!isset($_FILES['thumbnail']) || $_FILES['thumbnail']['error'] != 0) {
    echo "Thumbnail is required!";
    exit;
}

$thumb_name = time() . '_' . basename($_FILES['thumbnail']['name']);
$thumb_tmp  = $_FILES['thumbnail']['tmp_name'];
$thumb_dir = ROOT_PATH . "uploads/thumbnails/";
if (!is_dir($thumb_dir)) {
    if (!mkdir($thumb_dir, 0777, true)) {
        echo "Failed to create thumbnail directory!";
        exit;
    }
}
$thumb_path = $thumb_dir . $thumb_name;

if (move_uploaded_file($thumb_tmp, $thumb_path)) {
    // Begin transaction
    $pdo->beginTransaction();
    try {
        // Insert car
        $car_stmt = $pdo->prepare("
            INSERT INTO cars (
                user_id, title, des, image_url,
                make, model, year, body_type, t_type, fuel_type,
                engine_capacity, mileage,
                registration_number, registration_year, ownership_status,
                tax_token_validity, fitness_validity, insurance_validity,
                accident_history, service_history, car_condition, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, NOW()
            )
        ");

        $car_stmt->execute([
            $user_id, $title, $des, $thumb_name,
            $make, $model, $year, $body_type, $t_type, $fuel_type,
            $engine_capacity, $mileage,
            $registration_number, $registration_year, $ownership_status,
            $tax_token_validity, $fitness_validity, $insurance_validity,
            $accident_history, $service_history, $car_condition
        ]);

        $car_id = $pdo->lastInsertId();

        // Insert auction
        $auction_stmt = $pdo->prepare("
            INSERT INTO auctions (
                car_id, start_price, auction_location, start_time, end_time, user_id, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, NOW()
            )
        ");
        $auction_stmt->execute([
            $car_id, $start_price, $auction_location, $start_time, $end_time, $user_id
        ]);

        // Handle Multiple Car Images
        if (!empty($_FILES['car_images']['name'][0])) {
            $car_images_dir = ROOT_PATH . "uploads/car_images/";
            if (!is_dir($car_images_dir)) {
                if (!mkdir($car_images_dir, 0777, true)) {
                    throw new Exception("Failed to create directory: $car_images_dir");
                }
            }

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_file_size = 5 * 1024 * 1024; // 5MB

            foreach ($_FILES['car_images']['name'] as $index => $img_name) {
                if ($_FILES['car_images']['error'][$index] !== UPLOAD_ERR_OK) {
                    error_log("Upload error for file $img_name: " . $_FILES['car_images']['error'][$index]);
                    continue;
                }

                $file_type = $_FILES['car_images']['type'][$index];
                $file_size = $_FILES['car_images']['size'][$index];
                if (!in_array($file_type, $allowed_types)) {
                    error_log("Invalid file type for $img_name: $file_type");
                    continue;
                }
                if ($file_size > $max_file_size) {
                    error_log("File $img_name exceeds size limit: $file_size bytes");
                    continue;
                }

                $new_name = time() . '_' . basename($img_name);
                $tmp_path = $_FILES['car_images']['tmp_name'][$index];
                $dest_path = $car_images_dir . $new_name;

                if (move_uploaded_file($tmp_path, $dest_path)) {
                    $img_stmt = $pdo->prepare("INSERT INTO car_images (car_id, file_name) VALUES (?, ?)");
                    $img_stmt->execute([$car_id, $new_name]);
                } else {
                    throw new Exception("Failed to move file $img_name to $dest_path");
                }
            }
        }


        $pdo->commit();
        echo "Auction created zz successfully!";
        echo "<pre>";
            print_r($_FILES['car_images']);
        echo "</pre>";
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error: " . $e->getMessage());
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Thumbnail upload failed!";
}
?>