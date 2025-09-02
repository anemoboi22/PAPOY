<?php
include('../../db/dbconnection.php');
session_start();

header('Content-Type: application/json'); // Set JSON header

ini_set('display_errors', 0); // Disable HTML error display
ini_set('log_errors', 1);     // Enable error logging

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['userid'];
    $grades = $_POST['grades'] ?? [];
    $totalUnits = $_POST['total_units'] ?? [];
    $totalGrades = $_POST['total_grades'] ?? [];
    $gpas = $_POST['gpas'] ?? [];

    try {
        foreach ($grades as $courseId => $grade) {
            // Normalize grade value
            $grade = trim($grade);
            if ($grade === 'INC') {
                $gradeValue = 'INC'; // Save as INC
            } elseif (is_numeric($grade) && $grade >= 0 && $grade <= 5) {
                $gradeValue = floatval($grade);
            } else {
                // Invalid grade value, skip update
                continue;
            }

            // Check if the grade already exists for the course
            $stmtCheckGrade = $dbh->prepare("SELECT id FROM grades WHERE user_id = :user_id AND course_id = :course_id");
            $stmtCheckGrade->execute([':user_id' => $userId, ':course_id' => $courseId]);
            $gradeId = $stmtCheckGrade->fetchColumn();

            if ($gradeId) {
                // Update existing grade
                $stmtUpdateGrade = $dbh->prepare("UPDATE grades SET grade = :grade, updated_at = NOW() WHERE id = :grade_id");
                $stmtUpdateGrade->execute([':grade_id' => $gradeId, ':grade' => $gradeValue]);
            } else {
                // Insert new grade
                $stmtInsertGrade = $dbh->prepare("INSERT INTO grades (user_id, course_id, grade, created_at) VALUES (:user_id, :course_id, :grade, NOW())");
                $stmtInsertGrade->execute([':user_id' => $userId, ':course_id' => $courseId, ':grade' => $gradeValue]);
            }
        }

        // Save total units, total grade, and GPA for each year and semester
        foreach ($totalUnits as $yearSemester => $units) {
            list($year, $semester) = explode('-', $yearSemester);
            $totalGrade = $totalGrades[$yearSemester] ?? 0;
            $gpa = $gpas[$yearSemester] ?? 0;

            // Check if the record already exists for the year and semester
            $stmtCheckRecord = $dbh->prepare("SELECT id FROM grades_summary WHERE user_id = :user_id AND year = :year AND semester = :semester");
            $stmtCheckRecord->execute([':user_id' => $userId, ':year' => $year, ':semester' => $semester]);
            $summaryId = $stmtCheckRecord->fetchColumn();

            if ($summaryId) {
                // Update existing record
                $stmtUpdateSummary = $dbh->prepare("UPDATE grades_summary SET total_units = :total_units, total_grade = :total_grade, gpa = :gpa, updated_at = NOW() WHERE id = :summary_id");
                $stmtUpdateSummary->execute([
                    ':summary_id' => $summaryId,
                    ':total_units' => $units,
                    ':total_grade' => $totalGrade,
                    ':gpa' => $gpa
                ]);
            } else {
                // Insert new record
                $stmtInsertSummary = $dbh->prepare("INSERT INTO grades_summary (user_id, year, semester, total_units, total_grade, gpa, created_at) VALUES (:user_id, :year, :semester, :total_units, :total_grade, :gpa, NOW())");
                $stmtInsertSummary->execute([
                    ':user_id' => $userId,
                    ':year' => $year,
                    ':semester' => $semester,
                    ':total_units' => $units,
                    ':total_grade' => $totalGrade,
                    ':gpa' => $gpa
                ]);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Grades updated successfully!']);
    } catch (Exception $e) {
        error_log("Error in update-grades.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
} ?>
