<?php
include('../../db/dbconnection.php');

// Start a session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['userid'];
$courseId = isset($_POST['courseId']) ? $_POST['courseId'] : null;

// Validate the course ID
if (empty($courseId)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid course ID.']);
    exit;
}

try {
    // Update the user's degree program in the database
    $sql = "UPDATE users SET course_id = :courseId WHERE user_id = :userId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':courseId', $courseId, PDO::PARAM_INT);
    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
    $query->execute();

    if ($query->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Degree program updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes made to the degree program.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
