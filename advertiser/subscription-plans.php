<?php

    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    /*echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';*/

    include_once "../dbconnect.php";
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'advertiser') {
        header("Location: ../login-registration.php");
        exit();
    }


    // get the subscripton 

    $sql_q = "SELECT * FROM subscription_plans";
    $result1 = mysqli_query($connect, $sql_q);


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Subscription Plans</title>
        <link rel="stylesheet" href="advertiser-style/subscription-plans.css">
        <link rel="stylesheet" href="advertiser-style/header.css">
        <link rel="stylesheet" href="../main-css/footer.css">
        <link rel="stylesheet" href="../main-css/popup-message.css">
        <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
    </head>
    <body>
    <?php include "header.php" ?>
        <div class="plans-container">
            <h2>Subscription Types</h2>

            <div class="subscription-container">

                <?php if (mysqli_num_rows($result1) > 0): 
                    while ($row = mysqli_fetch_assoc($result1)): 
                        
                        $plan_id = $row['plan_id']; 
                ?>     
                <div class="subscription-modal-content">
                        <h4><?php echo $row['plan_type']; ?></h4>
                        <hr>
                        <p><?php echo (int)$row['plan_price']; ?>/=</p>
                        <button type="button" onclick="window.location.href='../payment/payment.php?plan_id=<?php echo $row['plan_id']; ?>'"> Subscribe Now</button>
                </div>
                <?php endwhile;?>
                <?php else: ?>
                    <p>No subscription plans found</p>
                <?php endif; ?>
                
            </div>
        </div>
        <?php include "footer.php" ?>   
        <script src="../main-js/dropdown.js"></script>
        <script src="../main-js/popup.js"></script>
    </body>
</html>
