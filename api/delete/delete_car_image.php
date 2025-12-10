<?php
    session_start();
    require_once '../../config.php';
    auth_check();

    $image_id = $_GET['id'] ?? 0;
    if (!$image_id) { echo "invalid"; exit; }

    // Fetch file name
    $stmt = $pdo->prepare("SELECT file_name, car_id FROM car_images WHERE id=?");
    $stmt->execute([$image_id]);
    $image = $stmt->fetch();

    if (!$image) { echo "not found"; exit; }

    $file_path = ROOT_PATH."uploads/car_images/".$image['file_name'];

    try {
        $pdo->beginTransaction();

        // Delete DB record
        $del = $pdo->prepare("DELETE FROM car_images WHERE id=?");
        $del->execute([$image_id]);

        // Delete file from server
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $pdo->commit();
        echo "success";
    } catch(Exception $e) {
        $pdo->rollBack();
        echo "error: ".$e->getMessage();
    }
