<?php
include('../db/dbconnection.php');

if (isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // Fetch students under the selected course
    $stmt = $dbh->prepare("
        SELECT user_id, fullname, enrollment_year
        FROM users
        WHERE course_id = :course_id
        ORDER BY enrollment_year DESC
    ");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($students) {
        // Generate HTML for displaying students
        $html = "<table class='table'>";
        $html .= "<thead><tr><th>Student ID</th><th>Name</th><th>Enrollment Year</th></tr></thead>";
        $html .= "<tbody>";

        foreach ($students as $student) {
            $html .= "<tr>";
            $html .= "<td>" . htmlspecialchars($student['user_id']) . "</td>";
            $html .= "<td>" . htmlspecialchars($student['fullname']) . "</td>";
            $html .= "<td>" . htmlspecialchars($student['enrollment_year']) . "</td>";
            $html .= "</tr>";
        }

        $html .= "</tbody></table>";

        echo $html;
    } else {
        echo "<p>No students found for the selected degree program.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>
