<?php
include('../../db/dbconnection.php');

if (isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // Prepare SQL query to fetch data based on course_id
    $stmt = $dbh->prepare("
        SELECT id, year, semester, course_code, descriptive_title, co_prerequisite, units, lec_hours, lab_hours, total_hours
        FROM courses
        WHERE course_id = :course_id
        ORDER BY FIELD(year, 'First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year', 'Sixth Year'), semester
    ");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($courses) {
        // Group courses by year and semester
        $groupedCourses = [];
        foreach ($courses as $course) {
            $year = $course['year'];
            $semester = $course['semester'];

            if (!isset($groupedCourses[$year])) {
                $groupedCourses[$year] = ['1st Semester' => [], '2nd Semester' => []];
            }

            $groupedCourses[$year][$semester][] = $course;
        }

        // Generate HTML for displaying courses
        $html = '';
        foreach ($groupedCourses as $year => $semesters) {
            $html .= "<div class='year-section'>";
            $html .= "<h5>$year</h5>";

            foreach ($semesters as $semesterName => $courses) {
                $html .= "<div class='semester-section'>";
                $html .= "<h6>$semesterName</h6>";
                $html .= "<div class='scrollable-table-wrapper-1'>";
                $html .= "<table class='prospectus-table'>";
                $html .= "<thead><tr><th>Course Code</th><th>Descriptive Title</th><th>Co-/Prerequisite</th><th>Units</th><th>Hours (Lec)</th><th>Hours (Lab)</th><th>Total Hours</th><th>Action</th></tr></thead>";
                $html .= "<tbody>";

                foreach ($courses as $course) {
                    $html .= "<tr>";
                    $html .= "<td>" . htmlspecialchars($course['course_code']) . "</td>";
                    $html .= "<td>" . htmlspecialchars($course['descriptive_title']) . "</td>";
                    $html .= "<td>" . htmlspecialchars($course['co_prerequisite']) . "</td>";
                    $html .= "<td>" . htmlspecialchars($course['units']) . "</td>";
                    $html .= "<td>" . htmlspecialchars($course['lec_hours']) . "</td>";
                    $html .= "<td>" . htmlspecialchars($course['lab_hours']) . "</td>";
                    $html .= "<td>" . htmlspecialchars($course['total_hours']) . "</td>";
                    $html .= "<td><button class='btn-remove-course' data-id='" . htmlspecialchars($course['id']) . "'><i class='bi bi-trash'></i></button></td>";
                    $html .= "</tr>";
                }

                // Add input form below the courses in each semester section
                $html .= "<tr class='add-row'>";
                $html .= "<td><input type='text' class='form-control' name='course_code' required></td>";
                $html .= "<td><input type='text' class='form-control' name='descriptive_title' required></td>";
                $html .= "<td><input type='text' class='form-control' name='co_prerequisite' required></td>";
                $html .= "<td><input type='text' class='form-control' name='units' required></td>";
                $html .= "<td><input type='text' class='form-control' name='lec_hours' required></td>";
                $html .= "<td><input type='text' class='form-control' name='lab_hours' required></td>";
                $html .= "<td><input type='text' class='form-control' name='total_hours' readonly></td>";
                $html .= "<td><button class='btn-add-course' data-year='" . htmlspecialchars($year) . "' data-semester='" . htmlspecialchars($semesterName) . "'><i class='bi bi-plus-square'></i></i></button></td>";
                $html .= "</tr>";

                $html .= "</tbody></table></div></div>";
            }
            $html .= "</div>";
        }

        echo $html;
    } else {
        echo "<p>No courses found for this degree program.</p>";
    }
} else {
    echo "<p>Invalid request. Course ID is required.</p>";
}
?>


<script>
$(document).ready(function() {
    $(document).on('input', 'input[name="lec_hours"], input[name="lab_hours"]', function() {
        const row = $(this).closest('tr');
        const lecHours = parseInt(row.find('input[name="lec_hours"]').val()) || 0;
        const labHours = parseInt(row.find('input[name="lab_hours"]').val()) || 0;
        const totalHours = lecHours + labHours;
        row.find('input[name="total_hours"]').val(totalHours);
    });

    // Handle delete button click
    $(document).on('click', '.btn-remove-course', function() {
        var courseId = $(this).data('id');
        var row = $(this).closest('tr');

        // Confirm before deleting
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the course.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete-degree.php',
                    method: 'POST',
                    data: { id: courseId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            row.remove(); // Remove the row from the table

                            // Check if there are no rows left in the table body
                            var tableBody = row.closest('tbody');
                            if (tableBody.children('tr').length === 0) {
                                // Check if this was the last table in the section
                                var semesterSection = tableBody.closest('.semester-section');
                                if (semesterSection.children('table').length === 1) {
                                    // Remove the entire semester section if it's empty
                                    semesterSection.remove();
                                }
                            }

                            // Optionally, check if there are no semester sections left in the year section
                            var yearSection = tableBody.closest('.year-section');
                            if (yearSection.children('.semester-section').length === 0) {
                                yearSection.remove(); // Remove the year section if it's empty
                            }

                            Swal.fire('Deleted!', 'The course has been deleted.', 'success');
                        } else {
                            Swal.fire('Error!', response.message || 'An error occurred while deleting the course.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error!', 'An error occurred while deleting the course: ' + error, 'error');
                    }
                });
            }
        });
    });
});
</script>


