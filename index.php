<?php
// Start session and include database connection
session_start();
include "dbconnect.php";

// Check if cookies are set and if session variables are not set, initialize session with cookies
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id']) && isset($_COOKIE['email']) && isset($_COOKIE['user_type'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['email'] = $_COOKIE['email'];
    $_SESSION['user_type'] = $_COOKIE['user_type'];
}

// Fetch user details if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userID = $_SESSION['user_id'];
    $user_type = $_SESSION['user_type'];

    // Use prepared statement to fetch user's first name and profile picture
    $query = "SELECT first_name, profile_pic FROM user WHERE user_id = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, 'i', $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch user details if the user exists
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $firstName = $row['first_name'];
        $profilePic = $row['profile_pic'];

        // Check if profile picture is empty or file does not exist
        if (empty($profilePic) || !file_exists("uploads/profile-pic/" . $profilePic)) {
            $profilePic = "default_pro_pic.jpg"; // Set default profile picture
        }
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stayfinder</title>
    <link rel="stylesheet" href="main-css/index.css">
    <link rel="stylesheet" href="main-css/header.css">
    <link rel="stylesheet" href="main-css/footer.css">
    <link rel="stylesheet" href="main-css/popup-message.css"> <!-- Including CSS for modal -->
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
    <div class="banner">
        <div class="navbar">
            <a href="index.php"><img src="src-image/logo-white.png" alt="site-logo" class="logo"></a>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="pages/aboutus.php">About Us</a></li>
                <li><a href="pages/contactus.php">Contact Us</a></li>
                <li><a href="pages/privacy-policy.php">Privacy Policy</a></li>
            </ul>
            <div class="user-info">
                <?php if (!isset($_SESSION['user_id'])):?> 
                    <a href="login-registration.php"><button class="head-button" type="button"><span class="span-class"></span>Login</button></a>
                <?php else: ?>
                    <div class="user-profile">
                        <a href="seeker/seeker-profile.php"><img src="uploads/profile-pic/<?php echo htmlspecialchars($profilePic); ?>" alt="User Profile Picture"></a>
                        <span onclick="toggleDropdown()">Hi, <?php echo htmlspecialchars($firstName); ?>&nbsp;<i class='fas fa-angle-double-down'></i></span>
                    </div>
                    <div id="dropdown" class="dropdown">
                        <a class="dropdown-top-2" href="seeker/seeker-profile.php">View My Profile</a>
                        <a class="dropdown-top-2" href="update-profile.php">Update My Profile</a>
                        <a href="logout.php">Log Out</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="content">
            <h1>STAYFINDER</h1>
            <p>Your trusted destination, <br>for finding the perfect home away from home!</p>
            <div>
                <?php
                    $buttonHTML = '';
                    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'advertiser') {
                        $buttonHTML = '<button onclick="window.location.href=\'advertiser/postad.php\'" class="head-button" type="button"><span class="span-class"></span>POST AD</button>';
                    } else {
                        $buttonHTML = '<button onclick="showLoginPopup()" class="head-button" type="button"><span class="span-class"></span>POST AD</button>';
                    }
                 ?>
                <?php echo $buttonHTML; ?>
                <button onclick="window.location.href='seeker/viewads.php'" class="head-button" type="button"><span class="span-class"></span>VIEW ADS</button>
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
    
    <?php include "footer.php" ?>   
    <script src="main-js/dropdown.js"></script>
    <script src="main-js/popup.js"></script>
</body>
</html>
