<?php
if (isset($_SESSION['admin_id'])) {
    $userID = $_SESSION['admin_id'];
    

    // Query to fetch user's first name and profile picture
    $query = "SELECT first_name, profile_pic FROM admin WHERE admin_id = '$userID'";
    $result = mysqli_query($connect, $query);

    // Fetch user details if the user exists
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $firstName = $row['first_name'];
        $profilePic = $row['profile_pic'];

        // Check if profile picture is empty or file does not exist
        if (empty($profilePic) || !file_exists("../uploads/profile-pic/" . $profilePic)) {
            $profilePic = "default_pro_pic.jpg"; // Set default profile picture
        }
        } 
} 

?>


<header>
    <div class="navbar">
        <div class="logo-nav">
            <a href="dashboard.php"><img src="../src-image/logo-white.png" alt="site-logo" class="logo"></a>
            <button onclick="window.location.href='dashboard.php'" class="head-button" type="button"><span class="span-class"></span>Admin Dashboard</button>
        </div>
        
        <div class="user-info">

            <?php if (!isset($_SESSION['admin_id'])):?> 
                <a href="../login-registration.php"><button class="head-button" type="button"><span class="span-class"></span>Login</button></a>
            <?php else: ?>
                <div class="user-profile">
                    <a href="dashboard.php"><img src="../uploads/profile-pic/<?php echo htmlspecialchars($profilePic); ?>" alt="User Profile Picture"></a>
                    <span onclick="toggleDropdown()">Hi, <?php echo htmlspecialchars($firstName); ?> &nbsp<i class='fas fa-angle-double-down'></i></span>
                </div>
                <div id="dropdown" class="dropdown">
                    <a class="dropdown-top-2" href="update-profile.php">Update My Profile</a>
                    <a href="../logout.php">Log Out</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>