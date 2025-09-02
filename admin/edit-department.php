<?php
// Include database connection
include '../db/dbconnection.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $departmentId = intval($_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle form submission
        $departmentName = isset($_POST['department_name']) ? trim($_POST['department_name']) : '';

        if (empty($departmentName)) {
            echo "<script>Swal.fire({icon: 'error', title: 'Error!', text: 'Department name is required.', confirmButtonText: 'OK'});</script>";
        } else {
            try {
                // Update department details
                $query = "UPDATE tbldepartment SET department_name = :department_name WHERE department_id = :department_id";
                $stmt = $dbh->prepare($query);
                $stmt->bindParam(':department_name', $departmentName);
                $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
                $stmt->execute();

                // Redirect with a success message
                header("Location: manage-department.php?id=" . urlencode($departmentId) . "&status=success&message=" . urlencode("Department updated successfully."));
                exit();
            } catch (PDOException $e) {
                echo "<script>Swal.fire({icon: 'error', title: 'Error!', text: 'Database error: " . htmlspecialchars($e->getMessage()) . "', confirmButtonText: 'OK'});</script>";
            }
        }
    }

    try {
        // Fetch department details
        $query = "SELECT department_name FROM tbldepartment WHERE department_id = :department_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
        $stmt->execute();
        $department = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($department) {
            // Display edit form
            ?>
            <!doctype html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Edit Department</title>
                <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
                <link href="../admin/assets/css/styles.css?v=1.0" rel="stylesheet">
                <link href="../admin/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet" >
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
                                <h3 class="page-title enhanced-page-title">Edit Department</h3>
                                <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                                    <ol class="breadcrumb enhanced-breadcrumb">
                                        <li class="breadcrumb-item"><a href="manage-department.php?id=<?= htmlspecialchars($departmentId) ?>">Manage Department & Courses</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Edit Department</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <div class="custom-form-container">
                            <div class="custom-form-wrapper">
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="department_name" class="custom-label">Department Name</label>
                                        <input type="text" class="form-control" id="department_name" name="department_name" value="<?php echo htmlspecialchars($department['department_name']); ?>" required>
                                    </div>
                                    <!-- Cancel Button -->
                                    <button type="button" class="action-button cancel-action" onclick="window.location.href='manage-department.php?id=<?= htmlspecialchars($departmentId) ?>'">Cancel</button>
                                    <!-- Submit Button -->
                                    <button type="submit" class="action-button submit-action">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- main-panel ends -->
            </div>
            <script src="../admin/assets/js/popper.min.js"></script>
            <script src="../admin/assets/js/bootstrap.min.js"></script>
            <script src="../admin/assets/js/sweetalert2.all.min.js"></script>
            </body>
            </html>
            <?php
        } else {
            echo "<p>Department not found.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>No department ID specified.</p>";
}
?>
