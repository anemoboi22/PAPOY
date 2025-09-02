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

// Check if the user has already selected a course
$sqlCheckCourse = "SELECT course_id FROM users WHERE user_id = :user_id";
$queryCheckCourse = $dbh->prepare($sqlCheckCourse);
$queryCheckCourse->bindParam(':user_id', $userId, PDO::PARAM_INT);
$queryCheckCourse->execute();
$userCourse = $queryCheckCourse->fetchColumn();

$courseDetails = [];
$selectedCourseName = '';
$effectiveYear = '';
$gradedCourses = [];
$ungradedCourses = [];

if ($userCourse) {
    // Get selected course name and effective year
    $stmtCourseName = $dbh->prepare("SELECT tc.course_name, c.effective_year FROM tblcourses tc INNER JOIN courses c ON tc.course_id = c.course_id WHERE tc.course_id = :course_id LIMIT 1");
    $stmtCourseName->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
    $stmtCourseName->execute();
    $selectedCourse = $stmtCourseName->fetch(PDO::FETCH_ASSOC);
    $selectedCourseName = $selectedCourse['course_name'] ?? '';
    $effectiveYear = $selectedCourse['effective_year'] ?? '';

    // Fetch all graded courses for the user from both past and current degree programs
    $stmtFetchGradedCourses = $dbh->prepare("SELECT c.id, c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, g.grade FROM courses c JOIN grades g ON c.id = g.course_id WHERE g.user_id = :user_id ORDER BY FIELD(c.year, 'First Year', 'Second Year', 'Third Year', 'Fourth Year'), FIELD(c.semester, '1st Semester', '2nd Semester')");
    $stmtFetchGradedCourses->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtFetchGradedCourses->execute();
    $gradedCourses = $stmtFetchGradedCourses->fetchAll(PDO::FETCH_ASSOC);

    // Fetch ungraded courses for the selected degree program and effective year
    $stmtFetchUngradedCourses = $dbh->prepare("SELECT c.id, c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, c.effective_year, NULL AS grade FROM courses c WHERE c.course_id = :course_id AND c.effective_year = :effective_year AND c.id NOT IN (SELECT course_id FROM grades WHERE user_id = :user_id) ORDER BY FIELD(c.year, 'First Year', 'Second Year', 'Third Year', 'Fourth Year'), FIELD(c.semester, '1st Semester', '2nd Semester')");
    $stmtFetchUngradedCourses->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
    $stmtFetchUngradedCourses->bindParam(':effective_year', $enrollmentYear, PDO::PARAM_STR);
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
    <link href="../user/assets/css/styles.css?v=2.0" rel="stylesheet">
    <link href="../user/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
                        <h4><?php echo htmlspecialchars($selectedCourseName) . ' (' . ($effectiveYear == $enrollmentYear ? htmlspecialchars($effectiveYear) : 'Effective year does not exist') . ')'; ?></h4>
                        <form method="POST" id="grades-form" action="functions/update-grades.php">
                            <?php if ($groupedCourses): ?>
                                <?php foreach ($groupedCourses as $year => $semesters): ?>
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
                                                                    <input type="text" name="grades[<?php echo $course['id']; ?>]" class="grade-input text-center" data-units="<?php echo $course['units']; ?>" data-co-prerequisite="<?php echo htmlspecialchars($course['co_prerequisite']); ?>" value="<?php echo htmlspecialchars($course['grade'] === 'INC' ? 'INC' : $course['grade']); ?>" placeholder="Enter grade">
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td><strong>Total Units:</strong> <span class="total-units" data-year-semester="<?php echo $year . '-' . $semesterName; ?>">0</span><input type="hidden" name="total_units[<?php echo $year . '-' . $semesterName; ?>]" class="hidden-total-units" value="0"></td>
                                                            <td><strong>Total Grade:</strong> <span class="total-grade" data-year-semester="<?php echo $year . '-' . $semesterName; ?>">0.00</span><input type="hidden" name="total_grades[<?php echo $year . '-' . $semesterName; ?>]" class="hidden-total-grade" value="0.00"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4"></td>
                                                            <td><strong>GPA:</strong> <span class="weighted-grade" data-year-semester="<?php echo $year . '-' . $semesterName; ?>">0.00</span><input type="hidden" name="gpas[<?php echo $year . '-' . $semesterName; ?>]" class="hidden-weighted-grade" value="0.00"></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                                <button type="submit" class="btn btn-primary">Save Grades</button>
                            <?php else: ?>
                                <p>No courses found for this degree program.</p>
                            <?php endif; ?>
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
document.addEventListener('DOMContentLoaded', function() {
    calculateAll();
    applyPrerequisiteRules(); // Apply rules on page load
});

document.addEventListener('input', function(event) {
    if (event.target.classList.contains('grade-input')) {
        calculateAll();
        applyPrerequisiteRules(); // Apply rules whenever grades are updated
    }
});

document.querySelector('#grades-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    fetch('functions/update-grades.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', data.message, 'success').then(() => {
                // Update the DOM dynamically without reloading the page
                document.querySelectorAll('.grade-input').forEach(function(input) {
                    const courseId = input.name.match(/grades\[(\d+)\]/)[1]; // Extract course ID from input name
                    if (formData.get(`grades[${courseId}]`)) {
                        input.value = formData.get(`grades[${courseId}]`);
                    }
                });

                // Apply the prerequisite rules after saving grades
                applyPrerequisiteRules();
                calculateAll(); // Recalculate total units, grades, and GPA
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'An unexpected error occurred.', 'error');
        console.error('Error:', error);
    });
});

