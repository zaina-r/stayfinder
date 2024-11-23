<?php

    session_start();
    error_reporting(E_ALL);
    ini_set("display_errors",1);

    include_once "../dbconnect.php";

    if(!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
    }

    if(isset($_GET['buy_plan'])){
        $buy_plan=$_GET['buy_plan'];
    }
    
    if(isset($_GET['owner_plan'])){
        $buy_plan=$_GET['owner_plan'];
    }



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PostAd</title>
    <link rel="stylesheet" href="../header.css">
    <style>
        body{
            background-image: linear-gradient(rgba(0,0,0,0.80),rgba(0,0,0,0.80)), url(../src-image/background-image.jpg);
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
        /* Popup modal styles */
        .modal {
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 40px 20px 20px 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
            border-radius: 10px;
        }

        .message p{
            margin: 30px 10px;
        }
        .modal button {
            padding: 10px 20px;
            margin: 30px 10px 10px 10px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-btn {
            background-color: #9B1809;
            color: white;
            border: none;
        }
        .cancel-btn {
            background-color: #d3d3d3;
            color: black;
            border: none;
        }
    </style>
</head>
<body>

    <?php if((isset($_GET['buy_plan']))): ?>
        <div id="loginModal" class="modal">
            <div class="modal-content">
                <p class="message">To post an ad you have to buy a plan.</p>
                <button class="cancel-btn" onclick="window.location.href='dashboard.php'">Cancel</button>
                <button class="login-btn"  onclick="window.location.href='subscription-plans.php'">Buy Now</button>
            </div>    
        </div>
    <?php elseif(isset($_GET['owner_plan'])): ?>
        <div id="loginModal" class="modal">
            <div class="modal-content">
                <p class="message">Your plan is expired. Buy a new plan</p>
                <button class="cancel-btn" onclick="window.location.href='advertiser-dashboard.php'">Cancel</button>
                <button class="login-btn"  onclick="window.location.href='subscription-plans.php'">Buy Now</button>
            </div>
        </div>

    <?php endif; ?>
</body>
</html>