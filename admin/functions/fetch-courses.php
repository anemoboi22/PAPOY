<?php
include('../../db/dbconnection.php');

if (isset($_POST['course_id']) && isset($_POST['effective_year'])) {
    $course_id = $_POST['course_id'];
    $effective_year = $_POST['effective_year'];

    // Prepare SQL query to fetch data based on course_id and effective_year
    $stmt = $dbh->prepare("
        SELECT id, year, semester, course_code, descriptive_title, co_prerequisite, units, lec_hours, lab_hours, total_hours
        FROM courses
        WHERE course_id = :course_id AND effective_year = :effective_year
        ORDER BY FIELD(year, 'First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year', 'Sixth Year'), semester
    ");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':effective_year', $effective_year);
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

        // Generate HTML for displaying courses with Bootstrap 5 tabs
        $html = '<ul class="nav nav-tabs" id="yearTab" role="tablist">';
        $tabContent = '<div class="tab-content" id="yearTabContent">';
        $isFirst = true;

        foreach ($groupedCourses as $year => $semesters) {
            $yearTabId = str_replace(' ', '-', strtolower($year));
            $activeClass = $isFirst ? 'active' : '';

            // Create tab links
            $html .= "<li class='nav-item' role='presentation'>";
            $html .= "<button class='nav-link $activeClass' id='{$yearTabId}-tab' data-bs-toggle='tab' data-bs-target='#{$yearTabId}' type='button' role='tab' aria-controls='{$yearTabId}' aria-selected='true'>$year</button>";
            $html .= "</li>";

            // Create tab content
            $tabContent .= "<div class='tab-pane fade show $activeClass' id='{$yearTabId}' role='tabpanel' aria-labelledby='{$yearTabId}-tab'>";
            $tabContent .= "<div class='year-section'>";
            $tabContent .= "<h5 class='mt-2'>$year</h5>";

            foreach ($semesters as $semesterName => $courses) {
                $tabContent .= "<div class='semester-section'>";
                $tabContent .= "<h6>$semesterName</h6>";
                $tabContent .= "<div class='scrollable-table-wrapper'>";
                $tabContent .= "<table class='prospectus-table'>";
                $tabContent .= "<thead><tr><th>Course Code</th><th>Descriptive Title</th><th>Co-/Prerequisite</th><th>Units</th><th>Hours (Lec)</th><th>Hours (Lab)</th><th>Total Hours</th><th>Action</th></tr></thead>";
                $tabContent .= "<tbody>";

                foreach ($courses as $course) {
                    $tabContent .= "<tr>";
                    $tabContent .= "<td>" . htmlspecialchars($course['course_code']) . "</td>";
                    $tabContent .= "<td>" . htmlspecialchars($course['descriptive_title']) . "</td>";
                    $tabContent .= "<td>" . htmlspecialchars($course['co_prerequisite']) . "</td>";
                    $tabContent .= "<td>" . htmlspecialchars($course['units']) . "</td>";
                    $tabContent .= "<td>" . htmlspecialchars($course['lec_hours']) . "</td>";
                    $tabContent .= "<td>" . htmlspecialchars($course['lab_hours']) . "</td>";
                    $tabContent .= "<td>" . htmlspecialchars($course['total_hours']) . "</td>";
                    $tabContent .= "<td>
                                    <button class='btn-edit-course' data-id='" . htmlspecialchars($course['id']) . "'><i class='bi bi-pencil-square'></i></button> 
                                    <button class='btn-remove-course' data-id='" . htmlspecialchars($course['id']) . "'><i class='bi bi-trash'></i></button>
                            </td>";
                    $tabContent .= "</tr>";
                }

                // Add input form below the courses in each semester section
                $tabContent .= "<tr class='add-row'>";
                $tabContent .= "<td><input type='text' class='form-control' name='course_code' required></td>";
                $tabContent .= "<td><input type='text' class='form-control' name='descriptive_title' required></td>";
                $tabContent .= "<td><input type='text' class='form-control' name='co_prerequisite' required></td>";
                $tabContent .= "<td><input type='text' class='form-control' name='units' required></td>";
                $tabContent .= "<td><input type='text' class='form-control' name='lec_hours' required></td>";
                $tabContent .= "<td><input type='text' class='form-control' name='lab_hours' required></td>";
                $tabContent .= "<td><input type='text' class='form-control' name='total_hours'></td>";
                $tabContent .= "<td><button class='btn-add-course' data-year='" . htmlspecialchars($year) . "' data-semester='" . htmlspecialchars($semesterName) . "'><i class='bi bi-plus-square'></i></button></td>";
                $tabContent .= "</tr>";

                $tabContent .= "</tbody></table></div></div>";
            }

            $tabContent .= "</div></div>";
            $isFirst = false;
        }

        $html .= '</ul>';
        $tabContent .= '</div>';

        echo $html . $tabContent;
    } else {
        echo "<p>No courses found for this degree program and effective year.</p>";
    }
} else {
    echo "<p>Invalid request. Course ID and Effective Year are required.</p>";
}
?>

