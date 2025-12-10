<?php
    session_start();
    include '../../config.php';
    auth_check();

    $auction_id = $_GET['id'] ?? 0;

    // Fetch car + auction info
    $stmt = $pdo->prepare("
        SELECT c.*, a.start_price, a.auction_location, a.start_time, a.end_time, a.id as auction_id
        FROM cars c 
        JOIN auctions a ON c.id = a.car_id
        WHERE a.id = ?
    ");
    $stmt->execute([$auction_id]);
    $auction = $stmt->fetch();

    if (!$auction || $auction['user_id'] !== $user['id']) {
        exit("Something went wrong");
    }

    $img_stmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id=?");
    $img_stmt->execute([$auction['id']]);
    $car_images = $img_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Auction</title>
    
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
                <p class="mb-4 text-center fs-2">Edit Acution</p>

                    <form method="POST" id="updateBidPost" enctype="multipart/form-data">
                        <!-- Basic Information -->
                        <section id="basic_information" class="mb-3 pb-3 border-bottom">
                            <p class="mb-3 fs-4">Basic Information</p>
                            <div class="form-floating mb-3">
                                <input 
                                    name="title"
                                    type="text" 
                                    class="form-control" 
                                    value="<?php echo htmlspecialchars($auction['title']); ?>"
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
                                    placeholder="Description"><?php echo htmlspecialchars($auction['des']); ?></textarea>
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
                                    onchange="previewThumbnail(event)">
                                <label for="thumbnail" class="btn btn-app btn-primary">Choose Thumbnail</label>

                                <!-- Thumbnail Preview -->
                                <div id="thumbnail-preview" class="mt-3">
                                    <?php if (!empty($auction['image_url'])): ?>
                                        <img src="<?php echo $main_url.'/uploads/thumbnails/'.$auction['image_url']; ?>" 
                                            id="thumbnail-img" 
                                            class="img-fluid rounded shadow-sm" 
                                            style="max-width: 100%;" 
                                            alt="Thumbnail Preview">
                                    <?php else: ?>
                                        <img src="" id="thumbnail-img" 
                                            class="img-fluid rounded shadow-sm d-none" 
                                            style="max-width: 100%;" 
                                            alt="Thumbnail Preview">
                                    <?php endif; ?>
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
                                    <?php foreach ($car_images as $img): ?>
                                        <div class="position-relative d-inline-block" id="img-<?php echo $img['id']; ?>">
                                            <img src="<?php echo $main_url.'/uploads/car_images/'.$img['file_name']; ?>" 
                                                class="img-thumbnail rounded shadow-sm" 
                                                style="max-width:200px; max-height:150px;">
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-image-btn"
                                                    data-id="<?php echo $img['id']; ?>">
                                                Remove
                                            </button>
                                        </div>
                                    <?php endforeach; ?>

                                    
                                    <!-- Add More Button (inline with images) -->
                                    <label for="car-images" id="add-more-btn" 
                                        class="d-flex align-items-center justify-content-center" 
                                        style="width: 200px; height: 200px; border-style: dashed;">+ Add</label>
                                </div>
                            </div>
                        </section>

                        <!-- Technical Specifications -->
                        <section id="technical_information" class="mb-3 pb-3 border-bottom">
                            <p class="mb-3 fs-4">Technical Specifications</p>
                            <select name="make" class="form-select mb-3">
                                <option value="" hidden>Make</option>
                                <option value="Toyota" <?php echo ($auction['make']=="Toyota" ? "selected":""); ?>>Toyota</option>
                                <option value="Honda" <?php echo ($auction['make']=="Honda" ? "selected":""); ?>>Honda</option>
                                <option value="Nissan" <?php echo ($auction['make']=="Nissan" ? "selected":""); ?>>Nissan</option>
                            </select>

                            <div class="form-floating mb-3">
                                <input 
                                    name="model"
                                    type="text" 
                                    class="form-control" 
                                    value="<?php echo htmlspecialchars($auction['model']); ?>"
                                    required>
                                <label>Model name</label>
                            </div>

                            <select name="year" class="form-select mb-3">
                                <option value="" hidden>Year</option>
                                <?php for($y=1980;$y<=date('Y');$y++): ?>
                                    <option value="<?php echo $y;?>" <?php echo ($auction['year']==$y?"selected":""); ?>><?php echo $y;?></option>
                                <?php endfor; ?>
                            </select>

                            <!-- Body & Transmission -->
                            <div class="row">
                                <div class="col-6">
                                    <p class="fw-bold">Body type</p>
                                    <?php foreach(['Sedan','SUV','Hatchback'] as $bt): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="body_type" value="<?php echo $bt;?>" 
                                                <?php echo ($auction['body_type']==$bt?"checked":""); ?>>
                                            <label class="form-check-label"><?php echo $bt;?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="col-6">
                                    <p class="fw-bold">Transmission</p>
                                    <?php foreach(['Automatic','Manual'] as $tt): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="t_type" value="<?php echo $tt;?>" 
                                                <?php echo ($auction['t_type']==$tt?"checked":""); ?>>
                                            <label class="form-check-label"><?php echo $tt;?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Fuel -->
                            <p class="fw-bold mt-3">Fuel Type</p>
                            <?php foreach(['Petrol','Diesel','CNG','Hybrid'] as $ft): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fuel_type" value="<?php echo $ft;?>" 
                                        <?php echo ($auction['fuel_type']==$ft?"checked":""); ?>>
                                    <label class="form-check-label"><?php echo $ft;?></label>
                                </div>
                            <?php endforeach; ?>

                            <!-- Engine & Mileage -->
                            <div class="row mt-3">
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input name="engine_capacity" type="number" class="form-control" 
                                            value="<?php echo $auction['engine_capacity']; ?>">
                                        <label>Engine capacity (CC)</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input name="mileage" type="number" class="form-control" 
                                            value="<?php echo $auction['mileage']; ?>">
                                        <label>Mileage (KM)</label>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Legal -->
                        <section id="legal_detail" class="mb-3 pb-3 border-bottom">
                            <p class="fs-4">Legal & Registration</p>
                            <div class="form-floating mb-3">
                                <input name="registration_number" type="text" class="form-control" 
                                    value="<?php echo htmlspecialchars($auction['registration_number']); ?>">
                                <label>Registration Number</label>
                            </div>

                            <select name="registration_year" class="form-select mb-3">
                                <option value="" hidden>Registration Year</option>
                                <?php for($y=1980;$y<=date('Y');$y++): ?>
                                    <option value="<?php echo $y;?>" <?php echo ($auction['registration_year']==$y?"selected":""); ?>><?php echo $y;?></option>
                                <?php endfor; ?>
                            </select>

                            <p class="fw-bold">Ownership</p>
                            <?php foreach(['First owner','Second owner','Reconditioned'] as $os): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ownership_status" value="<?php echo $os;?>" 
                                        <?php echo ($auction['ownership_status']==$os?"checked":""); ?>>
                                    <label class="form-check-label"><?php echo $os;?></label>
                                </div>
                            <?php endforeach; ?>

                            <p class="fw-bold mt-3">Tax Token Validity</p>
                            <input type="date" class="form-control mb-3" name="tax_token_validity" value="<?php echo $auction['tax_token_validity']; ?>">

                            <p class="fw-bold">Fitness Validity</p>
                            <input type="date" class="form-control mb-3" name="fitness_validity" value="<?php echo $auction['fitness_validity']; ?>">

                            <p class="fw-bold">Insurance Validity</p>
                            <input type="date" class="form-control mb-3" name="insurance_validity" value="<?php echo $auction['insurance_validity']; ?>">
                        </section>

                        <!-- Condition & History -->
                        <section class="mb-3 pb-3 border-bottom">
                            <p class="fs-4">Condition & History</p>
                            <div class="row">
                                <div class="col-6">
                                    <p>Accident History?</p>
                                    <?php foreach(['Yes','No'] as $ah): ?>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="accident_history" value="<?php echo $ah;?>" 
                                                <?php echo ($auction['accident_history']==$ah?"checked":""); ?>>
                                            <label class="form-check-label"><?php echo $ah;?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="col-6">
                                    <p>Service History?</p>
                                    <?php foreach(['Yes','No'] as $sh): ?>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="service_history" value="<?php echo $sh;?>" 
                                                <?php echo ($auction['service_history']==$sh?"checked":""); ?>>
                                            <label class="form-check-label"><?php echo $sh;?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <p class="fw-bold mt-3">Condition</p>
                            <?php foreach(['Excellent','Good','Fair','Bad'] as $cond): ?>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="condition" value="<?php echo $cond;?>" 
                                        <?php echo ($auction['car_condition']==$cond?"checked":""); ?>>
                                    <label class="form-check-label"><?php echo $cond;?></label>
                                </div>
                            <?php endforeach; ?>
                        </section>

                        <!-- Auction Info -->
                        <section id="auc_spe_info" class="mb-3 pb-3 border-bottom">
                            <p class="fs-4">Auction-Specific Info</p>
                            <div class="form-floating mb-3">
                                <input name="start_price" type="number" class="form-control" 
                                    value="<?php echo $auction['start_price']; ?>">
                                <label>Starting Price</label>
                            </div>

                            <select name="auction_location" class="form-select mb-3">
                                <option value="" hidden>Location</option>
                                <?php foreach(['Dhaka','Comilla','Chitaggong'] as $loc): ?>
                                    <option value="<?php echo $loc;?>" <?php echo ($auction['auction_location']==$loc?"selected":""); ?>><?php echo $loc;?></option>
                                <?php endforeach; ?>
                            </select>

                            <div class="row">
                                <div class="col-6">
                                    <p>Start Time:</p>
                                    <input type="datetime-local" class="form-control mb-3" name="start_time" 
                                        value="<?php echo date('Y-m-d\TH:i',strtotime($auction['start_time'])); ?>">
                                </div>
                                <div class="col-6">
                                    <p>End Time:</p>
                                    <input type="datetime-local" class="form-control mb-3" name="end_time" 
                                        value="<?php echo date('Y-m-d\TH:i',strtotime($auction['end_time'])); ?>">
                                </div>
                            </div>
                        </section>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary mb-3">Update Auction</button>
                        </div>
                    </form>

                    <div id="postStatus" class="mt-3"></div>
                    </div>                
                <div id="postStatus" class="mt-3"></div>
            </div>
            <div class="col-xl-3 col-lg-2 col-md-12 col-sm-12 col-12"></div>
        </div> <!-- row end -->
    </div>  

    <?php
        include ROOT_PATH . "comp/nav/MainFooter.php"; 
    ?>

    <script>
        document.getElementById('updateBidPost').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            let apiUrl = "<?php echo $get_api;?>/update/update_bid_post.php?id=<?php echo $auction_id; ?>";

            fetch(apiUrl, { method:'POST', body:formData })
                .then(res=>res.text())
                .then(data=>{
                document.getElementById('postStatus').innerHTML = `<div class="alert alert-info">${data}</div>`;
                });
        });


        // Handle delete image buttons
        document.querySelectorAll('.delete-image-btn').forEach(btn => {
            btn.onclick = function() {
                const imageId = this.dataset.id;

                if (!confirm("Are you sure you want to delete this image?")) return;

                fetch("<?php echo $get_api;?>/delete/delete_car_image.php?id=" + imageId)
                    .then(res => res.text())
                    .then(data => {
                        if (data.trim() === "success") {
                            document.getElementById("img-" + imageId).remove();
                        } else {
                            alert("Error deleting image: " + data);
                        }
                    })
                    .catch(err => alert("Request failed: " + err));
            };
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

        // Car Images Preview
        function previewCarImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('car-images-preview');
            const addMoreBtn = document.getElementById('add-more-btn');

            // Do NOT clear existing DB images, only add new previews
            Array.from(files).forEach(file => {
                const imgWrapper = document.createElement("div");
                imgWrapper.className = "position-relative d-inline-block";

                const img = document.createElement("img");
                img.src = URL.createObjectURL(file);
                img.className = "img-thumbnail rounded shadow-sm";
                img.style.maxWidth = "200px";
                img.style.maxHeight = "150px";

                const removeBtn = document.createElement("button");
                removeBtn.innerHTML = "&times;";
                removeBtn.className = "btn btn-sm btn-danger position-absolute top-0 end-0 m-1";
                removeBtn.onclick = () => imgWrapper.remove();

                imgWrapper.appendChild(img);
                imgWrapper.appendChild(removeBtn);

                previewContainer.insertBefore(imgWrapper, addMoreBtn);
            });
        }
    </script>
</body>
</html>
