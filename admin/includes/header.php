<?php
include('../db/dbconnection.php'); // Database connection

$aid = $_SESSION['adminid'];

$sql = "SELECT * FROM tbladmin WHERE ID = :aid";

$query = $dbh->prepare($sql);
$query->bindParam(':aid', $aid, PDO::PARAM_STR);
$query->execute();
$admin = $query->fetch(PDO::FETCH_OBJ);

// If user data is found, extract necessary values
if ($admin) {
    $adminName = htmlentities($admin->AdminName);
    $adminEmail = htmlentities($admin->Email);
    $username = htmlentities($admin->UserName);
    $mobileNumber = htmlentities($admin->MobileNumber);
    $campusNumber = htmlentities($admin->campus_number);
    $profileImage = $admin->ProfileImage;
}

// Profile image logic
$defaultImage = '../includes/images/face8.jpg';
$profileImageTag = '<img src="' . (empty($admin->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($admin->ProfileImage)) . '" alt="Admin" class="profile-image rounded-circle me-3" width="150" height="150"/>';
$profileImageDropdownTag = '<img src="' . (empty($admin->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($admin->ProfileImage)) . '" alt="Admin" class="profile-image-lg rounded-circle my-3" width="150" height="150"/>';
$profileImageXLDropdownTag = '<img src="' . (empty($admin->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($admin->ProfileImage)) . '" alt="Admin" class="custom-profile-img rounded-circle my-3" width="150" height="150"/>';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">PAPOY</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $profileImageTag; // Display profile image ?>
                        <span><?php echo $adminName; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-center" aria-labelledby="navbarDropdown">
                        <li>
                            <?php echo $profileImageDropdownTag; // Use separate image tag for dropdown ?>
                            <div class="dropdown-header">
                                <strong><?php echo $adminName; ?></strong><br>
                                <small><?php echo $adminEmail; ?></small>
                            </div>
                        </li>
                        <li><a class="dropdown-item d-flex align-items-center" href="profile.php"><i class="bi bi-person me-2"></i> My Profile</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="settings.php"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="logout.php"><i class="bi bi-power me-2"></i> Sign Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
