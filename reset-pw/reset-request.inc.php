<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST['reset-request-submit'])) {

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'stayfinder.ads@gmail.com';
    $mail->Password = 'rjrtaucmidpfjrvs';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;




    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);

    $url = "http://localhost/stayfinder/reset-pw/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);
    $expires = date("U") + 1800;

    include "../dbconnect.php";

    $userEmail = $_POST['email'] ?? '';

    if (empty($userEmail)) {
        echo "Email is required!";
        exit();
    }

    // Delete any existing token entries for this email
    $sql = "DELETE FROM pwdReset WHERE pwdResetEmail=?";
    $stmt = mysqli_stmt_init($connect);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "There was an error: " . mysqli_error($connect);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, 's', $userEmail);
        mysqli_stmt_execute($stmt);
    }

    // Insert the new reset request
    $sql = "INSERT INTO pwdReset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($connect);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "There was an error: " . mysqli_error($connect);
        exit();
    } else {
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connect);

    $to = $userEmail;
    $subject = 'Reset your password for stayfinder';
    $message = '<p> We received a password reset request. The link to reset your password is below. If you did not make this request, you can ignore this email </p>';
    $message .= '<p>Here is your password reset link: </br>';
    $message .= '<a href="' . $url . '">' . $url . '</a></p>';
    $headers = "From: stayfinder <stayfinder.ads@gmail.com>\r\n";
    $headers .= "Reply-To: stayfinder.ads@gmail.com\r\n";
    $headers .= "Content-type: text/html\r\n";

    $mail->addAddress($to);
    
    $mail->isHTML(true);

    $mail->Subject = $subject;
    $mail->Body = $message;

    $mail->send();
    
    header("location: reset-password.php?reset=success");

} else {
    header("location: ../index.php");
}

?>
