<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db/dbconnection.php');
require '../Twilio/autoload.php';
use Twilio\Rest\Client;

$action = $_POST['action'] ?? '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo "";
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Username and Password are required.', 'error'); });</script>";
    } else {
        $query = "SELECT ID, Password FROM tbladmin WHERE Email = :username";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashed_password = md5($password);

        if ($admin && $hashed_password === $admin['Password']) {
            $_SESSION['adminid'] = $admin['ID'];
            $_SESSION['login'] = $username;
            $_SESSION['login_status'] = 'success';

            if (!empty($_POST["remember"])) {
                setcookie("user_login", $username, time() + (10 * 365 * 24 * 60 * 60));
                setcookie("userpassword", $password, time() + (10 * 365 * 24 * 60 * 60));
            } else {
                if (isset($_COOKIE["user_login"])) {
                    setcookie("user_login", "", time() - 3600);
                    setcookie("userpassword", "", time() - 3600);
                }
            }

            header("Location: dashboard.php");
            exit;
        } else {
            echo "";
            echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Invalid username or password.', 'error'); });</script>";
        }
    }
}

// Handle Forgot Password (send PIN)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'forgot_password') {
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $sql = "SELECT ID, MobileNumber, email FROM tbladmin WHERE MobileNumber = REPLACE(:phoneNumber, '+', '') AND email = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $pinCode = rand(100000, 999999);
        $_SESSION['reset_pin'] = $pinCode;
        $_SESSION['reset_admin_id'] = $result->ID;
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
            $adminId = $_SESSION['reset_admin_id'];

            $sql = "UPDATE tbladmin SET Password = :password WHERE ID = :adminId";
            $query = $dbh->prepare($sql);
            $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $query->bindParam(':adminId', $adminId, PDO::PARAM_INT);

            if ($query->execute()) {
                echo "";
                echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Success', 'Password has been successfully reset.', 'success'); });</script>";
                unset($_SESSION['reset_pin']);
                unset($_SESSION['reset_admin_id']);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/login.css?v=7.0" rel="stylesheet">
    <link href="../admin/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../webicon/android-chrome-192x192.png">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg" style="width: 25rem;">
            <div class="card-body">
                <h2 class="card-title text-center text-primary">Hello, Admin!</h2>
                <form id="loginForm" method="POST" action="">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" value="<?php if(isset($_COOKIE["user_login"])) { echo $_COOKIE["user_login"]; } ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" value="<?php if(isset($_COOKIE["userpassword"])) { echo $_COOKIE["userpassword"]; } ?>" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" id="remember" name="remember" class="form-check-input" <?php if(isset($_COOKIE["user_login"])) { ?> checked <?php } ?>>
                        <label for="remember" class="form-check-label">Keep me signed in</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                <div class="text-center mt-3">
                    <a href="#" onclick="showForgotPasswordModal()" class="text-decoration-none">Forgot Password?</a>
                </div>
                <div class="text-center mt-3">
                    <a href="../index.php" class="btn btn-secondary">Back Home</a>
                </div>
            </div>
        </div>
    </div>

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

    <script src="../admin/assets/js/popper.min.js"></script>
    <script src="../admin/assets/js/bootstrap.min.js"></script>
    <script src="../admin/assets/js/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginStatus = "<?php echo isset($_SESSION['login_status']) ? $_SESSION['login_status'] : ''; ?>";
            
            if (loginStatus === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: 'Invalid username or password.',
                    confirmButtonText: 'Try Again'
                });
            }
            
            // Clear the login status after displaying the error message
            <?php unset($_SESSION['login_status']); ?>
        });

        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePasswordIcon.classList.remove('bi-eye');
                togglePasswordIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                togglePasswordIcon.classList.remove('bi-eye-slash');
                togglePasswordIcon.classList.add('bi-eye');
            }
        });

        function showForgotPasswordModal() {
            const forgotPasswordModal = document.getElementById('forgotPasswordModal');
            const overlayModal = document.querySelector('.overlay-modal');
            if (forgotPasswordModal && overlayModal) {
                forgotPasswordModal.style.display = 'block';
                overlayModal.style.display = 'block';
            }
        }

        function showResetPasswordModal() {
            const resetPasswordModal = document.getElementById('resetPasswordModal');
            const overlayModal = document.querySelector('.overlay-modal');
            if (resetPasswordModal && overlayModal) {
                resetPasswordModal.style.display = 'block';
                overlayModal.style.display = 'block';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const overlayModal = document.querySelector('.overlay-modal');
            if (modal && overlayModal) {
                modal.style.display = 'none';
                overlayModal.style.display = 'none';
            }
        }

        function togglePasswordVisibility() {
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            const type = newPassword.type === 'password' ? 'text' : 'password';
            newPassword.type = type;
            confirmPassword.type = type;
        }
    </script>
</body>
</html>
