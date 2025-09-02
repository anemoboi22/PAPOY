<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db/dbconnection.php';
require 'Twilio/autoload.php';

use Twilio\Rest\Client;

$action = $_POST['action'] ?? '';

// Handle account creation (signup)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'signup') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        echo "";
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'All fields are required.', 'error'); });</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "";
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Invalid email format.', 'error'); });</script>";
    } elseif (strlen($password) < 8) {
        echo "";
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Password must be at least 8 characters long.', 'error'); });</script>";
    } else {
        $query = "SELECT email FROM users WHERE email = :email";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "";
            echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Email is already in use.', 'error'); });</script>";
        } else {
            $hashed_password = md5($password);
            $query = "INSERT INTO users (fullname, email, password) VALUES (:fullname, :email, :password)";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':fullname', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?signup=success');
                exit;
            } else {
                echo "";
                echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Error inserting data: " . $stmt->errorInfo()[2] . "', 'error'); });</script>";
            }
        }
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo "";
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Email and Password are required.', 'error'); });</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "";
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Invalid email format.', 'error'); });</script>";
    } else {
        $query = "SELECT user_id, fullname, password FROM users WHERE email = :email";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashed_password = md5($password);

        if ($user && $hashed_password === $user['password']) {
            $_SESSION['userid'] = $user['user_id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['login_status'] = 'success';
            header('Location: user/prospectus.php?login=success');
            exit;
        } else {
            echo "";
            echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Invalid email or password.', 'error'); });</script>";
        }
    }
}

// Handle Forgot Password (send PIN)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'forgot_password') {
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $sql = "SELECT user_id, phone_number, email FROM users WHERE phone_number = REPLACE(:phoneNumber, '+', '') AND email = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $pinCode = rand(100000, 999999);
        $_SESSION['reset_pin'] = $pinCode;
        $_SESSION['reset_user_id'] = $result->user_id;
        $_SESSION['reset_pin_expiry'] = time() + 300; // PIN valid for 5 minutes

        $account_sid = 'ACf4f1bd8904d81310abeb14bc6632932b';
        $auth_token = '9af3c99bc62be501da598225b3cedc79';
        $twilio_number = '+13608726516';

        $client = new Client($account_sid, $auth_token);

        try {
            $client->messages->create(
                $phoneNumber,
                [
                    'from' => $twilio_number,
                    'body' => "Your password reset PIN code is: $pinCode"
                ]
            );
            echo "";
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() { 
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'A PIN code has been sent to your phone number.'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                showResetPasswordModal();
                            }
                        });
                    });
                </script>";
        } catch (Exception $e) {
            echo "";
            echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Failed to send the PIN code. Please try again.', 'error'); });</script>";
        }
    } else {
        echo "";
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Phone number or email not found.', 'error'); });</script>";
    }
}

// Handle Reset Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'reset_password') {
    $enteredPin = $_POST['pin_code'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($enteredPin == $_SESSION['reset_pin'] && time() < $_SESSION['reset_pin_expiry']) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = md5($newPassword);
            $userId = $_SESSION['reset_user_id'];

            $sql = "UPDATE users SET password = :password WHERE user_id = :userId";
            $query = $dbh->prepare($sql);
            $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $query->bindParam(':userId', $userId, PDO::PARAM_INT);

            if ($query->execute()) {
                echo "";
                echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Success', 'Password has been successfully reset.', 'success'); });</script>";
                unset($_SESSION['reset_pin']);
                unset($_SESSION['reset_user_id']);
                unset($_SESSION['reset_pin_expiry']);
            } else {
                echo "";
                echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Failed to reset the password. Please try again.', 'error'); });</script>";
            }
        } else {
            echo "";
            echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Passwords do not match.', 'error'); });</script>";
        }
    } else {
        echo "";
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Invalid or expired PIN code.', 'error'); });</script>";
    }
}

if (!empty($error_message)) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '" . htmlspecialchars($error_message) . "',
                confirmButtonText: 'OK'
            });
        });
    </script>";
}
?>

