<?php
// mark_as_read.php
include('../../db/dbconnection.php'); // Include database connection

header('Content-Type: application/json'); // Ensure that the response is JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageId = $_POST['id'];

    // Update the notification's is_read status
    $sql = "UPDATE message SET is_read = 1 WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $messageId, PDO::PARAM_INT);

    if ($query->execute()) {
        echo json_encode(['success' => true, 'message' => 'Notification marked as read.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to mark notification as read.']);
    }
}
