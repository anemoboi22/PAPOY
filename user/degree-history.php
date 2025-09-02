<?php 
include('../db/dbconnection.php');
session_start();

$userId = $_SESSION['userid'];

// Fetch all degrees for the user
$sqlDegrees = "SELECT old_courseID, course_name, start_date, end_date FROM course_history WHERE user_id = :user_id ORDER BY start_date ASC";
$queryDegrees = $dbh->prepare($sqlDegrees);
$queryDegrees->bindParam(':user_id', $userId, PDO::PARAM_INT);
$queryDegrees->execute();
$userDegrees = $queryDegrees->fetchAll(PDO::FETCH_ASSOC);

$degreeTabs = [];
$groupedCourses = [];

foreach ($userDegrees as $degree) {
    $degreeId = $degree['old_courseID'];
    $degreeTabs[$degreeId] = $degree;

    // Fetch graded courses for the user's past degree programs (for the specific degree)
    $stmtFetchCourses = $dbh->prepare("SELECT c.id, c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, g.grade FROM courses c JOIN grades g ON c.id = g.course_id WHERE g.user_id = :user_id AND c.course_id = :current_course_id ORDER BY c.year ASC, c.semester ASC");
    $stmtFetchCourses->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtFetchCourses->bindParam(':current_course_id', $degreeId, PDO::PARAM_INT);
    $stmtFetchCourses->execute();
    $gradedCourses = $stmtFetchCourses->fetchAll(PDO::FETCH_ASSOC);

    // Debugging output to check if courses are fetched correctly
    echo "";

    // Group the graded courses by year and semester
    $groupedCourses[$degreeId] = [];
    foreach ($gradedCourses as $course) {
        $year = $course['year'];
        $semester = $course['semester'];

        if (!isset($groupedCourses[$degreeId][$year])) {
            $groupedCourses[$degreeId][$year] = ['1st Semester' => [], '2nd Semester' => []];
        }

        $groupedCourses[$degreeId][$year][$semester][] = $course;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Degree Program History</title>
    <link href="../user/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../user/assets/css/styles.css?v=1.5" rel="stylesheet">
    <link href="../user/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../webicon/android-chrome-192x192.png">
    <script src="../user/assets/js/sweetalert2.all.min.js"></script>
</head>
<body>
<div class="page-body-wrapper g-0">
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content-wrapper">
            <div class="page-header enhanced-page-header">
                <div class="header-content">
                    <h3 class="page-title enhanced-page-title"> Degree Program History </h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="prospectus.php">Prospectus</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Degree Program History</li>
                        </ol>
                    </nav>
                </div>             
            </div>

            <div class="prospectus-container">
                <?php if ($degreeTabs): ?>
                    <!-- Degree Tabs Navigation -->
                    <ul class="nav nav-tabs" id="degreeTabs" role="tablist">
                        <?php $isFirst = true; ?>
                        <?php foreach ($degreeTabs as $degreeId => $degree): ?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?php echo $isFirst ? 'active' : ''; ?>" id="degree-<?php echo $degreeId; ?>-tab" data-bs-toggle="tab" href="#degree-<?php echo $degreeId; ?>" role="tab" aria-controls="degree-<?php echo $degreeId; ?>" aria-selected="true">
                                    <?php echo htmlspecialchars($degree['course_name'], ENT_QUOTES, 'UTF-8') . ' ('  . htmlspecialchars($degree['end_date'], ENT_QUOTES, 'UTF-8') . ')'; ?>
                                </a>
                            </li>
                            <?php $isFirst = false; ?>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Degree Tab Content -->
                    <div class="tab-content" id="degreeTabsContent">
                        <?php $isFirst = true; ?>
                        <?php foreach ($degreeTabs as $degreeId => $degree): ?>
                            <div class="tab-pane fade <?php echo $isFirst ? 'show active' : ''; ?>" id="degree-<?php echo $degreeId; ?>" role="tabpanel" aria-labelledby="degree-<?php echo $degreeId; ?>-tab">
                                <h4 class="mt-3"><?php echo htmlspecialchars($degree['course_name'], ENT_QUOTES, 'UTF-8'); ?></h4>

                                <?php if (isset($groupedCourses[$degreeId]) && !empty($groupedCourses[$degreeId])): ?>
                                    <?php foreach ($groupedCourses[$degreeId] as $year => $semesters): ?>
                                        <h5 class="mt-3"><?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?></h5>
                                        <?php foreach ($semesters as $semesterName => $courses): ?>
                                            <?php if (!empty($courses)): ?>
                                                <h6><?php echo htmlspecialchars($semesterName, ENT_QUOTES, 'UTF-8'); ?></h6>
                                                <div class="scrollable-table-wrapper">
                                                    <table class="prospectus-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Course Code</th>
                                                                <th>Descriptive Title</th>
                                                                <th>Co-/Prerequisite</th>
                                                                <th>Units</th>
                                                                <th>Grades</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($courses as $course): ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($course['course_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['descriptive_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['co_prerequisite'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['units'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                    <td><?php echo htmlspecialchars($course['grade'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning" role="alert">
                                        No grades found for this degree program.
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php $isFirst = false; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        No history of degree programs available yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script src="../user/assets/js/popper.min.js"></script>
<script src="../user/assets/js/bootstrap.min.js"></script>
<script src="../user/assets/js/sweetalert2.all.min.js"></script>
</body>
</html>
