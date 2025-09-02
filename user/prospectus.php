<?php 
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the login status is set
$loginStatus = isset($_SESSION['login_status']) ? $_SESSION['login_status'] : '';

// Clear the login status after displaying the notification
unset($_SESSION['login_status']);

include('../db/dbconnection.php'); // Include your database connection file

// Assuming user_id is stored in the session after login
$userId = $_SESSION['userid']; // Adjust as necessary to match your session variable name

// Fetch student ID from users table using user_id from session
$sqlStudentID = "SELECT student_id FROM users WHERE user_id = :user_id";
$queryStudentID = $dbh->prepare($sqlStudentID);
$queryStudentID->bindParam(':user_id', $userId, PDO::PARAM_INT);
$queryStudentID->execute();
$studID = $queryStudentID->fetchColumn();

// Extract campus number and year from student ID
$campusNumber = substr($studID, 0, 1);
$enrollmentYear = '20' . substr($studID, 1, 2);

// Fetch campus_number from tbladmin table to validate student ID
$sqlCampusNumber = "SELECT campus_number FROM tbladmin WHERE ID = 1";
$queryCampusNumber = $dbh->prepare($sqlCampusNumber);
$queryCampusNumber->execute();
$setCampusNumber = $queryCampusNumber->fetchColumn();

// Check if the campus number from student ID matches the set campus number
$campusMismatch = false;
if ($campusNumber != $setCampusNumber) {
    $campusMismatch = true;
}

// Check if the user has already selected a course
$sqlCheckCourse = "SELECT course_id FROM users WHERE user_id = :user_id";
$queryCheckCourse = $dbh->prepare($sqlCheckCourse);
$queryCheckCourse->bindParam(':user_id', $userId, PDO::PARAM_INT);
$queryCheckCourse->execute();
$userCourse = $queryCheckCourse->fetchColumn();

// Fetch Departments for the modal
$sqlDepartments = "SELECT * FROM tbldepartment";
$queryDepartments = $dbh->prepare($sqlDepartments);
$queryDepartments->execute();
$departments = $queryDepartments->fetchAll(PDO::FETCH_OBJ);

