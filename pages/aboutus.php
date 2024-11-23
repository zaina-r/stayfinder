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
    <title>About Us - StayFinder</title>
    <link rel="stylesheet" href="pages-css/aboutus.css">
    <link rel="stylesheet" href="../seeker/seeker-styles/header.css">
    <link rel="stylesheet" href="../seeker/seeker-styles/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
    
</head>
<?php include "header.php" ?>
<body>
    <div class="container">
        <div class="header">
            <h1>About Us</h1>
        </div>

        <div class="content">
            <p>Welcome to StayFinder! We are a dedicated platform designed to make accommodation rentals seamless and efficient for everyone involved. Whether you're a property owner looking to showcase your listings or a user searching for the perfect place to stay, we aim to bridge the gap and make connections effortlessly.</p>
            
            <h2>Our Mission</h2>
            <p>At StayFinder, our mission is to simplify the accommodation rental process by providing an intuitive, trustworthy, and user-friendly platform for both advertisers and users. We believe that finding the perfect place to stay or promoting a property should be hassle-free and secure.</p>

            <h2>What We Offer</h2>
            <ul>
                <li><strong>Ad Posting:</strong>  Advertisers can post their properties quickly and manage their listings with ease.</li>
                <li><strong>Range of Listings:</strong>  Users can explore a variety of accommodation options tailored to their needs and preferences.</li>
                <li><strong>Direct Communication:</strong> StayFinder facilitates direct contact between users and advertisers, streamlining the rental process.</li>
                <li><strong>Trusted Platform:</strong> We strive to ensure quality and trust by verifying and managing listings to enhance user experience.</li>
            </ul>

            <h2>Why Choose StayFiner?</h2>
            <p>StayFinder is more than just a platform; it is a community-driven solution for accommodation needs. We emphasize:</p>
            <ul>
                <li><strong>User-Centric Design:</strong> We focus on creating a smooth and enjoyable experience for both advertisers and users.</li>
                <li><strong>Transparency:</strong> Clear communication and trustworthy interactions are at the heart of what we do.</li>
                <li><strong>Efficiency: </strong>From posting ads to finding the right place, we prioritize a quick and effective process for everyone.</li>
            </ul>

            
        </div>
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
