<?php
include "dbconnect.php";
session_start();

// Initialize error messages
$errors = [
    'firstname' => '',
    'lastname' => '',
    'contact' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
    'user_type' => '',
    'terms' => '',
];
$signin_errors = [
    'email' => '',
    'password' => ''
];

// Initialize input values
$firstname = $lastname = $contact = $email = $password = $confirm_password = $user_type = '';
$show_sign_up = false;
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sign-up form submission
    if (isset($_POST['sign_up_submit'])) {
        $show_sign_up = true;

        $firstname = clean_input($_POST["firstname"]);
        $lastname = clean_input($_POST["lastname"]);
        $contact = clean_input($_POST["contact"]);
        $email = clean_input($_POST["email"]);
        $password = clean_input($_POST["password"]);
        $confirm_password = clean_input($_POST["confirm_password"]);
        $user_type = isset($_POST["user_type"]) ? strtolower(clean_input($_POST["user_type"])) : '';

        // Validate terms and conditions agreement
        if (!isset($_POST['terms'])) {
            $errors['terms'] = "*You must agree to the terms and conditions";
        }
        
        // Validate user type
        if (empty($user_type) || !in_array($user_type, ['seeker', 'advertiser'])) {
            $errors['user_type'] = "*Please select a valid user type";
        }

        // Validate first name
        if (empty($firstname)) {
            $errors['firstname'] = "*First name is required";
        } elseif (!preg_match("/^[a-zA-Z]*$/", $firstname)) {
            $errors['firstname'] = "*First name can only contain letters";
        }

        // Validate last name
        if (empty($lastname)) {
            $errors['lastname'] = "*Last name is required";
        } elseif (!preg_match("/^[a-zA-Z]*$/", $lastname)) {
            $errors['lastname'] = "*Last name can only contain letters";
        }

        // Validate contact
        if (empty($contact)) {
            $errors['contact'] = "*Contact number is required";
        } elseif (!preg_match("/^(\+94|0)?7[0-9]{8}$/", $contact)) {
            $errors['contact'] = "*Please enter a valid Sri Lankan mobile number";
        }

        // Validate email
        if (empty($email)) {
            $errors['email'] = "*Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "*Please enter a valid email address";
        } else {
            $sql = "SELECT * FROM user WHERE email = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                $errors['email'] = "*This email is already registered";
            }
            mysqli_stmt_close($stmt);
        }

        // Validate password
        if (empty($password)) {
            $errors['password'] = "*Password is required";
        } elseif (strlen($password) < 5) {
            $errors['password'] = "*Password must be at least 5 characters long";
        }

        // Validate confirm password
        if (empty($confirm_password)) {
            $errors['confirm_password'] = "*Please confirm your password";
        } elseif ($password !== $confirm_password) {
            $errors['confirm_password'] = "*Passwords do not match";
        }



        // If no errors, proceed with registration
        if (!array_filter($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO user (email, password, contact_no, first_name, last_name, user_type) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "ssssss", $email, $hashed_password, $contact, $firstname, $lastname, $user_type);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $success_message = "Successfully registered. Please sign in.";
            $firstname = $lastname = $contact = $email = $password = $confirm_password = $user_type = '';
            $errors = array_fill_keys(array_keys($errors), '');
            $show_sign_up = false;
        }
    }

    // Sign-in form submission
    if (isset($_POST['sign_in_submit'])) {
        $email = clean_input($_POST["email"]);
        $password = clean_input($_POST["password"]);

        // Validate email
        if (empty($email)) {
            $signin_errors['email'] = "*Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $signin_errors['email'] = "*Please enter a valid email address";
        }

        // Validate password
        if (empty($password)) {
            $signin_errors['password'] = "*Password is required";
        }

        if (!array_filter($signin_errors)) {
            $sql = "SELECT * FROM user WHERE email = ?";
            $stmt = mysqli_prepare($connect, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $row['password'])) {
                    // Store session data
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['user_type'] = $row['user_type'];

                    // Handle "Remember me" functionality
                    if (isset($_POST['remember_me'])) {
                        setcookie('user_id', $row['user_id'], time() + (86400 * 30), "/");
                        setcookie('email', $row['email'], time() + (86400 * 30), "/");
                        setcookie('user_type', $row['user_type'], time() + (86400 * 30), "/");
                    }

                    // Redirect based on user type
                    if ($_SESSION['user_type'] == "advertiser") {
                        header("Location: advertiser/postad.php");
                        exit();
                    } else {
                        header("Location: seeker/viewads.php");
                        exit();
                    }
                } else {
                    $signin_errors['password'] = "*Invalid password";
                }
            } else {
                $signin_errors['email'] = "*No account found with this email";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Auto-login if cookies are set
if (isset($_COOKIE['user_id']) && isset($_COOKIE['email']) && isset($_COOKIE['user_type'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['email'] = $_COOKIE['email'];
    $_SESSION['user_type'] = $_COOKIE['user_type'];

    // Redirect based on user type
    if ($_SESSION['user_type'] == "advertiser") {
        header("Location: advertiser/postad.php");
        exit();
    } else {
        header("Location: seeker/viewads.php");
        exit();
    }
}

// Function to clean input
function clean_input($data) {
    return htmlspecialchars(trim($data));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in || Sign up</title>
    <link rel="stylesheet" href="main-css/login-registration.css">
</head>
<body>

    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h1>Create Account</h1>
                <div class="infield">
                    <div class="user-type">
                        <div class="user-type-option">
                            <input type="radio" name="user_type" value="seeker" id="seeker" <?php if ($user_type === "seeker") echo "checked"; ?>>
                            <label for="seeker">I am a Seeker</label>    
                        </div>
                        <div class="user-type-option">
                            <input type="radio" name="user_type" value="advertiser" id="advertiser" <?php if ($user_type === "advertiser") echo "checked"; ?>>  
                            <label for="advertiser">I am an Advertiser</label>
                        </div>   
                    </div>
                    <span class="error-message"><?php echo $errors['user_type']; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="text" name="firstname" id="first_name" placeholder="First Name" value="<?php echo $firstname; ?>"/>
                    <span class="error-message"><?php echo $errors['firstname']; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="text" name="lastname" id="last_name" placeholder="Last Name" value="<?php echo $lastname; ?>" />
                    <span class="error-message"><?php echo $errors['lastname']; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="email" name="email" id="email" placeholder="Email" value="<?php echo $email; ?>"/>
                    <span class="error-message"><?php echo $errors['email']; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="tel" name="contact" id="contact" placeholder="Contact Number" value="<?php echo $contact; ?>"/>
                    <span class="error-message"><?php echo $errors['contact']; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="password" name="password" id="password" placeholder="Password (Must be 5 characters long)" value="<?php echo $password; ?>"/>
                    <span class="error-message"><?php echo $errors['password']; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" value="<?php echo $confirm_password; ?>"/>
                    <span class="error-message"><?php echo $errors['confirm_password']; ?></span>
                </div>
                <div class="infield">
                    <input type="checkbox" id="terms" name="terms">
                    <label style="cursor: pointer;" for="terms" onclick="window.open('pages/terms-cond.php', '_blank');">I have read the terms and conditions</label>
                    <span class="error-message"><?php echo $errors['terms']; ?></span>
                </div>
                <input type="submit" name="sign_up_submit" class="sign-btn" value="Sign Up"/>
            </form>
        </div>
        <div class="form-container sign-in-container">
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h1>Sign in</h1>
                <?php
                    if ($success_message) {
                        echo '<p class="success-message">' . $success_message . '</p>';
                    }
                ?>
                <div class="infield">
                    <input class="normal-input" type="email" placeholder="Email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
                    <span class="error-message"><?php echo $signin_errors['email']; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="password" placeholder="Password" name="password"/>
                    <span class="error-message"><?php echo $signin_errors['password']; ?></span>
                </div>
                <div style="text-align: right; width:100%;" class="forget-sec">
                    <a  href="reset-pw/reset-password.php"><p style="color: black;">Forget Password ?</p></a>
                </div>
                <input type="submit" name="sign_in_submit" class="sign-btn" value="Sign In"/>
            
                <input type="submit" name="sign_up_submit" class="sign-btn" value="Sign Up"/>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h1>Sign in</h1>
                <?php
                if ($success_message) {
                    echo '<p class="success-message">' . $success_message . '</p>';
                }
                ?>
                <div class="infield">
                    <input class="normal-input" type="email" placeholder="Email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
                    <span class="error-message"><?php echo $signin_errors['email']; ?></span>
                </div>
                <div class="infield">
                    <input class="normal-input" type="password" placeholder="Password" name="password"/>
                    <span class="error-message"><?php echo $signin_errors['password']; ?></span>
                </div>
                <div style="display: flex; justify-content:space-between" class="infield" id="forget-remember">
                    <div>
                        <input type="checkbox" name="remember_me" id="remember_me">
                        <label for="remember_me">Remember me</label>
                    </div>
                    <div>
                        <label><a href="reset-pw/reset-password.php">Forget Password ?</a></label>
                    </div>
                    
                
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
                    <h1>Hello, Friend!</h1>
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

            <?php if ($show_sign_up): ?>
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
