<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>

<aside class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white sidebar sidebar-offcanvas">
    <div class="user-info text-center">
    <?php
        include('../db/dbconnection.php'); // Make sure to include your database connection file

        $aid = $_SESSION['adminid']; // Fetch the admin ID from the session

        $sql = "SELECT * FROM tbladmin WHERE ID = :aid";

            $query = $dbh->prepare($sql);
            $query->bindParam(':aid', $aid, PDO::PARAM_INT);
            $query->execute();
            $admin = $query->fetch(PDO::FETCH_OBJ);

                // If user data is found, extract necessary values
                if ($admin) {
                    $adminName = htmlentities($admin->AdminName);
                    $adminEmail = htmlentities($admin->Email);
                    $profileImage = $admin->ProfileImage;
                }

            // Profile image logic
            $defaultImage = '../includes/images/face8.jpg';
            $profileImageTag = '<img src="' . (empty($admin->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($admin->ProfileImage)) . '" alt="Admin" class="img-fluid rounded-circle mb-2 custom-profile-img" width="150" height="150"/>';    
    ?>
        <?php echo $profileImageTag; // Display profile image ?>
        <div class="user-name"><?php echo $adminName; ?></div>
        <div class="user-email"><?php echo $adminEmail; ?></div>
    </div>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link" id="dash" aria-current="page">
                <i class="bi bi-tv"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="manage-department.php" class="nav-link">
                <i class="bi bi-journals"></i>
                Manage Department & Degree Programs
            </a>
        </li>
        <li>
            <a href="#prospectusSubmenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false">
                <i class="bi bi-file-earmark-text"></i>
                Prospectus
            </a>
            <ul class="collapse" id="prospectusSubmenu">
                <li class="nav-item">
                    <a href="add-prospectus.php" class="nav-link">
                        <i class="bi bi-chevron-right"></i>
                        <i class="bi bi-file-earmark-plus-fill"></i>
                        Add Prospectus
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage-prospectus.php" class="nav-link">
                        <i class="bi bi-chevron-right"></i>
                        <i class="bi bi-file-earmark-text-fill"></i>
                        Manage Prospectus
                    </a>
                </li>
                <li class="nav-item">
                    <a href="report-prospectus.php" class="nav-link">
                        <i class="bi bi-chevron-right"></i>
                        <i class="bi bi-filetype-pdf"></i>
                        Prospectus Report
                    </a>
                </li>
            </ul>
        </li>
        <!-- <li>
            <a href="#verifySubmenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false">
                <i class="bi bi-person-check"></i>
                Student Affiliation Verification
            </a>
            <ul class="collapse" id="verifySubmenu">
                <li class="nav-item">
                    <a href="manage-users.php" class="nav-link">
                        <i class="bi bi-chevron-right"></i>
                        <i class="bi bi-person-lines-fill"></i>
                        Manage Student Affiliation
                    </a>
                </li>
                <li class="nav-item">
                    <a href="report-users.php" class="nav-link">
                        <i class="bi bi-chevron-right"></i>
                        <i class="bi bi-person-fill-down"></i>
                        Student Affiliation Report
                    </a>
                </li>
            </ul>
        </li> -->
    </ul>
    <script src="../admin/assets/js/sidebar.js"></script>
</aside>
