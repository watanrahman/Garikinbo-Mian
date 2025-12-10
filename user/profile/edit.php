<?php
session_start();
include '../../config.php';
auth_check();

$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['full_name']);
    $location = trim($_POST['location']);

    if (!$fullname || !$location) {
        $errors[] = "Full name and location are required.";
    }

    // Keep old pic if not updated
    $profile_pic_url = $user['profile_pic_url'];

        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_pic'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $target_max_size = 25 * 1024; // 25KB

            $mime = mime_content_type($file['tmp_name']);
            if (!in_array($mime, $allowed_types)) {
                exit('Only JPEG, PNG, or GIF images are allowed.');
            }

            // Ensure upload directory exists
            $upload_dir = ROOT_PATH . "uploads/users/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $username . '_' . time() . '.' . strtolower($ext);
            $upload_path = $upload_dir . $filename;

            // GIFs are saved directly (no compression)
            if ($mime === 'image/gif') {
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $profile_pic_url = $filename;
                }
                return;
            }

            // Load image
            $src_img = ($mime === 'image/jpeg') 
                ? imagecreatefromjpeg($file['tmp_name']) 
                : imagecreatefrompng($file['tmp_name']);

            if (!$src_img) exit('Failed to process the image.');

            list($width, $height) = getimagesize($file['tmp_name']);

            // Resize to max 600px width (preserve aspect ratio)
            $max_width = 600;
            if ($width > $max_width) {
                $new_width = $max_width;
                $new_height = intval(($height / $width) * $new_width);
            } else {
                $new_width = $width;
                $new_height = $height;
            }

            $dst_img = imagecreatetruecolor($new_width, $new_height);

            if ($mime === 'image/png') {
                imagealphablending($dst_img, false);
                imagesavealpha($dst_img, true);
            }

            imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($src_img);

            // Compress to <= 100KB
            $temp_path = $upload_dir . 'temp_' . uniqid() . '.' . strtolower($ext);
            $quality = 85;

            if ($mime === 'image/jpeg') {
                do {
                    imagejpeg($dst_img, $temp_path, $quality);
                    $filesize = filesize($temp_path);
                    $quality -= 5;
                } while ($filesize > $target_max_size && $quality > 10);
            } else {
                $compression = 6;
                do {
                    imagepng($dst_img, $temp_path, $compression);
                    $filesize = filesize($temp_path);
                    $compression++;
                } while ($filesize > $target_max_size && $compression <= 9);
            }

            imagedestroy($dst_img);

            // Move final image to actual path
            if (rename($temp_path, $upload_path) && file_exists($upload_path)) {
                $profile_pic_url = $filename;
            } else {
                @unlink($temp_path);
                exit('Image compression failed.');
            }
        }

    // Update DB if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, location = ?, profile_pic_url = ? WHERE id = ?");
        if ($stmt->execute([$fullname, $location, $profile_pic_url, $user['id']])) {
            $success = "Profile updated successfully!";

            // Refresh session data
            $user['full_name'] = $fullname;
            $user['location'] = $location;
            $user['profile_pic_url'] = $profile_pic_url;
        } else {
            $errors[] = "Failed to update profile. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['full_name']); ?> - Edit Profile</title>

    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
</head>
<body>
    <?php include ROOT_PATH . "comp/nav/MainNavbar.php"; ?>

    <div class="page-content">
        <div class="container pt-5 pb-5">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8 col-md-10 col-sm-12 col-12">
                    <button class="btn btn-app btn-outline border mb-0 fs-5" onclick="history.go(-1);"><i class="bi bi-arrow-left"></i> Back</button>
                    <div class="mt-3 card app-form-card p-4">

                        <h2 class="text-center mb-4">Edit Profile</h2>

                        <!-- Show error messages -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $err): ?>
                                    <div><?php echo htmlspecialchars($err); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Show success message -->
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <form 
                            method="POST" 
                            enctype="multipart/form-data">

                            <!-- User Profile Picture -->
                            <div class="d-flex flex-column align-items-center mb-4">
                                <img src="<?php echo $main_url?>/uploads/users/<?php echo $user['profile_pic_url'] ?? 'default.jpg'; ?>" 
                                     id="pp-previewer-img" 
                                     class="rounded-circle mb-2" 
                                     style="width:100px; height:100px; object-fit:cover;" 
                                     alt="Profile Pic">
                                <label for="profile-pic" class="btn btn-dark btn-sm btn-app">Change Profile Pic</label>
                            </div>

                            <input type="file" name="profile_pic" id="profile-pic" hidden>

                            <!-- FULL NAME -->
                            <div class="form-floating mb-3">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    value="<?php echo $user['full_name']; ?>"
                                    name="full_name" 
                                    id="fullnameInput" 
                                    placeholder="Full Name"
                                    required>
                                <label for="fullnameInput">Full Name</label>
                            </div>
                            
                            <!-- LOCATION -->
                            <p class="ms-1 mb-1">Your current division is <b>"<?php echo htmlspecialchars($user['location']); ?>"</b></p>
                            <div class="mb-3">
                                <select name="location" class="form-select" required>
                                    <option value="" hidden>Select Division</option>
                                    <?php
                                    $divisions = ["Dhaka","Chittagong","Khulna","Rajshahi","Sylhet","Barisal","Rangpur","Mymensingh"];
                                    foreach ($divisions as $div) {
                                        $selected = ($user['location'] === $div) ? "selected" : "";
                                        echo "<option value=\"$div\" $selected>$div</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <!-- SUBMIT -->
                            <button type="submit" class="btn btn-success w-100 mb-3 btn-app">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include ROOT_PATH . "comp/nav/MainFooter.php"; ?>

    <script src="<?php echo $main_url;?>/lib/js/bootstrap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Preview profile picture before upload -->
    <script>
        document.getElementById('profile-pic').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewImg = document.getElementById('pp-previewer-img');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
