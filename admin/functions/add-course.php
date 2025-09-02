<?php
include('../../db/dbconnection.php');

session_start(); // Start the session to access session variables
$aid = $_SESSION['adminid']; // Get admin id from session

if (isset($_POST['course_id'], $_POST['course_code'], $_POST['descriptive_title'], $_POST['co_prerequisite'], $_POST['units'], $_POST['lec_hours'], $_POST['lab_hours'], $_POST['total_hours'], $_POST['year'], $_POST['semester'], $_POST['effective_year'])) {
    $course_id = $_POST['course_id'];
    $effective_year = $_POST['effective_year'];
    $course_code = $_POST['course_code'];
    $descriptive_title = $_POST['descriptive_title'];
    $co_prerequisite = $_POST['co_prerequisite'];
    $units = $_POST['units'];
    $lec_hours = $_POST['lec_hours'];
    $lab_hours = $_POST['lab_hours'];
    $total_hours = $_POST['total_hours'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    try {
        $stmt = $dbh->prepare("INSERT INTO courses (admin_id, course_id, effective_year, course_code, descriptive_title, co_prerequisite, units, lec_hours, lab_hours, total_hours, year, semester) 
                               VALUES (:admin_id, :course_id, :effective_year, :course_code, :descriptive_title, :co_prerequisite, :units, :lec_hours, :lab_hours, :total_hours, :year, :semester)");
        $stmt->bindParam(':admin_id', $aid);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':effective_year', $effective_year);
        $stmt->bindParam(':course_code', $course_code);
        $stmt->bindParam(':descriptive_title', $descriptive_title);
        $stmt->bindParam(':co_prerequisite', $co_prerequisite);
        $stmt->bindParam(':units', $units);
        $stmt->bindParam(':lec_hours', $lec_hours);
        $stmt->bindParam(':lab_hours', $lab_hours);
        $stmt->bindParam(':total_hours', $total_hours);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':semester', $semester);
        $stmt->execute();

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
