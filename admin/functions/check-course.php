<?php
include '../../db/dbconnection.php';

header('Content-Type: application/json');

if (isset($_GET['name']) && isset($_GET['department_id'])) {
    $courseName = trim($_GET['name']);
    $departmentId = intval($_GET['department_id']);

    try {
        // Query to check if the course name exists in the same department
        $query = "SELECT COUNT(*) as count FROM tblcourses WHERE course_name = :course_name AND department_id = :department_id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':course_name', $courseName, PDO::PARAM_STR);
        $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return JSON response
        echo json_encode(['exists' => $result['count'] > 0]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid parameters.']);
}
?>
