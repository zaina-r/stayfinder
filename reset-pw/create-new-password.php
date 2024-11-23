<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password</title>
    <link rel="stylesheet" href="reset-pw-css/create-new-password.css">
</head>
<body>
    <div class="container">
        <h1>Create New Password</h1>
        <?php
            $selector = $_GET["selector"];
            $validator = $_GET["validator"];

            if (empty($selector) || empty($validator)) {
                echo '<p class="message">Could not validate your request!</p>';
            } else {
                ?>
                <form action="reset-password.inc.php" method="post">
                    <input type="hidden" name="selector" value="<?php echo htmlspecialchars($selector); ?>">
                    <input type="hidden" name="validator" value="<?php echo htmlspecialchars($validator); ?>">
                    <input type="password" name="pwd" placeholder="Enter a new password." required>
                    <input type="password" name="pwd-repeat" placeholder="Confirm new password" required>
                    <button type="submit" name="reset-password-submit">Reset</button>
                </form>
                <?php
            }

            if (isset($_GET["newpwd"])) {
                if ($_GET["newpwd"] == "empty") {
                    echo '<p class="message">*Password is required</p>';
                } elseif ($_GET["newpwd"] == "not_equal") {
                    echo '<p class="message">*Passwords do not match</p>';
                } elseif ($_GET["newpwd"] == "length_than_5") {
                    echo '<p class="message">*Password must be at least 5 characters long</p>';
                }
            }
        ?>
    </div>
</body>
</html>
