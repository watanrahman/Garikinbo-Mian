<?php 
  $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  include ROOT_PATH . 'comp/system_message/Navbar_TopMessages.php';
?>




<nav class="navbar-for-pc navbar navbar-expand-lg bg-light shadow-sm app-nav p-0">
  
  <?php verification_message($user, $main_url); ?>

  <div class="container py-2">
    <!-- Left: Logo -->
    <a class="navbar-brand fw-bold" href="<?php echo $main_url?>">
      <img src="<?php echo $main_url; ?>/logo2.png" alt="Logo" width="110" class="rounded d-inline-block align-text-middle">
    </a>

    <!-- Center: Searchbar -->
    <form action="<?php echo $main_url; ?>/search.php" method="get" class="bar d-flex mx-auto w-50">
      <input 
        class="form-control me-2 app-searchbar" 
        type="search"
        name="q"
        placeholder="Search cars..."
        aria-label="Search"
        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
      
     <button class="btn text-dark" type="submit">
        <i class="bi bi-search fs-4"></i>
      </button>
    </form>

    <!-- Right: Nav Links -->
    <div class="d-flex align-items-center app-nav-link">
      <a class="nav-link text-uppercase fw-bold px-3 d-flex align-items-center justify-content-center" href="<?php echo $main_url?>"><i class="bi bi-house-door fs-4 me-2"></i> <span class="pt-1">Home</span></a>
      <a class="nav-link text-uppercase fw-bold px-3 d-flex align-items-center justify-content-center" href="<?php echo $main_url?>"><span class="pt-1">About</span></a>
      <?php
        if(!isset($_SESSION['auth_token']) || isset($_SESSION['auth_token']) !== ADMIN_TOKEN){
            // check the token 
            // admin_token_check();
            ?>
              <a class="btn btn-dark mx-2 btn-app" href="<?php echo $main_url?>/user/auction/create.php"><i class="bi bi-plus-lg"></i> Post</a> 
            <?php
        }
      ?>

      <?php 
        if (isset($_SESSION['user_id']) == true ) {
          ?>
            <!-- Profile Dropdown -->
            <div class="navbar-list dropdown">
                <button class="btn dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <img class="border profile-pic profile-pic-sm" src="<?php echo $main_url?>/uploads/users/<?php echo $user['profile_pic_url']; ?>" alt="">
                </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item" href="<?php echo $main_url; ?>/user/index.php?u=<?php echo $user['user_name']?>">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?php echo $main_url; ?>/auth/logout.php">Logout</a></li>
              </ul>
            </div>
          <?php
        }else if(isset($_SESSION['auth_token']) || isset($_SESSION['auth_token']) == ADMIN_TOKEN){
            // check the token 
            admin_token_check();
            ?>
              <a class="btn btn-primary btn-app" href="<?php echo $main_url; ?>/admin/">Admin Panel</a>
            <?php
        } else {
          ?>
            <a class="btn btn-primary btn-app" href="<?php echo $main_url; ?>/auth/signin.php">Sign In</a>
          <?php
        }
      ?>
    </div>
  </div>
</nav>




<!-- Navbar For Mobile Device -->
<nav class="navbar-for-mobile navbar navbar-expand-lg bg-light shadow-sm app-nav p-0">
    <?php verification_message($user, $main_url); ?>
 
    <div class="container py-2">
        <div class="d-flex align-items-center justify-content-between w-100 pe-1">
            <a class="navbar-brand fw-bold" href="<?php echo $main_url?>">
              <img src="<?php echo $main_url; ?>/logo2.png" alt="Logo" width="110" class="rounded d-inline-block align-text-middle">
            </a>


            <div class="d-flex align-items-center">
                <!-- <div class="me-2">
                  <button class="btn text-dark" type="submit">
                    <i class="bi bi-search fs-4"></i>
                  </button>  
                </div>  -->
                <?php 
                  if (isset($_SESSION['user_id']) == true || isset($_SESSION['role']) === 'user') {
                    ?>
                      <!-- Profile Dropdown -->
                      <div class="navbar-list dropdown">
                          <button class="btn dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img class="border profile-pic profile-pic-sm" src="<?php echo $main_url?>/uploads/users/<?php echo $user['profile_pic_url']; ?>" alt="">
                          </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                          <li><a class="dropdown-item" href="<?php echo $main_url; ?>/user/index.php?u=<?php echo $user['user_name']?>">Profile</a></li>
                          <li><a class="dropdown-item" href="#">Settings</a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item text-danger" href="<?php echo $main_url; ?>/auth/logout.php">Logout</a></li>
                        </ul>
                      </div>
                    <?php
                  }else if(isset($_SESSION['auth_token']) || isset($_SESSION['auth_token']) == ADMIN_TOKEN){
                      // check the token 
                      // admin_token_check();
                      ?>
                        <a class="btn btn-primary btn-app" href="<?php echo $main_url; ?>/admin/">Admin Panel</a>
                      <?php
                  } else {
                    ?>
                      <a class="btn btn-primary btn-app" href="<?php echo $main_url; ?>/auth/signin.php">Sign In</a>
                    <?php
                  }
                ?>           
            </div>
        </div>
    </div>
</nav>


<!-- Navbar For Mobile Device -->
<nav class="bottombar-for-mobile navbar navbar-expand-lg bg-light shadow-sm app-bottombar p-0">
    <div class="d-flex bottom-bar-buttons">
        <a 
          href="<?php echo $main_url?>" 
          class="btn btn-bottom-bar btn-app <?php echo ($current_path == '/' || $current_path == '/bid/') ? 'btn-bottom-bar-active' : ''; ?>">
          <i class="bi bi-house"></i> <br> 
          <span>Home</span>
        </a>
        <a href="<?php echo $main_url?>/user/auction/create.php" 
          class="btn btn-bottom-bar btn-app <?php echo ($current_path == '/bid/user/auction/create.php') ? 'btn-bottom-bar-active' : ''; ?>">
          <i class="bi bi-plus-square"></i> <br> 
          <span>Post Car</span>
        </a>       
      <button id="searchToggleBtn" class="btn btn-bottom-bar btn-app">
          <i id="toggleIcon" class="bi bi-search"></i> <br> <span id="toggleText">Search</span>
      </button>
    </div>
</nav>



<div class="search-wrapper-popup">
    <!-- Center: Searchbar -->
    <form action="<?php echo $main_url; ?>/search.php" method="get" class="bar d-flex mx-auto w-50">
      <input 
        class="form-control me-2 app-searchbar" 
        type="search"
        name="q"
        placeholder="Search cars..."
        aria-label="Search"
        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
      
     <button class="btn btn-primary btn-app btn-app-primary text-dark" type="submit">
        <i class="bi bi-search fs-4" id=""></i>
      </button>
    </form>
</div>

<script>
  const toggleBtn = document.getElementById('searchToggleBtn');
  const popup = document.querySelector('.search-wrapper-popup');
  const toggleIcon = document.getElementById('toggleIcon');
  const toggleText = document.getElementById('toggleText');

  toggleBtn.addEventListener('click', function () {
      popup.classList.toggle('search-wrapper-popup-active');

      if (popup.classList.contains('search-wrapper-popup-active')) {
          toggleIcon.className = 'bi bi-x'; // X icon
          toggleText.textContent = 'Close';
      } else {
          toggleIcon.className = 'bi bi-search'; // Search icon
          toggleText.textContent = 'Search';
      }
  });
</script>