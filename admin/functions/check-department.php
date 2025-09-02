<?php
header('Content-Type: application/json');

// Include the database connection
include '../../db/dbconnection.php';

// Get the department name from the query parameter
$departmentName = isset($_GET['name']) ? trim($_GET['name']) : '';

if ($departmentName === '') {
    echo json_encode(['exists' => false]);
    exit;
}

try {
    // Prepare and execute the query to check if the department name exists
    $query = "SELECT COUNT(*) AS count FROM tbldepartment WHERE department_name = :department_name";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':department_name', $departmentName, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $exists = $result['count'] > 0;

    // Return the result as JSON
    echo json_encode(['exists' => $exists]);
} catch (Exception $e) {
    // Return an error response in case of any issues
    echo json_encode(['error' => 'An error occurred while checking the department name.']);
}
?>
