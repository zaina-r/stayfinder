<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";


if (!isset($_SESSION['admin_id'])) {
    header("Location: login-registraion.php");
    exit();
}


$query = "SELECT * FROM user WHERE user_type='seeker'";
$result = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seekers' Profiles</title>
    <link rel="stylesheet" href="admin-css/seeker-profile.css">
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
        <h1>Seekers Details</h1>

        <table class="seeker-table">
            <tr>
                <th>Seeker ID</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>Contact no</th>
                
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['first_name']; ?></td>
                    <td><?php echo $row['last_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['contact_no']; ?></td>
                    
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
<?php include "footer.php"; ?> 
</html>
