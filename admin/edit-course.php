<?php
// Include database connection
include '../db/dbconnection.php';

// Check if the 'id' parameter is set in the URL and is a valid integer
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $courseId = intval($_GET['id']); // Ensure it's an integer

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle form submission
        $courseName = isset($_POST['course_name']) ? trim($_POST['course_name']) : '';

        if (empty($courseName)) {
            echo "<script>Swal.fire({icon: 'error', title: 'Error!', text: 'Course name is required.', confirmButtonText: 'OK'});</script>";
        } else {
            try {
                // Update course details
                $query = "UPDATE tblcourses SET course_name = :course_name WHERE course_id = :course_id";
                $stmt = $dbh->prepare($query);
                $stmt->bindParam(':course_name', $courseName);
                $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
                $stmt->execute();

                // Get department ID for redirection
                $departmentId = isset($_GET['department_id']) && filter_var($_GET['department_id'], FILTER_VALIDATE_INT) ? intval($_GET['department_id']) : 0;

                // Redirect with a success message
                header("Location: view-department.php?id=" . urlencode($departmentId) . "&status=success&message=" . urlencode("Course updated successfully."));
                exit();
            } catch (PDOException $e) {
                // Display error message
                echo "<script>Swal.fire({icon: 'error', title: 'Error!', text: 'Database error: " . htmlspecialchars($e->getMessage()) . "', confirmButtonText: 'OK'});</script>";
            }
        }
    }

    try {
        // Fetch course details
        $query = "SELECT course_name, department_id FROM tblcourses WHERE course_id = :course_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($course) {
            // Display edit form
            ?>
            <!doctype html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Department & Degree Programs</title>
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
                                <h3 class="page-title enhanced-page-title">Edit Degree Name</h3>
                                <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                                    <ol class="breadcrumb enhanced-breadcrumb">
                                        <li class="breadcrumb-item"><a href="view-department.php?id=<?= htmlspecialchars($course['department_id']) ?>">View Department & Courses</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Edit Course</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <div class="custom-form-container">
                            <div class="custom-form-wrapper">
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="course_name" class="custom-label">Degree Name</label>
                                        <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                                    </div>
                                    <!-- Cancel Button -->
                                    <button type="button" class="action-button cancel-action" onclick="window.location.href='view-department.php?id=<?= htmlspecialchars($course['department_id']) ?>'">Cancel</button>
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
            echo "<p>Course not found.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>No course ID specified or invalid ID.</p>";
}
?>
