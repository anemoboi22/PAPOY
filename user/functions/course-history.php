<?php
// course-history.php
session_start();
include('../../db/dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['userId']) && isset($_POST['oldCourseName']) && isset($_POST['oldCourseId']) && isset($_POST['courseId'])) {
        $userId = intval($_POST['userId']);
        $oldCourseName = $_POST['oldCourseName'];
        $oldCourseId = intval($_POST['oldCourseId']);
        $courseId = intval($_POST['courseId']);
        
        try {
            $endDate = date('Y');
            
            // Fetch the starting year from the users table
            $startingYearSQL = "SELECT starting_year FROM users WHERE user_id = :userId";
            $startingYearQuery = $dbh->prepare($startingYearSQL);
            $startingYearQuery->bindParam(':userId', $userId, PDO::PARAM_INT);
            $startingYearQuery->execute();
            $startingYear = $startingYearQuery->fetchColumn();

            // Fetch the number of times the user has shifted
            $shiftCountSQL = "SELECT COUNT(*) FROM course_history WHERE user_id = :userId";
            $shiftCountQuery = $dbh->prepare($shiftCountSQL);
            $shiftCountQuery->bindParam(':userId', $userId, PDO::PARAM_INT);
            $shiftCountQuery->execute();
            $shiftCount = $shiftCountQuery->fetchColumn();

            // Check if the starting year is valid
            if ($startingYear !== false && !empty($startingYear)) {
                // Compute the number of years stayed
                $yearsStayed = (int)$endDate - (int)$startingYear;

                // Determine start and end dates based on shift count
                $startDate = $shiftCount > 0 ? null : $startingYear;

                // Insert the old course into the course_history table
                $insertSQL = "INSERT INTO course_history (user_id, course_id, old_courseID, course_name, start_date, end_date, years_stayed) 
                              VALUES (:userId, :courseId, :oldCourseId, :courseName, :startDate, :endDate, :yearsStayed)";
                $insertQuery = $dbh->prepare($insertSQL);
                $insertQuery->bindParam(':userId', $userId, PDO::PARAM_INT);
                $insertQuery->bindParam(':courseId', $courseId, PDO::PARAM_INT);
                $insertQuery->bindParam(':oldCourseId', $oldCourseId, PDO::PARAM_INT);
                $insertQuery->bindParam(':courseName', $oldCourseName, PDO::PARAM_STR);
                if ($startDate !== null) {
                    $insertQuery->bindParam(':startDate', $startDate, PDO::PARAM_STR);
                } else {
                    $insertQuery->bindValue(':startDate', null, PDO::PARAM_NULL);
                }
                $insertQuery->bindParam(':endDate', $endDate, PDO::PARAM_STR);
                $insertQuery->bindParam(':yearsStayed', $yearsStayed, PDO::PARAM_INT);
                
                if ($insertQuery->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Course history saved successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to save course history.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Starting year not found.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters provided.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
