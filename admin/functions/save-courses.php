<?php
include('../../db/dbconnection.php');
session_start(); // Start session to access admin id

$aid = $_SESSION['adminid']; // Get admin id from session

// Assuming you have validated and sanitized your input data
$college_year = $_POST['college_year'];
$effective_year = $_POST['effective_year'];
$course_id = $_POST['course_id'];
$course_code = $_POST['course_code'];
$descriptive_title = $_POST['descriptive_title'];
$co_prerequisite = $_POST['co_prerequisite'];
$units = $_POST['units'];
$lec_hours = $_POST['lec_hours'];
$lab_hours = $_POST['lab_hours'];
$total_hours = $_POST['total_hours'];
$semester = $_POST['semester'];

try {
    $dbh->beginTransaction();
    
    for ($i = 0; $i < count($course_code); $i++) {
        $stmt = $dbh->prepare("INSERT INTO courses (admin_id, course_id, year, effective_year, semester, course_code, descriptive_title, co_prerequisite, units, lec_hours, lab_hours, total_hours)
                               VALUES (:admin_id, :course_id, :year, :effective_year, :semester, :course_code, :descriptive_title, :co_prerequisite, :units, :lec_hours, :lab_hours, :total_hours)");
        $stmt->bindParam(':admin_id', $aid);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':year', $college_year);
        $stmt->bindParam(':effective_year', $effective_year);
        $stmt->bindParam(':semester', $semester[$i]);
        $stmt->bindParam(':course_code', $course_code[$i]);
        $stmt->bindParam(':descriptive_title', $descriptive_title[$i]);
        $stmt->bindParam(':co_prerequisite', $co_prerequisite[$i]);
        $stmt->bindParam(':units', $units[$i]);
        $stmt->bindParam(':lec_hours', $lec_hours[$i]);
        $stmt->bindParam(':lab_hours', $lab_hours[$i]);
        $stmt->bindParam(':total_hours', $total_hours[$i]);
        $stmt->execute();
    }

    $dbh->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    $dbh->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
