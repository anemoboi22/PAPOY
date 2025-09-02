<?php
session_start();
include('../../db/dbconnection.php');

$aid = $_SESSION['adminid']; // Fetch the admin ID from the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminName = $_POST['adminName'];
    $username = $_POST['username'];
    $mobileNumber = $_POST['mobileNumber'];
    $email = $_POST['email'];
    $campusNumber = $_POST['campusNumber'];

    // Fetch the current profile image from the database
    $sql = "SELECT ProfileImage FROM tbladmin WHERE ID = :aid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':aid', $aid, PDO::PARAM_STR);
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

    // Update admin details in the database
    $sql = "UPDATE tbladmin SET AdminName = :adminName, UserName = :username, MobileNumber = :mobileNumber, Email = :email, campus_number = :campusNumber, ProfileImage = :profileImage WHERE ID = :aid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':adminName', $adminName, PDO::PARAM_STR);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':mobileNumber', $mobileNumber, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':profileImage', $profileImage, PDO::PARAM_LOB);
    $query->bindParam(':aid', $aid, PDO::PARAM_INT);
    $query->bindParam(':campusNumber', $campusNumber, PDO::PARAM_INT);

    if ($query->execute()) {
        header('Location: ../profile.php?status=success');
    } else {
        header('Location: ../profile.php?status=error');
    }
}
?>
