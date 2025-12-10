<?php 
    session_start();
    include '../config.php';

    if ($user && $user['profile_status'] !== 'banned') {
        header("Location: " . $main_url);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <!-- Css Files -->
    <link rel="stylesheet" href="../lib/css/bootstrap.css">
</head>
<body>
    <div class="d-flex align-items-center justify-content-center" style="height: 100vh; ">
        <div class="text-center text-danger">
            <img 
                src="../lib/image/user_banned.png" 
                class="img-fluid"
                alt="">
            <h1>You Are Banned!</h1>
        </div>
    </div>
</body>
</html>