<?php
include('../db/dbconnection.php');

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
    <title>Manage Subject Evaluation</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/styles10.css" rel="stylesheet">
    <link href="../admin/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
                    <h3 class="page-title enhanced-page-title">Manage Subject Evaluation</h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Subject Evaluation</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Combined Container -->
            <div class="prospectus-container">
                            <!-- Dropdown and Scrollable Table Container -->
                            <div class="dropdown-container">
                                <div class="dropdown mb-3">
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

                                <!-- Scrollable Table Container -->
                                <div class="scrollable-container mt-4" id="prospectus-container">
                                    <h4>Degree Program Information</h4>
                                    <div id="year-semester-container"></div>
                                </div>
                            </div>
                </div>
        </div>
    </div>
</div>

<script src="../admin/js/popper.min.js"></script>
<script src="../admin/js/bootstrap.min.js"></script>
<script src="../admin/js/jquery.min.js"></script>
<script src="../admin/js/sweetalert2.all.min.js"></script>

<script>
    // Handle degree program selection
    document.querySelectorAll('.degree .dropdown-item').forEach(function(item) {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            var selectedCourseId = this.getAttribute('data-value');
            document.getElementById('dropdownDegreeProgram').textContent = this.textContent;
            document.getElementById('degree_program').value = selectedCourseId;

            // Fetch courses for the selected degree program
            fetchProspectus(selectedCourseId);
        });
    });

    function fetchProspectus(courseId) {
        $.ajax({
            url: 'fetch-courses.php',
            method: 'POST',
            data: { course_id: courseId },
            success: function(response) {
                $('#prospectus-container').show();
                $('#year-semester-container').html(response);
            }
        });
    }

    $(document).ready(function() {
    // Handle adding new course
    $(document).on('click', '.btn-add-course', function() {
        var row = $(this).closest('tr');
        var courseId = $('#degree_program').val();
        var year = $(this).data('year');
        var semester = $(this).data('semester');

        // Fetch input values
        var courseCode = row.find('input[name="course_code"]').val().trim();
        var descriptiveTitle = row.find('input[name="descriptive_title"]').val().trim();
        var coPrerequisite = row.find('input[name="co_prerequisite"]').val().trim();
        var units = row.find('input[name="units"]').val().trim();
        var lecHours = row.find('input[name="lec_hours"]').val().trim();
        var labHours = row.find('input[name="lab_hours"]').val().trim();
        var totalHours = row.find('input[name="total_hours"]').val().trim();

        // Validation: Check if all fields are filled
        if (!courseCode || !descriptiveTitle || !coPrerequisite || !units || !lecHours || !labHours || !totalHours) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Details',
                text: 'Please fill in all the details before adding the course.',
            });
            return; // Prevent form submission
        }

        var courseData = {
            course_id: courseId,
            course_code: courseCode,
            descriptive_title: descriptiveTitle,
            co_prerequisite: coPrerequisite,
            units: units,
            lec_hours: lecHours,
            lab_hours: labHours,
            total_hours: totalHours,
            year: year,
            semester: semester
        };

        // Perform AJAX request to add course
        $.ajax({
            url: 'add-course.php',
            method: 'POST',
            data: courseData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Refresh the course list after adding
                    fetchProspectus(courseId);
                } else {
                    Swal.fire('Error!', response.message || 'Failed to add course.', 'error');
                }
            }
        });
    });
});

</script>

</body>
</html>
