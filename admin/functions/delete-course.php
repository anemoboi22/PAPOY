<?php
include '../../db/dbconnection.php';

if (isset($_GET['id']) && isset($_GET['department_id'])) {
    $course_id = intval($_GET['id']);
    $department_id = intval($_GET['department_id']);

    if ($course_id > 0 && $department_id > 0) {
        try {
            // Check if the department ID is valid
            $query = "SELECT department_id FROM tbldepartment WHERE department_id = :department_id";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // Delete the course based on the course ID
                $query = "DELETE FROM tblcourses WHERE course_id = :course_id AND department_id = :department_id";
                $stmt = $dbh->prepare($query);
                $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
                $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // If successful, redirect back to view-department with a success message
                    header("Location: ../view-department.php?id=" . $department_id . "&status=success&message=Course+deleted+successfully");
                    exit();
                } else {
                    // If failed, redirect back to view-department with an error message
                    header("Location: ../view-department.php?id=" . $department_id . "&status=error&message=Failed+to+delete+course");
                    exit();
                }
            } else {
                // Invalid department ID
                header("Location: ../manage-department.php?status=error&message=Invalid+department+ID");
                exit();
            }
        } catch (PDOException $e) {
            // Handle any exceptions
            header("Location: ../view-department.php?id=" . $department_id . "&status=error&message=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        // Redirect with an error if the course or department ID is invalid
        header("Location: ../manage-department.php?status=error&message=Invalid+course+or+department+ID");
        exit();
    }
} else {
    // Redirect with an error if parameters are missing
    header("Location: ../manage-department.php?status=error&message=Missing+parameters");
    exit();
}
?>