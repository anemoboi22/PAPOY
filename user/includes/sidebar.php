<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>

<aside class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white sidebar sidebar-offcanvas">
    <div class="user-info text-center">
        <?php
            include('../db/dbconnection.php'); // Make sure to include your database connection file

            $aid = $_SESSION['userid']; // Fetch the admin ID from the session

            $sql = "SELECT * FROM users WHERE user_id = :aid";

            $query = $dbh->prepare($sql);
            $query->bindParam(':aid', $aid, PDO::PARAM_INT);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_OBJ);

                // If user data is found, extract necessary values
                if ($user) {
                    $adminName = htmlentities($user->nickName);
                    $adminEmail = htmlentities($user->email);
                    $username = htmlentities($user->fullname);
                    $profileImage = $user->ProfileImage;
                }

            // Profile image logic
            $defaultImage = '../includes/images/face8.jpg';
            $profileImageTag = '<img src="' . (empty($user->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($user->ProfileImage)) . '" alt="User" class="img-fluid rounded-circle mb-2 custom-profile-img" width="150" height="150"/>';    
        ?>
        <?php echo $profileImageTag; // Display profile image ?>
        <div class="user-name"><?php echo $username; ?></div>
        <div class="user-email"><?php echo $adminEmail; ?></div>
    </div>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="prospectus.php" class="nav-link" id="dash" aria-current="page">
                <i class="bi bi-file-earmark-check-fill"></i>
                Prospectus
            </a>
        </li>
        </li>
        <li>
            <a href="view-Evaluation.php" class="nav-link">
                <i class="bi bi-file-earmark-text"></i>
                Self Evaluation
            </a>
        </li>
        <li>
            <a href="degree-history.php" class="nav-link">
                <i class="bi bi-file-earmark-text"></i>
                Degree Program History
            </a>
        </li>
    </ul>
    <script src="../user/assets/js/sidebar.js"></script>
</aside>
