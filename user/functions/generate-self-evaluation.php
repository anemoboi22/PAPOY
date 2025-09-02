<?php
require('../assets/fpdf186/fpdf.php');
include('../../db/dbconnection.php');
session_start();

$userId = $_SESSION['userid'];

// Fetch student ID from users table
$sqlStudentID = "SELECT student_id, fullname, course_id FROM users WHERE user_id = :user_id";
$queryStudentID = $dbh->prepare($sqlStudentID);
$queryStudentID->bindParam(':user_id', $userId, PDO::PARAM_INT);
$queryStudentID->execute();
$userDetails = $queryStudentID->fetch(PDO::FETCH_ASSOC);

$studentId = $userDetails['student_id'];
$fullname = $userDetails['fullname'];
$courseId = $userDetails['course_id'];

// Fetch course name from tblcourses and effective year from courses table
$sqlCourse = "SELECT tc.course_name, c.effective_year 
              FROM tblcourses tc 
              JOIN courses c ON tc.course_id = c.course_id 
              WHERE tc.course_id = :course_id LIMIT 1";
$queryCourse = $dbh->prepare($sqlCourse);
$queryCourse->bindParam(':course_id', $courseId, PDO::PARAM_INT);
$queryCourse->execute();
$courseDetails = $queryCourse->fetch(PDO::FETCH_ASSOC);

$courseName = $courseDetails['course_name'] ?? 'Unknown';
$effectiveYear = $courseDetails['effective_year'] ?? 'Unknown';

// Fetch courses and grades for the user, specific to their degree/course
$sqlFetchCourses = "SELECT c.year, c.semester, c.course_code, c.descriptive_title, c.co_prerequisite, c.units, g.grade 
                    FROM courses c 
                    LEFT JOIN grades g ON c.id = g.course_id 
                    WHERE g.user_id = :user_id AND c.course_id = :course_id 
                    ORDER BY c.year, c.semester, c.id";
$queryFetchCourses = $dbh->prepare($sqlFetchCourses);
$queryFetchCourses->bindParam(':user_id', $userId, PDO::PARAM_INT);
$queryFetchCourses->bindParam(':course_id', $courseId, PDO::PARAM_INT);
$queryFetchCourses->execute();
$courses = $queryFetchCourses->fetchAll(PDO::FETCH_ASSOC);

// Organize courses by year and semester
$groupedCourses = [];
foreach ($courses as $course) {
    $year = $course['year'];
    $semester = $course['semester'];
    
    if (!isset($groupedCourses[$year])) {
        $groupedCourses[$year] = ['1st Semester' => [], '2nd Semester' => []];
    }
    $groupedCourses[$year][$semester][] = $course;
}

// Calculate GWA
$totalAllUnits = 0;
$totalAllGrades = 0;
$totalUnitsForGWA = 0;
$hasIncompleteGradesByYear = [
    'First Year' => false,
    'Second Year' => false,
    'Third Year' => false,
    'Fourth Year' => false
];
$requiredSemesters = ['1st Semester', '2nd Semester'];
$hasGrades = false;
$gwa = 'N/A'; // Default GWA to N/A
$hasSpecialGrades = false; // To check for DRP, NG, NA

$orderedYears = ['First Year', 'Second Year', 'Third Year', 'Fourth Year'];
foreach ($orderedYears as $year) {
    $yearHasGrades = false;
    foreach ($requiredSemesters as $semester) {
        if (isset($groupedCourses[$year][$semester]) && !empty($groupedCourses[$year][$semester])) {
            $semesterCourses = $groupedCourses[$year][$semester];
            foreach ($semesterCourses as $course) {
                $units = $course['units'];
                $grade = $course['grade'];

                if ($grade !== null && $grade !== '' && !in_array($grade, ['DRP', 'NA', 'NG'])) {
                    $yearHasGrades = true;
                    $hasGrades = true;
                    $totalAllUnits += $units;
                    $numericGrade = $grade === 'INC' ? 5.0 : (is_numeric($grade) ? floatval($grade) : 0);
                    $totalAllGrades += $numericGrade * $units;
                    $totalUnitsForGWA += $units;
                } elseif (in_array($grade, ['DRP', 'NA', 'NG'])) {
                    $hasSpecialGrades = true;
                }
            }
        }
    }
    if (!$yearHasGrades) {
        $hasIncompleteGradesByYear[$year] = true;
    }
}

