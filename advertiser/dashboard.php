<?php
session_start();
include "../dbconnect.php";

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'advertiser') {
    header("Location: ../login-registration.php");
    exit();
}

$userId = $_SESSION['user_id'];


// Check if availability status is being updated
if (isset($_POST['update_availability'])) {
    $adId = (int)$_POST['ad_id'];
    $newAvailability = ($_POST['availability'] === 'Available') ? 'Available' : 'Not Available';
    $sqlUpdateAvailability = "UPDATE listings SET availability = ? WHERE ad_id = ? AND advertiser_id = ?";
    $stmtUpdateAvailability = mysqli_prepare($connect, $sqlUpdateAvailability);
    if ($stmtUpdateAvailability) {
        mysqli_stmt_bind_param($stmtUpdateAvailability, "sii", $newAvailability, $adId, $userId);
        mysqli_stmt_execute($stmtUpdateAvailability);
        mysqli_stmt_close($stmtUpdateAvailability);
    }
    header("Location:dashboard.php?message=Availability updated successfully!");
    exit();
}

// Fetch advertiser profile details
$sqlProfile = "SELECT * FROM user WHERE user_id = ?";
$stmtProfile = mysqli_prepare($connect, $sqlProfile);
$profile = null;
if ($stmtProfile) {
    mysqli_stmt_bind_param($stmtProfile, "i", $userId);
    mysqli_stmt_execute($stmtProfile);
    $resultProfile = mysqli_stmt_get_result($stmtProfile);
    $profile = mysqli_fetch_assoc($resultProfile);

    $profilePic = $profile['profile_pic'];

    if (empty($profilePic) || !file_exists("../uploads/profile-pic/" . $profilePic)) {
        $profilePic = "default_pro_pic.jpg"; 
    }

    mysqli_stmt_close($stmtProfile);
}

// Fetch subscription details
$sqlSubscription = "
    SELECT subscription_plans.plan_type, subscription_plans.plan_price, advertiser_plan.end_date 
    FROM advertiser_plan 
    JOIN subscription_plans ON advertiser_plan.plan_id = subscription_plans.plan_id
    WHERE advertiser_plan.advertiser_id = ?";
$stmtSubscription = mysqli_prepare($connect, $sqlSubscription);
$subscription = null;
if ($stmtSubscription) {
    mysqli_stmt_bind_param($stmtSubscription, "i", $userId);
    mysqli_stmt_execute($stmtSubscription);
    $resultSubscription = mysqli_stmt_get_result($stmtSubscription);
    $subscription = mysqli_fetch_assoc($resultSubscription);
    mysqli_stmt_close($stmtSubscription);
}

// Fetch all ads posted by the advertiser
$sqlAds = "SELECT * FROM listings 
JOIN district ON listings.district_id = district.district_id 
JOIN category ON listings.category_id = category.category_id 
JOIN nearestuni ON listings.nearestuni_id = nearestuni.uni_id
JOIN user ON listings.advertiser_id = user.user_id WHERE user_id = ?";
$stmtAds = mysqli_prepare($connect, $sqlAds);
$resultAds = null;
if ($stmtAds) {
    mysqli_stmt_bind_param($stmtAds, "i", $userId);
    mysqli_stmt_execute($stmtAds);
    $resultAds = mysqli_stmt_get_result($stmtAds);
    mysqli_stmt_close($stmtAds);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ADVERTISER DASHBOARD</title>
    <link rel="stylesheet" href="advertiser-style/dashboard.css">
    <link rel="stylesheet" href="advertiser-style/header.css">
    <link rel="stylesheet" href="../main-css/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
<?php include "header.php" ?>
<div class="div-container">
<div class="dashboard-container">
    <h1>ADVERTISER DASHBOARD</h1>

    <!-- Profile Details Section -->
    <section class="profile-section">
        <h2>Profile Details</h2>
        <div class="adevertiser-info">
            <div class="profile-picture-container">
                <img src="../uploads/profile-pic/<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="profile-picture">
            </div>
            <div class="user-details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars(($profile['first_name'] ?? '') . " " . ($profile['last_name'] ?? '')); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email'] ?? ''); ?></p>
                <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($profile['contact_no'] ?? ''); ?></p>
            </div>
        </div>
    </section>

    <!-- Action Buttons -->
    <section class="action-buttons">
        <button onclick="window.location.href='postad.php'">Post Ad</button>
        <button onclick="window.location.href='../update-profile.php'">Update Profile</button>
        <button onclick="window.location.href='../logout.php'">Log Out</button>
    </section>

    <!-- Subscription Details Section -->
    <section class="subscription-section">
        <h2>Your Subscription Details</h2>
        <div class="subscription-details">
            <?php if ($subscription): ?>
                <p><strong>Plan Type:</strong> <?php echo htmlspecialchars($subscription['plan_type']); ?></p>
                <p><strong>Plan Price:</strong> Rs <?php echo number_format($subscription['plan_price'], 2); ?></p>
                <p><strong>Subscription Expired Date:</strong> <?php echo htmlspecialchars($subscription['end_date']); ?></p>
            <?php else: ?>
                <p>No active subscription found.</p>
            <?php endif; ?>
        </div>
        <div class="action-buttons">
            <button onclick="window.location.href='subscription-plans.php'">View subscription plans</button>
        </div>
    </section>

    <!-- Posted Ads Section -->
    <section class="ads-section">
        <h2>Your Posted Ads</h2>
        <?php if ($resultAds && mysqli_num_rows($resultAds) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        
                        <th>Price</th>
                        <th>District</th>
                        <th>Availability</th>
                        <th>Approval Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($ad = mysqli_fetch_assoc($resultAds)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ad['title']); ?></td>
                            
                            <td>Rs <?php echo number_format($ad['price']); ?></td>
                            <td><?php echo htmlspecialchars($ad['district_name']); ?></td>
                            <td>
                                <form action="dashboard.php" method="post">
                                    <input type="hidden" name="ad_id" value="<?php echo $ad['ad_id']; ?>">
                                    <select name="availability" onchange="this.form.submit()">
                                        <option value="Available" <?php echo $ad['availability'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                                        <option value="Not Available" <?php echo $ad['availability'] == 'Not Available' ? 'selected' : ''; ?>>Not Available</option>
                                    </select>
                                    <input type="hidden" name="update_availability" value="1">
                                </form>
                            </td>
                            <td><?php echo htmlspecialchars($ad['approval_status']); ?></td> 
                            <td>
                                <a href="../seeker/moredetails.php?ad_id=<?php echo $ad['ad_id']; ?>" class="action-btn">View More</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not posted any ads yet.</p>
        <?php endif; ?>
    </section>
</div>
</div>
</body>
</html>
<?php include "footer.php" ?>   
<script src="../main-js/dropdown.js"></script>
<script src="../main-js/popup.js"></script>
<?php
mysqli_close($connect);
?>
