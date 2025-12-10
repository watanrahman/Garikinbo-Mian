<?php
    session_start();
    include '../config.php';
    admin_token_check();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garikinbo Admin</title>

    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/dashboard_css.php"; ?>
</head>
<body>

  <?php include ROOT_PATH . "comp/nav/DashboadSidebar.php"; ?>


  <!-- Main Content -->
  <div id="main-content">
    <h3 class="mb-3">Users</h3>
  </div>
</body>
</html>