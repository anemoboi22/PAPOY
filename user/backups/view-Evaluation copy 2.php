<?php 
include('../db/dbconnection.php');
session_start();

require('../assets/fpdf186/fpdf.php');
include('../../db/dbconnection.php');

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

$courseDetails = [];
$selectedCourseName = '';
$effectiveYear = '';
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

    // Get selected course name and effective year
    $stmtCourseName = $dbh->prepare("SELECT tc.course_name, c.effective_year FROM tblcourses tc INNER JOIN courses c ON tc.course_id = c.course_id WHERE tc.course_id = :course_id LIMIT 1");
    $stmtCourseName->bindParam(':course_id', $userCourse, PDO::PARAM_INT);
    $stmtCourseName->execute();
    $selectedCourse = $stmtCourseName->fetch(PDO::FETCH_ASSOC);
    $selectedCourseName = $selectedCourse['course_name'] ?? '';

    // Fetch all graded courses for the user from both past and current degree programs
    $stmtFetchGradedCourses = $dbh->prepare("SELECT c.id, c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, g.grade FROM courses c JOIN grades g ON c.id = g.course_id WHERE g.user_id = :user_id ORDER BY c.id");
    $stmtFetchGradedCourses->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtFetchGradedCourses->execute();
    $gradedCourses = $stmtFetchGradedCourses->fetchAll(PDO::FETCH_ASSOC);

    // Fetch ungraded courses for the selected degree program and effective year
    $stmtFetchUngradedCourses = $dbh->prepare("SELECT c.id, c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, c.effective_year, NULL AS grade FROM courses c WHERE c.course_id = :course_id AND c.effective_year = :effective_year AND c.id NOT IN (SELECT course_id FROM grades WHERE user_id = :user_id) ORDER BY c.id");
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
    <link href="../user/assets/css/styles.css?v=2.0" rel="stylesheet">
    <link href="../user/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../webicon/android-chrome-192x192.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
