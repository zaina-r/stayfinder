<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'advertiser') {
    header("Location: ../login-registration.php");
    exit();
}

$success = false;

$merchant_id = 1228547;
$currency = "LKR";
$merchant_secret = "ODAwMjE4Mzc5Mjk3NTgzMTI1MDI2Mjc5NDQzNjUzNTkyMTUwMDUx";

if (isset($_GET['plan_id']) && !empty($_GET['plan_id'])) {
    $plan_id = (int)$_GET['plan_id'];
    $stmt = mysqli_prepare($connect, "SELECT * FROM subscription_plans WHERE plan_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $plan_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $price = $row['plan_price'];
        $plane_name = $row['plan_type'];

        $hash = strtoupper(
            md5(
                $merchant_id .
                $plan_id .
                number_format($price, 2, '.', '') .
                $currency .
                strtoupper(md5($merchant_secret))
            )
        );
    }
    mysqli_stmt_close($stmt);
}

// Fetch current advertiser's details
$user_id = (int)$_SESSION['user_id'];
$stmt = mysqli_prepare($connect, "SELECT * FROM user WHERE user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user_data = mysqli_fetch_assoc($user_result);
    $first_Name = $user_data['first_name'];
    $last_Name = $user_data['last_name'];
    $email = $user_data['email'];
    $contactNumber = $user_data['contact_no'];
}
mysqli_stmt_close($stmt);

if (isset($_GET['payment']) && $_GET['payment'] == "success") {
    $firstname = $_GET['firstname'];
    $lastname = $_GET['lastname'];
    $email = $_GET['email'];
    $contactNumber = $_GET['contactNumber'];
    $addressNo = $_GET['addressNo'];
    $addressStreet = $_GET['addressStreet'];
    $addressCity = $_GET['addressCity'];
    $amount = $_GET['amount'];
    $advertiser_id = (int)$_SESSION['user_id'];
    $plan_id = (int)$_GET['plan_id'];

    $stmt = mysqli_prepare($connect, "INSERT INTO subscription_payment(payment_date, advertiser_id, first_name, last_name, contact_no, address_no, address_street, address_city, payment_amount, email) VALUES (CURRENT_DATE, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'issssssss', $advertiser_id, $firstname, $lastname, $contactNumber, $addressNo, $addressStreet, $addressCity, $amount, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($connect, "SELECT * FROM advertiser_plan WHERE advertiser_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $advertiser_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $date = new DateTime();
    if ($plan_id == 1) {
        $date->modify('+1 months');
    } elseif ($plan_id == 2) {
        $date->modify('+6 months');
    } else {
        $date->modify('+12 months');
    }
    $end_date = $date->format('Y-m-d');

    if (mysqli_num_rows($result) > 0) {
        $stmt = mysqli_prepare($connect, "UPDATE advertiser_plan SET plan_id = ?, end_date = ? WHERE advertiser_id = ?");
        mysqli_stmt_bind_param($stmt, 'isi', $plan_id, $end_date, $advertiser_id);
    } else {
        $stmt = mysqli_prepare($connect, "INSERT INTO advertiser_plan(advertiser_id, plan_id, end_date) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iis', $advertiser_id, $plan_id, $end_date);
    }
    if (mysqli_stmt_execute($stmt)) {
        $success = true;
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link rel="stylesheet" href="payment-styles/payment.css">
    <link rel="stylesheet" href="../advertiser/advertiser-style/header.css">
    <link rel="stylesheet" href="../main-css/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
<?php include "header.php"; ?>
<div class="container-log">
    <?php if ($success): ?>
        <?php header("location:../advertiser/dashboard.php "); ?>
    <?php else: ?>
    <h1 class="form-title">Payments</h1>
    <form action="gateway_api.php" method="post">
        <?php if (isset($_GET['plan_id']) && !empty($_GET['plan_id'])): ?>
            <input type="hidden" name="merchant_id" value="1228547">
            <input type="hidden" name="return_url" value="http://localhost/stayfinder/payment/return.php?id=1234">
            <input type="hidden" name="cancel_url" value="http://localhost/stayfinder/payment/cancel.php">
            <input type="hidden" name="notify_url" value="http://localhost/stayfinder/payment/notify.php">
            <div class="payment-details">
                <label class="label-2">Plan: <?php echo $plane_name; ?></label><br><br>
                <label class="label-2">Rs: <?php echo $price; ?></label><br><br>
            </div>
            <hr>
            <input type="hidden" name="items" value="Stayfinder Subscription">
            <input type="hidden" name="currency" value="LKR">
            <input type="hidden" name="order_id" value="<?php echo $plan_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $price; ?>">
            <div class="input-group">
                <label class="label" for="firstname">First Name</label>
                <input class="user-input" type="text" name="first_name" id="first_name" placeholder="First Name" value="<?php echo $first_Name; ?>" required>
            </div>
            <div class="input-group">
                <label class="label" for="lastname">Last Name</label>
                <input class="user-input" type="text" name="last_name" id="last_name" placeholder="Last Name" value="<?php echo $last_Name; ?>" required>
            </div>
            <div class="input-group">
                <label class="label" for="email">Email</label>
                <input class="user-input" type="email" name="email" id="email" placeholder="Email" value="<?php echo $email; ?>" required>
            </div>
            <div class="input-group">
                <label class="label" for="contact_no">Contact Number</label>
                <input class="user-input" type="tel" name="mobile" id="contact" placeholder="Contact Number" value="<?php echo $contactNumber; ?>" required>
            </div>
            <div class="input-group">
                <label class="label" for="address">Address No:</label>
                <input class="user-input" type="text" name="address" placeholder="Address No" required>
            </div>
            <div class="input-group">
                <label class="label" for="address_street">Address Street</label>
                <input class="user-input" type="text" name="address_street" placeholder="Address Street" required>
            </div>
            <div class="input-group">
                <label class="label" for="city">Address City</label>
                <input class="user-input" type="text" name="city" placeholder="Address City" required>
            </div>
            <input type="hidden" name="country" value="Sri Lanka">
            <input type="hidden" name="hash" value="<?php echo $hash; ?>">
            <input type="submit" class="btn" value="Proceed Payment">
        <?php endif; ?>
    </form>
    <?php endif; ?>
</div>
<?php include "footer.php"; ?>
<script src="../main-js/dropdown.js"></script>
<script src="../main-js/popup.js"></script>
</body>
</html>
