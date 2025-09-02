<?php

// Set the timezone and enable error reporting
date_default_timezone_set('Asia/Manila');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include('../../db/dbconnection.php');

// Start output buffering to suppress any unwanted output
ob_start();

// Clear any existing buffer
ob_clean();

// Set Content-Type header for JSON response
header('Content-Type: application/json');

// Check if the POST parameters are set
if (isset($_POST['user_id']) && isset($_POST['extend_year'])) {
    $userId = $_POST['user_id'];
    $extendYear = $_POST['extend_year'];

    try {
        // Debugging: Log received values
        error_log("Received user_id: $userId, extend_year: $extendYear");

        // Prepare the SQL query to update the extended year for the user
        $sql = "UPDATE users SET extended_year = :extend_year WHERE user_id = :user_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':extend_year', $extendYear, PDO::PARAM_STR);
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        // Execute the query
        if ($query->execute()) {
            // Clear output buffer and send a success response if the update was successful
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Enrollment year extended successfully.']);
        } else {
            // Clear output buffer and send an error response if the update failed
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to extend the enrollment year.']);
        }
    } catch (PDOException $e) {
        // Clear output buffer and send an error response if an exception occurs
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    // Debugging: Log missing parameters
    error_log("Invalid parameters provided: " . print_r($_POST, true));
    
    // Clear output buffer and send an error response if the required parameters are not set
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
}

?>