<?php

session_start();

include '../dbconnect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login-registration.php");
    exit();
}

// Fetch Advertisers Count
$advertisers_count_query = "SELECT COUNT(*) AS count FROM user WHERE user_type = ?";
$stmt = mysqli_prepare($connect, $advertisers_count_query);
$user_type = 'advertiser';
mysqli_stmt_bind_param($stmt, "s", $user_type);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $advertisers_count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Fetch Seekers Count
$seekers_count_query = "SELECT COUNT(*) AS count FROM user WHERE user_type = ?";
$stmt = mysqli_prepare($connect, $seekers_count_query);
$user_type = 'seeker';
mysqli_stmt_bind_param($stmt, "s", $user_type);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $seekers_count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Fetch Total Earnings
$total_earnings_query = "SELECT SUM(payment_amount) AS total FROM subscription_payment";
$stmt = mysqli_prepare($connect, $total_earnings_query);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total_earnings);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-css/dashboard.css">
    <link rel="stylesheet" href="admin-css/header.css">
    <link rel="stylesheet" href="admin-css/footer.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>

<?php include "header.php"; ?>

    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        
        <hr>
        
        <div class="stats-section">
            <div class="stat-box">
                <h2>Advertisers Count</h2>
                <p><?php echo sprintf("%02d", $advertisers_count); ?></p>
            </div>
            <div class="stat-box">
                <h2>Seekers Count</h2>
                <p><?php echo sprintf("%02d", $seekers_count); ?></p>
            </div>
            <div class="stat-box">
                <h2>Total Earnings</h2>
                <p>Rs <?php echo number_format($total_earnings, 2); ?></p>
            </div>
        </div>
        <hr>
        <div class="links-section">
            <a href="approve_post.php" class="link-box">Pending Approve Posts & Posted Ads</a>
            <a href="add_new_uni.php" class="link-box">Add University</a>
            <a href="add_new_category.php" class="link-box">Add Category</a>
            <a href="seeker_details.php" class="link-box">View Seekers Details</a>
            <a href="advertisers-profile.php" class="link-box">View Advertisers Details</a>
            <a href="view-messages.php" class="link-box">Messages</a>
        </div>
    </div>
</body>
    <?php include "footer.php"; ?>   
    <script src="admin-js/dropdown.js"></script>
</html>
