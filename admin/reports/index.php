<?php
session_start();
include '../../config.php';
admin_token_check();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garikinbo Admin — All Auctions</title>
    <?php include ROOT_PATH . "lib/css/dashboard_css.php"; ?>
</head>
<body>

<?php include ROOT_PATH . "comp/nav/DashboadSidebar.php"; ?>

<div id="main-content">
  <h3 class="mb-3">All Car Auctions</h3>
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle text-center" id="auctions-table">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>From</th>
          <th>To</th>
          <th>Reason</th>
          <th>Message</th>
          <th>Reported At</th>
        </tr>
      </thead>
      <tbody id="repots-data">
        <tr><td colspan="10">Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
async function loadAuctions() {
  try {
    const res = await fetch(`<?php echo $main_url; ?>/api/get/get_allreport.php`);
    const data = await res.json();

    const tbody = document.getElementById('repots-data');
    tbody.innerHTML = '';

    if (data.reports && data.reports.length > 0) {
      data.reports.forEach(r => {
        tbody.innerHTML += `<tr>
            <td>${r.report_id}</td>
            <td>${r.to_id}</td>
            <td>${r.from_id}</td>
            <td>${r.report_type}</td>
            <td>${r.message}</td>
            <td>${r.created_at}</td>
          </tr>`;
      });
    } else {
      tbody.innerHTML = '<tr><td colspan="10">No reports found.</td></tr>';
    }
  } catch (err) {
    console.error(err);
    document.getElementById('auction-data').innerHTML = '<tr><td colspan="10">Error loading data.</td></tr>';
  }
}

loadAuctions();
setInterval(loadAuctions, 10000);
</script>

</body>
</html>
