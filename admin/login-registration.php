<?php
include "../dbconnect.php";
session_start();

// Set default form state
if (!isset($_SESSION['show_sign_up'])) {
    $_SESSION['show_sign_up'] = false;
}

// Ensure errors arrays are always defined
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [
        'firstname' => '',
        'lastname' => '',
        'contact' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
    ];
}

if (!isset($_SESSION['signin_errors'])) {
    $_SESSION['signin_errors'] = [
        'email' => '',
        'password' => ''
    ];
}

// Clear errors and reset form state if there is no POST request (on page refresh)
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['errors'] = [];
    $_SESSION['signin_errors'] = [];
    $_SESSION['show_sign_up'] = false;
}

// Initialize error messages from session
$errors = $_SESSION['errors'];
$signin_errors = $_SESSION['signin_errors'];

// Initialize input values
$firstname = $lastname = $contact = $email = $password = $confirm_password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sign-up form submission
    if (isset($_POST['sign_up_submit'])) {
        $_SESSION['show_sign_up'] = true;
        $_SESSION['errors'] = [
            'firstname' => '',
            'lastname' => '',
            'contact' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            
        ];

        $firstname = clean_input($_POST["firstname"]);
        $lastname = clean_input($_POST["lastname"]);
        $contact = clean_input($_POST["contact"]);
        $email = clean_input($_POST["email"]);
        $password = clean_input($_POST["password"]);
        $confirm_password = clean_input($_POST["confirm_password"]);
       


        // Validate first name
        if (empty($firstname)) {
            $_SESSION['errors']['firstname'] = "*First name is required";
        } elseif (!preg_match("/^[a-zA-Z]*$/", $firstname)) {
            $_SESSION['errors']['firstname'] = "*First name can only contain letters";
        }

        // Validate last name
        if (empty($lastname)) {
            $_SESSION['errors']['lastname'] = "*Last name is required";
        } elseif (!preg_match("/^[a-zA-Z]*$/", $lastname)) {
            $_SESSION['errors']['lastname'] = "*Last name can only contain letters";
        }

        // Validate contact
        if (empty($contact)) {
            $_SESSION['errors']['contact'] = "*Contact number is required";
        } elseif (!preg_match("/^(\+94|0)?7[0-9]{8}$/", $contact)) {
            $_SESSION['errors']['contact'] = "*Please enter a valid Sri Lankan mobile number";
        }

        // Validate email
        if (empty($email)) {
            $_SESSION['errors']['email'] = "*Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['errors']['email'] = "*Please enter a valid email address";
        } else {
            $sql = "SELECT * FROM admin WHERE email = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                $_SESSION['errors']['email'] = "*This email is already registered";
            }
            mysqli_stmt_close($stmt);
        }

        // Validate password
        if (empty($password)) {
            $_SESSION['errors']['password'] = "*Password is required";
        } elseif (strlen($password) < 5) {
            $_SESSION['errors']['password'] = "*Password must be at least 5 characters long";
        }

        // Validate confirm password
        if (empty($confirm_password)) {
            $_SESSION['errors']['confirm_password'] = "*Please confirm your password";
        } elseif ($password !== $confirm_password) {
            $_SESSION['errors']['confirm_password'] = "*Passwords do not match";
        }

        // If no errors, proceed with registration
        if (!array_filter($_SESSION['errors'])) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO admin (email, password, contact_no, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $email, $hashed_password, $contact, $firstname, $lastname);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $_SESSION['success_message'] = "Successfully registered. Please sign in.";
            $firstname = $lastname = $contact = $email = $password = $confirm_password = '';
            $_SESSION['errors'] = [];
            $_SESSION['show_sign_up'] = false;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Sign-in form submission
    if (isset($_POST['sign_in_submit'])) {
        $_SESSION['signin_errors'] = [
            'email' => '',
            'password' => ''
        ];
        $email = clean_input($_POST["email"]);
        $password = clean_input($_POST["password"]);

        // Validate email
        if (empty($email)) {
            $_SESSION['signin_errors']['email'] = "*Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['signin_errors']['email'] = "*Please enter a valid email address";
        }

        // Validate password
        if (empty($password)) {
            $_SESSION['signin_errors']['password'] = "*Password is required";
        }

        if (!array_filter($_SESSION['signin_errors'])) {
            $sql = "SELECT * FROM admin WHERE email = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $row['password'])) {
                    $_SESSION['admin_id'] = $row['admin_id'];
                    $_SESSION['email'] = $row['email'];

                    
                    header("Location: dashboard.php");
                    exit();
            
                    
                } else {
                    $_SESSION['signin_errors']['password'] = "*Invalid password";
                }
            } else {
                $_SESSION['signin_errors']['email'] = "*No account found with this email";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Function to clean input
function clean_input($data) {
    return htmlspecialchars(trim($data));
}

$errors = $_SESSION['errors'];
$signin_errors = $_SESSION['signin_errors'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in || Sign up</title>
    <link rel="stylesheet" href="admin-css/login-registration.css">
</head>
<body>

    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h1>Create new admin</h1>
                
                <div class="infield">
                    <input class="normal-input" type="text" name="firstname" id="first_name" placeholder="First Name" value="<?php echo $firstname; ?>"/>
                    <span class="error-message"><?php echo isset($errors['firstname']) ? $errors['firstname'] : ''; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="text" name="lastname" id="last_name" placeholder="Last Name" value="<?php echo $lastname; ?>" />
                    <span class="error-message"><?php echo isset($errors['lastname']) ? $errors['lastname'] : ''; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="email" name="email" id="email" placeholder="Email" value="<?php echo $email; ?>"/>
                    <span class="error-message"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="tel" name="contact" id="contact" placeholder="Contact Number" value="<?php echo $contact; ?>"/>
                    <span class="error-message"><?php echo isset($errors['contact']) ? $errors['contact'] : ''; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="password" name="password" id="password" placeholder="Password" value="<?php echo $password; ?>"/>
                    <span class="error-message"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" value="<?php echo $confirm_password; ?>"/>
                    <span class="error-message"><?php echo isset($errors['confirm_password']) ? $errors['confirm_password'] : ''; ?></span>
                </div>
                <input type="submit" name="sign_up_submit" class="sign-btn" value="Sign Up"/>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h1>Sign in</h1>
                <?php
                if (isset($_SESSION['success_message'])) {
                    echo '<p class="success-message">' . $_SESSION['success_message'] . '</p>';
                    unset($_SESSION['success_message']);
                }
                ?>
                <div class="infield">
                    <input class="normal-input" type="email" placeholder="Email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
                    <span class="error-message"><?php echo isset($signin_errors['email']) ? $signin_errors['email'] : ''; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="password" placeholder="Password" name="password"/>
                    <span class="error-message"><?php echo isset($signin_errors['password']) ? $signin_errors['password'] : ''; ?></span>
                </div>
                <input type="submit" name="sign_in_submit" class="sign-btn" value="Sign In"/>
            </form>
        </div>
        <div class="overlay-container" id="overlayCon">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="sign-btn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Admin!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="sign-btn">Sign Up</button>
                </div>
            </div>
            <button class="sign-btn" id="overlayBtn"></button>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            const container = document.getElementById('container');
            const overlayBtn = document.getElementById('overlayBtn');

            <?php if ($_SESSION['show_sign_up']): ?>
                container.classList.add('right-panel-active');
            <?php endif; ?>

            overlayBtn.addEventListener('click', () => {
                container.classList.toggle('right-panel-active');
                overlayBtn.classList.remove('btnScaled');
                window.requestAnimationFrame(() => {
                    overlayBtn.classList.add('btnScaled');
                });
            });
        };
    </script>

</body>
</html>
