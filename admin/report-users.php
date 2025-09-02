<?php 
include('../db/dbconnection.php');

// Fetch course names and IDs from tblcourses
$stmt = $dbh->prepare("SELECT course_id, course_name FROM tblcourses");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Affiliation Report</title>
    <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../admin/assets/css/styles.css?v=2.0" rel="stylesheet">
    <link href="../admin/assets/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
</head>

<body>

<div class="page-body-wrapper g-0">
    <!-- Sidebar -->
    <?php include_once('includes/sidebar.php'); ?>
    <div class="main-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content-wrapper">
            <div class="page-header enhanced-page-header">
                <div class="header-content">
                    <h3 class="page-title enhanced-page-title">Student Affiliation Report</h3>
                    <nav aria-label="breadcrumb" class="enhanced-breadcrumb-nav">
                        <ol class="breadcrumb enhanced-breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Student Affiliation Report</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Dropdown to select Degree Program -->
            <div class="report-section">
                <div class="dropdown-container">
                    <div class="dropdown mb-3">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownDegreeProgram" data-bs-toggle="dropdown" aria-expanded="false">
                            Select a Degree Program
                        </button>
                        <ul class="dropdown-menu degree" aria-labelledby="dropdownDegreeProgram">
                            <?php foreach ($courses as $course): ?>
                                <li>
                                    <a class="dropdown-item degree" href="#" data-value="<?php echo htmlspecialchars($course['course_id']); ?>">
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <input type="hidden" id="degree_program" name="course_id" required>
                    </div>

                    <!-- Container to display fetched data -->
                    <div class="report-container" id="prospectus-report">
                    </div>
                    <button id="generatePDF" class="btn btn-primary mt-3" style="display: none;">Download Report as PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../admin/assets/js/popper.min.js"></script>