<?php include_once('includes/header.php'); ?>

<main>
    <div class="container">
        <div class="form-container login-container">
            <form id="login-form" method="POST" action="">
                <input type="hidden" name="action" value="login">
                <h1 class="blackText">Login</h1>
                <input type="email" name="email" placeholder="Email" required />
                <div class="password-container">
                    <input type="password" name="password" placeholder="Password" id="login-password" required />
                    <span class="input-icon">
                        <i class="bi bi-eye-fill toggle-password" id="toggle-login-password"></i>
                    </span>
                </div>
                <button type="submit" name="login">Login</button>
                <a href="#" onclick="showForgotPasswordModal()" class="gap">Forgot Password?</a>
            </form>
        </div>
        <div class="form-container sign-up-container">
            <form id="sign-up-form" method="POST" action="">
                <input type="hidden" name="action" value="signup">
                <h1 class="blackText">Create Account</h1>
                <input type="text" name="name" placeholder="Name" required />
                <input type="email" name="email" placeholder="Email" required />
                <div class="password-container">
                    <input type="password" name="password" placeholder="Password" id="signup-password" required />
                    <span class="input-icon">
                        <i class="bi bi-eye-fill toggle-password" id="toggle-signup-password"></i>
                    </span>
                </div>
                <button type="submit" name="signup">Sign Up</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p class="displayText">To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="login">Login</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p class="displayText">Enter your personal details and start your journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="overlay-modal"></div>

<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="custom-modal" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Forgot Password</h5>
                <button type="button" class="close" onclick="closeModal('forgotPasswordModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="forgot_password">
                    <div class="mb-3">
                        <input type="email" class="custom-input" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="custom-input" id="phoneNumber" name="phoneNumber" value="+63" placeholder="Phone number" required>
                    </div>
                    <button type="submit" name="forgot_password" class="btn btn-primary">Send PIN Code</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="custom-modal" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="close" onclick="closeModal('resetPasswordModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="reset_password">
                    <div class="mb-3">
                        <input type="text" class="custom-input" id="pin_code" name="pin_code" placeholder="Enter the PIN code" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="custom-input" id="new_password" name="new_password" placeholder="Enter new password" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="custom-input" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>
                    <div class="checkbox-container">
                        <input type="checkbox" id="show_password_checkbox" onclick="togglePasswordVisibility()">
                        <label for="show_password_checkbox" class="small-gap">Show Passwords</label>
                    </div>
                    <button type="submit" name="reset_password" class="btn btn-primary">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include_once('includes/footer.php'); ?>
<script src="includes/js/sweetalert2.all.js"></script>
<script src="includes/js/scripts.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const overlayModal = document.querySelector('.overlay-modal');
        const forgotPasswordModal = document.getElementById('forgotPasswordModal');
        const resetPasswordModal = document.getElementById('resetPasswordModal');

        function showForgotPasswordModal() {
            if (forgotPasswordModal && overlayModal) {
                forgotPasswordModal.style.display = 'block';
                overlayModal.style.display = 'block';
            }
        }

        function showResetPasswordModal() {
            if (resetPasswordModal && overlayModal) {
                resetPasswordModal.style.display = 'block';
                overlayModal.style.display = 'block';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal && overlayModal) {
                modal.style.display = 'none';
                overlayModal.style.display = 'none';
            }
        }

        // Expose functions to the global scope if needed
        window.showForgotPasswordModal = showForgotPasswordModal;
        window.showResetPasswordModal = showResetPasswordModal;
        window.closeModal = closeModal;
    });

    document.addEventListener('DOMContentLoaded', function() {
        const showPasswordCheckbox = document.getElementById('show_password_checkbox');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');

        showPasswordCheckbox.addEventListener('click', function() {
            const type = newPassword.type === 'password' ? 'text' : 'password';
            newPassword.type = type;
            confirmPassword.type = type;
        });
    });
</script>