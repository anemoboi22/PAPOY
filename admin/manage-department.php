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
                    <h3 class="page-title enhanced-page-title">Manage Department & Degree</h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Department & Degree</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Department Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include '../db/dbconnection.php';

                        $query = "SELECT * FROM tbldepartment";
                        $stmt = $dbh->prepare($query);
                        $stmt->execute();
                        $count = 1;

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . $count++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['department_name']) . "</td>";
                            echo "<td class='action-buttons'>
                                    <button class='view-btn' onclick='viewDepartment({$row['department_id']})'><i class='bi bi-eye'></i> View</button>
                                    <button class='edit-btn' onclick='editDepartment({$row['department_id']})'><i class='bi bi-pencil-square'></i> Edit</button>
                                    <button class='delete-btn' onclick='confirmDelete({$row['department_id']})'><i class='bi bi-trash'></i> Delete</button>
                                  </td>";
                            echo "</tr>";
                        }

                        // Add input field for a new department (Department Name)
                        echo "<tr class='add-department-row'>
                                <td></td>
                                <td><input type='text' id='newDepartmentName' class='form-control' placeholder='Enter Department Name'></td>
                                <td class='action-buttons'>
                                    <button class='btn-add-course' onclick='addDepartment()'><i class='bi bi-plus-square'></i> Add</button>
                                </td>
                              </tr>";
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- main-panel ends -->
</div>

<script src="../admin/assets/js/popper.min.js"></script>
<script src="../admin/assets/js/bootstrap.min.js"></script>
<script src="../admin/assets/js/sweetalert2.all.min.js"></script>

<script>
    function viewDepartment(departmentId) {
        // Redirect to view-department.php with the department ID
        window.location.href = 'view-department.php?id=' + departmentId;
    }

    function editDepartment(departmentId) {
        // Redirect to edit-department.php with the department ID
        window.location.href = 'edit-department.php?id=' + departmentId;
    }

    function confirmDelete(departmentId) {
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
                window.location.href = `./functions/delete-department.php?id=${departmentId}`;
            }
        });
    }

    function addDepartment() {
        const departmentName = document.getElementById('newDepartmentName').value.trim();

        if (departmentName === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Department name cannot be empty!',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Check if the department name already exists via an AJAX call
        fetch(`./functions/check-department.php?name=${encodeURIComponent(departmentName)}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'This department name already exists.',
                        confirmButtonText: 'OK'
                    });
                } else {
                    // Redirect to a PHP script to add the new department
                    window.location.href = "./functions/add-department.php?name=" + encodeURIComponent(departmentName);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while checking the department name.',
                    confirmButtonText: 'OK'
                });
            });
    }

    // Display SweetAlert2 messages based on query parameters
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

</body>
</html>
