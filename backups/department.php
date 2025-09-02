<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Department & Degree</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/finalsss.css" rel="stylesheet">
    <link href="../admin/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
                        <h3 class="page-title enhanced-page-title">Add Department & Degree Program</h3>
                        <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                            <ol class="breadcrumb enhanced-breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Department & Degree Program</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card add-department-container">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align: center;">Add Department</h4>
                                <form class="forms-sample" method="post" action="">
                                    <div class="form-group">
                                        <label>College Name</label>
                                        <input type="text" name="department_name" class="form-control" >
                                    </div>
                                    <div class="form-group">
                                        <label>Add Degree Program</label>
                                        <div class="course-input-group">
                                            <input type="text" id="course" class="course-input" placeholder="Degree Name" >
                                            <button type="button" id="add-course" class="course-button">Add Degree</button>
                                        </div>
                                        <div id="course-list"></div>
                                    </div>                                   
                                    <button type="submit" class="btn btn-primary mr-2" name="submit">ADD</button>                                          
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- main-panel ends -->
    </div>

    <script src="../admin/assets/js/popper.min.js"></script>
    <script src="../admin/assets/js/bootstrap.min.js"></script>
    <script src="../admin/assets/js/addDep.js"></script>
    <script src="../admin/assets/js/sweetalert2.all.min.js"></script>

    <?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    include '../db/dbconnection.php';  // Ensure dbconnection.php includes a secure connection

    $department_name = trim($_POST['department_name'] ?? '');
    $course_name = trim($_POST['course'] ?? ''); // Assuming this is the degree program name

    if (empty($department_name) && empty($course_name)) {
        echo "<script>Swal.fire({icon: 'error', title: 'Error!', text: 'At least one of Department or Degree Program name is required.', confirmButtonText: 'OK'});</script>";
    } else {
        try {
            // Initialize messages for existing department and course
            $existing_department_message = "";
            $existing_course_message = "";

            // Check if the department already exists
            $query = "SELECT * FROM tbldepartment WHERE department_name = :department_name";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':department_name', $department_name);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $existing_department = $stmt->fetch(PDO::FETCH_ASSOC)['department_name'];
                $existing_department_message = "Department \"$existing_department\" already exists. ";
            }

            // Insert department if it doesn't exist
            if (empty($existing_department_message)) {
                $query = "INSERT INTO tbldepartment (department_name) VALUES (:department_name)";
                $stmt = $dbh->prepare($query);
                $stmt->bindParam(':department_name', $department_name);
                $stmt->execute();
                
                $department_id = $dbh->lastInsertId();  // Get the ID of the newly inserted department
            } else {
                // If the department exists, get its ID for course checking
                $query = "SELECT department_id FROM tbldepartment WHERE department_name = :department_name";
                $stmt = $dbh->prepare($query);
                $stmt->bindParam(':department_name', $existing_department);
                $stmt->execute();
                $department_id = $stmt->fetchColumn(); // Adjusted to fetch department_id
            }

            // Check if the course already exists
            if (!empty($course_name)) {
                $query = "SELECT * FROM tblcourses WHERE course_name = :course_name AND department_id = :department_id";
                $stmt = $dbh->prepare($query);
                $stmt->bindValue(':course_name', $course_name);
                $stmt->bindValue(':department_id', $department_id);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $existing_course_message = "Course \"$course_name\" already exists in this department.";
                }
            }

            // Display error messages if department or course exists
            if (!empty($existing_department_message) || !empty($existing_course_message)) {
                // Debugging output to check the messages
                error_log("Department message: " . $existing_department_message);
                error_log("Course message: " . $existing_course_message);

                $full_message = trim($existing_department_message . $existing_course_message);
                echo "<script>Swal.fire({icon: 'error', title: 'Error!', text: '$full_message', confirmButtonText: 'OK'});</script>";
                exit; // Stop further execution if either exists
            }

            // If course does not exist, insert it
            if (!empty($course_name)) {
                $query = "INSERT INTO tblcourses (department_id, course_name) VALUES (:department_id, :course_name)";
                $stmt = $dbh->prepare($query);
                $stmt->bindValue(':course_name', $course_name);
                $stmt->bindValue(':department_id', $department_id);
                $stmt->execute();
            }

            echo "<script>Swal.fire({icon: 'success', title: 'Success!', text: 'Department and courses added successfully.', confirmButtonText: 'OK'});</script>";
        } catch (PDOException $e) {
            echo "<script>Swal.fire({icon: 'error', title: 'Error!', text: 'Database error: {$e->getMessage()}', confirmButtonText: 'OK'});</script>";
        }
    }
}
?>

</body>
</html>
