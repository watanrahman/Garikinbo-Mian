<?php
    session_start();
    require_once '../config.php';
    if (isset($_SESSION['user_id']) || !empty($_SESSION['user_id'])) {
        header("Location: " . $main_url);
        exit;
    }


    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullname = trim($_POST['full_name']);
        $username = strtolower(str_replace(' ', '_', trim($_POST['user_name'])));
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone_number']);
        $location = $_POST['location'];
        $password = $_POST['password'];

        // Basic validation
        if (!$fullname || !$username || !$email || !$phone || !$location || !$password) {
            $errors[] = 'Please fill all fields.';
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            exit('Please enter a valid email address.');
        }

        // Validate phone number (basic check for Bangladesh numbers)
        if (!preg_match('/^01[3-9]\d{8}$/', $phone)) {
            exit('Please enter a valid Bangladeshi phone number (11 digits starting with 01).');
        }

        // Check if username, email or phone already exists
        $stmt = $pdo->prepare("SELECT id, user_name, email, phone_number FROM users WHERE user_name = ? OR email = ? OR phone_number = ?");
        $stmt->execute([$username, $email, $phone]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $errors = [];
            
            if ($existingUser['user_name'] === $username) {
                $errors[] = 'Username already taken.';
            }
            
            if ($existingUser['email'] === $email) {
                $errors[] = 'Email already registered.';
            }
            
            if ($existingUser['phone_number'] === $phone) {
                $errors[] = 'Phone number already registered.';
            }
            
            exit(implode(' ', $errors));
        }

        // Handle profile picture upload
        $profile_pic_url = null;

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

        // Secure password hash
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (full_name, profile_pic_url, user_name, email, phone_number, location, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$fullname, $profile_pic_url == null ? 'default.jpg' : $profile_pic_url, $username, $email, $phone, $location, $hashedPassword])) {
            header("Location: " . $main_url . "/auth/signin.php?registration=success");
            exit; // always good practice after a redirect
        } else {
            exit("Signup Fail");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Auction - Sign Up</title>
    
    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
</head>
<body class="body-bg-signup">
    <div class="container pt-5 pb-5">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10 col-sm-12 col-12">
                <div class="p-2 mb-4 mt-4 d-flex align-items-center justify-content-center">
                    <img src="<?php echo $main_url?>/logo.png" class="img-fluid w-50" alt="Logo">
                </div>
                <div class="card app-form-card">
                    <h2 class="text-center mb-4">Create Account</h2>
                    <form 
                        method="POST" 
                        enctype="multipart/form-data"
                        onsubmit="return validateForm()">

                        <!-- User Profile Picture -->
                        <div class="d-flex align-items-center justify-content-center mb-5">
                            <label class="image-previewer" for="profile-pic">
                                <label for="profile-pic" class="btn btn-dark btn-sm btn-app">Set Profile Pic</label>
                                <img src="<?php echo $main_url?>/uploads/users/default.jpg" id="pp-previewer-img" class="" alt="">
                            </label>
                        </div>

                        <input type="file" name="profile_pic" id="profile-pic" hidden>

                        <!-- FULL NAME -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="full_name" id="fullnameInput" placeholder="Full Name" required>
                            <label for="fullnameInput">Full Name</label>
                        </div>
                        
                        <!-- USERNAME -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="user_name" id="usernameInput" placeholder="Username" required>
                            <label for="usernameInput">Username</label>
                        </div>
                        
                        <!-- EMAIL -->
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" id="emailInput" placeholder="Email" required>
                            <label for="emailInput">Email</label>
                        </div>
                        
                        <!-- PHONE -->
                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" name="phone_number" id="phoneInput" placeholder="Phone" 
                                   pattern="01[3-9]\d{8}" maxlength="11" required
                                   oninput="validatePhone(this)">
                            <label for="phoneInput">Mobile (01XXXXXXXXX)</label>
                            <div id="phoneError" class="text-danger small mt-1" style="display: none;">
                                Please enter a valid Bangladeshi phone number
                            </div>
                        </div>
                        
                        <!-- LOCATION -->
                        <div class="mb-3">
                            <select name="location" class="form-select" required>
                                <option value="" hidden>Select Division</option>
                                <option value="Dhaka">Dhaka</option>
                                <option value="Chittagong">Chittagong</option>
                                <option value="Khulna">Khulna</option>
                                <option value="Rajshahi">Rajshahi</option>
                                <option value="Sylhet">Sylhet</option>
                                <option value="Barisal">Barisal</option>
                                <option value="Rangpur">Rangpur</option>
                                <option value="Mymensingh">Mymensingh</option>
                            </select>
                        </div>
                        
                        <!-- PASSWORD -->
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" name="password" id="passwordInput" 
                                   placeholder="Password" minlength="6" required
                                   oninput="checkPasswordMatch()">
                            <label for="passwordInput">Password (min. 6 characters)</label>
                        </div>
                        
                        <!-- CONFIRM PASSWORD -->
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="confirmPasswordInput" 
                                   placeholder="Confirm Password" required
                                   oninput="checkPasswordMatch()">
                            <label for="confirmPasswordInput">Confirm Password</label>
                        </div>
                        
                        <!-- SUBMIT -->
                        <button type="submit" class="btn btn-primary w-100 mb-3 btn-lg btn-app-primary">Sign Up</button>
                        <p class="text-center">Already have an account? <a href="<?php echo $main_url;?>/auth/signin.php" class="fw-bold">Sign In</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
    <script>
        function validateForm() {
            const password = document.getElementById('passwordInput').value;
            const confirmPassword = document.getElementById('confirmPasswordInput').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                alert('Password must be at least 6 characters long!');
                return false;
            }
            
            return true;
        }
        
        document.getElementById('profile-pic').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewImg = document.getElementById('pp-previewer-img');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block'; // Ensure image is visible
               }
                reader.readAsDataURL(file);
            } else {
                previewImg.src = '';
                previewImg.style.display = 'none'; // Hide image if no file is selected
            }
        });

        function validatePhone(input) {
            // Remove any non-digit characters
            input.value = input.value.replace(/\D/g, '');
            
            // Validate Bangladesh phone number format
            const phoneRegex = /^01[3-9]\d{8}$/;
            const phoneInput = document.getElementById('phoneInput');
            
            if (input.value.length === 11 && !phoneRegex.test(input.value)) {
                phoneInput.style.borderColor = 'red';
                document.getElementById('phoneError').style.display = 'block';
            } else {
                phoneInput.style.borderColor = '';
                document.getElementById('phoneError').style.display = 'none';
            }
        }
        
        // Real-time password confirmation check
        function checkPasswordMatch() {
            const password = document.getElementById('passwordInput').value;
            const confirmPassword = document.getElementById('confirmPasswordInput').value;
            const confirmInput = document.getElementById('confirmPasswordInput');
            
            if (confirmPassword === '') {
                confirmInput.style.borderColor = '';
            } else if (password === confirmPassword) {
                confirmInput.style.borderColor = 'green';
            } else {
                confirmInput.style.borderColor = 'red';
            }
        }
    </script>
</html>