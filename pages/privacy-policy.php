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
    <title>Privacy Policy - StayFiner</title>
    <link rel="stylesheet" href="pages-css/privacy-policy.css">
    <link rel="stylesheet" href="../seeker/seeker-styles/header.css">
    <link rel="stylesheet" href="../seeker/seeker-styles/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<?php include "header.php" ?>
<body>
    <div class="container">
        <h1>Privacy Policy</h1>
        <p>Welcome to StayFinder. Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your information when you use our platform. By using StayFiner, you agree to the terms outlined in this policy.</p>

        <h2>1. Information We Collect</h2>
        <p>We may collect the following types of information:</p>
        <ul>
            <li><strong>Personal Information:</strong> When you create an account, we may collect your name, email address, contact information, and profile details.</li>
            <li><strong>Usage Data:</strong> We may collect information about how you access and use our platform, such as your IP address, browser type, pages visited, and interaction patterns.</li>
            <li><strong>Communication Data:</strong> We may keep records of any communication between you and StayFiner, including messages sent through our platform.</li>
        </ul>

        <h2>2. How We Use Your Information</h2>
        <p>We use the information we collect to:</p>
        <ul>
            <li>Provide and improve our services</li>
            <li>Facilitate communication between advertisers and users</li>
            <li>Respond to inquiries and support requests</li>
            <li>Monitor and analyze usage patterns for better user experience</li>
            <li>Comply with legal obligations</li>
        </ul>

        <h2>3. Sharing Your Information</h2>
        <p>We do not sell or rent your personal information to third parties. However, we may share your information with:</p>
        <ul>
            <li><strong>Service Providers:</strong> Trusted third-party services that assist us in operating our platform (e.g., payment processors, hosting services).</li>
            <li><strong>Legal Compliance:</strong> If required by law, to protect our rights, or as part of a legal process.</li>
        </ul>

        <h2>4. Cookies and Tracking Technologies</h2>
        <p>We may use cookies and similar tracking technologies to enhance your experience on StayFiner. You can modify your browser settings to refuse cookies, but this may limit some features of our platform.</p>

        <h2>5. Data Security</h2>
        <p>We implement reasonable security measures to protect your data. However, no method of transmission over the internet or electronic storage is 100% secure, so we cannot guarantee absolute security.</p>

        <h2>6. Your Rights</h2>
        <p>You have the right to:</p>
        <ul>
            <li>Access and update your personal information</li>
            <li>Request deletion of your data</li>
            <li>Object to certain data processing practices</li>
        </ul>
        <p>To exercise your rights, please contact us using the information provided below.</p>

        <h2>7. Changes to This Policy</h2>
        <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page with the updated date. Your continued use of StayFiner after any changes indicates your acceptance of the revised policy.</p>

        <h2>8. Contact Us</h2>
        <p>If you have any questions or concerns about this Privacy Policy, please contact us at:</p>
        <p><strong>Email:</strong> stayfinder@gmail.com<br>
        <strong>Address:</strong> StayFinder, No 93, Dalugama Kelaniya, PIN 11300, Sri Lanka</p>

        <p>Thank you for choosing StayFiner. Your privacy and trust are important to us.</p>
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
