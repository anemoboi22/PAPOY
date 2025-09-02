<?php
session_start();
include('../../db/dbconnection.php');

// Check if department, course, and scholarship data are set
if (isset($_POST['department_id']) && isset($_POST['course_id']) && isset($_POST['student_id'])) {
    $department_id = $_POST['department_id'];
    $course_id = $_POST['course_id'];
    $student_id = $_POST['student_id'];
    $user_id = $_SESSION['userid']; // Assuming the logged-in user's ID is stored in session

    // Initialize scholarship variables
    $scholarship_name = isset($_POST['scholarship_name']) ? $_POST['scholarship_name'] : null;
    $scholarship_start = isset($_POST['scholarship_start']) ? $_POST['scholarship_start'] : null;
    
    // Calculate starting year and expected year
    $starting_year = isset($_POST['starting_year']) ? $_POST['starting_year'] : null;
    $expected_year = $starting_year + 4; // Add 4 years to get expected graduation year
    
    $scholarship_end = isset($_POST['scholarship_end']) ? $_POST['scholarship_end'] : null;

    try {
        // Update the users table with the selected course_id, department_id, and scholarship details
        $sql = "UPDATE users SET 
                course_id = :course_id, 
                student_id = :student_id, 
                scholarship_name = :scholarship_name, 
                scholarship_start = :scholarship_start,
                starting_year = :starting_year,
                expected_year = :expected_year,
                scholarship_end = :scholarship_end 
                WHERE user_id = :user_id";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $query->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $query->bindParam(':scholarship_name', $scholarship_name, PDO::PARAM_STR);
        $query->bindParam(':scholarship_start', $scholarship_start, PDO::PARAM_STR);
        $query->bindParam(':starting_year', $starting_year, PDO::PARAM_STR);
        $query->bindParam(':expected_year', $expected_year, PDO::PARAM_STR);
        $query->bindParam(':scholarship_end', $scholarship_end, PDO::PARAM_STR);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        $query->execute();
        echo 'success';
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo 'Invalid selection';
}