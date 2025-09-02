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
    <title>Manage Prospectus</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/styles.css?v=1.1" rel="stylesheet">
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
                    <h3 class="page-title enhanced-page-title">Manage Prospectus</h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Prospectus</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Combined Container -->
            <div class="prospectus-container">
                <!-- Dropdown and Scrollable Table Container -->
                <div class="dropdown-container">
                    <!-- Degree Program Dropdown -->
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

                    <!-- Effective Year Dropdown (Initially Hidden) -->
                    <div class="dropdown mb-3" id="effectiveYearContainer" style="display: none;">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownEffectiveYear" data-bs-toggle="dropdown" aria-expanded="false">
                            Select Effective Year
                        </button>
                        <ul class="dropdown-menu effective-year" aria-labelledby="dropdownEffectiveYear">
                            <!-- Effective years will be populated dynamically -->
                        </ul>
                        <input type="hidden" id="effective_year" name="effective_year" required>
                    </div>

                    <!-- Scrollable Table Container -->
                    <div class="scrollable-container mt-4">
                        <h4>Degree Program Information</h4>
                        <div id="year-semester-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../admin/assets/js/popper.min.js"></script>
<script src="../admin/assets/js/bootstrap.min.js"></script>
<script src="../admin/assets/js/jquery.min.js"></script>
<script src="../admin/assets/js/sweetalert2.all.min.js"></script>

<script>
    // Handle degree program selection
    document.querySelectorAll('.degree .dropdown-item').forEach(function(item) {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Clear prospectus information and effective year when selecting a new degree program
            $('#year-semester-container').empty();
            $('#effectiveYearContainer').hide();
            document.getElementById('dropdownEffectiveYear').textContent = "Select Effective Year";
            document.getElementById('effective_year').value = '';

            // Update selected degree program
            var selectedCourseId = this.getAttribute('data-value');
            document.getElementById('dropdownDegreeProgram').textContent = this.textContent;
            document.getElementById('degree_program').value = selectedCourseId;

            // Fetch available effective years for the selected degree program
            fetchEffectiveYears(selectedCourseId);
        });
    });

    // Function to fetch effective years for the selected degree program
    function fetchEffectiveYears(courseId) {
        $.ajax({
            url: './functions/fetch-effective-years.php',
            method: 'POST',
            data: { course_id: courseId },
            success: function(response) {
                var effectiveYears = JSON.parse(response);
                var effectiveYearDropdown = document.querySelector('.effective-year');
                effectiveYearDropdown.innerHTML = '';

                effectiveYears.forEach(function(year) {
                    var listItem = document.createElement('li');
                    listItem.innerHTML = `<a class="dropdown-item effective-year-item" href="#" data-value="${year}">${year}</a>`;
                    effectiveYearDropdown.appendChild(listItem);
                });

                // Show the Effective Year dropdown
                $('#effectiveYearContainer').show();

                // Add event listener for Effective Year dropdown items
                document.querySelectorAll('.effective-year .dropdown-item').forEach(function(item) {
                    item.addEventListener('click', function(event) {
                        event.preventDefault();
                        var selectedYear = this.getAttribute('data-value');
                        document.getElementById('dropdownEffectiveYear').textContent = this.textContent;
                        document.getElementById('effective_year').value = selectedYear;

                        // Fetch prospectus for the selected year and degree program
                        fetchProspectus(courseId, selectedYear);
                    });
                });
            }
        });
    }

    // Function to fetch prospectus based on degree program and effective year
    function fetchProspectus(courseId, effectiveYear) {
        $.ajax({
            url: './functions/fetch-courses.php',
            method: 'POST',
            data: { course_id: courseId, effective_year: effectiveYear },
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
            var effectiveYear = $('#effective_year').val(); // Get the selected effective year
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
            if (!courseCode || !descriptiveTitle || !coPrerequisite || !units || !lecHours || !labHours || !totalHours || !effectiveYear) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Details',
                    text: 'Please fill in all the details before adding the course.',
                });
                return; // Prevent form submission
            }

            var courseData = {
                course_id: courseId,
                effective_year: effectiveYear, // Include effective year
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
                url: './functions/add-course.php',
                method: 'POST',
                data: courseData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Refresh the course list after adding
                        fetchProspectus(courseId, effectiveYear);
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
