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
                                    <li><a class="dropdown-item" href="#" onclick="filterGrades('blank')">BLANK</a></li> 
                                    <li><a class="dropdown-item" href="#" onclick="filterGrades('INC')">INC</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterGrades('DRP')">DRP</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterGrades('NA')">NA</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterGrades('NG')">NG</a></li>
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

<script>
document.addEventListener('DOMContentLoaded', function() {  
    // Generate PDF button click event
    document.querySelector('#generate-report-btn').addEventListener('click', function() {
        window.open('functions/generate-self-evaluation.php', '_blank');
    });

    calculateAll();
    applyPrerequisiteRules(); // Apply rules on page load
});

document.querySelectorAll('.grade-select').forEach(function(select) {
    select.addEventListener('change', function() {
        calculateAll();
        applyPrerequisiteRules(); // Apply rules whenever grades are updated
    });
});

document.querySelector('#grades-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    document.querySelectorAll('.grade-select').forEach(function(select) {
        const courseId = select.closest('tr').querySelector('td:first-child').textContent.trim();
        formData.append('grades[' + courseId + ']', select.value);
    });

    fetch('./functions/update-grades.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('success')) {
            Swal.fire('Success', 'Grades updated successfully!', 'success');
            setTimeout(() => {
                location.reload(); // Reload the page to reflect changes
            }, 500);
        } else {
            Swal.fire('Error', 'Failed to update grades. Please try again.', 'error');
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
    let hasIncompleteGradesByYear = {
        'First Year': false,
        'Second Year': false,
        'Third Year': false,
        'Fourth Year': false
    };

    document.querySelectorAll('.tab-pane').forEach(function(yearTab) {
        let yearLabel = yearTab.querySelector('h5').textContent;
        let yearHasIncompleteGrades = false;
        let yearTotalUnits = 0;
        let yearTotalGrades = 0;
        let yearTotalUnitsForGWA = 0;

        yearTab.querySelectorAll('.semester-section').forEach(function(semesterSection) {
            var totalUnits = 0;
            var weightedGrade = 0;
            var yearSemester = semesterSection.querySelector('.total-units').getAttribute('data-year-semester');

            semesterSection.querySelectorAll('.grade-select').forEach(function(select) {
                var gradeValue = select.value.trim();
                var units = parseFloat(select.closest('tr').querySelector('td:nth-child(4)').textContent.trim());
                totalUnits += units;
                totalAllUnits += units;
                yearTotalUnits += units;

                if (['INC', 'DRP', 'NA', 'NG', ''].includes(gradeValue)) {
                    yearHasIncompleteGrades = true;
                    hasIncompleteGradesByYear[yearLabel] = true;
                    return; // Skip the current course if grade is incomplete
                }

                var grade = parseFloat(gradeValue) || 0;
                weightedGrade += grade * units;
                yearTotalGrades += grade * units;

                // Calculate GWA values
                yearTotalUnitsForGWA += units;
                totalUnitsForGWA += units;
                totalAllGrades += grade * units;
            });

            var gpa = totalUnits ? (weightedGrade / totalUnits) : 0;

            document.querySelector('.total-units[data-year-semester="' + yearSemester + '"]').textContent = totalUnits.toFixed(0);
            document.querySelector('.total-grade[data-year-semester="' + yearSemester + '"]').textContent = weightedGrade.toFixed(1);
            document.querySelector('.weighted-grade[data-year-semester="' + yearSemester + '"]').textContent = yearHasIncompleteGrades ? 'N/A' : gpa.toFixed(1);

            // Update hidden input fields with the calculated values
            document.querySelector('input.hidden-total-units[name="total_units[' + yearSemester + ']"]').value = totalUnits.toFixed(0);
            document.querySelector('input.hidden-total-grade[name="total_grades[' + yearSemester + ']"]').value = weightedGrade.toFixed(1);
            document.querySelector('input.hidden-weighted-grade[name="gpas[' + yearSemester + ']"]').value = yearHasIncompleteGrades ? 'N/A' : gpa.toFixed(1);
        });
    });

    // Determine if overall GWA should be N/A
    let hasOverallIncompleteGrades = Object.values(hasIncompleteGradesByYear).some(val => val);

    // Calculate Overall GWA
    let gwa = totalUnitsForGWA ? (totalAllGrades / totalUnitsForGWA) : 0;

    // Update GWA and Total Units in the DOM
    if (hasOverallIncompleteGrades) {
        document.getElementById('gwa').textContent = 'GWA: N/A';
        document.getElementById('gwa').setAttribute('title', 'GWA is not available because there are incomplete or missing grades in some year levels.');
    } else {
        document.getElementById('gwa').textContent = 'GWA: ' + gwa.toFixed(1);
        document.getElementById('gwa').removeAttribute('title');
    }

    document.getElementById('totalAllUnits').textContent = 'Total Units: ' + totalAllUnits;

    // Update hidden input fields
    document.getElementById('hiddenTotalAllUnits').value = totalAllUnits;
    document.getElementById('hiddenGwa').value = hasOverallIncompleteGrades ? 'N/A' : gwa.toFixed(1);
}

