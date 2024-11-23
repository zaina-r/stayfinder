<?php

session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "../dbconnect.php";

// Check if the user is logged in as an advertiser
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'advertiser') {
    header("Location: ../login-registration.php?login=You have to login as an advertiser.");
    exit();
}

$advertiser_id = $_SESSION['user_id'];

// Verify if the owner has an active plan
$query = "SELECT * FROM advertiser_plan WHERE advertiser_id = ? LIMIT 1";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, "i", $advertiser_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: check_plan.php?buy_plan=To_post_an_ad_you_have_to_buy_a_plan");
    exit();
}

$data = mysqli_fetch_assoc($result);
$exp_date = $data['end_date'];
$current_date = date("Y-m-d");

if (strtotime($current_date) > strtotime($exp_date)) {
    header("Location: check_plan.php?owner_plan=expired");
    exit();
}

$errors = [];

if (isset($_POST['post_add'])) {
    $ad_id = uniqid();
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = (float) $_POST['price'];
    $category_id = (int) $_POST['category_id'];
    $district_id = (int) $_POST['district_id'];
    $nearestuni_id = (int) $_POST['nearestuni_id'];
    $address_no = $_POST['address_no'];
    $address_street = $_POST['address_street'];
    $address_city = $_POST['address_city'];
    $availability = "Available";
    $approval_status = "pending";

    // Prepared statement for inserting listing data
    $insert_q_data = "INSERT INTO listings (ad_id, title, description, address_no, address_street, address_city, price, availability, approval_status, advertiser_id, category_id, district_id, nearestuni_id) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connect, $insert_q_data);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssdssiiii", $ad_id, $title, $description, $address_no, $address_street, $address_city, $price, $availability, $approval_status, $advertiser_id, $category_id, $district_id, $nearestuni_id);
        if (!mysqli_stmt_execute($stmt)) {
            $errors[] = "Error in inserting listing data: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $errors[] = "Error preparing statement for listing insertion: " . mysqli_error($connect);
    }

    // Image handling
    $images = [];
    foreach ($_FILES['images']['name'] as $key => $file_name) {
        if ($_FILES['images']['size'][$key] > 0 && $_FILES['images']['error'][$key] == 0) {
            $images[] = [
                'name' => $file_name,
                'tmp_name' => $_FILES['images']['tmp_name'][$key],
                'type' => $_FILES['images']['type'][$key],
                'error' => $_FILES['images']['error'][$key],
                'size' => $_FILES['images']['size'][$key],
            ];
        }
    }

    // Process only if there are no errors with the ad insertion
    if (count($errors) === 0) {
        foreach ($images as $image) {
            $file_name = $image['name'];
            $file_tmp = $image['tmp_name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $unique_file_name = uniqid() . '.' . $file_extension;

            if (!in_array($file_extension, ['png', 'jpg', 'jpeg'])) {
                $errors[] = "Invalid file type for image $file_name.";
                continue;
            }

            $save_dir = "../uploads/listings_images/" . $unique_file_name;

            // Move the file and insert its path into `listing_images`
            if (move_uploaded_file($file_tmp, $save_dir)) {
                $insert_img = "INSERT INTO listing_images (ad_id, image_name) VALUES (?, ?)";
                $stmt = mysqli_prepare($connect, $insert_img);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ss", $ad_id, $unique_file_name);
                    if (!mysqli_stmt_execute($stmt)) {
                        $errors[] = "Error in inserting image $unique_file_name: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $errors[] = "Error preparing statement for image insertion: " . mysqli_error($connect);
                }
            } else {
                $errors[] = "Error uploading image $file_name.";
            }
        }
    }

    // Redirect on successful submission
    if (count($errors) == 0) {
        header("Location: dashboard.php?upload=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Ad</title>
    <link rel="stylesheet" href="advertiser-style/postad.css">
    <link rel="stylesheet" href="advertiser-style/header.css">
    <link rel="stylesheet" href="../main-css/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<?php include "header.php" ?>
<body>
    <div class="form-container">
        <h1>Post New Ad</h1>

        <!-- Display any errors -->
        <?php if (count($errors) > 0): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="postad.php" method="post" enctype="multipart/form-data" onsubmit="return validateFiles();">
            <div>
                <!-- Category Selection -->
                <label for="category_id">Category</label>
                <select name="category_id" required>
                    <option value="" disabled selected>Select Category</option>
                    <?php
                    $category_query = "SELECT * FROM category";
                    $category_result = mysqli_query($connect, $category_query);
                    while ($row = mysqli_fetch_assoc($category_result)) {
                        echo "<option value='".$row['category_id']."'>".$row['category_name']."</option>";
                    }
                    ?>
                </select>

                <!-- District Selection -->
                <label for="district_id">District</label>
                <select name="district_id" required>
                    <option value="" disabled selected>Select District</option>
                    <?php
                    $location_query = "SELECT * FROM district";
                    $location_result = mysqli_query($connect, $location_query);
                    while ($row = mysqli_fetch_assoc($location_result)) {
                        echo "<option value='".$row['district_id']."'>".$row['district_name']."</option>";
                    }
                    ?>
                </select>

                <!-- NearestUni Selection -->
                <label for="nearestuni_id">Nearest University</label>
                <select name="nearestuni_id" required>
                    <option value="" disabled selected>Select Nearest University</option>
                    <?php
                    $location_query = "SELECT * FROM nearestuni";
                    $location_result = mysqli_query($connect, $location_query);
                    while ($row = mysqli_fetch_assoc($location_result)) {
                        echo "<option value='".$row['uni_id']."'>".$row['uni_name']."</option>";
                    }
                    ?>
                </select>

                <!-- Address Fields -->
                <label for="address_no">Address No</label>
                <input type="text" name="address_no" placeholder="Enter Address No" required>

                <label for="address_street">Address Street</label>
                <input type="text" name="address_street" placeholder="Enter Address Street" required>

                <label for="address_city">Address City</label>
                <input type="text" name="address_city" placeholder="Enter City" required>

                <label for="title">Title</label>
                <input type="text" name="title" placeholder="Enter Title" required>
            </div>

           <div> <!-- Other Listing Details -->
                <label for="description">Description</label>
                <textarea name="description" placeholder="Add description" required></textarea>

                <label for="price">Price</label>
                <input type="number" name="price" placeholder="Enter the price" required>

                <!-- Image Upload (max 5) -->
                <label for="images">Add Pictures (Max 5)</label>
                <input type="file" name="images[]" accept="image/*" >
                <input type="file" name="images[]" accept="image/*" >
                <input type="file" name="images[]" accept="image/*" >
                <input type="file" name="images[]" accept="image/*" >
                <input type="file" name="images[]" accept="image/*" >
                <br><small>(Minimum 1 image, Maximum 5 images)</small>

                <!-- Submit Button -->
                <button type="submit" name="post_add">Post Ad</button>
            </div>
        </form>
    </div>
<?php include "footer.php" ?>   
<script src="../main-js/dropdown.js"></script>
<script src="../main-js/popup.js"></script>
</body>
</html>
