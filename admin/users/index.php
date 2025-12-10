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
    <title>Garikinbo Admin</title>

    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/dashboard_css.php"; ?>
</head>
<body>

  <?php include ROOT_PATH . "comp/nav/DashboadSidebar.php"; ?>


  <!-- Main Content -->
  <div id="main-content">
    <h3 class="mb-3">Users Application</h3>
      <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-12 col-12">
            <select class="form-control fs-5 px-3 py-2 mb-3" id="statusFilter">
              <option value="">All Users</option>
              <option value="normal">Non-Verified Users</option>
              <option value="verified">Verified Users</option>
              <option value="review">In-Review Users</option>
              <option value="banned">Banned Users</option>
            </select>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-12">
            <div class="mb-3 d-flex">
              <input 
                placeholder="serach user"
                class="form-control fs-5 w-100 me-2 px-3 py-2"
                type="text"
                id="searchInput"  
              />
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle text-center" id="users-table">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Profile</th>
              <th>Full Name</th>
              <th>Username</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Location</th>
              <th>Joined</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="user-data">
            <tr><td colspan="9">Loading...</td></tr>
          </tbody>
        </table>
      </div>
  </div>
  </div>




  <script>
    async function loadUsers() {
      try {
        const search = document.getElementById('searchInput').value.trim();
        const status = document.getElementById('statusFilter').value;
        const res = await fetch(`<?php echo $main_url; ?>/api/get/get_a_alluserinfo.php?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`);
        const data = await res.json();

        const tbody = document.getElementById('user-data');
        tbody.innerHTML = ''; // Clear existing rows

        if (data.users && data.users.length > 0) {
          data.users.forEach(u => {
            let application_url = "<?php echo $main_url; ?>/admin/user/applications.php?user=" + u.id;
            tbody.innerHTML += `
              <tr>
                <td>${u.id}</td>
                <td><img src="<?php echo $main_url?>/uploads/users/${u.profile_pic_url}" 
                        class="rounded-circle" width="40" height="40"></td>
                <td>${u.full_name || '-'}</td>
                <td>${u.user_name}</td>
                <td>${u.email}</td>
                <td>${u.phone_number}</td>
                <td>${u.location}</td>
                <td>${new Date(u.created_at).toLocaleDateString()}</td>
                <td>
                  <span class="badge ${u.profile_status === 'verified' ? 'bg-success' :
                                      u.profile_status === 'banned' ? 'bg-danger' : 'bg-secondary'}">
                    ${u.profile_status}
                  </span>
                </td>
                <td>
                  ${u.profile_status === 'verified'
                    ? `<button class='btn btn-app btn-warning' onclick="updateUserStatus(${u.id}, 'demote')">Demote</button>
                      <button class='btn btn-app btn-danger' onclick="updateUserStatus(${u.id}, 'ban')">Ban</button>`
                    : ""}

                  ${u.profile_status === 'normal'
                    ? `<button class='btn btn-app btn-success' onclick="updateUserStatus(${u.id}, 'verify')">Verify</button>
                      <button class='btn btn-app btn-danger' onclick="updateUserStatus(${u.id}, 'ban')">Ban</button>`
                    : ""}

                  ${u.profile_status === 'banned'
                    ? `<button class='btn btn-app btn-primary' onclick="updateUserStatus(${u.id}, 'activate')">Activate</button>`
                    : ""}

                  ${u.profile_status === 'review'
                    ? `<a href="<?php echo $main_url; ?>/admin/users/verification.php?user=${u.id}" 
                        class="btn btn-app btn-primary">View Application</a>`
                    : ""}
                </td>
              </tr>`;
          });
        } else {
          tbody.innerHTML = '<tr><td colspan="10">No users found.</td></tr>';
        }
      } catch (err) {
        console.log(err);
        document.getElementById('user-data').innerHTML = '<tr><td colspan="10">Error loading data.</td></tr>';
      }
    }

    // Live search and sort
    document.getElementById('searchInput').addEventListener('input', () => loadUsers());
    document.getElementById('statusFilter').addEventListener('change', () => loadUsers());

    // Initial load + auto-refresh
    loadUsers();
    setInterval(loadUsers, 10000);

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
        loadUsers(); // Refresh table
      } catch (err) {
        console.error(err);
        alert('Failed to update user status.');
      }
    }
  </script>
</body>
</html>