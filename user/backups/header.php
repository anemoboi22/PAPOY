<?php
date_default_timezone_set('Asia/Manila');

include('../db/dbconnection.php');

$aid = $_SESSION['userid'];

// Fetch user data and the course_name by joining users with tblcourses
$sql = "SELECT u.nickName, u.email, u.fullname, u.phone_number, u.ProfileImage, u.student_id, 
        u.scholarship_name, u.scholarship_start, u.scholarship_end, u.starting_year, 
        u.expected_year, u.extended_year, c.course_name
        FROM users u
        INNER JOIN tblcourses c ON u.course_id = c.course_id
        WHERE u.user_id = :aid";
        
$query = $dbh->prepare($sql);
$query->bindParam(':aid', $aid, PDO::PARAM_INT);
$query->execute();
$user = $query->fetch(PDO::FETCH_OBJ);

// If user data is found, extract necessary values
if ($user) {
    $adminName = htmlentities($user->nickName);
    $adminEmail = htmlentities($user->email);
    $username = htmlentities($user->fullname);
    $mobileNumber = htmlentities($user->phone_number);
    $profileImage = $user->ProfileImage;
    $studID = htmlentities($user->student_id);
    $scholarName = htmlentities($user->scholarship_name);
    $scholarStart = htmlentities($user->scholarship_start);
    $scholarEnd = htmlentities($user->scholarship_end);
    $startYear = htmlentities($user->starting_year);
    $expectYear = htmlentities($user->expected_year);
    $extendYear = htmlentities($user->extended_year);
    $courseName = htmlentities($user->course_name);

    $notifications = [];

    $currentDate = new DateTime();
    $scholarEndDate = new DateTime($scholarEnd);

    $interval = $currentDate->diff($scholarEndDate);
    $years = $interval->y;
    $months = $interval->m;
    $days = $interval->d;

    $scholarshipEnded = $currentDate > $scholarEndDate;
    $oneYearInterval = new DateInterval('P1Y');
    $oneYearBeforeEnd = (clone $scholarEndDate)->sub($oneYearInterval);
    $scholarshipNearEnd = !$scholarshipEnded && $currentDate >= $oneYearBeforeEnd && $currentDate <= $scholarEndDate;

    $scholarMessage = '';
    $scholarTime = '';
    $scholarType = '';

    if ($scholarshipEnded) {
        $scholarMessage = 'Your scholarship has ended.';
        $scholarTime = 'Please consult the Student Affairs Office.';
        $scholarType = 'error';
    } elseif ($scholarshipNearEnd) {
        $scholarMessage = 'Your scholarship is nearing its end.';
        $scholarTime = 'It ends in ' . $years . ' years, ' . $months . ' months, and ' . $days . ' days.';
        $scholarType = 'warning';
    } else {
        $scholarMessage = 'Your scholarship is active.';
        $scholarTime = 'It lasts for ' . $years . ' years, ' . $months . ' months, and ' . $days . ' days.';
        $scholarType = 'info';
    }

    if (!empty($scholarMessage)) {
        $notifications[] = [
            'message' => $scholarMessage,
            'time' => $scholarTime,
            'type' => $scholarType
        ];
    }

    // Check if both expected year and extended year are set and not empty/null
    if (!empty($expectYear)) {
        $expectDate = new DateTime($expectYear);

        if ($currentDate->format('Y-m-d') == $expectDate->format('Y-m-d')) {
            $notifications[] = [
                'message' => 'Are you graduating today?',
                'time' => $currentDate->format('h:i A'),
                'type' => 'info',
                'action' => 'graduation_check'
            ];
        } elseif ($currentDate > $expectDate) {
            if (!empty($extendYear)) {
                $extendDate = new DateTime($extendYear);

                if ($currentDate <= $extendDate) {
                    $notifications[] = [
                        'message' => 'You are still enrolled but have not graduated. Please ensure your grades are in order.',
                        'time' => $currentDate->format('h:i A'),
                        'type' => 'warning'
                    ];
                } else {
                    $notifications[] = [
                        'message' => 'You have overstayed your enrollment period. Please consult the Students Affairs Office.',
                        'time' => $currentDate->format('h:i A'),
                        'type' => 'error'
                    ];
                }
            } else {
                $notifications[] = [
                    'message' => 'Your expected graduation date has passed, and no extension year is recorded.',
                    'time' => $currentDate->format('h:i A'),
                    'type' => 'error'
                ];
            }
        }
    }

    // Check for missing grades and prerequisites
    $missingGrades = [];
    $prerequisiteIssues = [];
    $stmtFetchGrades = $dbh->prepare("SELECT c.id, c.descriptive_title, g.grade, c.co_prerequisite FROM courses c LEFT JOIN grades g ON c.id = g.course_id WHERE g.user_id = :user_id");
    $stmtFetchGrades->bindParam(':user_id', $aid, PDO::PARAM_INT);
    $stmtFetchGrades->execute();
    $gradesDetails = $stmtFetchGrades->fetchAll(PDO::FETCH_ASSOC);

    foreach ($gradesDetails as $gradeDetail) {
        if ($gradeDetail['grade'] === null || $gradeDetail['grade'] == 'INC' || floatval($gradeDetail['grade']) == 0) {
            $missingGrades[] = $gradeDetail['descriptive_title'];
        }
    
        if (!empty($gradeDetail['co_prerequisite']) && $gradeDetail['co_prerequisite'] !== 'NONE') {
            // Get the ID of the prerequisite course
            $stmtGetPrerequisiteId = $dbh->prepare("SELECT id FROM courses WHERE descriptive_title = :prerequisite_title");
            $stmtGetPrerequisiteId->execute([':prerequisite_title' => $gradeDetail['co_prerequisite']]);
            $prerequisiteCourseId = $stmtGetPrerequisiteId->fetchColumn();
    
            // If the prerequisite course ID is found, check the grade
            if ($prerequisiteCourseId) {
                $stmtCheckPrerequisite = $dbh->prepare("SELECT grade FROM grades WHERE user_id = :user_id AND course_id = :course_id");
                $stmtCheckPrerequisite->execute([':user_id' => $aid, ':course_id' => $prerequisiteCourseId]);
                $prerequisiteGrade = $stmtCheckPrerequisite->fetchColumn();
    
                // If the prerequisite grade is missing or not passing, add a notification
                if ($prerequisiteGrade === false || $prerequisiteGrade == 'INC' || floatval($prerequisiteGrade) == 0) {
                    $prerequisiteIssues[] = $gradeDetail['descriptive_title'];
                }
            }
        }
    }
    

    // Create individual notifications for each missing grade
    foreach ($missingGrades as $missingGrade) {
        $notifications[] = [
            'message' => 'You have a missing grade for the course: ' . $missingGrade,
            'type' => 'warning'
        ];
    }

    // Create individual notifications for each prerequisite issue
    foreach ($prerequisiteIssues as $prerequisiteIssue) {
        $notifications[] = [
            'message' => 'You have not met the prerequisite for the course: ' . $prerequisiteIssue,
            'type' => 'error'
        ];
    }


    // Insert new notifications to the database
    foreach ($notifications as $notification) {
        // Check if the notification for today already exists
        $checkSQL = "SELECT 1 FROM message WHERE user_id = :user_id AND message = :message AND DATE(created_at) = CURDATE()";
        $checkQuery = $dbh->prepare($checkSQL);
        $checkQuery->bindParam(':user_id', $aid, PDO::PARAM_INT);
        $checkQuery->bindParam(':message', $notification['message'], PDO::PARAM_STR);
        $checkQuery->execute();
        
        // Set a default time if not provided in the notification
        $notificationTime = $notification['time'] ?? (new DateTime())->format('h:i A');
    
        // If no notification exists for today, insert the new notification
        if ($checkQuery->rowCount() == 0) {
            $insertSQL = "INSERT INTO message (user_id, message, time, type, is_read, created_at) VALUES (:user_id, :message, :time, :type, 0, NOW())";
            $insertQuery = $dbh->prepare($insertSQL);
            $insertQuery->bindParam(':user_id', $aid, PDO::PARAM_INT);
            $insertQuery->bindParam(':message', $notification['message'], PDO::PARAM_STR);
            $insertQuery->bindParam(':time', $notificationTime, PDO::PARAM_STR);
            $insertQuery->bindParam(':type', $notification['type'], PDO::PARAM_STR);
            $insertQuery->execute();
        }
    }      
}

