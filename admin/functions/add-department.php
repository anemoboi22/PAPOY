<?php
include '../../db/dbconnection.php';

// Get department name from URL
$department_name = isset($_GET['name']) ? trim($_GET['name']) : '';

if ($department_name !== '') {
    try {
        // Insert new department into the database
        $query = "INSERT INTO tbldepartment (department_name) VALUES (:department_name)";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);
        $stmt->execute();

        // Redirect back to manage-department.php with success message
        header("Location: ../manage-department.php?status=success&message=" . urlencode("Department added successfully."));
        exit();
    } catch (PDOException $e) {
        // Redirect back to manage-department.php with error message
        header("Location: ../manage-department.php?status=error&message=" . urlencode("Error: " . $e->getMessage()));
        exit();
    }
} else {
    // Redirect back to manage-department.php with error message if department name is empty
    header("Location: ../manage-department.php?status=error&message=" . urlencode("Department name cannot be empty."));
    exit();
}
?>
