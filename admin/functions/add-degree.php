<?php
include '../../db/dbconnection.php';

if (isset($_GET['name']) && isset($_GET['department_id'])) {
    $course_name = trim($_GET['name']);
    $department_id = intval($_GET['department_id']);

    // Check if the course name and department ID are valid
    if (!empty($course_name) && $department_id > 0) {
        try {
            // Insert the new course into the tblcourses table
            $query = "INSERT INTO tblcourses (course_name, department_id) VALUES (:course_name, :department_id)";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':course_name', $course_name, PDO::PARAM_STR);
            $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Redirect back to the department view page with success message
                $message = "New course added successfully!";
                header("Location: ../view-department.php?id=$department_id&status=success&message=" . urlencode($message));
                exit();
            } else {
                // If insertion failed
                $message = "Failed to add new course.";
                header("Location: ../view-department.php?id=$department_id&status=error&message=" . urlencode($message));
                exit();
            }
        } catch (PDOException $e) {
            // Handle database errors
            $message = "Error: " . htmlspecialchars($e->getMessage());
            header("Location: ../view-department.php?id=$department_id&status=error&message=" . urlencode($message));
            exit();
        }
    } else {
        // Handle invalid inputs
        $message = "Invalid course name or department ID.";
        header("Location: ../view-department.php?id=$department_id&status=error&message=" . urlencode($message));
        exit();
    }
} else {
    // Handle missing inputs
    $message = "Missing required parameters.";
    header("Location: ../view-department.php?status=error&message=" . urlencode($message));
    exit();
}
