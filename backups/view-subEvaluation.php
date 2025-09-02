<?php
include('../db/dbconnection.php');
session_start();

$userId = $_SESSION['userid'];

// Check if the user has already selected a course
$sqlCheckCourse = "SELECT course_id FROM users WHERE user_id = :user_id";
$queryCheckCourse = $dbh->prepare($sqlCheckCourse);
$queryCheckCourse->bindParam(':user_id', $userId, PDO::PARAM_INT);
$queryCheckCourse->execute();
$userCourse = $queryCheckCourse->fetchColumn();

$courseDetails = [];
$totalUnits = [];
$totalGrades = [];
$weightedGrades = [];

if ($userCourse) {
    // Get selected course name
    $stmtCourseName = $dbh->prepare("SELECT course_name FROM tblcourses WHERE course_id = :course_id");
    $stmtCourseName->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
    $stmtCourseName->execute();
    $selectedCourse = $stmtCourseName->fetch(PDO::FETCH_ASSOC);
    $selectedCourseName = $selectedCourse['course_name'] ?? '';

    // Fetch course details and grades from the database
    $stmtFetchCourses = $dbh->prepare("
        SELECT c.id, c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, g.grade
        FROM courses c
        LEFT JOIN grades g ON c.id = g.course_id AND g.user_id = :user_id
        INNER JOIN tblcourses tc ON c.course_id = tc.course_id
        WHERE tc.course_id = :course_id
        ORDER BY FIELD(c.year, 'First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year', 'Sixth Year'), c.semester
    ");
    $stmtFetchCourses->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
    $stmtFetchCourses->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtFetchCourses->execute();
    $courseDetails = $stmtFetchCourses->fetchAll(PDO::FETCH_ASSOC);

    // Group courses and calculate total grades and units if grades exist
    foreach ($courseDetails as $courseDetail) {
        $year = $courseDetail['year'];
        $semester = $courseDetail['semester'];
        $units = $courseDetail['units'];
        $grade = $courseDetail['grade'] ?? 0;

        if (!isset($totalUnits[$year][$semester])) {
            $totalUnits[$year][$semester] = 0;
        }
        $totalUnits[$year][$semester] += $units;

        if ($grade > 0) { // Only process if grade exists
            if (!isset($totalGrades[$year][$semester])) {
                $totalGrades[$year][$semester] = 0;
            }

            $totalGrades[$year][$semester] += $grade;
        }
    }

    // Calculate weighted grades
    foreach ($totalUnits as $year => $semesters) {
        foreach ($semesters as $semester => $units) {
            if ($units > 0) {
                $weightedGrades[$year][$semester] = isset($totalGrades[$year][$semester]) ? ($totalGrades[$year][$semester] * 3) / $units : 0;
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Self Evaluation</title>
    <link href="../user/css/bootstrap.min.css" rel="stylesheet">
    <link href="../user/css/styles7.css" rel="stylesheet">
    <link href="../user/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

<div class="page-body-wrapper g-0">
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content-wrapper">
            <?php if ($userCourse): ?>
                <div class="page-header enhanced-page-header">
                    <div class="header-content">
                        <h3 class="page-title enhanced-page-title"> View Self Evaluation </h3>
                        <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                            <ol class="breadcrumb enhanced-breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Self Evaluation</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="prospectus-container">
                    <div class="course-details">
                        <h4><?php echo htmlspecialchars($selectedCourseName); ?></h4>
                        <form method="POST" action="update-grades.php">
                            <?php if ($courseDetails): ?>
                                <?php
                                $groupedCourses = [];
                                foreach ($courseDetails as $courseDetail) {
                                    $year = $courseDetail['year'];
                                    $semester = $courseDetail['semester'];

                                    if (!isset($groupedCourses[$year])) {
                                        $groupedCourses[$year] = ['1st Semester' => [], '2nd Semester' => []];
                                    }

                                    $groupedCourses[$year][$semester][] = $courseDetail;
                                }

                                foreach ($groupedCourses as $year => $semesters): ?>
                                    <div class="year-section">
                                        <h5><?php echo $year; ?></h5>

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
                                                            <th>Grades</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($courses as $course): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                                                <td><?php echo htmlspecialchars($course['descriptive_title']); ?></td>
                                                                <td><?php echo htmlspecialchars($course['co_prerequisite']); ?></td>
                                                                <td><?php echo htmlspecialchars($course['units']); ?></td>
                                                                <td class="small-td">
                                                                    <input type="text" name="grades[<?php echo $course['id']; ?>]" class="grade-input text-center" data-units="<?php echo $course['units']; ?>" value="<?php echo htmlspecialchars($course['grade']); ?>" placeholder="Enter grade">
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td><strong>Total Units:</strong> <?php echo $totalUnits[$year][$semesterName] ?? 0; ?></td>
                                                            <td><strong>Total Grade:</strong> <span id="total-grade-<?php echo $year . '-' . $semesterName; ?>"><?php echo round($totalGrades[$year][$semesterName] ?? 0, 2); ?></span></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4"></td>
                                                            <td><strong>GPA:</strong> <span id="weighted-grade-<?php echo $year . '-' . $semesterName; ?>"><?php echo round($weightedGrades[$year][$semesterName] ?? 0, 2); ?></span></td>
                                                        </tr>
                                                        <!-- Hidden fields for total and weighted grades -->
                                                        <input type="hidden" name="totalGrades[<?php echo $year; ?>][<?php echo $semesterName; ?>]" value="<?php echo round($totalGrades[$year][$semesterName] ?? 0, 2); ?>">
                                                        <input type="hidden" name="weightedGrades[<?php echo $year; ?>][<?php echo $semesterName; ?>]" value="<?php echo round($weightedGrades[$year][$semesterName] ?? 0, 2); ?>">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No courses found for this degree program.</p>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary">Save Grades</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <p>No course selected for the user.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('input', function(event) {
        if (event.target.classList.contains('grade-input')) {
            var semesterSection = event.target.closest('tbody');
            var yearSemester = semesterSection.querySelector('span[id^="weighted-grade-"]').id.split('-').slice(2).join('-');
            var totalUnits = 0;
            var totalGrades = 0;

            semesterSection.querySelectorAll('.grade-input').forEach(function(input) {
                var grade = parseFloat(input.value) || 0;
                var units = parseFloat(input.getAttribute('data-units'));
                totalUnits += units;
                totalGrades += grade;
            });

            var weightedGrade = totalUnits ? (totalGrades * 3) / totalUnits : 0;

            document.getElementById('total-grade-' + yearSemester).textContent = totalGrades.toFixed(2);
            document.getElementById('weighted-grade-' + yearSemester).textContent = weightedGrade.toFixed(2);
        }
    });
</script>

<script src="../user/js/popper.min.js"></script>
<script src="../user/js/bootstrap.min.js"></script>
<script src="../user/js/sweetalert2.all.min.js"></script>
</body>
</html>
