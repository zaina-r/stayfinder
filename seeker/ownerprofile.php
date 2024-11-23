<?php
error_reporting(E_ALL);
ini_set('display_error', 1);

include '../dbconnect.php';
session_start();

if (!isset($_GET['user_id'])) {
    echo "Invalid owner ID.";
    exit;
}

$ownerId = (int)$_GET['user_id'];

// Fetch owner details
$ownerQuery = "SELECT first_name, last_name, email, contact_no, profile_pic FROM user WHERE user_id = ?";
if ($stmt = mysqli_prepare($connect, $ownerQuery)) {
    mysqli_stmt_bind_param($stmt, "i", $ownerId);
    mysqli_stmt_execute($stmt);
    $ownerResult = mysqli_stmt_get_result($stmt);
    $ownerDetails = mysqli_fetch_assoc($ownerResult);

    $profilePic_owner = $ownerDetails['profile_pic'];

    if (empty($profilePic_owner) || !file_exists("../uploads/profile-pic/" .  $profilePic_owner)) {
        $profilePic_owner = "default_pro_pic.jpg"; 
    }

    if (!$ownerDetails) {
        echo "Owner not found.";
        exit;
    }
} else {
    echo "Error fetching owner details: " . mysqli_error($connect);
    exit;
}

// Fetch approved ads by the owner
$adsQuery = "SELECT listings.*, 
             (SELECT image_name FROM listing_images WHERE listing_images.ad_id = listings.ad_id ORDER BY image_id LIMIT 1) AS image_name
             FROM listings 
             WHERE advertiser_id = ? AND approval_status = 'approved'";
if ($stmt = mysqli_prepare($connect, $adsQuery)) {
    mysqli_stmt_bind_param($stmt, "i", $ownerId);
    mysqli_stmt_execute($stmt);
    $adsResult = mysqli_stmt_get_result($stmt);
} else {
    echo "Error fetching ads: " . mysqli_error($connect);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Profile</title>
    <link rel="stylesheet" href="seeker-styles/ownerprofile.css">
    <link rel="stylesheet" href="seeker-styles/header.css">
    <link rel="stylesheet" href="seeker-styles/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
    <?php include "header.php"; ?>
    <main>
        <div class="owner-profile-card">
            <div class="profile-picture">
                <img src="../uploads/profile-pic/<?php echo htmlspecialchars($profilePic_owner); ?>" alt="Profile Picture">
            </div>
            <div class="owner-details">
                <h2><?php echo htmlspecialchars($ownerDetails['first_name'] . ' ' . $ownerDetails['last_name']); ?></h2>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($ownerDetails['email']); ?></p>
                <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($ownerDetails['contact_no']); ?></p>
            </div>
        </div>

        <div class="owner-ads-section">
            <h3>Ads Posted by <?php echo htmlspecialchars($ownerDetails['first_name']); ?></h3>
            <?php if (mysqli_num_rows($adsResult) > 0): ?>
                <div class="ads-list">
                    <?php while ($ad = mysqli_fetch_assoc($adsResult)): ?>
                        <div class="ad-card">
                            <div class="ad-image">
                                <img src="../uploads/listings_images/<?php echo htmlspecialchars($ad['image_name'] ?? 'default.jpg'); ?>" alt="Ad Image">
                            </div>
                            <div class="ad-details">
                                <h4><?php echo htmlspecialchars($ad['title']); ?></h4>
                                <p><strong>Features: </strong><?php echo htmlspecialchars($ad['description']); ?></p>
                                <p><strong>Price: </strong> Rs <?php echo number_format($ad['price']); ?> / Monthly</p>
                                <a href="moredetails.php?ad_id=<?php echo $ad['ad_id']; ?>" class="view-details-btn">View Details</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No approved ads found for this owner.</p>
            <?php endif; ?>
        </div>
    </main>
     <!-- Modal for Popup -->
     <div id="loginModal" class="modal-popup" style="display: none;">
        <div class="modal-content">
            <div class="message"><p>You have to log in as an Advertiser to post an ad.</p></div>
            <button class="cancel-btn" onclick="closeLoginPopup()">Cancel</button>
            <button class="login-btn" onclick="redirectToLogin()">Log In</button>    
        </div>
    </div>
    <?php include "footer.php"; ?>
    <script src="seeker-js/dropdown.js"></script>
    <script src="seeker-js/popup.js"></script>
</body>
</html>
