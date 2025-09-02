<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications</title>
    <link href="../user/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../user/assets/css/styles.css?v=2.0" rel="stylesheet">
    <link href="../user/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../webicon/android-chrome-192x192.png">
</head>

<body>

<div class="page-body-wrapper g-0">
    <!-- Partial for the sidebar -->
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content-wrapper">
            <div class="page-header enhanced-page-header">
                <div class="header-content">
                    <h3 class="page-title enhanced-page-title"> View Notifications </h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="prospectus.php">Prospectus</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Notifications</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="prospectus-container">
                <!-- New Notifications Section -->
                <div class="card mb-4 shadow-lg border-primary" style="border-radius: 0;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">New Notifications</h5>
                    </div>
                    <div class="card-body">
                        <div id="newNotifications">
                            <!-- Notifications will be loaded here -->
                            <div class="alert alert-info" role="alert">
                                No new notifications.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Read Notifications Section -->
                <div class="card mb-4 shadow-lg border-success" style="border-radius: 0;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Read Notifications</h5>
                    </div>
                    <div class="card-body">
                        <div id="readNotifications">
                            <!-- Notifications will be loaded here -->
                            <div class="alert alert-secondary" role="alert">
                                No read notifications.
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>

<script src="../user/assets/js/popper.min.js"></script>
<script src="../user/assets/js/bootstrap.min.js"></script>
<script src="../user/assets/js/sweetalert2.all.min.js"></script>
<script src="../user/assets/js/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        function loadNotifications() {
            $.ajax({
                url: './functions/fetch-notifications.php',
                type: 'GET',
                success: function(data) {
                    const notifications = JSON.parse(data);
                    $('#newNotifications').html(notifications.unread);
                    $('#readNotifications').html(notifications.read);
                }
            });
        }

        // Load notifications initially
        loadNotifications();

        // Set an interval to reload notifications every 10 seconds
        setInterval(loadNotifications, 10000);
    });
</script>

</body>
</html>