function calculateAll() {
    document.querySelectorAll('.semester-section').forEach(function(semesterSection) {
        var totalUnits = 0;
        var weightedGrade = 0;
        var yearSemester = semesterSection.querySelector('.total-units').getAttribute('data-year-semester');

        semesterSection.querySelectorAll('.grade-input').forEach(function(input) {
            var gradeValue = input.value.trim();
            var grade = (gradeValue === 'INC') ? 5 : parseFloat(gradeValue) || 0;
            var units = parseFloat(input.getAttribute('data-units'));
            totalUnits += units;
            weightedGrade += grade * units;
        });

        var gpa = totalUnits ? (weightedGrade / totalUnits) : 0;

        document.querySelector('.total-units[data-year-semester="' + yearSemester + '"]').textContent = totalUnits.toFixed(0);
        document.querySelector('.total-grade[data-year-semester="' + yearSemester + '"]').textContent = weightedGrade.toFixed(1);
        document.querySelector('.weighted-grade[data-year-semester="' + yearSemester + '"]').textContent = gpa.toFixed(1);

        // Update hidden input fields with the calculated values
        document.querySelector('input.hidden-total-units[name="total_units[' + yearSemester + ']"').value = totalUnits.toFixed(0);
        document.querySelector('input.hidden-total-grade[name="total_grades[' + yearSemester + ']"').value = weightedGrade.toFixed(1);
        document.querySelector('input.hidden-weighted-grade[name="gpas[' + yearSemester + ']"').value = gpa.toFixed(1);
    });
}

function applyPrerequisiteRules() {
    // First, remove all previous styles and reset attributes
    document.querySelectorAll('.grade-input').forEach(function(input) {
        input.removeAttribute('readonly');
        input.closest('tr').classList.remove('text-danger', 'fw-bold');
    });

    // Go through all grade inputs and mark prerequisites for INC or 5 grades
    document.querySelectorAll('.grade-input').forEach(function(input) {
        var gradeValue = input.value.trim();
        var courseRow = input.closest('tr');

        if (gradeValue === 'INC') {
            // Mark the current course as having an INC grade
            courseRow.classList.add('text-danger', 'fw-bold');

            // Get all courses that depend on this one (based on prerequisite column)
            var courseTitle = courseRow.querySelector('td:nth-child(2)').textContent.trim();
            
            document.querySelectorAll('.grade-input').forEach(function(linkedInput) {
                var linkedCourseRow = linkedInput.closest('tr');
                var linkedPrerequisiteText = linkedCourseRow.querySelector('td:nth-child(3)').textContent;

                if (linkedPrerequisiteText.includes(courseTitle)) {
                    // Mark the dependent course row and disable input
                    linkedCourseRow.classList.add('text-danger', 'fw-bold');
                    linkedInput.setAttribute('readonly', true);
                }
            });
        } else if (gradeValue == '5') {
            // If grade is 5, mark the row in red but do not disable input
            courseRow.classList.add('text-danger', 'fw-bold');
        }
    });
}
</script>
<script src="../user/assets/js/popper.min.js"></script>
<script src="../user/assets/js/bootstrap.min.js"></script>
<script src="../user/assets/js/sweetalert2.all.min.js"></script>
</body>
</html>
