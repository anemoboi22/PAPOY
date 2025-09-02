<?php
include('../../db/dbconnection.php');
session_start();
$aid = $_SESSION['userid'];

$response = [
    'unread' => '',
    'read' => ''
];

// Fetch unread notifications
$unreadSQL = "SELECT id, message, time, type FROM message WHERE user_id = :user_id AND is_read = 0 ORDER BY time DESC";
$unreadQuery = $dbh->prepare($unreadSQL);
$unreadQuery->bindParam(':user_id', $aid, PDO::PARAM_INT);
$unreadQuery->execute();
$unreadNotifications = $unreadQuery->fetchAll(PDO::FETCH_ASSOC);

if (count($unreadNotifications) > 0) {
    foreach ($unreadNotifications as $notification) {
        $response['unread'] .= '<div class="alert alert-warning" role="alert">';
        $response['unread'] .= '<strong>' . htmlentities($notification['message']) . '</strong><br>';
        $response['unread'] .= '<small>' . htmlentities($notification['time']) . '</small>';
        $response['unread'] .= '</div>';
    }
} else {
    $response['unread'] = '<div class="alert alert-info" role="alert">No new notifications.</div>';
}

// Fetch read notifications
$readSQL = "SELECT id, message, time, type FROM message WHERE user_id = :user_id AND is_read = 1 ORDER BY time DESC";
$readQuery = $dbh->prepare($readSQL);
$readQuery->bindParam(':user_id', $aid, PDO::PARAM_INT);
$readQuery->execute();
$readNotifications = $readQuery->fetchAll(PDO::FETCH_ASSOC);

if (count($readNotifications) > 0) {
    foreach ($readNotifications as $notification) {
        $response['read'] .= '<div class="alert alert-secondary" role="alert">';
        $response['read'] .= '<strong>' . htmlentities($notification['message']) . '</strong><br>';
        $response['read'] .= '<small>' . htmlentities($notification['time']) . '</small>';
        $response['read'] .= '</div>';
    }
} else {
    $response['read'] = '<div class="alert alert-info" role="alert">No read notifications.</div>';
}

echo json_encode($response);
?>
