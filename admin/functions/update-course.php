<?php
include('../../db/dbconnection.php');
session_start(); // Start session to access admin id

$aid = $_SESSION['adminid']; // Get admin id from session

if (isset($_POST['id'], $_POST['course_code'], $_POST['descriptive_title'], $_POST['co_prerequisite'], $_POST['units'], $_POST['lec_hours'], $_POST['lab_hours'], $_POST['total_hours'])) {
    $course_id = $_POST['id'];
    $course_code = $_POST['course_code'];
    $descriptive_title = $_POST['descriptive_title'];
    $co_prerequisite = $_POST['co_prerequisite'];
    $units = $_POST['units'];
    $lec_hours = $_POST['lec_hours'];
    $lab_hours = $_POST['lab_hours'];
    $total_hours = $_POST['total_hours'];

    try {
        $stmt = $dbh->prepare("UPDATE courses SET admin_id = :admin_id, course_code = :course_code, descriptive_title = :descriptive_title, co_prerequisite = :co_prerequisite, units = :units, lec_hours = :lec_hours, lab_hours = :lab_hours, total_hours = :total_hours WHERE id = :id");
        $stmt->bindParam(':admin_id', $aid);
        $stmt->bindParam(':course_code', $course_code);
        $stmt->bindParam(':descriptive_title', $descriptive_title);
        $stmt->bindParam(':co_prerequisite', $co_prerequisite);
        $stmt->bindParam(':units', $units);
        $stmt->bindParam(':lec_hours', $lec_hours);
        $stmt->bindParam(':lab_hours', $lab_hours);
        $stmt->bindParam(':total_hours', $total_hours);
        $stmt->bindParam(':id', $course_id);
        $stmt->execute();

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
