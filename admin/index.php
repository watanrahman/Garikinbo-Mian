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
    <h3 class="mb-3">Welcome, Admin</h3>

    <div class="row g-3" id="dashboard-stats">
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5>Total Users</h5>
                <h3 id="total_users">0</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5>Total Cars</h5>
                <h3 id="total_cars">0</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5>Active Auctions</h5>
                <h3 id="active_auctions">0</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5>Ended Auctions</h5>
                <h3 id="ended_auctions">0</h3>
            </div>
        </div>
    </div>

    <div class="mt-4 text-center text-muted">
        <small>Last Updated: <span id="last_update">--</span></small>
    </div>
  </div>

  <script>
  async function loadDashboardData() {
      try {
          const res = await fetch('<?php echo $main_url; ?>/api/get/get_a_dashboard_stats.php');
          const data = await res.json();

          if (data.error) {
              console.error(data.error);
              return;
          }

          document.getElementById('total_users').innerText = data.total_users;
          document.getElementById('total_cars').innerText = data.total_cars;
          document.getElementById('active_auctions').innerText = data.active_auctions;
          document.getElementById('ended_auctions').innerText = data.ended_auctions;
          document.getElementById('last_update').innerText = data.last_update;
      } catch (err) {
          console.error('Failed to fetch stats', err);
      }
  }

  // Run once immediately
  loadDashboardData();

  // Auto-refresh every 5 seconds
  setInterval(loadDashboardData, 2000);
  </script>
</body>
</html>