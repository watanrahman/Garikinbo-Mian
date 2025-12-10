<?php 
    session_start();
    include '../../config.php';
    auth_check();

    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "404";
        exit;
    }

    // Check verifucation status
    if ($user && $user['profile_status'] !== 'normal') {
        echo "404";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $upload_dir = ROOT_PATH . "uploads/nid/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

    $nid_front = $_FILES['nid_front'];
    $nid_back  = $_FILES['nid_back'];

    // Validation: file type
    if (!in_array($nid_front['type'], $allowed_types) || !in_array($nid_back['type'], $allowed_types)) {
        die("Only JPG and PNG images are allowed.");
    }

    // === Function to Compress & Resize ===
    function compressImage($source, $destination, $max_width = 1000, $max_size_kb = 500) {
        $info = getimagesize($source);
        $mime = $info['mime'];

        // Load image
        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($mime === 'image/png') {
            $image = imagecreatefrompng($source);
        } else {
            return false;
        }

        list($width, $height) = getimagesize($source);

        // Resize to max width (keep aspect ratio)
        if ($width > $max_width) {
            $new_width = $max_width;
            $new_height = intval(($height / $width) * $new_width);
        } else {
            $new_width = $width;
            $new_height = $height;
        }

        $dst = imagecreatetruecolor($new_width, $new_height);

        if ($mime === 'image/png') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($image);

        // Compress until below max size
        $quality = 85;
        $temp_path = $destination . '.tmp';

        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            do {
                imagejpeg($dst, $temp_path, $quality);
                $size_kb = filesize($temp_path) / 1024;
                $quality -= 5;
            } while ($size_kb > $max_size_kb && $quality > 30);
        } else {
            $compression = 6;
            do {
                imagepng($dst, $temp_path, $compression);
                $size_kb = filesize($temp_path) / 1024;
                $compression++;
            } while ($size_kb > $max_size_kb && $compression < 9);
        }

        rename($temp_path, $destination);
        imagedestroy($dst);
        return true;
    }

    // Generate unique file names
    $front_filename = "front_" . $user_id . "_" . time() . "." . pathinfo($nid_front['name'], PATHINFO_EXTENSION);
    $back_filename  = "back_"  . $user_id . "_" . time() . "." . pathinfo($nid_back['name'], PATHINFO_EXTENSION);

    $front_path = $upload_dir . $front_filename;
    $back_path  = $upload_dir . $back_filename;

    // Compress and save
    compressImage($nid_front['tmp_name'], $front_path);
    compressImage($nid_back['tmp_name'], $back_path);

    // Store URLs in database
    $front_url = $main_url . "/uploads/nid/" . $front_filename;
    $back_url  = $main_url . "/uploads/nid/" . $back_filename;

    $stmt = $pdo->prepare("
        INSERT INTO user_verifications (user_id, nid_front_url, nid_back_url, status)
        VALUES (?, ?, ?, 'pending')
        ON DUPLICATE KEY UPDATE 
            nid_front_url = VALUES(nid_front_url),
            nid_back_url = VALUES(nid_back_url),
            status = 'pending',
            updated_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$user_id, $front_url, $back_url]);

    // ✅ Update user's profile_status to 'review'
    $update_user = $pdo->prepare("
        UPDATE users 
        SET profile_status = 'review' 
        WHERE id = ?
    ");
    $update_user->execute([$user_id]);
    header("Location: " . $main_url . "/user/verify.php");
    exit();
?>
