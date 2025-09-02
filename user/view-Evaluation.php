<?php 
include('../db/dbconnection.php');
session_start();

$userId = $_SESSION['userid'];

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

$selectedCourseName = '';
$effectiveYear = '';

$courseDetails = [];
$gradedCourses = [];
$ungradedCourses = [];

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

    // Get selected course name from the tblcourses table using the course_id from the users table
    $stmtCourseName = $dbh->prepare("SELECT course_name 
                                     FROM tblcourses 
                                     WHERE course_id = :course_id 
                                     LIMIT 1");
    $stmtCourseName->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
    $stmtCourseName->execute();
    $selectedCourse = $stmtCourseName->fetch(PDO::FETCH_ASSOC);
    $selectedCourseName = $selectedCourse['course_name'] ?? '';

    // Fetch all graded courses for the user based on their course_id from the users table
    $stmtFetchGradedCourses = $dbh->prepare("SELECT c.id, c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, g.grade 
                                              FROM courses c 
                                              JOIN grades g ON c.id = g.course_id 
                                              WHERE g.user_id = :user_id 
                                              AND c.course_id = :course_id 
                                              ORDER BY c.id");
    $stmtFetchGradedCourses->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtFetchGradedCourses->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
    $stmtFetchGradedCourses->execute();
    $gradedCourses = $stmtFetchGradedCourses->fetchAll(PDO::FETCH_ASSOC);

    // Fetch ungraded courses based on the course_id and effective year
    $stmtFetchUngradedCourses = $dbh->prepare("SELECT c.id, c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, c.effective_year, NULL AS grade 
                                               FROM courses c 
                                               WHERE c.course_id = :course_id 
                                               AND c.effective_year = :effective_year 
                                               AND c.id NOT IN (SELECT course_id FROM grades WHERE user_id = :user_id) 
                                               ORDER BY c.id");
    $stmtFetchUngradedCourses->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
    $stmtFetchUngradedCourses->bindParam(':effective_year', $effectiveYear, PDO::PARAM_STR);
    $stmtFetchUngradedCourses->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtFetchUngradedCourses->execute();
    $ungradedCourses = $stmtFetchUngradedCourses->fetchAll(PDO::FETCH_ASSOC);

    // Combine graded and ungraded courses, ensuring all relevant courses are displayed
    $courseDetails = array_merge($gradedCourses, $ungradedCourses);

    // Group courses by year and semester for display
    $groupedCourses = [];
    foreach ($courseDetails as $courseDetail) {
        $year = $courseDetail['year'];
        $semester = $courseDetail['semester'];

        if (!isset($groupedCourses[$year])) {
            $groupedCourses[$year] = ['1st Semester' => [], '2nd Semester' => []];
        }

        $groupedCourses[$year][$semester][] = $courseDetail;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Self Evaluation</title>
    <link href="../user/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../user/assets/css/styles.css?v=1.4" rel="stylesheet">
    <link href="../user/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../webicon/android-chrome-192x192.png">
    <script src="../user/assets/js/sweetalert2.all.min.js"></script>
</head>
<body>
<div class="page-body-wrapper g-0">
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content-wrapper">
            <?php if (isset($_SESSION['success_message'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire('Success', '<?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>', 'success');
                    });
                </script>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if ($campusMismatch): ?>
                <div class="alert alert-warning" role="alert">
                    Set your Student ID# first.
                </div>
            <?php elseif ($userCourse): ?>
                <div class="page-header enhanced-page-header">
                    <div class="header-content">
                        <h3 class="page-title enhanced-page-title"> View Self Evaluation </h3>
                        <span class="total-all-units" id="totalAllUnits">Total Units: 0</span>
                        <span class="gwa" id="gwa" data-bs-toggle="tooltip" title="">GWA: N/A</span>
                        <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                            <ol class="breadcrumb enhanced-breadcrumb">
                                <li class="breadcrumb-item"><a href="prospectus.php">Prospectus</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Self Evaluation</li>
                            </ol>
                        </nav>
                    </div>             
                </div>

                <div class="prospectus-container">
                    <div class="course-details">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h4><?php echo htmlspecialchars($selectedCourseName ?? '', ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($effectiveYear ?? '', ENT_QUOTES, 'UTF-8') . ')'; ?></h4>
                        <div>
                            <button class="btn btn-success dropdown-toggle" style="transform: translateY(-5px);" data-bs-toggle="dropdown" aria-expanded="false">
                                Filter Grades
                            </button>
                                <ul class="dropdown-menu bg-success text-light" id="filter-grades-menu">
                                    <li><a class="dropdown-item" href="#" onclick="filterGrades('all')">All Grades</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterGrades('blank')">Blank</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterGrades('honor-dq')">Honor DQ (2.6 to 3.0)</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterGrades('none-passing')">None Passing (below 3.1, including INC, DRP, NA, NG)</a></li>
                                </ul>
                            <button type="submit" class="btn btn-primary" onclick="document.querySelector('#grades-form').submit();" style="transform: translateY(-5px);">Save Grades</button>
                            <button class="btn btn-danger" style="transform: translateY(-5px);" id="generate-report-btn">
                                <i class="bi bi-filetype-pdf" style="margin-right: 5px;"></i> Generate Self Evaluation
                            </button>
                        </div>
                    </div>

                        <form method="POST" id="grades-form" action="functions/update-grades.php">
                            <?php if ($groupedCourses): ?>
                                <!-- Tab Navigation -->
                                <ul class="nav nav-tabs" id="yearTabs" role="tablist">
                                    <?php $isFirst = true; ?>
                                    <?php foreach ($groupedCourses as $year => $semesters): ?>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link <?php echo $isFirst ? 'active' : ''; ?>" id="<?php echo strtolower(str_replace(' ', '-', $year)); ?>-tab" data-bs-toggle="tab" href="#<?php echo strtolower(str_replace(' ', '-', $year)); ?>" role="tab" aria-controls="<?php echo strtolower(str_replace(' ', '-', $year)); ?>" aria-selected="true"><?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?></a>
                                        </li>
                                        <?php $isFirst = false; ?>
                                    <?php endforeach; ?>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content" id="yearTabsContent">
                                    <?php foreach ($groupedCourses as $year => $semesters): ?>
                                        <div class="tab-pane fade <?php echo ($year === 'First Year') ? 'show active' : ''; ?>" id="<?php echo strtolower(str_replace(' ', '-', $year)); ?>" role="tabpanel" aria-labelledby="<?php echo strtolower(str_replace(' ', '-', $year)); ?>-tab">
                                            <h5 class="mt-2"><?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?></h5>

                                            <?php foreach ($semesters as $semesterName => $courses): ?>
                                                <div class="semester-section">
                                                    <h6><?php echo htmlspecialchars($semesterName, ENT_QUOTES, 'UTF-8'); ?></h6>
                                                    <div class="scrollable-table-wrapper">
                                                        <table class="prospectus-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Course Code</th>
                                                                    <th>Descriptive Title</th>
                                                                    <th>Co-/Prerequisite</th>
                                                                    <th>Units</th>
                                                                    <th>Grades</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($courses as $course): ?>
                                                                    <tr class="course-row" data-grade="<?php echo htmlspecialchars($course['grade'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                                        <td <?php echo (isset($course['dependent']) && $course['dependent']) ? 'data-bs-toggle="tooltip" title="You can enroll this course because you have an INC in the prerequisite"' : ''; ?>><?php echo htmlspecialchars($course['course_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                        <td><?php echo htmlspecialchars($course['descriptive_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                        <td><?php echo htmlspecialchars($course['co_prerequisite'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                        <td><?php echo htmlspecialchars($course['units'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                        <td class="small-td">
                                                                            <select name="grades[<?php echo htmlspecialchars($course['id'], ENT_QUOTES, 'UTF-8'); ?>]" class="form-select grade-select">
                                                                                <option value="">Select Grade</option>
                                                                                <?php 
                                                                                $grades = ['1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0', '2.1', '2.2', '2.3', '2.4', '2.5', '2.6', '2.7', '2.8', '2.9', '3.0', '3.1', '3.2', '3.3', '3.4', '3.5', 'INC', 'DRP', 'NA', 'NG'];
                                                                                foreach ($grades as $grade): ?>
                                                                                    <option value="<?php echo $grade; ?>" <?php echo ($course['grade'] === $grade) ? 'selected' : ''; ?>><?php echo $grade; ?></option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="3"></td>
                                                                    <td><strong>Total Units:</strong> <span class="total-units" data-year-semester="<?php echo htmlspecialchars($year . '-' . $semesterName, ENT_QUOTES, 'UTF-8'); ?>">0</span><input type="hidden" name="total_units[<?php echo htmlspecialchars($year . '-' . $semesterName, ENT_QUOTES, 'UTF-8'); ?>]" class="hidden-total-units" value="0"></td>
                                                                    <td><strong>Total Grade:</strong> <span class="total-grade" data-year-semester="<?php echo htmlspecialchars($year . '-' . $semesterName, ENT_QUOTES, 'UTF-8'); ?>">0.00</span><input type="hidden" name="total_grades[<?php echo htmlspecialchars($year . '-' . $semesterName, ENT_QUOTES, 'UTF-8'); ?>]" class="hidden-total-grade" value="0.00"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4"></td>
                                                                    <td><strong>GPA:</strong> <span class="weighted-grade" data-year-semester="<?php echo htmlspecialchars($year . '-' . $semesterName, ENT_QUOTES, 'UTF-8'); ?>">0.00</span><input type="hidden" name="gpas[<?php echo htmlspecialchars($year . '-' . $semesterName, ENT_QUOTES, 'UTF-8'); ?>]" class="hidden-weighted-grade" value="0.00"></td>
                                                                </tr>
                                                            </tfoot>
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

                            <input type="hidden" name="total_all_units" id="hiddenTotalAllUnits" value="0">
                            <input type="hidden" name="gwa" id="hiddenGwa" value="0.00">
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    No course selected for the user.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../user/assets/js/popper.min.js"></script>
<script src="../user/assets/js/evaluation.js"></script>
<script src="../user/assets/js/bootstrap.min.js"></script>
<script src="../user/assets/js/sweetalert2.all.min.js"></script>
</body>
</html>