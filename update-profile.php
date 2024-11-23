<?php
session_start();
include 'dbconnect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-registration.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];

// Initialize error messages and input values
$errors = [
    'profile_picture' => '',
    'firstname' => '',
    'lastname' => '',
    'email' => '',
    'contact' => '',
    'password' => '',
    'confirm_password' => ''
];
$firstname = $lastname = $email = $contact = $password = $confirm_password = '';

// Fetch current user details to display as initial values
$sqlUser = "SELECT first_name, last_name, email, contact_no, profile_pic, password FROM user WHERE user_id = ?";
$stmtUser = mysqli_prepare($connect, $sqlUser);
mysqli_stmt_bind_param($stmtUser, "i", $userId);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$userDetails = mysqli_fetch_assoc($resultUser);
$currentProfilePicture = $userDetails['profile_pic'] ?? '';

// Handle remove profile picture action
if (isset($_POST['remove_picture'])) {
    $targetDir = "uploads/profile-pic/";
    $targetFile = $targetDir . $currentProfilePicture;
    if (!empty($currentProfilePicture) && file_exists($targetFile)) {
        unlink($targetFile);
        $currentProfilePicture = ""; // Clear profile picture
        $sqlRemovePic = "UPDATE user SET profile_pic = '' WHERE user_id = ?";
        $stmtRemovePic = mysqli_prepare($connect, $sqlRemovePic);
        mysqli_stmt_bind_param($stmtRemovePic, "i", $userId);
        mysqli_stmt_execute($stmtRemovePic);
        mysqli_stmt_close($stmtRemovePic);
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['remove_picture'])) {
    // Get input values and sanitize them
    $firstname = clean_input($_POST["firstname"]);
    $lastname = clean_input($_POST["lastname"]);
    $email = clean_input($_POST["email"]);
    $contact = clean_input($_POST["contact"]);
    $password = clean_input($_POST["password"]);
    $confirm_password = clean_input($_POST["confirm_password"]);
    $profilePicture = $currentProfilePicture;

    // Profile picture validation and upload
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $targetDir = "uploads/profile-pic/";
        $fileType = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png"];

        if (in_array($fileType, $allowedTypes)) {
            $newFileName = $userId . "_" . time() . "." . $fileType;
            $targetFile = $targetDir . $newFileName;

            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                $profilePicture = $newFileName;
            } else {
                $errors['profile_picture'] = "Error uploading your file.";
            }
        } else {
            $errors['profile_picture'] = "Only JPG, JPEG, and PNG files are allowed.";
        }
    }

    // Validate input fields
    if (empty($firstname) || !preg_match("/^[a-zA-Z]*$/", $firstname)) {
        $errors['firstname'] = "*First name is required and should contain only letters";
    }
    if (empty($lastname) || !preg_match("/^[a-zA-Z]*$/", $lastname)) {
        $errors['lastname'] = "*Last name is required and should contain only letters";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "*Please enter a valid email address";
    }
    if (empty($contact) || !preg_match("/^(\+94|0)?7[0-9]{8}$/", $contact)) {
        $errors['contact'] = "*Please enter a valid Sri Lankan mobile number";
    }
    if (!empty($password) && strlen($password) < 5) {
        $errors['password'] = "*Password must be at least 5 characters long";
    }
    if (!empty($password) && $password !== $confirm_password) {
        $errors['confirm_password'] = "*Passwords do not match";
    }

    // Determine if password needs to be updated
    $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $userDetails['password'];

    // If there are no errors, update the user's profile
    if (!array_filter($errors)) {
        $sqlUpdateUser = "UPDATE user SET first_name = ?, last_name = ?, email = ?, contact_no = ?, profile_pic = ?, password = ? WHERE user_id = ?";
        $stmtUpdateUser = mysqli_prepare($connect, $sqlUpdateUser);
        mysqli_stmt_bind_param($stmtUpdateUser, "ssssssi", $firstname, $lastname, $email, $contact, $profilePicture, $hashed_password, $userId);
        mysqli_stmt_execute($stmtUpdateUser);
        mysqli_stmt_close($stmtUpdateUser);

        $redirectLocation = ($userType === "advertiser") ? "advertiser/dashboard.php" : "seeker/seeker-profile.php";
        header("Location: $redirectLocation?message=Profile updated successfully!");
        exit();
    }
}

function clean_input($data) {
    return htmlspecialchars(trim($data));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="main-css/update-profile.css">
    <link rel="stylesheet" href="seeker/seeker-styles/header.css">
    <link rel="stylesheet" href="main-css/footer.css">
    <link rel="stylesheet" href="main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<body>
<?php include "header.php"; ?>

<h1 class="form-title">Update Profile</h1>

<div class="container-log">

    <form action="update-profile.php" method="post" enctype="multipart/form-data">
        <!-- Display current profile picture and remove option if available -->
        <?php if (!empty($currentProfilePicture)): ?>
            <div class="profile-picture-container">
                <img src="uploads/profile-pic/<?php echo htmlspecialchars($currentProfilePicture); ?>" alt="Profile Picture" class="profile-picture"><br>
                <button type="submit" name="remove_picture" class="remove-btn">Remove Picture</button>
            </div>
        <?php endif; ?>

        <div class="input-group">
            <label class="label">Upload New Profile Picture</label>
            <input type="file" name="profile_picture">
            <span class="error-message"><?php echo $errors['profile_picture']; ?></span>
        </div>

        <div class="input-group">
            <label class="label">First Name</label>
            <input class="input" type="text" name="firstname" value="<?php echo htmlspecialchars($userDetails['first_name']); ?>">
            <span class="error-message"><?php echo $errors['firstname']; ?></span>
        </div>

        <div class="input-group">
            <label class="label">Last Name</label>
            <input class="input" type="text" name="lastname" value="<?php echo htmlspecialchars($userDetails['last_name']); ?>">
            <span class="error-message"><?php echo $errors['lastname']; ?></span>
        </div>

        <div class="input-group">
            <label class="label">Email</label>
            <input class="input" type="email" name="email" value="<?php echo htmlspecialchars($userDetails['email']); ?>">
            <span class="error-message"><?php echo $errors['email']; ?></span>
        </div>

        <div class="input-group">
            <label class="label">Contact Number</label>
            <input class="input" type="tel" name="contact" value="<?php echo htmlspecialchars($userDetails['contact_no']); ?>">
            <span class="error-message"><?php echo $errors['contact']; ?></span>
        </div>

        <div class="input-group">
            <label class="label">New Password</label>
            <input class="input" type="password" name="password">
            <span class="error-message"><?php echo $errors['password']; ?></span>
        </div>

        <div class="input-group">
            <label class="label">Confirm Password</label>
            <input class="input" type="password" name="confirm_password">
            <span class="error-message"><?php echo $errors['confirm_password']; ?></span>
        </div>

        <input class="remove-btn" type="submit" value="Update Profile">
    </form>
</div>
<!-- Modal for Popup -->
<div id="loginModal" class="modal-popup" style="display: none;">
        <div class="modal-content">
            <div class="message"><p>You have to log in as an Advertiser to post an ad.</p></div>
            <button class="cancel-btn" onclick="closeLoginPopup()">Cancel</button>
            <button class="login-btn" onclick="redirectToLogin()">Log In</button>    
        </div>
    </div>
    <?php include "footer.php"; ?>   
    <script src="seeker/seeker-js/dropdown.js"></script>
    <script src="main-js/popup.js"></script>
</body>
</html>
