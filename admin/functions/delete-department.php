<?php
// Include database connection
include '../../db/dbconnection.php';

// Check if the 'id' parameter is set in the URL and is a valid integer
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $departmentId = intval($_GET['id']); // Ensure it's an integer

    try {
        // Start a transaction
        $dbh->beginTransaction();

        // Delete courses associated with the department
        $query = "DELETE FROM tblcourses WHERE department_id = :department_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
        $stmt->execute();

        // Delete the department
        $query = "DELETE FROM tbldepartment WHERE department_id = :department_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $dbh->commit();

        // Redirect back with a success message
        header("Location: ../manage-department.php?status=success&message=" . urlencode("Department and associated courses deleted successfully."));
        exit();
    } catch (PDOException $e) {
        // Rollback the transaction if there's an error
        $dbh->rollBack();

        // Redirect back with an error message
        header("Location: ../manage-department.php?status=error&message=" . urlencode("Error deleting department: " . $e->getMessage()));
        exit();
    }
} else {
    // Redirect back with an error message if 'id' is not set or invalid
    header("Location: ../manage-department.php?status=error&message=" . urlencode("Department ID not specified or invalid."));
    exit();
}
?>
