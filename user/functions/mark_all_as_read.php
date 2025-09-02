<?php
include('../../db/dbconnection.php'); // Include database connection

header('Content-Type: application/json'); // Ensure the response is JSON

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start(); // Ensure session is started
        if (!isset($_SESSION['userid'])) {
            echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
            exit;
        }

        $userId = $_SESSION['userid']; // Get the logged-in user ID

        // Update all unread notifications for the user
        $sql = "UPDATE message SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
        $query = $dbh->prepare($sql);
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($query->execute()) {
            echo json_encode(['success' => true, 'message' => 'All notifications marked as read.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark all notifications as read.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
