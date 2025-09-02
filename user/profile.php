<?php
session_start();
if (!isset($_SESSION['userid'])) {
    echo "User ID not set in session.";
    exit;
}
$userId = $_SESSION['userid'];

$courseName = isset($courseName) ? $courseName : ''; // Default to empty string
$studID = isset($studID) ? $studID : '';             // Default to empty string
$mobileNumber = isset($mobileNumber) ? $mobileNumber : ''; // Default to empty string
$scholarName = isset($scholarName) ? $scholarName : ''; // Default to empty string

include('../db/dbconnection.php');

// Fetch user's extended year if set
$sql = "SELECT extended_year, course_id FROM users WHERE user_id = :userId";
$query = $dbh->prepare($sql);
$query->bindParam(':userId', $userId, PDO::PARAM_INT);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

// Set the extended_year if it exists
$extendedYear = isset($user['extended_year']) ? $user['extended_year'] : '';
// Get the old course_id
$oldCourseId = isset($user['course_id']) ? $user['course_id'] : '';
?>

<?php $status = isset($_GET['status']) ? $_GET['status'] : ''; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link href="../user/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../user/assets/css/styles.css?v=1.2" rel="stylesheet">
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
                    <h3 class="page-title enhanced-page-title">My Profile</h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="prospectus.php">Prospectus</a></li>
                            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Profile Form Starts Here -->
            <div class="profile-card">
                <form action="functions/update-profile.php" method="POST" enctype="multipart/form-data">
                    <div class="row justify-content-center mb-3">
                        <div class="col-md-3 text-center">
                            <?php echo isset($profileImageXLDropdownTag) ? $profileImageXLDropdownTag : ''; ?>
                        </div>
                    </div>
                    <div class="accordion" id="profileAccordion">

                        <!-- Personal Information -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="personalInfoHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#personalInfo" aria-expanded="true" aria-controls="personalInfo">
                                    Personal Information
                                </button>
                            </h2>
                            <div id="personalInfo" class="accordion-collapse collapse show" aria-labelledby="personalInfoHeading" data-bs-parent="#profileAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="adminName" class="form-label">Nick Name</label>
                                            <input type="text" class="form-control" id="adminName" name="adminName" value="<?php echo htmlspecialchars($adminName); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="username" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="degreeProgram" class="form-label">Degree Program</label>
                                        <div class="col-md-12 d-flex align-items-center">
                                            <input type="text" class="form-control border border-primary" id="degreeProgram" name="degreeProgram" value="<?php echo htmlspecialchars($courseName); ?>" readonly>
                                            <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip" data-bs-placement="right" title="Long press the field to switch to another degree program."></i>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="studentID" class="form-label">Student ID#</label>
                                            <input type="text" class="form-control" id="studentID" name="studentID" value="<?php echo htmlspecialchars($studID); ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="mobileNumber" class="form-label">Mobile Number</label>
                                            <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" value="<?php echo htmlspecialchars($mobileNumber); ?>" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($adminEmail); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Profile Picture</label>
                                            <input type="file" name="profile_picture" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course History -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="courseHistoryHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#courseHistory" aria-expanded="false" aria-controls="courseHistory">
                                    Degree Program History
                                </button>
                            </h2>
                            <div id="courseHistory" class="accordion-collapse collapse" aria-labelledby="courseHistoryHeading" data-bs-parent="#profileAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group">
                                        <?php
                                        $courseHistorySQL = "SELECT course_name, start_date, end_date, years_stayed FROM course_history WHERE user_id = :userId ORDER BY start_date DESC";
                                        $courseHistoryQuery = $dbh->prepare($courseHistorySQL);
                                        $courseHistoryQuery->bindParam(':userId', $userId, PDO::PARAM_INT);
                                        $courseHistoryQuery->execute();
                                        $courseHistory = $courseHistoryQuery->fetchAll(PDO::FETCH_OBJ);

                                        if ($courseHistory) {
                                            $isFirstShift = true;
                                            foreach ($courseHistory as $course) {
                                                echo "<li class='list-group-item'>";
                                                echo "<strong>" . htmlspecialchars($course->course_name) . "</strong>";
                                                if ($isFirstShift) {
                                                    echo "<br>Year started: " . htmlspecialchars($course->start_date);
                                                }
                                                echo "<br>Year shifted: " . (!empty($course->end_date) ? htmlspecialchars($course->end_date) : 'Ongoing');
                                                $isFirstShift = false;
                                                echo "</li>";
                                            }
                                        } else {
                                            echo "<li class='list-group-item'>No degree program history available.</li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Scholarship Information -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="scholarshipInfoHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#scholarshipInfo" aria-expanded="false" aria-controls="scholarshipInfo">
                                    Scholarship Information
                                </button>
                            </h2>
                            <div id="scholarshipInfo" class="accordion-collapse collapse" aria-labelledby="scholarshipInfoHeading" data-bs-parent="#profileAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="scholarshipName" class="form-label">Scholarship Name</label>
                                            <input type="text" class="form-control" id="scholarshipName" name="scholarshipName" value="<?php echo htmlspecialchars($scholarName); ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="scholarshipStart" class="form-label">Scholarship Year Started</label>
                                            <input type="number" class="form-control" id="scholarshipStart" name="scholarshipStart" value="<?php echo !empty($scholarStart) ? htmlspecialchars($scholarStart) : ''; ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="scholarshipEnd" class="form-label">Scholarship Year Ended</label>
                                            <input type="number" class="form-control" id="scholarshipEnd" name="scholarshipEnd" value="<?php echo htmlspecialchars($scholarEnd); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- School Year Progression -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="schoolYearProgressionHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#schoolYearProgression" aria-expanded="false" aria-controls="schoolYearProgression">
                                    School Year Progression
                                </button>
                            </h2>
                            <div id="schoolYearProgression" class="accordion-collapse collapse" aria-labelledby="schoolYearProgressionHeading" data-bs-parent="#profileAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Academic Year Started</label>
                                            <p class="form-control-plaintext text-primary fw-bold">
                                                <?php echo !empty($startYear) ? htmlspecialchars($startYear) : 'N/A'; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Expected Completion Year</label>
                                            <p class="form-control-plaintext text-primary fw-bold">
                                                <?php echo htmlspecialchars($expectYear); ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Extended School Year</label>
                                            <p class="form-control-plaintext fw-bold">
                                                <span class="text-primary"><?php echo $extendYear ? htmlspecialchars($extendYear) : 'N/A'; ?></span>
                                                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo $extendedYear ? 'Your academic school year is extended only to ' . $extendedYear . '.': 'Your extended school year has not been activated yet.'; ?>"></i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                </form>
            </div>
            <!-- Profile Form Ends Here -->

        </div>
    </div>
    <!-- main-panel ends -->
</div>

<!-- Modal for Shifting Degree Program -->
<div class="modal fade" id="shiftDegreeModal" tabindex="-1" aria-labelledby="shiftDegreeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shiftDegreeModalLabel">Change Degree Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Do you want to shift to a different degree program?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmShift">Yes, change it!</button>
            </div>
        </div>
    </div>
</div>

<script src="../user/assets/js/popper.min.js"></script>
<script src="../user/assets/js/bootstrap.min.js"></script>
<script src="../user/assets/js/sweetalert2.all.min.js"></script>
<script src="../user/assets/js/jquery.min.js"></script>
<script src="../user/assets/js/jquery-ui.js"></script>

<?php if ($status == 'success'): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Profile updated successfully.'
    });