$hasOverallIncompleteGrades = !$hasGrades || in_array(true, $hasIncompleteGradesByYear) || $hasSpecialGrades;
if (!$hasOverallIncompleteGrades && $totalUnitsForGWA > 0) {
    $gwa = round($totalAllGrades / $totalUnitsForGWA, 1);
}

// Create FPDF object
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Header
$pdf->Cell(0, 10, 'Self Evaluation Report', 0, 1, 'C');

// Student details table
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Name:', 1, 0, 'L');
$pdf->Cell(140, 10, $fullname, 1, 1, 'L');
$pdf->Cell(50, 10, 'Student ID#:', 1, 0, 'L');
$pdf->Cell(140, 10, $studentId, 1, 1, 'L');
$pdf->Cell(50, 10, 'Degree Program:', 1, 0, 'L');
$pdf->Cell(140, 10, $courseName . ' (' . $effectiveYear . ')', 1, 1, 'L');
$pdf->Cell(50, 10, 'GWA:', 1, 0, 'L');
$pdf->Cell(140, 10, $gwa, 1, 1, 'L');
$pdf->Ln(5);

// Iterate over each year and semester to generate tables
$pdf->SetFont('Arial', 'B', 14);
foreach ($orderedYears as $year) {
    if (!isset($groupedCourses[$year])) {
        continue;
    }
    $semesters = $groupedCourses[$year];
    $pdf->SetFont('Arial', 'B', 14); // Ensure bold font for year title
    $pdf->Cell(0, 3, $year, 0, 1, 'L');

    foreach ($semesters as $semesterName => $courses) {
        if (empty($courses)) {
            continue;
        }

        // Semester Title
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, $semesterName, 0, 1, 'L');

        // Table Header
        $pdf->SetFillColor(0, 128, 0); // Green background for header
        $pdf->SetTextColor(255, 255, 255); // White text for header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(28, 10, 'Course Code', 1, 0, 'C', true);
        $pdf->Cell(92, 10, 'Descriptive Title', 1, 0, 'C', true);
        $pdf->Cell(38, 10, 'Co-/Prerequisite', 1, 0, 'C', true);
        $pdf->Cell(15, 10, 'Units', 1, 0, 'C', true);
        $pdf->Cell(18, 10, 'Grades', 1, 1, 'C', true);

        // Reset colors for rows
        $pdf->SetTextColor(0, 0, 0);

        // Table Data
        $pdf->SetFont('Arial', '', 9);
        $totalUnits = 0;
        $totalGrade = 0;
        $totalCourses = 0;
        $hasSpecialGrades = false;

        foreach ($courses as $course) {
            $pdf->Cell(28, 10, $course['course_code'], 1);
            $pdf->Cell(92, 10, $course['descriptive_title'], 1);
            $pdf->Cell(38, 10, $course['co_prerequisite'], 1);
            $pdf->Cell(15, 10, $course['units'], 1);
            $pdf->Cell(18, 10, $course['grade'], 1);
            $pdf->Ln();

            // Calculate totals for the semester
            $totalUnits += $course['units'];
            if (in_array($course['grade'], ['DRP', 'NA', 'NG'])) {
                $hasSpecialGrades = true;
            } elseif ($course['grade'] === 'INC') {
                $totalGrade += 5.0 * $course['units'];
                $totalCourses += $course['units'];
            } elseif (is_numeric($course['grade'])) {
                $totalGrade += $course['grade'] * $course['units'];
                $totalCourses += $course['units'];
            }
        }

        // Calculate GPA
        $gpa = $hasSpecialGrades ? 'N/A' : ($totalCourses > 0 ? round($totalGrade / $totalCourses, 1) : 'N/A');

        // Add Total Units, Total Grade, and GPA aligned with respective columns
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(158, 10, '', 1, 0);
        $pdf->Cell(15, 10, $totalUnits, 1, 0, 'C');
        $pdf->Cell(18, 10, round($totalGrade, 1), 1, 1, 'C');
        $pdf->Cell(173, 10, '', 1, 0);
        $pdf->Cell(18, 10, 'GPA: ' . $gpa, 1, 1, 'C');

        // Add spacing after each semester
        $pdf->Ln(5);
    }
}

// Output PDF
$pdf->Output('D', 'Self Evaluation.pdf'); // Download the PDF
?>
