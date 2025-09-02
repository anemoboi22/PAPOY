<?php
session_start();
include('../db/dbconnection.php');

$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/styles.css?v=4.0" rel="stylesheet">
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
                    <h3 class="page-title enhanced-page-title">My Profile</h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Profile Form Starts Here -->
            <div class="profile-card">
                <form action="functions/update-profile.php" method="POST" enctype="multipart/form-data">
                    <div class="row justify-content-center mb-3">
                        <div class="col-md-3 text-center">
                            <?php echo $profileImageXLDropdownTag;?>
                        </div>
                    </div>
                    <label class="form-label fw-bold fs-5">Administrator's Information</label>
                    <div class="mb-3">
                        <label for="adminName" class="form-label">Admin Name</label>
                        <input type="text" class="form-control" id="adminName" name="adminName" value="<?php echo $adminName; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mobileNumber" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" value="<?php echo $mobileNumber; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $adminEmail; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="campusNumber" class="form-label">Campus ID#</label>
                        <input type="campusNumber" class="form-control" id="campusNumber" name="campusNumber" value="<?php echo $campusNumber; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Change Profile Picture</label>
                        <input type="file" name="profile_picture" class="form-control mb-3">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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

<?php if ($status == 'success'): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Profile updated successfully.'
    });
</script>
<?php elseif ($status == 'error'): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'There was an error updating your profile.'
    });
</script>
<?php elseif ($status == 'invalid_file'): ?>
<script>
    Swal.fire({
        icon: 'warning',
        title: 'Invalid File',
        text: 'Please upload a valid image file (jpg, jpeg, png, gif).'
    });
</script>
<?php endif; ?>

</body>
</html>