<div class="page-body-wrapper g-0">
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content-wrapper">
            <?php if ($campusMismatch): ?>
                <div class="alert alert-warning" role="alert">
                    Campus number does not match from the set campus number.
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
                            <button type="submit" class="btn btn-primary" style="transform: translateY(-5px);">Save Grades</button>
                            <button class="btn btn-success" style="transform: translateY(-5px);" id="generate-report-btn">
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
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($course['course_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                        <td><?php echo htmlspecialchars($course['descriptive_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                        <td><?php echo htmlspecialchars($course['co_prerequisite'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                        <td><?php echo htmlspecialchars($course['units'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                        <td class="small-td">
                                                                            <input type="text" name="grades[<?php echo $course['id']; ?>]" class="grade-input text-center" data-units="<?php echo htmlspecialchars($course['units'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-co-prerequisite="<?php echo htmlspecialchars($course['co_prerequisite'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($course['grade'] === 'INC' ? 'INC' : $course['grade'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter grade">
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
                                <p>No courses found for this degree program.</p>
                            <?php endif; ?>

                            <input type="hidden" name="total_all_units" id="hiddenTotalAllUnits" value="0">
                            <input type="hidden" name="gwa" id="hiddenGwa" value="0.00">
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
    console.log('Page loaded. Adding event listener to Generate Report button.');
    document.querySelector('#generate-report-btn').addEventListener('click', generatePDF);
    
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
            Swal.fire('Success', data.message, 'success');
            // Delay recalculation and DOM updates
            setTimeout(() => {
                document.querySelectorAll('.grade-input').forEach(function(input) {
                    const courseId = input.name.match(/grades\[(\d+)\]/)[1]; // Extract course ID from input name
                    if (formData.get(`grades[${courseId}]`)) {
                        input.value = formData.get(`grades[${courseId}]`);
                    }
                });
                applyPrerequisiteRules();
                calculateAll();
            }, 500); // Adjust the delay time if needed
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
    let totalAllUnits = 0;
    let totalAllGrades = 0;
    let totalUnitsForGWA = 0;
    let hasIncompleteOrMissingGrades = false;

    document.querySelectorAll('.semester-section').forEach(function(semesterSection) {
        var totalUnits = 0;
        var weightedGrade = 0;
        var yearSemester = semesterSection.querySelector('.total-units').getAttribute('data-year-semester');

        semesterSection.querySelectorAll('.grade-input').forEach(function(input) {
            var gradeValue = input.value.trim();
            if (gradeValue === '' || gradeValue === 'INC') {
                hasIncompleteOrMissingGrades = true;
            }
            var grade = (gradeValue === 'INC') ? 5 : parseFloat(gradeValue) || 0;
            var units = parseFloat(input.getAttribute('data-units'));
            totalUnits += units;
            weightedGrade += grade * units;

            // Calculate GWA values
            totalUnitsForGWA += units;
            totalAllGrades += grade * units;
            totalAllUnits += units;
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

    // Calculate GWA
    let gwa = totalUnitsForGWA ? (totalAllGrades / totalUnitsForGWA) : 0;

    // Update GWA and Total Units in the DOM
    if (hasIncompleteOrMissingGrades) {
        document.getElementById('gwa').textContent = 'GWA: N/A';
        document.getElementById('gwa').setAttribute('title', 'GWA is not available because there are incomplete or missing grades.');
    } else {
        document.getElementById('gwa').textContent = 'GWA: ' + gwa.toFixed(1);
        document.getElementById('gwa').removeAttribute('title');
    }

    document.getElementById('totalAllUnits').textContent = 'Total Units: ' + totalAllUnits;

    // Update hidden input fields
    document.getElementById('hiddenTotalAllUnits').value = totalAllUnits;
    document.getElementById('hiddenGwa').value = gwa.toFixed(1);
}

function applyPrerequisiteRules() {
    // First, remove all previous styles and reset attributes
    document.querySelectorAll('.grade-input').forEach(function(input) {
        input.removeAttribute('readonly');
        input.closest('tr').classList.remove('text-danger', 'fw-bold');
    });

    // Recursive function to mark dependent courses
    function markDependentCourses(courseTitle) {
        document.querySelectorAll('.grade-input').forEach(function(linkedInput) {
            var linkedCourseRow = linkedInput.closest('tr');
            var linkedPrerequisiteText = linkedCourseRow.querySelector('td:nth-child(3)').textContent;

            // Split prerequisites by comma, semicolon, or similar delimiter
            var prerequisites = linkedPrerequisiteText.split(/[,;]/).map(function(prereq) {
                return prereq.trim();
            });

            if (prerequisites.some(function(prereq) {
                return prereq === courseTitle; // Exact match check
            })) {
                // Mark the dependent course row and disable input
                linkedCourseRow.classList.add('text-danger', 'fw-bold');
                linkedInput.setAttribute('readonly', true);

                // Get the title of the dependent course
                var dependentCourseTitle = linkedCourseRow.querySelector('td:nth-child(1)').textContent.trim();

                // Recursively check for courses that depend on this one
                markDependentCourses(dependentCourseTitle);
            }
        });
    }

    // Go through all grade inputs and mark prerequisites for INC or 5 grades
    document.querySelectorAll('.grade-input').forEach(function(input) {
        var gradeValue = input.value.trim();
        var courseRow = input.closest('tr');

        if (gradeValue === 'INC') {
            // Mark the current course as having an INC grade
            courseRow.classList.add('text-danger', 'fw-bold');

            // Get the title of the current course
            var courseTitle = courseRow.querySelector('td:nth-child(1)').textContent.trim();

            // Recursively mark all dependent courses
            markDependentCourses(courseTitle);
        } else if (gradeValue === '5' || gradeValue === '5.0') {
            // If grade is 5, mark the row in red but do not disable input
            courseRow.classList.add('text-danger', 'fw-bold');
        }
    });
}

function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.text("Self Evaluation Report", 14, 15);
    doc.setFontSize(10);
    doc.text("<?php echo htmlspecialchars($selectedCourseName ?? '', ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($effectiveYear ?? '', ENT_QUOTES, 'UTF-8'); ?>", 14, 25);
    doc.text("Total Units: " + document.getElementById('totalAllUnits').textContent.split(': ')[1], 14, 30);
    doc.text("GWA: " + document.getElementById('gwa').textContent.split(': ')[1], 14, 35);

    let currentY = 45;
    document.querySelectorAll('.year-section').forEach((yearSection) => {
        const yearTitle = yearSection.querySelector('h5').textContent;
        doc.text(yearTitle, 14, currentY);
        currentY += 10;

        yearSection.querySelectorAll('.semester-section').forEach((semesterSection) => {
            const semesterTitle = semesterSection.querySelector('h6').textContent;
            doc.text(semesterTitle, 14, currentY);
            currentY += 5;

            const tableData = [];
            semesterSection.querySelectorAll('tbody tr').forEach((row) => {
                const rowData = [];
                row.querySelectorAll('td').forEach((cell, index) => {
                    if (index < 4) {
                        rowData.push(cell.textContent.trim());
                    } else if (index === 4) {
                        const input = cell.querySelector('input');
                        rowData.push(input ? input.value.trim() : '');
                    }
                });
                tableData.push(rowData);
            });

            // Fetch Total Units, Total Grade, and GPA values
            const totalUnits = semesterSection.querySelector(`.total-units[data-year-semester="${yearTitle}-${semesterTitle}"]`).textContent;
            const totalGrade = semesterSection.querySelector(`.total-grade[data-year-semester="${yearTitle}-${semesterTitle}"]`).textContent;
            const gpa = semesterSection.querySelector(`.weighted-grade[data-year-semester="${yearTitle}-${semesterTitle}"]`).textContent;

            // Add the summary row for Total Units and Total Grade in their respective columns
            tableData.push([
                '', '', '', `Total Units: ${totalUnits}`, `Total Grade: ${totalGrade}`
            ]);

            // Add a separate row for GPA below Total Grade
            tableData.push([
                '', '', '', '', `GPA: ${gpa}`
            ]);

            // Generate the table with proper alignment and padding adjustments
            doc.autoTable({
                head: [['Course Code', 'Descriptive Title', 'Co-/Prerequisite', 'Units', 'Grades']],
                body: tableData,
                startY: currentY,
                theme: 'grid',
                styles: {
                    cellPadding: 4, // Adjust the padding for better alignment
                    fontSize: 10,
                    valign: 'middle', // Center align vertically for better readability
                    halign: 'center' // Center align text horizontally in all cells
                },
                headStyles: {
                    fillColor: [0, 128, 0],
                    textColor: [255, 255, 255],
                    lineWidth: 0.5,
                    lineColor: [0, 0, 0]
                },
                bodyStyles: {
                    lineColor: [0, 0, 0],
                    lineWidth: 0.5
                }
            });

            currentY = doc.previousAutoTable.finalY + 10;
        });
    });

    doc.save("Self Evaluation.pdf");
}
</script>
<script src="../user/assets/js/popper.min.js"></script>
<script src="../user/assets/js/bootstrap.min.js"></script>
<script src="../user/assets/js/sweetalert2.all.min.js"></script>
</body>
</html>