<!-- Modal for editing course details -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-course-form">
                    <input type="hidden" id="edit-course-id">
                    <div class="mb-3">
                        <label for="edit-course-code" class="form-label">Course Code</label>
                        <input type="text" class="form-control" id="edit-course-code" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-descriptive-title" class="form-label">Descriptive Title</label>
                        <input type="text" class="form-control" id="edit-descriptive-title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-co-prerequisite" class="form-label">Co-/Prerequisite</label>
                        <input type="text" class="form-control" id="edit-co-prerequisite" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-units" class="form-label">Units</label>
                        <input type="text" class="form-control" id="edit-units" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-lec-hours" class="form-label">Hours (Lec)</label>
                        <input type="text" class="form-control" id="edit-lec-hours">
                    </div>
                    <div class="mb-3">
                        <label for="edit-lab-hours" class="form-label">Hours (Lab)</label>
                        <input type="text" class="form-control" id="edit-lab-hours">
                    </div>
                    <div class="mb-3">
                        <label for="edit-total-hours" class="form-label">Total Hours</label>
                        <input type="text" class="form-control" id="edit-total-hours">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-edit-course">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $(document).on('input', 'input[name="lec_hours"], input[name="lab_hours"]', function() {
        const row = $(this).closest('tr');
        const lecHours = parseInt(row.find('input[name="lec_hours"]').val()) || 0;
        const labHours = parseInt(row.find('input[name="lab_hours"]').val()) || 0;
        const totalHours = lecHours + labHours;
        row.find('input[name="total_hours"]').val(totalHours);
    });

    // Handle edit button click
    $(document).on('click', '.btn-edit-course', function() {
        var courseId = $(this).data('id');
        var row = $(this).closest('tr');

        // Populate modal with course details
        $('#edit-course-id').val(courseId);
        $('#edit-course-code').val(row.find('td:nth-child(1)').text().trim());
        $('#edit-descriptive-title').val(row.find('td:nth-child(2)').text().trim());
        $('#edit-co-prerequisite').val(row.find('td:nth-child(3)').text().trim());
        $('#edit-units').val(row.find('td:nth-child(4)').text().trim());
        $('#edit-lec-hours').val(row.find('td:nth-child(5)').text().trim());
        $('#edit-lab-hours').val(row.find('td:nth-child(6)').text().trim());
        $('#edit-total-hours').val(row.find('td:nth-child(7)').text().trim());

        // Show the modal
        $('#editCourseModal').modal('show');
    });

    // Handle save changes button in modal
    $('#save-edit-course').on('click', function() {
        var courseId = $('#edit-course-id').val();
        var courseData = {
            id: courseId,
            course_code: $('#edit-course-code').val(),
            descriptive_title: $('#edit-descriptive-title').val(),
            co_prerequisite: $('#edit-co-prerequisite').val(),
            units: $('#edit-units').val(),
            lec_hours: $('#edit-lec-hours').val(),
            lab_hours: $('#edit-lab-hours').val(),
            total_hours: parseInt($('#edit-lec-hours').val() || 0) + parseInt($('#edit-lab-hours').val() || 0) || $('#edit-total-hours').val()
        };

        // Perform AJAX request to update course
        $.ajax({
            url: './functions/update-course.php',
            method: 'POST',
            data: courseData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update the row with new values
                    var row = $('button[data-id="' + courseId + '"]').closest('tr');
                    row.find('td:nth-child(1)').text(courseData.course_code);
                    row.find('td:nth-child(2)').text(courseData.descriptive_title);
                    row.find('td:nth-child(3)').text(courseData.co_prerequisite);
                    row.find('td:nth-child(4)').text(courseData.units);
                    row.find('td:nth-child(5)').text(courseData.lec_hours);
                    row.find('td:nth-child(6)').text(courseData.lab_hours);
                    row.find('td:nth-child(7)').text(courseData.total_hours);

                    // Close the modal
                    $('#editCourseModal').modal('hide');
                    Swal.fire('Updated!', 'The course has been updated.', 'success');
                } else {
                    Swal.fire('Error!', response.message || 'An error occurred while updating the course.', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error!', 'An error occurred while updating the course: ' + error, 'error');
            }
        });
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
                    url: './functions/delete-degree.php',
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