</script>
<?php elseif ($status == 'error'): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'There was an error updating your profile.'
    });
</script>
<?php elseif ($status == 'invalid_file'): ?>
<script>
    Swal.fire({
        icon: 'warning',
        title: 'Invalid File',
        text: 'Please upload a valid image file (jpg, jpeg, png, gif).'
    });
</script>
<?php endif; ?>

<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Fetch available courses and store in a variable
        let courseOptions = {
            <?php
            $courseSQL = "SELECT course_id, course_name FROM tblcourses";
            $courseQuery = $dbh->prepare($courseSQL);
            $courseQuery->execute();
            $courses = $courseQuery->fetchAll(PDO::FETCH_OBJ);
            foreach ($courses as $course) {
                echo "'" . $course->course_id . "': '" . htmlspecialchars($course->course_name) . "',";
            }
            ?>
        };

        $('#confirmShift').on('click', function() {
            $('#shiftDegreeModal').modal('hide');
            Swal.fire({
                title: 'Select New Degree Program',
                input: 'select',
                inputOptions: courseOptions,
                inputPlaceholder: 'Select a course',
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to select a course!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const selectedCourseId = result.value;
                    const oldCourseName = $('#degreeProgram').val();
                    const userId = <?php echo json_encode($userId); ?>;

                    console.log("Selected Course ID:", selectedCourseId);
                    console.log("Old Course Name:", oldCourseName);
                    console.log("User ID:", userId);
                    console.log("Old Course ID:", <?php echo json_encode($oldCourseId); ?>);

                    // Save old course to course history
                    $.ajax({
                        url: './functions/course-history.php',
                        type: 'POST',
                        data: {
                            userId: userId,
                            oldCourseName: oldCourseName,
                            oldCourseId: <?php echo json_encode($oldCourseId); ?>,
                            courseId: selectedCourseId
                        },
                        success: function(response) {
                            console.log("Save Course History Response:", response);
                            const parsedResponse = JSON.parse(response);
                            if (parsedResponse.status === 'success') {
                                // Update the degree program after saving course history
                                $.ajax({
                                    url: './functions/update-course.php',
                                    type: 'POST',
                                    data: { courseId: selectedCourseId },
                                    success: function(response) {
                                        Swal.fire('Success', 'Degree program updated successfully.', 'success').then(() => {
                                            location.reload();
                                        });
                                    },
                                    error: function() {
                                        Swal.fire('Error', 'Unable to update degree program.', 'error');
                                    }
                                });
                            } else {
                                Swal.fire('Error', parsedResponse.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Unable to save course history.', 'error');
                        }
                    });
                }
            });
        });

        // Show shift modal when holding down the degree input field
        $('#degreeProgram').on('mousedown', function(e) {
            let holdTime = 500; // .5 second to trigger hold
            let timer = setTimeout(function() {
                $('#shiftDegreeModal').modal('show');
            }, holdTime);

            $(this).on('mouseup mouseleave', function() {
                clearTimeout(timer);
            });
        });

        // Automatically update the expected completion year based on the start year
        //$('#startingYear').on('input', function() {
        //  let startYear = parseInt($(this).val());
        //  if (!isNaN(startYear)) {
        //      let endYear = startYear + 4;
        //          $('#expectedYear').val(endYear);
        //  }else {
        //       $('#expectedYear').val("");
        //      } 
        //});
    });
</script>

</body>
</html>
