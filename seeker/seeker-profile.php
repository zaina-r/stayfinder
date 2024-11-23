<?php
error_reporting(E_ALL);
ini_set('display_error', 1);

include '../dbconnect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user profile details
$queryUser = "SELECT * FROM user WHERE user_id = ?";
$stmtUser = mysqli_prepare($connect, $queryUser);
mysqli_stmt_bind_param($stmtUser, "i", $userId);
mysqli_stmt_execute($stmtUser);
$userDetails = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUser));

$profilePic = $userDetails['profile_pic'];

    if (empty($profilePic) || !file_exists("../uploads/profile-pic/" . $profilePic)) {
        $profilePic = "default_pro_pic.jpg"; 
    }

// Fetch favorite ads
$queryFavorites = "SELECT listings.*, 
                    (SELECT image_name FROM listing_images WHERE listing_images.ad_id = favorites.ad_id ORDER BY image_id LIMIT 1) AS image_name
                    FROM favorites
                    JOIN listings ON favorites.ad_id = listings.ad_id
                    WHERE favorites.user_id = ? AND listings.approval_status = 'approved'";
$stmtFavorites = mysqli_prepare($connect, $queryFavorites);
mysqli_stmt_bind_param($stmtFavorites, "i", $userId);
mysqli_stmt_execute($stmtFavorites);
$resultFavorites = mysqli_stmt_get_result($stmtFavorites);

// Handle remove favorite action
if (isset($_GET['remove_favorite'])) {
    $adId = $_GET['remove_favorite'];
    $deleteFavorite = "DELETE FROM favorites WHERE user_id = ? AND ad_id = ?";
    $stmtDelete = mysqli_prepare($connect, $deleteFavorite);
    mysqli_stmt_bind_param($stmtDelete, "is", $userId, $adId);
    mysqli_stmt_execute($stmtDelete);
    header("Location: seeker-profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seeker Profile</title>
    <link rel="stylesheet" href="seeker-styles/seeker-profile.css">
    <link rel="stylesheet" href="seeker-styles/header.css">
    <link rel="stylesheet" href="seeker-styles/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
<?php include "header.php"; ?>

    <div class="profile-container">
    <h3>Your Profile</h3>
        <div class="profile-header">
            <img src="../uploads/profile-pic/<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture">
            <div class="user-info-head">
                <h2><?php echo htmlspecialchars($userDetails['first_name'] . ' ' . $userDetails['last_name']); ?></h2>
                <p>Email: <?php echo htmlspecialchars($userDetails['email']); ?></p>
                <p>Contact: <?php echo htmlspecialchars($userDetails['contact_no']); ?></p>
            </div>
            
        </div>

        <div class="profile-buttons">
            <a href="../update-profile.php" class="btn">Update Profile</a>
            <a href="../logout.php" class="btn">Log Out</a>
        </div>

        <div class="favorites-section">
            <h3>Your Favorite Ads</h3>
            <div class="favorites-list">
                <?php if (mysqli_num_rows($resultFavorites) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($resultFavorites)): ?>
                        <div class="favorite-item">
                            <img src="../uploads/listings_images/<?php echo htmlspecialchars($row['image_name'] ); ?>" alt="Ad Image">
                            <div class="favorite-info">
                                <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                                <p>Price: Rs <?php echo number_format($row['price']); ?></p>
                                <a href="moredetails.php?ad_id=<?php echo $row['ad_id']; ?>" class="btn">View Details</a>
                                <a href="seeker-profile.php?remove_favorite=<?php echo $row['ad_id']; ?>" class="btn btn-remove">Remove</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No favorite ads found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
