<?php
include('../../db/dbconnection.php');

if (isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    $stmt = $dbh->prepare("SELECT DISTINCT effective_year FROM courses WHERE course_id = :course_id ORDER BY effective_year ASC");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($years);
}
?>
