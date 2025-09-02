<?php

date_default_timezone_set('Asia/Manila');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db/dbconnection.php');

$aid = $_SESSION['userid'];

// Fetch user data and the course_name by joining users with tblcourses
$sql = "SELECT u.nickName, u.email, u.fullname, u.phone_number, u.ProfileImage, u.student_id, 
        u.scholarship_name, YEAR(u.scholarship_start) AS scholarship_start, YEAR(u.scholarship_end) AS scholarship_end, 
        u.starting_year, u.expected_year, u.extended_year, c.course_name
        FROM users u
        INNER JOIN tblcourses c ON u.course_id = c.course_id
        WHERE u.user_id = :aid";

$query = $dbh->prepare($sql);
$query->bindParam(':aid', $aid, PDO::PARAM_INT);
$query->execute();
$user = $query->fetch(PDO::FETCH_OBJ);

// If user data is found, extract necessary values
if ($user) {
    $adminName = htmlentities($user->nickName ?? '');
    $adminEmail = htmlentities($user->email ?? '');
    $username = htmlentities($user->fullname ?? '');
    $mobileNumber = htmlentities($user->phone_number ?? '');
    $profileImage = $user->ProfileImage;
    $studID = htmlentities($user->student_id);
    $scholarName = htmlentities($user->scholarship_name ?? '');
    $scholarStart = htmlentities($user->scholarship_start);
    $scholarEnd = htmlentities($user->scholarship_end);
    $startYear = htmlentities($user->starting_year ?? '');
    $expectYear = htmlentities($user->expected_year ?? '');
    $extendYear = htmlentities($user->extended_year ?? '');
    $courseName = htmlentities($user->course_name);

    $notifications = [];

    $currentDate = new DateTime();
    $scholarEndDate = new DateTime($scholarEnd . '-12-31');

    // Scholarship status check
    $scholarshipEnded = $currentDate > $scholarEndDate;
    $oneYearBeforeEnd = (clone $scholarEndDate)->sub(new DateInterval('P1Y'));
    $scholarshipNearEnd = !$scholarshipEnded && $currentDate >= $oneYearBeforeEnd;

    if ($scholarshipEnded) {
        $notifications[] = [
            'message' => 'Your scholarship has ended.',
            'time' => 'Please consult the Student Affairs Office.',
            'type' => 'error'
        ];
    } elseif ($scholarshipNearEnd) {
        $interval = $currentDate->diff($scholarEndDate);
        $notifications[] = [
            'message' => 'Your scholarship is nearing its end.',
            'time' => 'It ends in ' . $interval->y . ' years, ' . $interval->m . ' months, and ' . $interval->d . ' days.',
            'type' => 'warning'
        ];
    } else {
        $interval = $currentDate->diff($scholarEndDate);
        $notifications[] = [
            'message' => 'Your scholarship is active.',
            'time' => 'It lasts for ' . $interval->y . ' years, ' . $interval->m . ' months, and ' . $interval->d . ' days.',
            'type' => 'info'
        ];
    }

    // Graduation status check
    if (!empty($expectYear)) {
        $expectDate = new DateTime($expectYear . '-01-01');

        if ($currentDate->format('Y-m-d') == $expectDate->format('Y-m-d')) {
            $notifications[] = [
                'message' => 'Are you graduating today?',
                'time' => $currentDate->format('h:i A'),
                'type' => 'info',
                'action' => 'graduation_check'
            ];
        } elseif ($currentDate > $expectDate) {
            if (!empty($extendYear)) {
                $extendDate = new DateTime($extendYear . '-12-31');
                $overstayStartDate = (clone $extendDate)->add(new DateInterval('P3D'));

                if ($currentDate <= $extendDate) {
                    $notifications[] = [
                        'message' => 'You are still enrolled but have not graduated. Please ensure your grades are in order.',
                        'time' => $currentDate->format('h:i A'),
                        'type' => 'warning'
                    ];
                    $isOverstaying = false;
                } elseif ($currentDate <= $overstayStartDate) {
                    $notifications[] = [
                        'message' => 'You have overstayed your enrollment period. Please consult the Students Affairs Office.',
                        'time' => $currentDate->format('h:i A'),
                        'type' => 'error'
                    ];
                    $isOverstaying = false;
                } else {
                    $isOverstaying = true;
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

    // Fetch grades and prerequisites for the user
    $stmtFetchGrades = $dbh->prepare("SELECT c.id, c.course_code, c.descriptive_title, g.grade, c.co_prerequisite FROM courses c LEFT JOIN grades g ON c.id = g.course_id WHERE g.user_id = :user_id");
    $stmtFetchGrades->bindParam(':user_id', $aid, PDO::PARAM_INT);
    $stmtFetchGrades->execute();
    $gradesDetails = $stmtFetchGrades->fetchAll(PDO::FETCH_ASSOC);

    foreach ($gradesDetails as $gradeDetail) {
        // If the grade is null or one of the incomplete statuses
        if ($gradeDetail['grade'] === null || in_array($gradeDetail['grade'], ['INC', 'DRP', 'NA', 'NG'])) {
            $coursesWithIssues[$gradeDetail['course_code']] = $gradeDetail['descriptive_title'];
            $notifications[] = [
                'message' => 'The course: ' . $gradeDetail['descriptive_title'] . ' has a missing or incomplete grade (' . $gradeDetail['grade'] . ').',
                'type' => 'warning'
            ];
        }
    }

    // Insert new notifications to the database
    foreach ($notifications as $notification) {
        $checkSQL = "SELECT 1 FROM message WHERE user_id = :user_id AND message = :message AND DATE(created_at) = CURDATE()";
        $checkQuery = $dbh->prepare($checkSQL);
        $checkQuery->bindParam(':user_id', $aid, PDO::PARAM_INT);
        $checkQuery->bindParam(':message', $notification['message'], PDO::PARAM_STR);
        $checkQuery->execute();

        $notificationTime = $notification['time'] ?? (new DateTime())->format('h:i A');

        if ($checkQuery->rowCount() == 0) {
            $insertSQL = "INSERT INTO message (user_id, message, time, type, action, is_read, created_at) VALUES (:user_id, :message, :time, :type, :action, 0, NOW())";
            $insertQuery = $dbh->prepare($insertSQL);
            $insertQuery->bindParam(':user_id', $aid, PDO::PARAM_INT);
            $insertQuery->bindParam(':message', $notification['message'], PDO::PARAM_STR);
            $insertQuery->bindParam(':time', $notificationTime, PDO::PARAM_STR);
            $insertQuery->bindParam(':type', $notification['type'], PDO::PARAM_STR);
            $actionValue = isset($notification['action']) ? $notification['action'] : null;
            $insertQuery->bindParam(':action', $actionValue, PDO::PARAM_STR);
            $insertQuery->execute();
        }
    }
}

// Fetch unread notifications
$notificationSQL = "SELECT id, message, time, type, action, user_id FROM message WHERE user_id = :user_id AND is_read = 0";
$notificationQuery = $dbh->prepare($notificationSQL);
$notificationQuery->bindParam(':user_id', $aid, PDO::PARAM_INT);
$notificationQuery->execute();
$unreadNotifications = $notificationQuery->fetchAll(PDO::FETCH_ASSOC);

// Profile image logic
$defaultImage = '../includes/images/face8.jpg';
$profileImageTag = '<img src="' . (empty($user->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($user->ProfileImage)) . '" alt="User" class="profile-image rounded-circle me-3 border border-primary border-2" width="150" height="150"/>';
$profileImageDropdownTag = '<img src="' . (empty($user->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($user->ProfileImage)) . '" alt="User" class="profile-image-lg rounded-circle my-3" width="150" height="150"/>';
$profileImageXLDropdownTag = '<img src="' . (empty($user->ProfileImage) ? $defaultImage : 'data:image/jpeg;base64,' . base64_encode($user->ProfileImage)) . '" alt="User" class="custom-profile-img rounded-circle my-3" width="150" height="150"/>';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="prospectus.php">PAPOY</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            <?php if (isset($isOverstaying) && $isOverstaying): ?>
                <span class="text-danger ms-2">Overstaying</span>
            <?php endif; ?>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($isOverstaying) && $isOverstaying): ?>
                    <div style="transform: translateY(12px);">
                        <span class="text-danger ms-2 fw-bold">Overstaying</span>
                    </div>
                <?php endif; ?>
                <!-- Notification Icon -->
                <li class="nav-item dropdown">
                    <a class="nav-link" id="notificationBell" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div id="lottie-bell" style="width: 32px; height: 32px;"></div>
                    </a>
                    <!-- Use Bootstrap dropdown-menu class for proper styling -->
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationBell" style="background-color:#4682B4;">
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
                            <!-- Add the Mark All as Read button -->
                            <?php if (count($unreadNotifications) > 4): // Show icon if notifications exceed 4 
                            ?>
                                <div class="text-center my-1">
                                    <button id="markAllReadButton" class="btn btn-success">
                                        <i class="bi bi-check2-circle"></i> Mark All as Read
                                    </button>
                                </div>
                            <?php endif; ?>
                        </li>
                        <div class="text-center mt-2">
                            <a href="view-notifications.php" class="btn btn-success">View All Notifications</a>
                        </div>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $profileImageTag; // Display profile image 
                        ?>
                        <span><?php echo $username; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-center" aria-labelledby="navbarDropdown">
                        <li>
                            <?php echo $profileImageDropdownTag; // Use separate image tag for dropdown 
                            ?>
                            <div class="dropdown-header">
                                <strong><?php echo $username; ?></strong><br>
                                <small><?php echo $adminEmail; ?></small>
                            </div>
                        </li>
                        <li><a class="dropdown-item d-flex align-items-center" href="profile.php"><i class="bi bi-person me-2"></i> My Profile</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="settings.php"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
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
    const unreadNotifications = <?php echo json_encode($unreadNotifications); ?>;
    const expectedGraduationYear = <?php echo json_encode($expectYear); ?>;
</script>
<script>
    var bellAnimation = bodymovin.loadAnimation({
        container: document.getElementById('lottie-bell'),
        path: 'assets/notification-V3.json',
        renderer: 'svg',
        loop: false,
        autoplay: false,
    });

    // Function to mark all notifications as read
    async function markAllAsRead() {
        try {
            const response = await fetch('./functions/mark_all_as_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=mark_all_read',
            });

            const result = await response.json();
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    text: result.message,
                });

                // Clear notification container
                const notificationContainer = document.getElementById('notificationContainer');
                notificationContainer.innerHTML = `
                    <div style="text-align:center; padding:10px; color:#888;">
                        No new notifications
                    </div>
                `;
                bellAnimation.stop();

                // Remove "Mark All as Read" button
                const markAllReadButton = document.getElementById('markAllReadButton');
                if (markAllReadButton) {
                    markAllReadButton.remove();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    text: result.message,
                });
            }
        } catch (error) {
            console.error("Error marking all notifications as read:", error);
            Swal.fire({
                icon: 'error',
                text: 'Failed to mark all notifications as read.',
            });
        }
    }

    // Attach the click event handler to the Mark All as Read button
    const markAllReadButton = document.getElementById('markAllReadButton');
    if (markAllReadButton) {
        markAllReadButton.addEventListener('click', markAllAsRead);
    }

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

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    text: result.message, // Display success message from PHP
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    text: result.message, // Display failure message from PHP
                });
            }

            return result.success;
        } catch (error) {
            console.error("Error marking notification as read:", error);
            Swal.fire({
                icon: 'error',
                text: 'Failed to mark the notification as read.',
            });
            return false;
        }
    }

    function createNotificationElement(notification, index, totalNotifications) {
        const notificationItem = document.createElement('div');
        notificationItem.classList.add('notification-item');
        notificationItem.setAttribute('data-id', notification.id);
        notificationItem.setAttribute('data-user-id', notification.user_id);
        notificationItem.innerHTML = `
            <strong>${notification.message}</strong><br>
            <small>${notification.time}</small>
        `;

        if (index < totalNotifications - 1) {
            notificationItem.style.borderBottom = "1px solid #ccc";
            notificationItem.style.paddingBottom = "10px";
            notificationItem.style.marginBottom = "10px";
        }

        notificationItem.onclick = () => handleNotificationClick(notificationItem, notification);
        return notificationItem;
    }

    function handleNotificationClick(notificationItem, notification) {
        if (notification.action && notification.action === 'graduation_check') {
            showGraduationCheckModal(notificationItem, notification);
        } else {
            showGeneralNotificationModal(notificationItem, notification);
        }
    }

    function showGraduationCheckModal(notificationItem, notification) {
        Swal.fire({
            title: notification.message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            allowOutsideClick: true,
            allowEscapeKey: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                await markAsRead(notificationItem.getAttribute('data-id'));
                Swal.fire('Congratulations!', 'You have graduated!', 'success');
                notificationItem.remove();
                checkEmptyNotifications();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Only call this if user explicitly clicks "No"
                handleExtendYearAutomatically(notificationItem);
            }
        });
    }


    function handleExtendYearAutomatically(notificationItem) {
        const nextYear = parseInt(expectedGraduationYear) + 1;
        const userId = notificationItem.getAttribute('data-user-id');

        Swal.fire({
            title: 'Enrollment Extended',
            text: `Your enrollment year has been automatically extended to ${nextYear}.`,
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(async () => {
            await saveExtendYearToDatabase(userId, nextYear);
            await markAsRead(notificationItem.getAttribute('data-id'));
            notificationItem.remove();
            checkEmptyNotifications();
        });
    }

    async function saveExtendYearToDatabase(userId, extendYear) {
        console.log("User ID:", userId); // Debugging log
        console.log("Extend Year:", extendYear); // Debugging log

        try {
            const response = await fetch('./functions/update_extend_year.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `user_id=${userId}&extend_year=${extendYear}`,
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire('Success', result.message, 'success');
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (error) {
            console.error("Error saving extend year to database:", error);
            Swal.fire('Error', 'Failed to save the extended year in the database.', 'error');
        }
    }

    function showGeneralNotificationModal(notificationItem, notification) {
        Swal.fire({
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
                <div style="text-align:center; padding:10px; color:#ffffff;">
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