  <!-- Navbar -->
  <nav class="navbar navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
      <button class="btn btn-outline-light me-2" id="toggleSidebar">
        <i class="bi bi-list"></i>
      </button>
      <a class="navbar-brand" href="#">Auction Admin</a>
      <div class="d-flex align-items-center">
        <span class="text-white me-3">Welcome, <?= $_SESSION['username'] ?? 'Boss' ?></span>
        <a href="<?php echo $main_url?>"></a>
        <a href="<?php echo $main_url?>/auth/logout.php" class="btn btn-sm btn-danger">logout <i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </nav>

  <!-- Sidebar -->
  <div id="sidebar">
    <div class="brand"><span>Auction Panel</span></div>
    <ul class="nav flex-column mt-3">
      <li class="nav-item"><a href="<?php echo $main_url?>/admin" class="nav-link"><i class="bi bi-speedometer2"></i><span> Dashboard</span></a></li>
      <li class="nav-item"><a href="<?php echo $main_url?>/admin/auctions/" class="nav-link"><i class="bi bi-file-earmark-text"></i><span> Manage Auction </span></a></li>
      <li class="nav-item"><a href="<?php echo $main_url?>/admin/users" class="nav-link"><i class="bi bi-file-earmark-text"></i><span> Manage User </span></a></li>
      <li class="nav-item"><a href="<?php echo $main_url?>/admin/reports" class="nav-link"><i class="bi bi-file-earmark-text"></i><span> Manage Report </span></a></li>
    </ul>
  </div>


    <script>
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');

    toggleSidebar.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
    });
  </script>