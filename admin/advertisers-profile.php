<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login-registration.php");
    exit();
}

// Fetch advertiser details and their corresponding plan details
$query = "
    SELECT u.*, ap.plan_id, ap.end_date, sp.plan_type, sp.plan_price
    FROM user u
    LEFT JOIN advertiser_plan ap ON u.user_id = ap.advertiser_id
    LEFT JOIN subscription_plans sp ON ap.plan_id = sp.plan_id
    WHERE u.user_type = 'advertiser'
";
$result = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advertisers' Profiles</title>
    <link rel="stylesheet" href="admin-css/advertiser-profile.css">
    <link rel="stylesheet" href="admin-css/header.css">
    <link rel="stylesheet" href="admin-css/footer.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo-nav">
            <a href="dashboard.php"><img src="../src-image/logo-white.png" alt="site-logo" class="logo"></a>
            <button onclick="window.location.href='dashboard.php'" class="head-button" type="button"><span class="span-class"></span>Admin Dashboard</button>
        </div>
        
        <div class="user-info">
        <button onclick="window.location.href='../logout.php'" class="head-button" type="button"><span class="span-class"></span>Log Out</button>
        </div>
    </div>
</header>
    <div class="container">
        <h1>Advertisers Details</h1>

        <table class="advertiser-table">
            <tr>
                <th>Advertiser ID</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>Contact No</th>
                <th>Subscription Plan</th>
                <th>Plan Price (Rs)</th>
                <th>Plan End Date</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                    <td><?php echo !empty($row['plan_type']) ? htmlspecialchars($row['plan_type']) : 'No Plan'; ?></td>
                    <td><?php echo !empty($row['plan_price']) ? number_format($row['plan_price']) : '-'; ?></td>
                    <td><?php echo !empty($row['end_date']) ? htmlspecialchars($row['end_date']) : '-'; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
<?php include "footer.php"; ?> 
</html>