// Fetch unread notifications
$notificationSQL = "SELECT id, message, time, type FROM message WHERE user_id = :user_id AND is_read = 0";
$notificationQuery = $dbh->prepare($notificationSQL);
$notificationQuery->bindParam(':user_id', $aid, PDO::PARAM_INT);
$notificationQuery->execute();
$unreadNotifications = $notificationQuery->fetchAll(PDO::FETCH_ASSOC);

// Profile image logic
$defaultImage = '../includes/images/face8.jpg';
$profileImageTag = '<img src="' . (empty($user->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($user->ProfileImage)) . '" alt="User" class="profile-image rounded-circle me-3" width="150" height="150"/>';
$profileImageDropdownTag = '<img src="' . (empty($user->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($user->ProfileImage)) . '" alt="User" class="profile-image-lg rounded-circle my-3" width="150" height="150"/>';
$profileImageXLDropdownTag = '<img src="' . (empty($user->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($user->ProfileImage)) . '" alt="User" class="custom-profile-img rounded-circle my-3" width="150" height="150"/>';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">PAPOY</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Notification Icon -->
                <li class="nav-item dropdown">
                    <a class="nav-link" id="notificationBell" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div id="lottie-bell" style="width: 32px; height: 32px;"></div>
                    </a>
                    <!-- Use Bootstrap dropdown-menu class for proper styling -->
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationBell">
                        <li>
                            <div id="notificationContainer" class="notification-container">
                                <!-- Notifications will be dynamically loaded here -->
                                <?php foreach ($notifications as $key => $notification): ?>
                                    <div class="notification-item" data-index="<?php echo $key; ?>">
                                        <strong><?php echo $notification['message']; ?></strong><br>
                                        <small><?php echo $notification['time']; ?></small>
                                    </div>
                                    <hr>
                                <?php endforeach; ?>
                                <?php if (empty($notifications)): ?>
                                    <div class="notification-item" style="text-align:center; padding:10px; color:#888;">No new notifications</div>
                                <?php endif; ?>
                            </div>
                        </li>
                        <div class="text-center mt-2">
                            <a href="view-notifications.php" class="btn btn-primary">View All Notifications</a>
                        </div>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $profileImageTag; // Display profile image ?>
                        <span><?php echo $username; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-center" aria-labelledby="navbarDropdown">
                        <li>
                            <?php echo $profileImageDropdownTag; // Use separate image tag for dropdown ?>
                            <div class="dropdown-header">
                                <strong><?php echo $username; ?></strong><br>
                                <small><?php echo $adminEmail; ?></small>
                            </div>
                        </li>
                        <li><a class="dropdown-item d-flex align-items-center" href="profile.php"><i class="bi bi-person me-2"></i> My Profile</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="settings.php"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="logout.php"><i class="bi bi-power me-2"></i> Sign Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="../user/assets/js/lottie.min.js"></script>
<script src="../user/assets/js/sweetalert2.all.min.js"></script>

<script>
    var bellAnimation = bodymovin.loadAnimation({
        container: document.getElementById('lottie-bell'), 
        path: 'assets/notification-V3.json',              
        renderer: 'svg',                                  
        loop: false,                                      
        autoplay: false,                                  
    });

    async function markAsRead(messageId) {
        try {
            const response = await fetch('./functions/mark_as_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${messageId}`,
            });

            const result = await response.json();

            Swal.fire({
                icon: result.success ? 'success' : 'error',
                title: result.success ? 'Success' : 'Error',
                text: result.message,
            });

            return result.success;
        } catch (error) {
            console.error("Error marking notification as read:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to mark the notification as read.',
            });
            return false;
        }
    }

    function createNotificationElement(notification, index, totalNotifications) {
        const notificationItem = document.createElement('div');
        notificationItem.classList.add('notification-item');
        notificationItem.setAttribute('data-id', notification.id);
        notificationItem.innerHTML = `
            <strong>${notification.message}</strong><br>
            <small>${notification.time}</small>
        `;

        // Add a border line below each notification except the last one
        if (index < totalNotifications - 1) {
            notificationItem.style.borderBottom = "1px solid #ccc";
            notificationItem.style.paddingBottom = "10px";
            notificationItem.style.marginBottom = "10px";
        }

        notificationItem.onclick = () => handleNotificationClick(notificationItem, notification);
        return notificationItem;
    }

    function handleNotificationClick(notificationItem, notification) {
        if (notification.action === 'graduation_check') {
            showGraduationCheckModal(notificationItem, notification);
        } else {
            showGeneralNotificationModal(notificationItem, notification);
        }
    }

    function showGraduationCheckModal(notificationItem, notification) {
        Swal.fire({
            title: notification.message,
            text: "Have you graduated?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            footer: '<button id="markAsReadBtn" class="swal2-confirm swal2-styled">Mark as Read</button>'
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire('Congratulations!', 'You have graduated!', 'success');
                await markAsRead(notificationItem.getAttribute('data-id'));
            } else {
                handleExtendYearWarning();
            }
        });

        document.getElementById('markAsReadBtn')?.addEventListener('click', async () => {
            const messageId = notificationItem.getAttribute('data-id');
            if (await markAsRead(messageId)) {
                Swal.close();
            }
        });
    }

    function showGeneralNotificationModal(notificationItem, notification) {
        Swal.fire({
            title: 'Notification',
            text: `${notification.message}\n${notification.time}`,
            icon: notification.type === 'error' ? 'error' : 'warning',
            showCancelButton: true,
            confirmButtonText: 'Mark as Read',
            cancelButtonText: 'Close',
        }).then(async (result) => {
            if (result.isConfirmed) {
                const messageId = notificationItem.getAttribute('data-id');
                if (await markAsRead(messageId)) {
                    notificationItem.remove();
                    checkEmptyNotifications();
                }
            }
        });
    }

    function checkEmptyNotifications() {
        const notificationContainer = document.getElementById('notificationContainer');
        if (!notificationContainer.children.length) {
            bellAnimation.stop();
            notificationContainer.innerHTML = `
                <div style="text-align:center; padding:10px; color:#888;">
                    No new notifications
                </div>
            `;
        }
    }

    function handleExtendYearWarning() {
        Swal.fire({
            title: 'Overstay Warning',
            text: 'You are still enrolled but have not graduated. Please ensure your grades are in order.',
            icon: 'warning',
            confirmButtonText: 'Okay',
        });
    }

    function loadNotifications() {
        const unreadNotifications = <?php echo json_encode($unreadNotifications); ?>;
        const notificationContainer = document.getElementById('notificationContainer');
        notificationContainer.innerHTML = ''; 

        if (unreadNotifications.length > 0) {
            bellAnimation.loop = true;
            bellAnimation.play();

            unreadNotifications.forEach((notification, index) => {
                const notificationItem = createNotificationElement(notification, index, unreadNotifications.length);
                notificationContainer.appendChild(notificationItem);
            });
        } else {
            checkEmptyNotifications();
        }
    }

    loadNotifications();
</script>

