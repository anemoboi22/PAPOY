<?php
include('../db/dbconnection.php'); // Include the database connection

// Fetch course names and IDs from tblcourses
$stmt = $dbh->prepare("SELECT course_id, course_name FROM tblcourses");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prospectus</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/styles.css?v=4.0" rel="stylesheet">
    <link href="../admin/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
                    <h3 class="page-title enhanced-page-title">Add Prospectus</h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Prospectus</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Prospectus Form Starts Here -->
            <div class="prospectus-container">
                <form id="prospectus-form" action="functions/save-courses.php" method="POST">
                    <div class="form-group">
                        <label class="degree_label" for="degree_program">Degree Program:</label>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownDegreeProgram" data-bs-toggle="dropdown" aria-expanded="false">
                                Select a Degree Program
                            </button>
                            <ul class="dropdown-menu degree" aria-labelledby="dropdownDegreeProgram">
                                <?php foreach ($courses as $course): ?>
                                    <li>
                                        <a class="dropdown-item degree" href="#" data-value="<?php echo htmlspecialchars($course['course_id']); ?>">
                                            <?php echo htmlspecialchars($course['course_name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <input type="hidden" id="degree_program" name="course_id" required>
                        </div>

                        <!-- Year Level Dropdown -->
                        <label class="college_label" for="college_year">Year Level:</label>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownYearLevel" data-bs-toggle="dropdown" aria-expanded="false">
                                Select Year Level
                            </button>
                            <ul class="dropdown-menu collegeYear" aria-labelledby="dropdownYearLevel">
                                <li><a class="dropdown-item collegeYear" href="#" data-value="First Year">First Year</a></li>
                                <li><a class="dropdown-item collegeYear" href="#" data-value="Second Year">Second Year</a></li>
                                <li><a class="dropdown-item collegeYear" href="#" data-value="Third Year">Third Year</a></li>
                                <li><a class="dropdown-item collegeYear" href="#" data-value="Fourth Year">Fourth Year</a></li>
                            </ul>
                            <input type="hidden" id="college_year" name="college_year" required>
                        </div>

                        <!-- Effective Year Input -->
                        <label class="effective_year_label" for="effective_year">Effective Year:</label>
                        <input type="text" id="effective_year" name="effective_year" class="form-control custom-width" placeholder="Enter Effective Year" required>
                    </div>

                    <!-- 1st Semester -->
                    <div class="semester-table-container">
                        <h4>1st Semester</h4>
                        <div class="scrollable-table-wrapper">
                            <table class="prospectus-table">
                                <thead>
                                    <tr class="custom-header-row">
                                        <th>Course Code</th>
                                        <th>Descriptive Title</th>
                                        <th>Co-/Prerequisite</th>
                                        <th>Units</th>
                                        <th>Hours (Lec)</th>
                                        <th>Hours (Lab)</th>
                                        <th>Total Hours</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="first-semester-body">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn-add-degree" onclick="addCourseRow('first-semester-body', '1st Semester')">Add Course</button>
                    </div>

                    <!-- 2nd Semester -->
                    <div class="semester-table-container">
                        <h4>2nd Semester</h4>
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
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="second-semester-body">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn-add-degree" onclick="addCourseRow('second-semester-body', '2nd Semester')">Add Course</button>
                    </div>

                    <!-- Summer Class -->
                    <div class="semester-table-container">
                        <h4>Summer Class</h4>
                        <div class="scrollable-table-wrapper">
                            <table class="prospectus-table">
                                <thead>
                                    <tr class="custom-header-row">
                                        <th>Course Code</th>
                                        <th>Descriptive Title</th>
                                        <th>Co-/Prerequisite</th>
                                        <th>Units</th>
                                        <th>Hours (Lec)</th>
                                        <th>Hours (Lab)</th>
                                        <th>Total Hours</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="summer-class-body">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn-add-degree" onclick="addCourseRow('summer-class-body', 'Summer')">Add Course</button>
                    </div>

                    <div class="button-container">
                        <button type="submit" class="btn-save mt-3">Save Courses</button>
                    </div>
                </form>
            </div>
            <!-- Prospectus Form Ends Here -->

        </div>
    </div>
    <!-- main-panel ends -->
</div>

<script src="../admin/assets/js/popper.min.js"></script>
<script src="../admin/assets/js/bootstrap.min.js"></script>
<script src="../admin/assets/js/jquery.min.js"></script>
<script src="../admin/assets/js/sweetalert2.all.min.js"></script>

<script>
// Function to calculate and display total hours on the fly
function calculateTotalHours(row) {
    const lecHours = parseInt(row.querySelector('input[name="lec_hours[]"]').value) || 0;
    const labHours = parseInt(row.querySelector('input[name="lab_hours[]"]').value) || 0;
    const totalHours = lecHours + labHours;
    row.querySelector('input[name="total_hours[]"]').value = totalHours;
}

// Add event listeners to lec_hours and lab_hours inputs for real-time calculation
document.addEventListener('input', function(event) {
    if (event.target.name === 'lec_hours[]' || event.target.name === 'lab_hours[]') {
        const row = event.target.closest('tr');
        calculateTotalHours(row);
    }
});

function addCourseRow(semesterBodyId, semester) {
    const rowHtml = `
        <tr>
            <td><input type="text" name="course_code[]" class="form-inputs" required></td>
            <td><input type="text" name="descriptive_title[]" class="form-inputs" required></td>
            <td><input type="text" name="co_prerequisite[]" class="form-inputs"></td>
            <td><input type="number" name="units[]" class="form-inputs" required></td>
            <td><input type="number" name="lec_hours[]" class="form-inputs" required></td>
            <td><input type="number" name="lab_hours[]" class="form-inputs" required></td>
            <td><input type="number" name="total_hours[]" class="form-inputs"></td>
            <td><button type="button" class="btn-remove-course" onclick="removeCourseRow(this)"><i class='bi bi-trash'></i></button></td>
            <input type="hidden" name="semester[]" value="${semester}">
        </tr>
    `;
    $('#' + semesterBodyId).append(rowHtml);
}

function removeCourseRow(button) {
    $(button).closest('tr').remove();
}

// JavaScript to handle Degree Program dropdown selection
document.querySelectorAll('.degree .dropdown-item').forEach(function(item) {
    item.addEventListener('click', function(event) {
        event.preventDefault();
        var selectedCourse = this.textContent;
        var selectedValue = this.getAttribute('data-value');

        document.getElementById('dropdownDegreeProgram').textContent = selectedCourse;
        document.getElementById('degree_program').value = selectedValue;
    });
});

// JavaScript to handle Year Level dropdown selection
document.querySelectorAll('.collegeYear .dropdown-item').forEach(function(item) {
    item.addEventListener('click', function(event) {
        event.preventDefault();
        var selectedYear = this.textContent;
        var selectedValue = this.getAttribute('data-value');

        document.getElementById('dropdownYearLevel').textContent = selectedYear;
        document.getElementById('college_year').value = selectedValue;
    });
});

$('#prospectus-form').on('submit', function(event) {
    event.preventDefault();
    
    // Calculate total hours before submitting
    $('tbody tr').each(function() {
        const lecHours = parseInt($(this).find('input[name="lec_hours[]"]').val()) || 0;
        const labHours = parseInt($(this).find('input[name="lab_hours[]"]').val()) || 0;
        const totalHours = lecHours + labHours;
        $(this).find('input[name="total_hours[]"]').val(totalHours);
    });

    // Perform AJAX request to save data and display success message
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Prospectus saved successfully!',
            }).then(() => {
                // Reset the form and clear the tables
                $('#prospectus-form')[0].reset();
                $('tbody').empty();
                
                // Reset the course dropdown to the placeholder
                document.getElementById('dropdownDegreeProgram').textContent = "Select a Degree Program";
                document.getElementById('degree_program').value = '';
                // Reset the college year dropdown to the placeholder
                document.getElementById('dropdownYearLevel').textContent = "Select Year Level";
                document.getElementById('college_year').value = '';
            });
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while saving the prospectus.',
            });
        }
    });
});
</script>
</body>
</html>
