<?php 
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the login status is set
$loginStatus = isset($_SESSION['login_status']) ? $_SESSION['login_status'] : '';

// Clear the login status after displaying the notification
unset($_SESSION['login_status']);

include('../db/dbconnection.php'); // Make sure to include your database connection file

$aid = $_SESSION['adminid']; // Fetch the admin ID from the session
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/styles.css?v=5.0" rel="stylesheet">
    <link href="../admin/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../webicon/android-chrome-192x192.png">
</head>
<body>
<div class="page-body-wrapper g-0">
    <!-- Partial for the sidebar -->
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-sm-flex align-items-baseline report-summary-header">
                                        <h1 class="h3 mb-3">Welcome, <?php echo $adminName; ?>!</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="row report-inner-cards-wrapper">
                                <!-- Cards Content -->
                                <!-- Card 1 -->
                                <div class="col-md-6 col-sm-7 col-12">
                                    <div class="card text-black bg-success mb-3">
                                        <div class="card-body">
                                        <?php 
                                            $sql1 ="SELECT * from  tbldepartment";
                                            $query1 = $dbh -> prepare($sql1);
                                            $query1->execute();
                                            $results1=$query1->fetchAll(PDO::FETCH_OBJ);
                                            $totdepartment=$query1->rowCount();
                                        ?>
                                            <h5 class="card-title">Total Departments</h5>
                                            <p class="card-text"><?php echo htmlentities($totdepartment);?></p>
                                            <a href="manage-department.php" class="btn btn-light">Manage Departments</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card 2 -->
                                <div class="col-md-6 col-sm-7 col-12">
                                    <div class="card text-black bg-danger mb-3">
                                        <div class="card-body">
                                        <?php 
                                            $sql2 ="SELECT * from  tblcourses";
                                            $query2 = $dbh -> prepare($sql2);
                                            $query2->execute();
                                            $results2=$query2->fetchAll(PDO::FETCH_OBJ);
                                            $totcourses=$query2->rowCount();
                                        ?>
                                            <h5 class="card-title">Total Prospectus</h5>
                                            <p class="card-text"><?php echo htmlentities($totcourses);?></p>
                                            <a href="manage-prospectus.php" class="btn btn-light">Manage Prospectus</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="chart-container">
                                            <canvas id="courseUserChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- main-panel ends -->
</div>

<script src="../admin/assets/js/popper.min.js"></script>
<script src="../admin/assets/js/bootstrap.min.js"></script>
<script src="../admin/assets/js/sweetalert2.all.min.js"></script>
<script src="../admin/assets/js/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginStatus = "<?php echo $loginStatus; ?>";

    if (loginStatus === 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: "success",
            title: "Signed in successfully"
        });
    }

    // Fetch data for chart
    <?php
    $sql2 = "SELECT tblcourses.course_name, COUNT(users.user_id) as user_count FROM users INNER JOIN tblcourses ON users.course_id = tblcourses.course_id GROUP BY tblcourses.course_name";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $chartData = $query2->fetchAll(PDO::FETCH_ASSOC);
    ?>

    const chartLabels = <?php echo json_encode(array_column($chartData, 'course_name')); ?>;
    const chartData = <?php echo json_encode(array_column($chartData, 'user_count')); ?>;

    // Check if colors are already stored in localStorage
    let backgroundColors = localStorage.getItem('chartColors');
    if (backgroundColors) {
        backgroundColors = JSON.parse(backgroundColors);
    } else {
        // Generate new random colors and store them in localStorage
        backgroundColors = chartLabels.map(() => getRandomColor());
        localStorage.setItem('chartColors', JSON.stringify(backgroundColors));
    }

    // Handle updates or new labels
    if (backgroundColors.length < chartLabels.length) {
        for (let i = backgroundColors.length; i < chartLabels.length; i++) {
            backgroundColors.push(getRandomColor());
        }
        localStorage.setItem('chartColors', JSON.stringify(backgroundColors));
    }

    // Generate random color
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    // Create Bar Chart
    const ctx = document.getElementById('courseUserChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Number of students in this degree program',
                data: chartData,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false // Disable the legend
                }
            }
        }
    });
});
</script>
</body>
</html>