// Fetch course names and IDs from tblcourses
$stmt = $dbh->prepare("SELECT course_id, course_name FROM tblcourses");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If the user has selected a course, fetch corresponding course details from courses table
$courseDetails = [];
if ($userCourse && !$campusMismatch) {
    // Get all effective years from the courses table
    $stmtEffectiveYears = $dbh->prepare("SELECT DISTINCT effective_year FROM courses ORDER BY effective_year ASC");
    $stmtEffectiveYears->execute();
    $effectiveYears = $stmtEffectiveYears->fetchAll(PDO::FETCH_COLUMN);

    // Determine the applicable effective year for the student based on enrollment year
    $effectiveYear = null;
    foreach ($effectiveYears as $year) {
        if ($enrollmentYear >= $year) {
            $effectiveYear = $year;
        } else {
            break;
        }
    }

    // Set a default message if no effective year is found
    if ($effectiveYear === null) {
        $effectiveYear = 'No applicable effective year found';
    }

    // Fetch course name using the course_id
    $stmtCourseName = $dbh->prepare("SELECT course_name FROM tblcourses WHERE course_id = :course_id");
    $stmtCourseName->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
    $stmtCourseName->execute();
    $selectedCourse = $stmtCourseName->fetch(PDO::FETCH_ASSOC);
    $selectedCourseName = $selectedCourse['course_name'] ?? ''; 

    // Fetch course details based on the selected effective year
    if ($effectiveYear !== 'No applicable effective year found') {
        $stmtFetchCourses = $dbh->prepare("SELECT c.id, c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, c.lec_hours, c.lab_hours, c.total_hours, c.effective_year FROM courses c INNER JOIN tblcourses tc ON c.course_id = tc.course_id WHERE tc.course_id = :course_id AND c.effective_year = :effective_year ORDER BY FIELD(c.year, 'First Year', 'Second Year', 'Third Year', 'Fourth Year'), c.semester");
        $stmtFetchCourses->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
        $stmtFetchCourses->bindParam(':effective_year', $effectiveYear, PDO::PARAM_INT);
        $stmtFetchCourses->execute();
        $courseDetails = $stmtFetchCourses->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prospectus</title>
    <link href="../user/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../user/assets/css/styles.css?v=1.1" rel="stylesheet">
    <link rel="icon" type="image/png" href="../webicon/android-chrome-192x192.png">
    <link href="../user/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="page-body-wrapper g-0">
    <!-- Partial for the sidebar -->
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content-wrapper">
            <?php if ($campusMismatch): ?>
                <div class="alert alert-warning" role="alert">
                    Set your Student ID# first.
                </div>
            <?php elseif (!$userCourse): ?>
                <!-- Show modal if course is not selected -->
                <div class="row">
                    <div class="col-md-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="h3 mb-3">Please select your department and course.</h1>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="page-header enhanced-page-header">
                    <div class="header-content">
                        <h3 class="page-title enhanced-page-title">Welcome, <?php echo $username; ?>!</h3>
                        <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                            <ol class="breadcrumb enhanced-breadcrumb">
                                <li class="breadcrumb-item active" aria-current="page">Prospectus</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Combined Container -->
                <div class="prospectus-container">
                    <!-- Display Course Details if the user has selected a course -->
                    <div class="course-details">
                        <h4><?php echo htmlspecialchars($selectedCourseName) . ' (' . htmlspecialchars($effectiveYear) . ')'; ?></h4>
                        
                        <?php if ($courseDetails && $effectiveYear !== 'No applicable effective year found'): ?>
                            <?php
                            // Group courses by year and semester
                            $groupedCourses = [];
                            foreach ($courseDetails as $courseDetail) {
                                $year = $courseDetail['year'];
                                $semester = $courseDetail['semester'];

                                if (!isset($groupedCourses[$year])) {
                                    $groupedCourses[$year] = ['1st Semester' => [], '2nd Semester' => []];
                                }

                                $groupedCourses[$year][$semester][] = $courseDetail;
                            }

                            // Generate Bootstrap tabs for each year level
                            ?>
                            <ul class="nav nav-tabs" id="yearTabs" role="tablist">
                                <?php foreach ($groupedCourses as $year => $semesters): ?>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link <?php echo ($year === 'First Year') ? 'active' : ''; ?>" id="<?php echo strtolower(str_replace(' ', '-', $year)); ?>-tab" data-bs-toggle="tab" href="#<?php echo strtolower(str_replace(' ', '-', $year)); ?>" role="tab" aria-controls="<?php echo strtolower(str_replace(' ', '-', $year)); ?>" aria-selected="true"><?php echo $year; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="tab-content" id="yearTabsContent">
                                <?php foreach ($groupedCourses as $year => $semesters): ?>
                                    <div class="tab-pane fade <?php echo ($year === 'First Year') ? 'show active' : ''; ?>" id="<?php echo strtolower(str_replace(' ', '-', $year)); ?>" role="tabpanel" aria-labelledby="<?php echo strtolower(str_replace(' ', '-', $year)); ?>-tab">
                                        <h5 class="mt-2"><?php echo $year; ?></h5>
                                        <?php foreach ($semesters as $semesterName => $courses): ?>
                                            <div class="semester-section">
                                                <h6><?php echo $semesterName; ?></h6>
                                                <div class="scrollable-table-wrapper">
                                                    <table class="prospectus-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Course Code</th>
                                                                <th>Descriptive Title</th>
                                                                <th>Co-/Prerequisite</th>
                                                                <th>Units</th>
                                                                <th>Hours (Lec)</th>
                                                                <th>Hours (Lab)</th>
                                                                <th>Total Hours</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($courses as $course): ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['descriptive_title']); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['co_prerequisite']); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['units']); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['lec_hours']); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['lab_hours']); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['total_hours']); ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert">
                                No courses found for this degree program.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- main-panel ends -->
</div>

