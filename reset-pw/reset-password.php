<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="reset-pw-css/reset-password.css">
</head>
<body>
    <div class="container">
        <h1>Reset Your Password</h1>
        <p>An e-mail will be sent to you with instructions on how to reset your password.</p>
        <form action="reset-request.inc.php" method="post">
            <input type="text" name="email" placeholder="Enter your e-mail address.." required>
            <button type="submit" name="reset-request-submit">Receive new password by email</button>
        </form>
        <?php
            if (isset($_GET["reset"])) {
                if ($_GET["reset"] == "success") {
                    echo '<p class="signupsuccess">*Check your e-mail!</p>';
                }
            }
        ?>
    </div>
</body>
</html>
