<?php
include('../../db/dbconnection.php'); // Include your database connection

if (isset($_POST['department_id'])) {
    $departmentId = $_POST['department_id'];

    $sqlCourses = "SELECT course_id, course_name FROM tblcourses WHERE department_id = :department_id";
    $queryCourses = $dbh->prepare($sqlCourses);
    $queryCourses->bindParam(':department_id', $departmentId);
    $queryCourses->execute();

    $courses = $queryCourses->fetchAll(PDO::FETCH_OBJ);

    foreach ($courses as $course) {
        echo "<option value='{$course->course_id}'>{$course->course_name}</option>";
    }
}
?>