<script src="../admin/assets/js/bootstrap.min.js"></script>
<script src="../admin/assets/js/jquery.min.js"></script>
<script src="../admin/assets/js/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function() {
    let selectedYear = "";
    let courseId = "";
    
    // Fetch and display prospectus for the selected degree program and effective year
    function fetchProspectus(courseId, effectiveYear) {
        selectedYear = effectiveYear;
        $('#generatePDF').hide(); // Hide the button initially until data is fetched successfully
        $('#prospectus-report').html(''); // Clear previous report data initially
        $.ajax({
            url: 'fetch-prospectus.php',
            method: 'POST',
            data: { course_id: courseId, effective_year: effectiveYear },
            success: function(response) {
                // Inject the HTML response into the report section
                if ($.trim(response) !== "" && !response.includes("No courses found")) {
                    $('#prospectus-report').html(response);
                    if ($('#prospectus-report').find('.year-section').length > 0) {
                        $('#generatePDF').show(); // Show the download button only if data is present and courses are found
                    }
                } else {
                    Swal.fire('Error!', 'No data available for the selected effective year or no courses found.', 'warning').then(() => {
                        $('#dropdownDegreeProgram').text('Select a Degree Program'); // Reset dropdown text
                        $('#degree_program').val(''); // Clear the hidden input value
                    });
                    $('#generatePDF').hide();
                }
            },
            error: function() {
                console.error('Failed to fetch prospectus.');
                Swal.fire('Error!', 'Failed to fetch prospectus data.', 'error').then(() => {
                    $('#dropdownDegreeProgram').text('Select a Degree Program'); // Reset dropdown text
                    $('#degree_program').val(''); // Clear the hidden input value
                });
                $('#generatePDF').hide();
            }
        });
    }

    // Handle degree program selection
    $(document).on('click', '.dropdown-item.degree', function() {
        courseId = $(this).data('value');
        $('#degree_program').val(courseId); // Set the hidden input value
        $('#dropdownDegreeProgram').text($(this).text()); // Update button text
        $('#prospectus-report').html(''); // Clear previous report data
        $('#generatePDF').hide(); // Always hide the button when a new course is selected
        // Prompt user to select effective year
        Swal.fire({
            title: 'Select Effective Year',
            input: 'text',
            inputLabel: 'Enter Effective Year:',
            inputPlaceholder: 'e.g., 2021',
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to enter an effective year!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var effectiveYear = result.value;
                fetchProspectus(courseId, effectiveYear); // Fetch prospectus based on course and year
            } else {
                $('#generatePDF').hide(); // Hide the button if no valid effective year is entered
            }
        });
    });

    // Function to handle potential issues with invalid characters in the year input
    function isValidYear(year) {
        return /^\d{4}$/.test(year);
    }

    // Enhanced function to generate PDF using jsPDF and AutoTable
    $('#generatePDF').on('click', function() {
        var courseName = $('#dropdownDegreeProgram').text().trim(); // Get the selected course name
        if (courseName === 'Select a Degree Program') {
            Swal.fire('Error!', 'Please select a degree program and effective year first.', 'warning');
            return;
        }

        // Initialize jsPDF with the correct method for importing plugins
        const { jsPDF } = window.jspdf;
        var doc = new jsPDF();

        // Add the title and effective year with proper styling and spacing
        doc.setFontSize(16);
        doc.setTextColor('#4CAF50');
        doc.text("Prospectus Report for " + courseName, 14, 20);
        doc.setFontSize(12);
        doc.setTextColor('#000000');
        doc.text("(Effective Year: " + selectedYear + ")", 105, 30, { align: 'center' });

        // Extract data from the prospectus table
        $('#prospectus-report .year-section').each(function() {
            var yearTitle = $(this).find('h5').text();
            doc.setFontSize(14);
            doc.setTextColor('#4CAF50');
            var yearStartY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 5 : 32;
            doc.autoTable({
                head: [[yearTitle]],
                headStyles: { fillColor: [255, 255, 255], textColor: '#000000', halign: 'left' },
                theme: 'plain',
                startY: yearStartY
            });
            
            $(this).find('.semester-section').each(function() {
                var semesterTitle = $(this).find('h6').text();
                doc.setFontSize(12);
                doc.setTextColor('#000000');
                var semesterStartY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 5 : yearStartY + 5;
                doc.autoTable({
                    head: [[semesterTitle]],
                    headStyles: { fillColor: [180, 180, 180], textColor: '#FFFFFF', halign: 'center' },
                    theme: 'plain',
                    startY: semesterStartY
                });

                // Extract table data
                var tableData = [];
                $(this).find('table tbody tr').each(function() {
                    var row = [];
                    $(this).find('td').each(function() {
                        row.push($(this).text());
                    });
                    tableData.push(row);
                });

                // Add the table to PDF with custom header and line styles
                doc.autoTable({
                    head: [['Course Code', 'Descriptive Title', 'Co-/Prerequisite', 'Units', 'Hours (Lec)', 'Hours (Lab)', 'Total Hours']], // Table headers
                    body: tableData, // Data from the table
                    startY: doc.lastAutoTable ? doc.lastAutoTable.finalY + 5 : semesterStartY + 5,
                    theme: 'striped',
                    headStyles: { fillColor: '#4CAF50', textColor: '#FFFFFF', fontSize: 10 }, // Set header color to #4CAF50 with white text
                    styles: { lineColor: [0, 0, 0], lineWidth: 0.5, fontSize: 10, cellPadding: 4, halign: 'center' }, // Set line color to black for rows and columns
                    alternateRowStyles: { fillColor: [245, 245, 245] } // Add alternate row coloring for better readability
                });
            });
        });

        // Save the PDF
        console.log("Saving PDF..."); // Debugging statement
        doc.save(courseName + '-prospectus-report.pdf');
    });

    // Added additional check for missing or incorrect data before displaying the PDF button
    $('#generatePDF').on('click', function() {
        if ($('#prospectus-report').is(':empty')) {
            Swal.fire('Error!', 'Please make sure all the required information is properly selected before generating the report.', 'warning');
            return;
        }
    });
});
</script>

</body>
</html>
