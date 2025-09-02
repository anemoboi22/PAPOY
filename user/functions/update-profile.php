<?php
session_start();
include('../../db/dbconnection.php');

$aid = $_SESSION['userid']; // Fetch the admin ID from the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminName = $_POST['adminName'];
    $username = $_POST['username'];
    $mobileNumber = $_POST['mobileNumber'];
    $email = $_POST['email'];

    $scholarshipName = $_POST['scholarshipName'];
    $scholarshipStart = $_POST['scholarshipStart'];
    $scholarshipEnd = $_POST['scholarshipEnd'];

    // $startingYear = $_POST['startingYear'];
    // $expectedYear = $_POST['expectedYear'];

    // Fetch the current profile image from the database
    $sql = "SELECT ProfileImage FROM users WHERE user_id = :aid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':aid', $aid, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_OBJ);
    $currentProfileImage = $row->ProfileImage;

    // Handle file upload if a new image is provided
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileNameCmps = explode(".", $_FILES['profile_picture']['name']);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allow certain file formats
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Read the file content into a variable
            $profileImage = file_get_contents($fileTmpPath);
        } else {
            echo 'Upload failed. Allowed file types: jpg, jpeg, png, gif.';
            exit;
        }
    } else {
        // If no new file was uploaded, keep the current profile image
        $profileImage = $currentProfileImage;
    }

    // Update admin details in the database with the binary image data
    // sql: starting_year = :startingYear, expected_year = :expectedYear,
    $sql = "UPDATE users SET nickName = :adminName, fullname = :username, phone_number = :mobileNumber, email = :email, scholarship_name = :scholarshipName, 
            scholarship_start = :scholarshipStart, scholarship_end = :scholarshipEnd, ProfileImage = :profileImage WHERE user_id = :aid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':adminName', $adminName, PDO::PARAM_STR);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':mobileNumber', $mobileNumber, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':profileImage', $profileImage, PDO::PARAM_LOB);
    $query->bindParam(':scholarshipName', $scholarshipName, PDO::PARAM_STR);
    $query->bindParam(':scholarshipStart', $scholarshipStart, PDO::PARAM_STR);
    $query->bindParam(':scholarshipEnd', $scholarshipEnd, PDO::PARAM_STR);
    // $query->bindParam(':startingYear', $startingYear, PDO::PARAM_STR);
    // $query->bindParam(':expectedYear', $expectedYear, PDO::PARAM_STR);
    $query->bindParam(':aid', $aid, PDO::PARAM_INT);

    if ($query->execute()) {
        header('Location: ../profile.php?status=success');
    } else {
        header('Location: ../profile.php?status=error');
    }
}
?>
