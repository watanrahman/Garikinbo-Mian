<?php
    session_start();
    require_once '../config.php';
    if (isset($_SESSION['user_id']) || !empty($_SESSION['user_id'])) {
        header("Location: " . $main_url);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];


        // Check is The user admin or not first
        if ($email === ADMIN_EMAIL && $password === ADMIN_PASS) {
            $_SESSION['auth_token'] = ADMIN_TOKEN;
            $_SESSION['user_name'] = 'Boss';
            $_SESSION['role'] = 'admin';
            header('Location: ../admin/index.php');
            exit;
        }

        // Fetch user by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['role'] = 'user';
            $_SESSION['auth_token'] = null; // not admin
            $_SESSION['email'] = $user['email'];
            $_SESSION['location'] = $user['location'];

            header('Location: ../index.php');
            exit;
        } else {
            echo 'Invalid email or password.';
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Auction - Sign In</title>

    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
</head>
<body class="body-bg-signin">
    <div class="container pt-5 pb-5">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10 col-sm-12 col-12">
                <div class="p-2 mb-4 mt-4 d-flex align-itmes-center justify-content-center ">
                    <img src="<?php echo $main_url?>/logo.png" class="img-fluid w-50" class="" alt="">
                </div>
                <div class="card app-form-card">
                    <h2 class="text-center mb-4">Sign In</h2>
                    <?php 
                        if (isset($_GET['registration']) && $_GET['registration'] == "success") {
                            ?>
                                <p class="alert alert-success">Account created successfully! Please sign in to continue</p>
                            <?php
                        }
                    
                    ?>
                    <form method="POST">
                        <!-- EMAIL -->
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" id="emailInput" placeholder="Email">
                            <label for="emailInput">Email</label>
                        </div>
                        <!-- PASSWORD -->
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Password">
                            <label for="passwordInput">Password</label>
                        </div>
                        <!-- SUBMIT -->
                        <button class="btn btn-primary btn-app btn-app-primary w-100 mb-3 btn-lg">Sign In</button>
                        <p class="text-center">New to CarBid? <a href="<?php echo $main_url;?>/auth/signup.php" class="fw-bold">Create Account</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