<!-- Department and Course Selection Modal -->
<div class="modal fade" id="selectionModal" tabindex="-1" aria-labelledby="selectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectionModalLabel">Student's Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="selectionForm">
                    <label class="form-label fw-bold fs-5">Student's Information</label>
                    <!-- Student Information -->
                    <div class="mb-3">
                        <label for="studentId" class="form-label">Student ID#</label>
                        <input type="text" class="form-control" id="studentId" name="student_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="yearStarted" class="form-label">Academic Year Started</label>
                        <div class="col-md-12 d-flex align-items-center">
                            <input type="number" class="form-control" id="yearStarted" name="start_date" min="1900" max="2100" required>
                            <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip" data-bs-placement="right" title="Make sure to enter the correct year in this field, as it will become uneditable once you save it."></i>
                        </div>
                    </div>
                    <label class="form-label fw-bold fs-5">Degree Program Information</label>
                    <!-- Department Selection -->
                    <div class="mb-3">
                        <label for="departmentSelect" class="form-label">Select Department</label>
                        <select class="form-select" id="departmentSelect" name="department">
                            <option value="" selected disabled>Select a department</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo $department->department_id; ?>">
                                    <?php echo $department->department_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Course Selection -->
                    <div class="mb-3">
                        <label for="courseSelect" class="form-label">Select Course</label>
                        <select class="form-select" id="courseSelect" name="course">
                            <option value="" selected disabled>Select a course</option>
                            <!-- Courses will be populated by JavaScript based on department selection -->
                        </select>
                    </div>
                    <label class="form-label fw-bold fs-5">Scholarship Information</label>
                     <!-- Scholarship Name -->
                    <div class="mb-3">
                        <label for="scholarshipName" class="form-label">Scholarship Name</label>
                        <input type="text" class="form-control" id="scholarshipName" name="scholarship" required>
                    </div>
                    <!-- Scholarship Start Date -->
                    <div class="mb-3">
                        <label for="scholarshipStart" class="form-label">Scholarship Year Started</label>
                        <input type="number" class="form-control" id="scholarshipStart" name="start_date" min="1900" max="2100" required>
                    </div>
                    <!-- Scholarship End Date -->
                    <div class="mb-3">
                        <label for="scholarshipEnd" class="form-label">Scholarship Year Ended</label>
                        <input type="number" class="form-control" id="scholarshipEnd" name="end_date" min="1900" max="2100" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitSelection">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="../user/assets/js/popper.min.js"></script>
<script src="../user/assets/js/bootstrap.min.js"></script>
<script src="../user/assets/js/sweetalert2.all.min.js"></script>
<script src="../user/assets/js/jquery.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginStatus = "<?php echo $loginStatus; ?>";

        if (loginStatus === 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            Toast.fire({
                icon: "success",
                title: "Signed in successfully"
            });
        }
    });

    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Automatically update the scholarship end year based on the start year
        // $('#scholarshipStart').on('input', function() {
        //     let startYear = parseInt($(this).val());
        //     if (!isNaN(startYear)) {
        //         let endYear = startYear + 5;
        //         $('#scholarshipEnd').val(endYear);
        //     }else {
        //         $('#scholarshipEnd').val("");
        //     }
        // });

        // Existing code for showing the modal if the user has not selected a course
        <?php if (!$userCourse): ?>
            $('#selectionModal').modal('show');
        <?php endif; ?>

        // Handle department change event to fetch related courses
        $('#departmentSelect').on('change', function() {
            var departmentId = $(this).val();
            if (departmentId) {
                $.ajax({
                    type: 'POST',
                    url: './functions/fetch-degree.php',
                    data: { department_id: departmentId },
                    success: function(response) {
                        $('#courseSelect').html(response); // Populate the course dropdown with fetched courses
                    },
                    error: function() {
                        Swal.fire('Error', 'Unable to fetch courses. Try again.', 'error');
                    }
                });
            }
        });

        // Handle form submission
        $('#submitSelection').on('click', function() {
            $('#selectionForm').submit();
        });

        $('#selectionForm').on('submit', function(e) {
            e.preventDefault();

            var departmentId = $('#departmentSelect').val();
            var courseId = $('#courseSelect').val();
            var studentID = $('#studentId').val();
            var scholarshipName = $('#scholarshipName').val();
            var scholarshipStart = $('#scholarshipStart').val();
            var scholarshipEnd = $('#scholarshipEnd').val();
            var yearStarted = $('#yearStarted').val();

            if (departmentId && courseId && studentID && scholarshipName && scholarshipStart && scholarshipEnd && yearStarted) {
                $.ajax({
                    type: 'POST',
                    url: './functions/save-selection.php',
                    data: { department_id: departmentId, course_id: courseId, student_id: studentID, scholarship_name: scholarshipName, scholarship_start: scholarshipStart, scholarship_end: scholarshipEnd, starting_year: yearStarted},
                    success: function(response) {
                        if (response === 'success') {
                            $('#selectionModal').modal('hide');
                            location.reload(); // Refresh the page after saving
                        } else {
                            Swal.fire('Error', 'Unable to save the selection. Try again.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'An error occurred while saving the selection.', 'error');
                    }
                });
            } else {
                Swal.fire('Warning', 'Please fill in all fields.', 'warning');
            }
        });
    });
</script>

</body>
</html>
