<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";

// Ensure the user is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit();
}

// Get ad ID
if (!isset($_GET['ad_id'])) {
    echo "Invalid request.";
    exit();
}

$ad_id = $_GET['ad_id'];

// Fetch images for the ad
$sqlImages = "SELECT * FROM listing_images WHERE ad_id = ?";
$stmtImages = mysqli_prepare($connect, $sqlImages);
mysqli_stmt_bind_param($stmtImages, 's', $ad_id);
mysqli_stmt_execute($stmtImages);
$resultImages = mysqli_stmt_get_result($stmtImages);

// Handle image removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_image_id'])) {
    $image_id = $_POST['remove_image_id'];

    // Retrieve image name for deletion
    $sqlGetImageName = "SELECT image_name FROM listing_images WHERE image_id = ?";
    $stmtGetImageName = mysqli_prepare($connect, $sqlGetImageName);
    mysqli_stmt_bind_param($stmtGetImageName, 'i', $image_id);
    mysqli_stmt_execute($stmtGetImageName);
    $resultImage = mysqli_stmt_get_result($stmtGetImageName);
    $imageData = mysqli_fetch_assoc($resultImage);
    $imageName = $imageData['image_name'];

    // Delete image file from server
    $imagePath = "../uploads/listings_images/" . $imageName;
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Delete image record from database
    $sqlDeleteImage = "DELETE FROM listing_images WHERE image_id = ?";
    $stmtDeleteImage = mysqli_prepare($connect, $sqlDeleteImage);
    mysqli_stmt_bind_param($stmtDeleteImage, 'i', $image_id);
    if (mysqli_stmt_execute($stmtDeleteImage)) {
        header("Location: view_images.php?ad_id=$ad_id");
        exit();
    } else {
        echo "Error removing image: " . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Images for Ad <?php echo htmlspecialchars($ad_id); ?></title>
    <link rel="stylesheet" href="admin-css/view-images.css">
    <link rel="stylesheet" href="admin-css/header.css">
    <link rel="stylesheet" href="admin-css/footer.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
    
<?php include "header.php"; ?>
    <div class="container">
        <h1>Images for Ad ID: <?php echo htmlspecialchars($ad_id); ?></h1>
        <div class="images-container">
            <?php if (mysqli_num_rows($resultImages) > 0): ?>
                <?php while ($image = mysqli_fetch_assoc($resultImages)): ?>
                    <div class="image-card">
                        <img src="../uploads/listings_images/<?php echo htmlspecialchars($image['image_name']); ?>" alt="Ad Image">
                        <form method="post" action="">
                            <input type="hidden" name="remove_image_id" value="<?php echo $image['image_id']; ?>">
                            <button type="submit" class="remove-button">Remove Image</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No images found for this ad.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include "footer.php"; ?>   
    <script src="admin-js/dropdown.js"></script>
</body>
</html>

<?php 
mysqli_close($connect);
?>
