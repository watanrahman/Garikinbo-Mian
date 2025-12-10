<?php
    function verification_message($user, $main_url){
       if ($user && $user['profile_status'] === 'normal') {
            ?>
                <style>
                    .page-content{
                        margin-top: 150px!important;
                    }
                </style>
                <div class="text-center bg-dark text-white py-1">
                    <small>
                        Dear <b><?php echo htmlspecialchars($user['user_name']); ?></b>, 
                        it looks like your account isn’t verified yet. Please 
                        <a href="<?php echo $main_url; ?>/user/verify.php" class=" fw-bold">click here</a> 
                        to complete the verification process.
                    </small>
                </div>
            <?php
       }
        
    }
?>