<div class="profile-card bg-white">
    <div class="profile-header">
        <!-- Banner -->
        <img 
            class="profile-banner-img"
            src="https://images.unsplash.com/photo-1759239934518-5c69b744ff91?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" 
            alt=""
        >

        <!-- Profile pic -->
        <div class="profile-header-content">
            <div class="profile-pic-container mb-4">
                <img 
                    class="img-fluid"
                    src="<?php echo $main_url?>/uploads/users/<?php echo $user_profile['profile_pic_url']; ?>" 
                    alt="">
                
                <?php 
                    if ($user_profile['profile_status'] === 'verified') {
                        ?>
                            <div class="profile-badge" style="border: 6px solid #fff;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        <?php
                    }
                ?>
            </div>      
            <h2 class="mb-0"><?php echo $user_profile['full_name']; ?></h2>
            <p class="mb-0">@<?php echo $user_profile['user_name']; ?></p>
        </div>
    </div>
    <div class="profile-body">
        <div class="">
                <p class="mb-0 text-center">
                    <span class="me-2 fs-5"><i class="bi bi-geo-alt"></i></span> <?php echo $user_profile['location']; ?>
                </p>
                <p class="mb-0 text-center">
                    <span class="me-2 fs-5"><i class="bi bi-envelope"></i></span> <?php echo $user_profile['email']; ?>
                </p>
                <p class="mb-0 text-center">
                    <span class="me-2 fs-5"><i class="bi bi-telephone"></i></span> <?php echo $user_profile['phone_number']; ?> 
                </p>
                <?php 
                    if ($user_profile['id'] === $user_id) {
                        ?>
                            <div class="mt-1 text-center">
                                <a href="<?php echo $main_url?>/user/profile/edit.php">edit profile</a>
                            </div>
                        <?php
                    }
                ?>
        </div>
    </div>
</div>