<?php
include('../../db/dbconnection.php');

if (isset($_POST['course_id']) && isset($_POST['effective_year'])) {
    $course_id = $_POST['course_id'];
    $effective_year = $_POST['effective_year'];

    // Prepare SQL query to fetch data based on course_id and effective_year
    $stmt = $dbh->prepare("SELECT year, semester, course_code, descriptive_title, co_prerequisite, units, lec_hours, lab_hours, total_hours FROM courses WHERE course_id = :course_id AND effective_year = :effective_year ORDER BY FIELD(year, 'First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year', 'Sixth Year'), semester");
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

        // Generate HTML for displaying courses
        $html = '';
        foreach ($groupedCourses as $year => $semesters) {
            $html .= "<div class='year-section'>";
            $html .= "<h5>$year</h5>";

            foreach ($semesters as $semesterName => $courses) {
                if (!empty($courses)) {
                    $html .= "<div class='semester-section'>";
                    $html .= "<h6>$semesterName</h6>";
                    $html .= "<div class='scrollable-table-wrapper'>";
                    $html .= "<table class='prospectus-table'>";
                    $html .= "<thead><tr><th>Course Code</th><th>Descriptive Title</th><th>Co-/Prerequisite</th><th>Units</th><th>Hours (Lec)</th><th>Hours (Lab)</th><th>Total Hours</th></tr></thead>";
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
                        $html .= "</tr>";
                    }

                    $html .= "</tbody></table></div></div>";
                }
            }
            $html .= "</div>";
        }

        echo $html;
    } else {
        echo "<p>No courses found for this degree program and effective year.</p>";
    }
} else {
    echo "<p>Invalid request. Course ID and Effective Year are required.</p>";
}
?>
