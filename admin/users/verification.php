<?php
    session_start();
    include '../../config.php';
    admin_token_check();


    // get user id
    if (!isset($_GET['user']) || empty($_GET['user'])) {
        die("User ID is missing.");
    }
    $user_id = intval($_GET['user']);
    
    // fetch user
    $stmt = $pdo->prepare("SELECT id, full_name, user_name, email, phone_number, profile_status 
                          FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT nid_front_url, nid_back_url, created_at 
                            FROM user_verifications WHERE user_id = ? LIMIT 1");
    $stmt2->execute([$user_id]);
    $verify = $stmt2->fetch(PDO::FETCH_ASSOC);


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

  <div id="main-content" class="container mt-4">
    <div class="mb-3 d-flex align-items-center">
      <a href="<?php echo $main_url?>/admin/users/" class="me-4 btn btn-app btn-dark"><i class="bi bi-arrow-left"></i> Back</a>
      <h3 class="mb-0">User Verification Application</h3>
    </div>

    <?php if (!$user): ?>
      <div class="alert alert-danger">User not found.</div>
    <?php else: ?>
      <div class="card p-3 mb-4 shadow-sm">
        <h4>User Info</h4>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['user_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone_number']) ?></p>
        <p><strong>Status:</strong> <span class="badge 
            <?= $user['profile_status'] === 'verified' ? 'bg-success' : 
               ($user['profile_status'] !== 'verified' ? 'bg-danger' : 'bg-secondary') ?>">
            <?= $user['profile_status'] ?>
          </span></p>
      </div>

      <?php if ($verify): ?>
        <div class="card p-3 shadow-sm">
          <h4>Submitted Verification Files</h4>
          <div class="row mt-3">
            <div class="col-6 text-center mb-3">
              <p><strong>NID Front</strong></p>
              <img src="<?php echo $verify['nid_front_url']; ?>" 
                   class="img-fluid rounded border shadow-sm" alt="NID Front">
            </div>
            <div class="col-6 text-center mb-3">
              <p><strong>NID Back</strong></p>
              <img src="<?php echo $verify['nid_front_url']; ?>" 
                   class="img-fluid rounded border shadow-sm" alt="NID Back">
            </div>
          </div>

          <div class="text-center mt-4">
            <button class="btn btn-success px-4 py-2 me-2" onclick="updateUserStatus(<?php echo $user['id']; ?>, 'verify')">Approve</button>
            <button class="btn btn-danger px-4 py-2" onclick="updateUserStatus(<?php echo $user['id']; ?>, 'reject')">Reject</button>
          </div>
        </div>
      <?php else: ?>
        <div class="alert alert-warning mt-4">No verification files submitted.</div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <script>
    async function updateUserStatus(userId, action) {
      if (!confirm(`Are you sure you want to ${action} this user?`)) return;

      try {
        const res = await fetch('<?php echo $main_url; ?>/api/update/update_verification_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: userId, action })
        });

        const data = await res.json();
        alert(data.message);
        window.location.reload();
      } catch (err) {
        console.error(err);
        alert('Failed to update user status.');
      }
    }
  </script>
</body>
</html>