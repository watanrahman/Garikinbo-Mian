<?php
    error_reporting(E_ERROR | E_PARSE);
    session_start();
    include '../config.php';
    include ROOT_PATH . 'comp/card/AuctionPostCard.php';
    
    auth_check();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user_profile['full_name']); ?> - Profile</title>

    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
    
</head>
<body>
    <?php include ROOT_PATH . "comp/nav/MainNavbar.php"; ?>
    <div class="page-content">

    <div class="container page-content">
        <button class="btn btn-app btn-outline border mb-3 fs-5" onclick="history.go(-1);"><i class="bi bi-arrow-left"></i> Back</button>

        <div class="row">
            <div class="col-xl-3 col-lg-2 col-md-12 col-sm-12 col-12"></div>
            <div class="col-xl-6 col-lg-8 col-md-12 col-sm-12 col-12">
                <?php
                    if ($user && $user['profile_status'] === 'verified') {
                        ?>
                            <div class="text-center">
                                <div class="w-100 d-flex align-items-center justify-content-center">
                                    <div class="profile-pic-container mb-4">
                                        <img 
                                            class="img-fluid"
                                            src="<?php echo $main_url?>/uploads/users/<?php echo $user['profile_pic_url']; ?>" 
                                            alt="">
                                        
                                        <?php 
                                            if ($user['profile_staus'] === 'verified') {
                                                ?>
                                                    <div class="profile-badge">
                                                        <i class="bi bi-check-lg"></i>
                                                    </div>
                                                <?php
                                            }
                                        ?>
                                    </div>                                   
                                </div>
                                <p class="mb-3 text-center fs-2">Congratulations <?php echo $user['full_name']; ?> 🎉</p>
                                <p class="text-center small mb-5 fs-5">
                                    Your Account Has Been Verified
                                </p>     
                                <a href="<?php echo $main_url; ?>" class="btn btn-app btn-primary btn-app-primar"><i class="bi bi-arrow-return-left"></i> Home</a>       
                            </div>                        
                        <?php
                    }
                    else if ($user && $user['profile_status'] === 'review') {
                        ?>
                            <div class="text-center">
                                <p class="mb-4 text-center fs-2">Applied Successfully ✅</p>
                                <p class="text-center small mb-4 fs-5">
                                    It may take 1 to 3 days to verify your information.
                                </p>     
                                <a href="<?php echo $main_url; ?>" class="btn btn-app btn-primary btn-app-primar"><i class="bi bi-arrow-return-left"></i> Home</a>       
                            </div>                        
                        <?php
                    }else if ($user && $user['profile_status'] === 'normal'){
                        ?>
                            <div class="">
                                <p class="mb-4 text-center fs-2">Upload Your NID Card</p>
                                <p class="text-center small mb-4 fs-5">
                                    Please upload clear pictures of both sides of your National ID Card.
                                    Ensure all text and photos are fully visible. Blurry or cropped images
                                    may delay verification.
                                </p>            
                                <form method="POST" enctype="multipart/form-data" id="nidForm" action="<?php echo $main_url; ?>/api/create/upload_verification.php">
                                    <!-- FRONT SIDE -->
                                    <div class="mb-4 text-center">
                                        <label for="nid_front" class="form-label fw-semibold">Front Side of NID</label>
                                        <div class="border rounded p-3 bg-light position-relative">
                                        <img id="nidFrontPreview" 
                                            src="<?php echo $main_url?>/lib/image/nid-front.png" 
                                            alt="NID Front Preview"
                                            class="img-fluid rounded mb-2">
                                        <input type="file" class="form-control" name="nid_front" id="nid_front" accept="image/*" required>
                                        <div class="form-text mt-1">
                                            Make sure the image is not blurry and shows your photo and ID number clearly.
                                        </div>
                                        </div>
                                    </div>

                                    <!-- BACK SIDE -->
                                    <div class="mb-4 text-center">
                                        <label for="nid_back" class="form-label fw-semibold">Back Side of NID</label>
                                        <div class="border rounded p-3 bg-light position-relative">
                                        <img id="nidBackPreview" 
                                            src="<?php echo $main_url?>/lib/image/nid-back.png" 
                                            alt="NID Back Preview"
                                            class="img-fluid rounded mb-2">
                                        <input type="file" class="form-control" name="nid_back" id="nid_back" accept="image/*" required>
                                        <div class="form-text mt-1">
                                            Ensure the back side text is visible and not covered or cropped.
                                        </div>
                                        </div>
                                    </div>

                                    <!-- SUBMIT -->
                                    <button type="submit" class="btn btn-success w-100 btn-app">Submit for Verification</button>
                                </form> 
                            </div>
                        <?php
                    }else {
                        ?>

                        <?php
                    }
                ?>


            </div>
            <div class="col-xl-3 col-lg-2 col-md-12 col-sm-12 col-12"></div>
        </div> <!-- row end -->
    </div>  
    </div>
    <?php include ROOT_PATH . "comp/nav/MainFooter.php"; ?>
    <script src="<?php echo $main_url;?>/lib/js/bootstrap.js"></script>

    <script>
        document.getElementById('nid_front').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('nidFrontPreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
        }
        });

        document.getElementById('nid_back').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('nidBackPreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
        }
        });
    </script>
</body>
</html>