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
          <th>Car</th>
          <th>Seller</th>
          <th>Start Price</th>
          <th>Current Bid</th>
          <th>Location</th>
          <th>Start</th>
          <th>End</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="auction-data">
        <tr><td colspan="10">Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
async function loadAuctions() {
  try {
    const res = await fetch(`<?php echo $main_url; ?>/api/get/get_allauctioninfo.php`);
    const data = await res.json();

    const tbody = document.getElementById('auction-data');
    tbody.innerHTML = '';

    if (data.auctions && data.auctions.length > 0) {
      data.auctions.forEach(a => {
        tbody.innerHTML += `
          <tr>
            <td>${a.auction_id}</td>
            <td>
              <img src="<?php echo $main_url?>/uploads/thumbnails/${a.car_image}" width="70" height="45" class="rounded me-2">
              <div>${a.car_title}</div>
              <small>${a.make} ${a.model} (${a.year})</small>
            </td>
            <td>
              <strong>${a.seller_name}</strong><br>
              <small>${a.seller_phone}</small><br>
              <small>${a.seller_location}</small>
            </td>
            <td>৳${parseFloat(a.start_price).toLocaleString()}</td>
            <td>${a.current_highest_bid ? "৳" + parseFloat(a.current_highest_bid).toLocaleString() : '-'}</td>
            <td>${a.auction_location}</td>
            <td>${new Date(a.start_time).toLocaleString()}</td>
            <td>${new Date(a.end_time).toLocaleString()}</td>
            <td>
              <span class="badge 
                ${a.auction_status === 'active' ? 'bg-success' :
                  a.auction_status === 'ended' ? 'bg-secondary' :
                  a.auction_status === 'paid' ? 'bg-info' :
                  a.auction_status === 'delivered' ? 'bg-primary' :
                  'bg-danger'}">
                ${a.auction_status}
              </span>
            </td>
            <td>
              <a href="<?php echo $main_url; ?>/auction/index.php?id=${a.auction_id}" 
                 class="btn btn-app btn-primary">View</a>
            </td>
          </tr>`;
      });
    } else {
      tbody.innerHTML = '<tr><td colspan="10">No auctions found.</td></tr>';
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
