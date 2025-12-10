<?php
    session_start();
    include '../../config.php';
    auth_check();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <!-- Css Files -->
    <?php include ROOT_PATH . "lib/css/app_css.php"; ?>
</head>
<body>
    <?php
        include ROOT_PATH . "comp/nav/MainNavbar.php"; 
    ?>


    <div class="container page-content">
        <button class="btn btn-app btn-outline border mb-3 fs-5" onclick="history.go(-1);"><i class="bi bi-arrow-left"></i> Back</button>
        <div class="row">
            <div class="col-xl-3 col-lg-2 col-md-12 col-sm-12 col-12"></div>
            <div class="col-xl-6 col-lg-8 col-md-12 col-sm-12 col-12">
                <p class="mb-4 text-center fs-2">Create Acution</p>
                <form method="POST" id="createBidPost" enctype="multipart/form-data">
                    <!-- Basic Information -->
                    <section id="basic_information" class="mb-3 pb-3 border-bottom">
                        <p class="mb-4 mt-5 fs-4">Basic Information</p>
                        <div class="form-floating mb-3">
                            <input 
                                name="title"
                                type="text" 
                                class="form-control" 
                                value="X Corolla 2002"
                                id="cartitleInput" 
                                required
                                placeholder="title">
                            <label for="cartitleInput">Title</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea 
                                name="des" 
                                id="desInput" 
                                class="form-control"
                                required
                                style="min-height: 250px;"
                                placeholder="Description">This is a test des of the car</textarea>
                            <label for="desInput">Description</label>
                        </div>

                        <div class="mb-3">
                            <!-- Thumbnail Upload -->
                            <label class="form-label fw-bold">Thumbnail Image</label>
                            <input 
                                class="form-control" 
                                type="file" 
                                hidden
                                id="thumbnail"
                                name="thumbnail" 
                                accept="image/*" 
                                required
                                onchange="previewThumbnail(event)">
                            <label for="thumbnail" class="btn btn-app btn-primary">Choose Thumbnail</label>

                            <!-- Thumbnail Preview -->
                            <div id="thumbnail-preview" class="mt-3">
                                <img src="" id="thumbnail-img" class="img-fluid rounded shadow-sm d-none" style="max-width: 100%;" alt="Thumbnail Preview">
                            </div>
                        </div>

                        <div class="mb-3">
                            <!-- Car Images Upload -->
                            <label class="form-label fw-bold">Choose Car Images</label>
                            <input 
                                class="form-control" 
                                type="file" 
                                hidden
                                id="car-images"
                                name="car_images[]" 
                                accept="image/*" 
                                multiple
                                onchange="previewCarImages(event)"
                            >

                            <!-- Preview Grid + Add More Button -->
                            <div id="car-images-preview" class="mt-3 d-flex flex-wrap gap-2 align-items-start">
                                <!-- Add More Button (inline with images) -->
                                <label for="car-images" type="button" id="add-more-btn" class="d-flex align-items-center justify-content-center" 
                                    style="width: 200px; height: 200px; border-style: dashed;" 
                                    >+ Add</label>
                            </div>
                        </div>
                        
                    </section>

                    <!-- Technical Specifications -->
                    <section id="technical_information" class="mb-3 pb-3 border-bottom">
                        <p class="mb-3 fs-4">Technical Specifications</p>
                        <select name="make" class="form-select mb-3" aria-label="Default select example">
                            <option value="" hidden>Make</option>
                            <option selected value="Toyota">Toyota</option>
                            <option value="Honda">Honda</option>
                            <option value="Nissan">Nissan</option>
                        </select>

                        <div class="form-floating mb-3">
                            <input 
                                name="model"
                                type="text" 
                                class="form-control" 
                                value="Toyota"
                                id="cartitleInput" 
                                required
                                placeholder="model">
                            <label for="cartitleInput">Model name</label>
                        </div>

                        <select name="year" class="form-select mb-3" aria-label="Default select example">
                            <option value="" hidden>Year</option>
                            <option value="1989" >1989</option>
                            <option value="2015" selected>2015</option>
                            <option value="2022">2022</option>
                        </select>

                        <!-- Select Body Type and Trasnmission Type-->
                        <div class="mt-1 mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1 fw-bold">Select body type</p>
                                    <div class="form-check">
                                        <input class="form-check-input" value="Sedan" type="radio" name="body_type" id="bt_1">
                                        <label class="form-check-label" for="bt_1">
                                            Sedan
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" value="SUV" type="radio" name="body_type" id="bt_2">
                                        <label class="form-check-label" for="bt_2">
                                            SUV
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" value="Hatchback" type="radio" name="body_type" id="bt_3" checked>
                                        <label class="form-check-label" for="bt_3">
                                            Hatchback
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1 fw-bold">Select Transmission type</p>
                                    <div class="form-check">
                                        <input class="form-check-input" value="Automatic" type="radio" name="t_type" id="tt_1">
                                        <label class="form-check-label" for="tt_1">
                                            Automatic
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" value="Manual" type="radio" name="t_type" id="tt_2">
                                        <label class="form-check-label" for="tt_2">
                                            Manual
                                        </label>
                                    </div>
                                </div>                                 
                            </div>
                        </div>


                        <!-- fuel_type -->
                        <div class="col-6 mb-3">
                            <p class="mb-1 fw-bold">Select Fule type</p>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="Petrol" name="fuel_type" id="ft_1" >
                                <label class="form-check-label" for="ft_1">
                                    Petrol
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="Diesel" name="fuel_type" id="ft_2">
                                <label class="form-check-label" for="ft_2">
                                    Diesel
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="CNG" name="fuel_type" id="ft_3">
                                <label class="form-check-label" for="ft_3">
                                    CNG
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" checked type="radio" value="Hybrid" name="fuel_type" id="ft_4">
                                <label class="form-check-label" for="ft_4">
                                    Hybrid
                                </label>
                            </div>
                        </div>  


                        <!-- engine_capacity and mileage -->

                        <div class="mt-1">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input 
                                            name="engine_capacity"
                                            type="number" 
                                            class="form-control" 
                                            value="1500"
                                            id="cartitleInput" 
                                            required
                                            placeholder="engine_capacity">
                                        <label for="cartitleInput">Engine capacity in CC</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input 
                                            name="mileage"
                                            type="number" 
                                            class="form-control" 
                                            value="45000"
                                            id="cartitleInput" 
                                            required
                                            placeholder="mileage">
                                        <label for="cartitleInput">Mileage in KM</label>
                                    </div>
                                </div>                                 
                            </div>
                        </div>                        
                    </section>

                    <!-- Legal & Registration Details -->
                    <section id="legal_detail" class="mb-3 pb-3 border-bottom">
                        <p class="mb-3 fs-4">Legal & Registration Details</p>
                        <div class="form-floating mb-3">
                            <input 
                                name="registration_number"
                                type="text" 
                                class="form-control" 
                                value="DHAKA-D-12-1234"
                                id="registration_number" 
                                required
                                placeholder="username">
                            <label for="registration_number">Registration Number</label>
                        </div>   

                        <select name="registration_year" class="form-select mb-3" aria-label="Default select example">
                            <option value="" hidden>Registration year</option>
                            <option value="1989">1989</option>
                            <option value="2015" selected>2015</option>
                            <option value="2022">2022</option>
                        </select>      
                        
                        
                        <!-- Owner Ship -->
                        <div class="col-6 mb-3">
                            <p class="mb-1 fw-bold">Ownership Status</p>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="First owner" name="ownership_status" id="ot_1" checked>
                                <label class="form-check-label" for="ot_1">
                                    First owner
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="Second owner" name="ownership_status" id="ot_2">
                                <label class="form-check-label" for="ot_2">
                                    Second owner
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="Reconditioned" name="ownership_status" id="ot_3">
                                <label class="form-check-label" for="ot_3">
                                    Reconditioned
                                </label>
                            </div>
                        </div>         
                        
                        <div class="mb-3">
                            <p class="mb-1 fw-bold">Tax Token Validity</p>
                            <input class="form-control mb-3" type="date" name="tax_token_validity" value="2025-12-31" required>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 fw-bold">Fitness Validity</p>
                            <input class="form-control mb-3" type="date" name="fitness_validity" value="2025-12-31" required>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 fw-bold">Insurance Validity</p>
                            <input class="form-control mb-3" type="date" name="insurance_validity" value="2025-12-31" required>
                        </div>                        

                    </section>


                    <!-- Condition & History -->
                    <section id="legal_detail" class="mb-3 pb-3 border-bottom">
                        <p class="mb-3 fs-4">Condition & History</p>
                    

                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="mb-1 fw-bold">Has your car any accident history?</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="Yes" name="accident_history" id="act_1" checked>
                                    <label class="form-check-label" for="act_1">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="No" name="accident_history" id="act_2">
                                    <label class="form-check-label" for="act_2">
                                        No
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 fw-bold">Has your car any service history?</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="Yes" name="service_history" id="rtt_1" checked>
                                    <label class="form-check-label" for="rtt_1">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="No" name="service_history" id="rtt_2">
                                    <label class="form-check-label" for="rtt_2">
                                        No
                                    </label>
                                </div>
                            </div>                                 
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 fw-bold">Condition</p>
                            <div class="form-check">
                                <input class="form-check-input" value="Excellent" type="radio" name="condition" id="cdtt_1" checked>
                                <label class="form-check-label" for="cdtt_1">
                                    Excellent
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" value="Good" type="radio" name="condition" id="cdtt_2">
                                <label class="form-check-label" for="cdtt_2">
                                    Good
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" value="Fair" type="radio" name="condition" id="cdtt_3">
                                <label class="form-check-label" for="cdtt_3">
                                    Fair
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" value="Bad" type="radio" name="condition" id="cdtt_4">
                                <label class="form-check-label" for="cdtt_4">
                                    Bad
                                </label>
                            </div>
                        </div> 
                    </section>
                    
                    <section id="auc_spe_info" class="mb-3 pb-3 border-bottom">
                        <p class="mb-3 fs-4">Auction-Specific Info</p>

                        <div class="form-floating mb-3">
                            <input 
                                name="start_price"
                                type="number" 
                                class="form-control" 
                                id="spInput" 
                                value="1500000"
                                placeholder="email">
                            <label for="spInput">Starting Price in BDT</label>
                        </div>

                        <select name="auction_location" class="form-select mb-3" aria-label="Default select example">
                            <option value="" hidden>Location</option>
                            <option value="Dhaka">Dhaka</option>
                            <option value="Comilla">Comilla</option>
                            <option value="Chitaggong">Chitaggong</option>
                        </select>

                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1 fw-bold">Start Time:</p>
                                <input class="form-control mb-3" type="datetime-local" name="start_time" required>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 fw-bold">End Time:</p>
                                <input class="form-control mb-3" type="datetime-local" name="end_time" required>
                            </div>
                        </div>
                    </section>


                    <!-- SUBMIT -->
                    <div class="text-end">
                        <button type="submit" id="postBtn" class="btn btn-primary mb-3">Post Auction</button>
                    </div>
                </form>      
                
                <div id="postStatus" class="mt-3"></div>
            </div>
            <div class="col-xl-3 col-lg-2 col-md-12 col-sm-12 col-12"></div>
        </div> <!-- row end -->
    </div>  


    <?php
        include ROOT_PATH . "comp/nav/MainFooter.php"; 
    ?>


    <script>
        document.getElementById('createBidPost').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const postBtn = document.getElementById('postBtn');
        const postStatus = document.getElementById('postStatus');
        let post_api_location = "<?php echo $get_api?>/create/create_bid_post.php"

        postBtn.disabled = true;
        postBtn.innerText = "Posting...";

        fetch( post_api_location, {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(data => {
                setTimeout(() => {
                postBtn.disabled = false;
                postBtn.innerText = "Create Bid Post";
                postStatus.innerHTML = `<div class="alert alert-success text-center">Successfully created post</div>`;
               // form.reset();
                }, 3000); // 3-second delay
            });
        });


        // Thumbnail Preview
        function previewThumbnail(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('thumbnail-img');
            if (file) { 
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        }

        function previewCarImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('car-images-preview');
            const addMoreBtn = document.getElementById('add-more-btn');

            // Optional: Clear existing previews
            previewContainer.innerHTML = '';
            previewContainer.appendChild(addMoreBtn);

            Array.from(files).forEach(file => {
                const imgWrapper = document.createElement("div");
                imgWrapper.className = "position-relative d-inline-block";

                const img = document.createElement("img");
                img.src = URL.createObjectURL(file);
                img.className = "img-thumbnail rounded shadow-sm";
                img.style.maxWidth = "200px";
                img.style.maxHeight = "150px";

                // Remove Button
                const removeBtn = document.createElement("button");
                removeBtn.innerHTML = "&times;";
                removeBtn.className = "btn btn-sm btn-danger position-absolute top-0 end-0 m-1";
                removeBtn.onclick = () => imgWrapper.remove(); // Optional: just removes the preview

                imgWrapper.appendChild(img);
                imgWrapper.appendChild(removeBtn);

                previewContainer.insertBefore(imgWrapper, addMoreBtn);
            });
        }
    </script>

    <script src="<?php echo $main_url;?>/lib/js/bootstrap.js"></script>
</body>
</html>