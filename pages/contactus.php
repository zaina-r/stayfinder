<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../dbconnect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['send_message'])) {
        // Sanitize and validate input data
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $message = trim($_POST['message']);

        // Basic validation
        if (empty($name) || empty($email) || empty($message)) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            // Prepare the SQL statement in a procedural way
            $sql = "INSERT INTO contactus (name, email, message) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($connect, $sql);
            
            if ($stmt === false) {
                $error = "Error preparing the statement: " . mysqli_error($connect);
            } else {
                // Bind parameters
                mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);

                // Execute the statement
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Message sent successfully.";
                    // Optionally, you can clear the form fields after successful submission
                    $name = $email = $message = "";
                } else {
                    $error = "Error: " . mysqli_stmt_error($stmt);
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="pages-css/contactus.css">
    <link rel="stylesheet" href="../seeker/seeker-styles/header.css">
    <link rel="stylesheet" href="../seeker/seeker-styles/footer.css">
    <link rel="stylesheet" href="../main-css/popup-message.css">
    <script src="https://kit.fontawesome.com/e9287191e3.js"></script>
</head>
<?php include "header.php" ?>
<body>
    <div class="container">
        <h1>Contact Us</h1>
        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <button name="send_message" type="submit">Send Message</button>
            </div>
        </form>
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

<?php 
// Close the database connection only at the end of the script
mysqli_close($connect);
?>