function applyPrerequisiteRules() {
    // First, remove all previous styles and reset attributes
    document.querySelectorAll('.grade-select').forEach(function(select) {
        const courseRow = select.closest('tr');
        courseRow.classList.remove('text-danger', 'fw-bold');
        select.disabled = false;
        // Remove any existing tooltip icons
        const existingIcon = courseRow.querySelector('.bi-info-circle');
        if (existingIcon) {
            existingIcon.remove();
        }
    });

    // Recursive function to mark dependent courses
    function markDependentCourses(courseTitle, isDirectPrereq = false) {
        document.querySelectorAll('.grade-select').forEach(function(select) {
            var linkedCourseRow = select.closest('tr');
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
                select.disabled = true;

                if (isDirectPrereq) {
                    // Set tooltip indicating which prerequisite is causing the restriction
                    // Check if an icon already exists to avoid adding multiple icons
                    let existingIcon = linkedCourseRow.querySelector('.bi-info-circle');
                    if (!existingIcon) {
                        let tooltipText = `You cannot enroll in this course because you have a failed grade (such as INC, DRP, NG, NA, or a grade of 3.1 to 3.5) in the prerequisite: ${courseTitle}`;
                        let infoIcon = document.createElement('i');
                        infoIcon.className = 'bi bi-info-circle';
                        infoIcon.setAttribute('data-bs-toggle', 'tooltip');
                        infoIcon.setAttribute('title', tooltipText);

                        // Add a space before the icon to ensure it’s not stuck to the text
                        linkedCourseRow.querySelector('td:first-child').appendChild(document.createTextNode(' '));
                        linkedCourseRow.querySelector('td:first-child').appendChild(infoIcon);
                    }
                }

                // Get the title of the dependent course
                var dependentCourseTitle = linkedCourseRow.querySelector('td:nth-child(1)').textContent.trim();

                // Recursively check for courses that depend on this one, but they are not direct prerequisites
                markDependentCourses(dependentCourseTitle, false);
            }
        });
    }

    // Go through all selects and mark prerequisites for INC or 5 grades
    document.querySelectorAll('.grade-select').forEach(function(select) {
        var gradeValue = select.value.trim();
        var courseRow = select.closest('tr');

        if (['INC', 'DRP', 'NG', 'NA', '3.1', '3.2', '3.3', '3.4', '3.5'].includes(gradeValue)) {
            // Mark the current course as having an INC or failing grade
            courseRow.classList.add('text-danger', 'fw-bold');

            // Get the title of the current course
            var courseTitle = courseRow.querySelector('td:nth-child(1)').textContent.trim();

            // Recursively mark all dependent courses, starting with direct prerequisites
            markDependentCourses(courseTitle, true);
        }

        // if (['DRP', 'NG', 'NA', '3.1', '3.2', '3.3', '3.4', '3.5'].includes(gradeValue)) {
        //     // If grade is 3.1 mark the row in red but do not disable input 
        //     courseRow.classList.add('text-danger', 'fw-bold');
        // }
    });
}

// Remember to initialize tooltips after applying the rules
document.addEventListener('DOMContentLoaded', function() {
    applyPrerequisiteRules();
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function filterGrades(filter) {
    document.querySelectorAll('.course-row').forEach(function(row) {
        const grade = row.getAttribute('data-grade');
         // Check for blank grades if the filter is 'blank'
         if (filter === 'all' || grade === filter || (filter === 'blank' && (grade === '' || grade === null))) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
<script src="../user/assets/js/popper.min.js"></script>
<script src="../user/assets/js/bootstrap.min.js"></script>
<script src="../user/assets/js/sweetalert2.all.min.js"></script>
</body>
</html>
