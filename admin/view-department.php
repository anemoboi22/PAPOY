<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Department & Degree Programs</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/styles.css?v=2.0" rel="stylesheet">
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
            <div class="page-header enhanced-page-header">
                <div class="header-content">
                    <h3 class="page-title enhanced-page-title">View Department & Degree</h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="manage-department.php">Manage Department & Degree</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Department & Degree</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <?php
                include '../db/dbconnection.php';

                // Get department ID from URL
                $department_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

                if ($department_id) {
                    try {
                        // Fetch department details
                        $query = "SELECT department_name FROM tbldepartment WHERE department_id = :department_id";
                        $stmt = $dbh->prepare($query);
                        $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $department = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($department) {
                            echo "<div class='department-name-container'>";
                            echo "<h4>" . htmlspecialchars($department['department_name']) . "</h4>";
                            echo "</div>";

                            // Fetch courses associated with this department
                            $query = "SELECT course_id, course_name FROM tblcourses WHERE department_id = :department_id";
                            $stmt = $dbh->prepare($query);
                            $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
                            $stmt->execute();
                            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            echo "<div class='courses-table-container'>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Degree Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>";

                            if ($courses) {
                                // Display existing courses
                                foreach ($courses as $index => $course) {
                                    echo "<tr>";
                                    echo "<td>" . ($index + 1) . "</td>";
                                    echo "<td>" . htmlspecialchars($course['course_name']) . "</td>";
                                    echo "<td class='action-buttons'>
                                                <button class='edit-btn' onclick='editCourse(" . $course['course_id'] . ", " . $department_id . ")'><i class='bi bi-pencil-square'></i></button>
                                                <button class='delete-btn' onclick='confirmDelete(" . $course['course_id'] . ", " . $department_id . ")'><i class='bi bi-trash'></i></button>
                                        </td>";
                                    echo "</tr>";
                                }
                            }

                            // Add input field for a new course (Degree Name) - Only one input row at the bottom
                            echo "<tr class='add-course-row'>
                                    <td></td>
                                    <td><input type='text' id='newCourseName' class='form-control' placeholder='Enter Degree Name'></td>
                                    <td class='action-buttons'>
                                        <button class='btn-add-course' onclick='addCourse()'><i class='bi bi-plus-square'></i> Add</button>
                                    </td>
                                </tr>";

                            echo "</tbody>
                                    </table>
                                </div>";
                        } else {
                            echo "<div class='courses-table-container'><h4>No department found.</h4></div>";
                        }
                    } catch (PDOException $e) {
                        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                } else {
                    echo "<p>No department ID specified.</p>";
                }
                ?>

            <script src="../admin/assets/js/popper.min.js"></script>
            <script src="../admin/assets/js/bootstrap.min.js"></script>
            <script src="../admin/assets/js/sweetalert2.all.min.js"></script>
            <script>
                function editCourse(courseId, departmentId) {
                    window.location.href = "edit-course.php?id=" + courseId + "&department_id=" + departmentId;
                }

                function confirmDelete(courseId, departmentId) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `./functions/delete-course.php?id=${courseId}&department_id=${departmentId}`;
                        }
                    });
                }

                function addCourse() {
                    const courseName = document.getElementById('newCourseName').value.trim();

                    if (courseName === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Degree name cannot be empty!',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Check if the course name already exists via an AJAX call
                    fetch(`./functions/check-course.php?name=${encodeURIComponent(courseName)}&department_id=${<?= json_encode($department_id) ?>}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning!',
                                    text: 'This degree name already exists.',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                // Redirect to a PHP script to add the new course
                                window.location.href = "./functions/add-degree.php?name=" + encodeURIComponent(courseName) + "&department_id=" + <?= json_encode($department_id) ?>;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An error occurred while checking the degree name.',
                                confirmButtonText: 'OK'
                            });
                        });
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const urlParams = new URLSearchParams(window.location.search);
                    const status = urlParams.get('status');
                    const message = urlParams.get('message');

                    if (status && message) {
                        Swal.fire({
                            icon: status === 'success' ? 'success' : 'error',
                            title: status.charAt(0).toUpperCase() + status.slice(1) + '!',
                            text: decodeURIComponent(message),
                            confirmButtonText: 'OK'
                        });
                    }
                });
            </script>

        </div>
    </div>
    <!-- main-panel ends -->
</div>
</body>
</html>
