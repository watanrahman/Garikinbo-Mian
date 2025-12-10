<?php
    session_start();
    require_once '../../config.php';

    if (!isset($_SESSION['user_id'])) {
        echo "Login required.";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $auction_id = $_GET['id'] ?? 0;

    // Fetch car_id
    $stmt = $pdo->prepare("SELECT car_id FROM auctions WHERE id=?");
    $stmt->execute([$auction_id]);
    $auction = $stmt->fetch();
    if (!$auction) { echo "Auction not found."; exit; }
    $car_id = $auction['car_id'];

    // Collect form data
    $title       = $_POST['title'] ?? '';
    $des         = $_POST['des'] ?? '';
    $make        = $_POST['make'] ?? '';
    $model       = $_POST['model'] ?? '';
    $year        = $_POST['year'] ?? '';
    $body_type   = $_POST['body_type'] ?? '';
    $t_type      = $_POST['t_type'] ?? '';
    $fuel_type   = $_POST['fuel_type'] ?? '';
    $engine_capacity = $_POST['engine_capacity'] ?? 0;
    $mileage     = $_POST['mileage'] ?? 0;

    $registration_number = $_POST['registration_number'] ?? '';
    $registration_year   = $_POST['registration_year'] ?? '';
    $ownership_status    = $_POST['ownership_status'] ?? '';
    $tax_token_validity  = $_POST['tax_token_validity'] ?? null;
    $fitness_validity    = $_POST['fitness_validity'] ?? null;
    $insurance_validity  = $_POST['insurance_validity'] ?? null;

    $accident_history    = $_POST['accident_history'] ?? '';
    $service_history     = $_POST['service_history'] ?? '';
    $condition           = $_POST['condition'] ?? '';

    $start_price     = $_POST['start_price'] ?? 0;
    $auction_location= $_POST['auction_location'] ?? '';
    $start_time      = $_POST['start_time'] ?? '';
    $end_time        = $_POST['end_time'] ?? '';

    try {
        $pdo->beginTransaction();

        // Update cars table
        $updateCar = $pdo->prepare("
            UPDATE cars 
            SET title=?, des=?, make=?, model=?, year=?, body_type=?, t_type=?, fuel_type=?, 
                engine_capacity=?, mileage=?, registration_number=?, registration_year=?, ownership_status=?, 
                tax_token_validity=?, fitness_validity=?, insurance_validity=?, accident_history=?, 
                service_history=?, car_condition=? 
            WHERE id=?
        ");
        $updateCar->execute([
            $title, $des, $make, $model, $year, $body_type, $t_type, $fuel_type,
            $engine_capacity, $mileage, $registration_number, $registration_year, $ownership_status,
            $tax_token_validity, $fitness_validity, $insurance_validity, $accident_history,
            $service_history, $condition, $car_id
        ]);

        // Thumbnail update
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumb_name = time().'_'.basename($_FILES['thumbnail']['name']);
            $thumb_dir = ROOT_PATH."uploads/thumbnails/";
            if (!is_dir($thumb_dir)) mkdir($thumb_dir,0777,true);
            $thumb_path = $thumb_dir.$thumb_name;
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumb_path);

            $pdo->prepare("UPDATE cars SET image_url=? WHERE id=?")->execute([$thumb_name,$car_id]);
        }

        // Gallery images upload
        if (!empty($_FILES['car_images']['name'][0])) {
            $car_images_dir = ROOT_PATH."uploads/car_images/";
            if (!is_dir($car_images_dir)) mkdir($car_images_dir,0777,true);
            foreach($_FILES['car_images']['name'] as $i=>$img_name){
                if ($_FILES['car_images']['error'][$i] === UPLOAD_ERR_OK){
                    $new_name = time().'_'.basename($img_name);
                    $tmp = $_FILES['car_images']['tmp_name'][$i];
                    $dest = $car_images_dir.$new_name;
                    if (move_uploaded_file($tmp,$dest)){
                        $pdo->prepare("INSERT INTO car_images(car_id,file_name) VALUES(?,?)")
                            ->execute([$car_id,$new_name]);
                    }
                }
            }
        }

        // Update auction info
        $pdo->prepare("UPDATE auctions SET start_price=?, auction_location=?, start_time=?, end_time=? WHERE id=?")
            ->execute([$start_price, $auction_location, $start_time, $end_time, $auction_id]);

        $pdo->commit();
        echo "Auction updated successfully!";
    } catch(Exception $e) {
        $pdo->rollBack();
        echo "Error: ".$e->getMessage();
    }
