<?php
session_start();
include('../db/dbconnection.php');

if (isset($_POST['change_password'])) {
    $current_password = md5($_POST['current_password']);
    $new_password = md5($_POST['new_password']);
    $confirm_password = md5($_POST['confirm_password']);

    // Validate if the new password matches the confirm password
    if ($new_password != $confirm_password) {
        $_SESSION['password_status'] = 'mismatch';
    } else {
        // Get the admin ID from session
        $adminid = $_SESSION['adminid'];

        // Verify if the current password is correct
        $sql = "SELECT ID FROM tbladmin WHERE ID=:adminid AND Password=:current_password";
        $query = $dbh->prepare($sql);
        $query->bindParam(':adminid', $adminid, PDO::PARAM_STR);
        $query->bindParam(':current_password', $current_password, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() > 0) {
            // Update the password
            $sql = "UPDATE tbladmin SET Password=:new_password WHERE ID=:adminid";
            $update_query = $dbh->prepare($sql);
            $update_query->bindParam(':new_password', $new_password, PDO::PARAM_STR);
            $update_query->bindParam(':adminid', $adminid, PDO::PARAM_STR);
            $update_query->execute();

            $_SESSION['password_status'] = 'success';
        } else {
            $_SESSION['password_status'] = 'incorrect_current';
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/styles.css?v=1.0" rel="stylesheet">
    <link href="../admin/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../webicon/android-chrome-192x192.png">
</head>

<body>

<div class="page-body-wrapper g-0">
    <!-- Partial for the sidebar -->
    <?php include_once('includes/sidebar.php'); ?> 
    <div class="main-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content-wrapper">
            <div class="page-header enhanced-page-header">
                <div class="header-content">
                    <h3 class="page-title enhanced-page-title">Settings</h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Settings</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Profile Form Starts Here -->
            <div class="profile-card">
                <h4>Change Password</h4>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                <i class="bi bi-eye" id="toggleCurrentPasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                <i class="bi bi-eye" id="toggleNewPasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="bi bi-eye" id="toggleConfirmPasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
            <!-- Profile Form Ends Here -->

        </div>
    </div>
    <!-- main-panel ends -->
</div>

<script src="../admin/assets/js/popper.min.js"></script>
<script src="../admin/assets/js/bootstrap.min.js"></script>
<script src="../admin/assets/js/jquery.min.js"></script>
<script src="../admin/assets/js/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordStatus = "<?php echo isset($_SESSION['password_status']) ? $_SESSION['password_status'] : ''; ?>";

        if (passwordStatus === 'mismatch') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'New password and confirm password do not match.',
                confirmButtonText: 'OK'
            });
        } else if (passwordStatus === 'incorrect_current') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Current password is incorrect.',
                confirmButtonText: 'OK'
            });
        } else if (passwordStatus === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Password changed successfully.',
                confirmButtonText: 'OK'
            });
        }

        <?php unset($_SESSION['password_status']); ?>
    });

    document.getElementById('toggleCurrentPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('current_password');
        const togglePasswordIcon = document.getElementById('toggleCurrentPasswordIcon');

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

    document.getElementById('toggleNewPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('new_password');
        const togglePasswordIcon = document.getElementById('toggleNewPasswordIcon');

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

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('confirm_password');
        const togglePasswordIcon = document.getElementById('toggleConfirmPasswordIcon');

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
</script>

</body>
</html>
