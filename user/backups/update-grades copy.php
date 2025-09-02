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
    $totalAllUnits = $_POST['total_all_units'] ?? 0;
    $gwa = $_POST['gwa'] ?? 0.00;
    $gradesSummaryIds = [];

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
                $gradeId = $dbh->lastInsertId(); // Get the newly inserted grade ID
            }

            // Proceed with grades_summary only if $gradeId is valid
            if ($gradeId) {
                // Retrieve the corresponding grade_id
                foreach ($totalUnits as $yearSemester => $units) {
                    list($year, $semester) = explode('-', $yearSemester);
                    $totalGrade = $totalGrades[$yearSemester] ?? 0;
                    $gpa = $gpas[$yearSemester] ?? 0;

                    // Check if there are grades inputted for this year-semester
                    $gradesForYearSemester = array_filter($grades, function ($courseGrade, $courseKey) use ($year, $semester, $dbh) {
                        $stmtCourse = $dbh->prepare("SELECT year, semester FROM courses WHERE id = :course_id");
                        $stmtCourse->execute([':course_id' => $courseKey]);
                        $courseDetails = $stmtCourse->fetch(PDO::FETCH_ASSOC);
                        return !empty(trim($courseGrade)) && $courseDetails['year'] == $year && $courseDetails['semester'] == $semester;
                    }, ARRAY_FILTER_USE_BOTH);

                    if (count($gradesForYearSemester) > 0) {
                        // Proceed if there are grades inputted for the year-semester
                        // Check if the record already exists for the year and semester in grades_summary
                        $stmtCheckRecord = $dbh->prepare("SELECT id FROM grades_summary WHERE user_id = :user_id AND year = :year AND semester = :semester");
                        $stmtCheckRecord->execute([':user_id' => $userId, ':year' => $year, ':semester' => $semester]);
                        $summaryId = $stmtCheckRecord->fetchColumn();

                        if ($summaryId) {
                            // Update existing record
                            $stmtUpdateSummary = $dbh->prepare("UPDATE grades_summary SET grade_id = :grade_id, total_units = :total_units, total_grade = :total_grade, gpa = :gpa, updated_at = NOW() WHERE id = :summary_id");
                            $stmtUpdateSummary->execute([
                                ':summary_id' => $summaryId,
                                ':grade_id' => $gradeId,
                                ':total_units' => $units,
                                ':total_grade' => $totalGrade,
                                ':gpa' => $gpa
                            ]);
                        } else {
                            // Insert new record
                            $stmtInsertSummary = $dbh->prepare("INSERT INTO grades_summary (user_id, grade_id, year, semester, total_units, total_grade, gpa, created_at) VALUES (:user_id, :grade_id, :year, :semester, :total_units, :total_grade, :gpa, NOW())");
                            $stmtInsertSummary->execute([
                                ':user_id' => $userId,
                                ':grade_id' => $gradeId,
                                ':year' => $year,
                                ':semester' => $semester,
                                ':total_units' => $units,
                                ':total_grade' => $totalGrade,
                                ':gpa' => $gpa
                            ]);
                            $summaryId = $dbh->lastInsertId();
                        }

                        // Store summary ID for later use in overall_summary
                        $gradesSummaryIds[] = $summaryId;
                    }
                }
            } else {
                error_log("Grade ID not found or failed to insert for user_id: $userId and course_id: $courseId");
            }
        }

        // Update or insert total units and GWA in another table (e.g., overall_summary)
        $stmtCheckOverallSummary = $dbh->prepare("SELECT id FROM overall_summary WHERE user_id = :user_id");
        $stmtCheckOverallSummary->execute([':user_id' => $userId]);
        $overallSummaryId = $stmtCheckOverallSummary->fetchColumn();

        $gradesSummaryId = !empty($gradesSummaryIds) ? end($gradesSummaryIds) : null;

        if ($overallSummaryId) {
            // Update existing overall summary
            $stmtUpdateOverallSummary = $dbh->prepare("UPDATE overall_summary SET total_units = :total_units, gwa = :gwa, grades_summary_id = :grades_summary_id, updated_at = NOW() WHERE id = :summary_id");
            $stmtUpdateOverallSummary->execute([
                ':summary_id' => $overallSummaryId,
                ':total_units' => $totalAllUnits,
                ':gwa' => $gwa,
                ':grades_summary_id' => $gradesSummaryId
            ]);
        } else {
            // Insert new overall summary
            $stmtInsertOverallSummary = $dbh->prepare("INSERT INTO overall_summary (user_id, total_units, gwa, grades_summary_id, created_at) VALUES (:user_id, :total_units, :gwa, :grades_summary_id, NOW())");
            $stmtInsertOverallSummary->execute([
                ':user_id' => $userId,
                ':total_units' => $totalAllUnits,
                ':gwa' => $gwa,
                ':grades_summary_id' => $gradesSummaryId
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Grades updated successfully!']);
    } catch (Exception $e) {
        error_log("Error in update-grades.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>
