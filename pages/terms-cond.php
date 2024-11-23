<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - StayFinder</title>
    <link rel="stylesheet" href="pages-css/terms-cond.css">
    <link rel="stylesheet" href="../seeker/seeker-styles/header.css">
    <link rel="stylesheet" href="../seeker/seeker-styles/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<?php include "header.php" ?>
<body>
    <div class="container">
        <h1>Terms and Conditions</h1>
        <p>Welcome to StayFinder! By accessing and using our website, you agree to comply with and be bound by the following terms and conditions. Please read these terms carefully before using our services.</p>

        <h2>1. Acceptance of Terms</h2>
        <p>By accessing and using the StayFinder website, you accept and agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our website.</p>

        <h2>2. Services Provided</h2>
        <p>StayFinder provides a platform for advertisers to post rental accommodations and for users to view and contact advertisers regarding their listings. We act solely as a facilitator and do not guarantee the accuracy, reliability, or completeness of the listings.</p>

        <h2>3. User Accounts</h2>
        <ul>
            <li>To access certain features of StayFinder, you may be required to create an account.</li>
            <li>You are responsible for maintaining the confidentiality of your account information, including your password.</li>
            <li>You agree to notify us immediately of any unauthorized use of your account.</li>
        </ul>

        <h2>4. Posting Listings</h2>
        <p>Advertisers are responsible for the accuracy and authenticity of their listings. By posting a listing on StayFinder, you represent and warrant that:</p>
        <ul>
            <li>The information provided is accurate, up-to-date, and not misleading.</li>
            <li>You have the right to offer the rental accommodation listed.</li>
        </ul>

        <h2>5. User Conduct</h2>
        <p>As a user of StayFinder, you agree not to:</p>
        <ul>
            <li>Post false, misleading, or fraudulent content.</li>
            <li>Harass, abuse, or harm other users.</li>
            <li>Engage in any unlawful activities or violate any applicable laws.</li>
        </ul>

        <h2>6. Limitation of Liability</h2>
        <p>StayFinder is not responsible for any direct, indirect, incidental, consequential, or punitive damages arising from the use of our services. We do not guarantee the accuracy or reliability of listings posted by advertisers.</p>

        <h2>7. Privacy Policy</h2>
        <p>We value your privacy. Please review our <a href="privacy-policy.php">Privacy Policy</a> to understand how we collect, use, and safeguard your information.</p>

        <h2>8. Termination</h2>
        <p>We reserve the right to terminate or suspend your access to StayFinder at our discretion, without notice, for conduct that we believe violates these terms or is harmful to other users of the platform.</p>

        <h2>9. Changes to Terms</h2>
        <p>We may update these Terms and Conditions from time to time. Any changes will be posted on this page, and by continuing to use the website, you agree to be bound by the revised terms.</p>

        <h2>10. Contact Us</h2>
        <p>If you have any questions about these Terms and Conditions, please <a href="contactus.php">Contact Us</a>.</p>
    </div>
    <div id="loginModal" class="modal-popup" style="display: none;">
        <div class="modal-content">
            <div class="message"><p>You have to log in as an Advertiser to post an ad.</p></div>
            <button class="cancel-btn" onclick="closeLoginPopup()">Cancel</button>
            <button class="login-btn" onclick="redirectToLogin()">Log In</button>    
        </div>
    </div>
</body>
<?php include "../seeker/footer.php" ?>
<script src="../seeker/seeker-js/dropdown.js"></script>
<script src="../seeker/seeker-js/popup.js"></script>
</html>
