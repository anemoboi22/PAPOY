<?php
include('../../db/dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $graduatedStatus = $_POST['graduated'] ?? null;

    if ($userId && $graduatedStatus) {
        try {
            $sql = "UPDATE users SET graduated = :graduated WHERE user_id = :user_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':graduated', $graduatedStatus, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Graduated status updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update graduated status.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
