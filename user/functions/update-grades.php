<?php
include('../../db/dbconnection.php');
session_start();

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
        $dbh->beginTransaction();

        // Prepare reusable statements
        $stmtCheckGrade = $dbh->prepare("SELECT id, grade FROM grades WHERE user_id = :user_id AND course_id = :course_id");
        $stmtUpdateGrade = $dbh->prepare("UPDATE grades SET grade = :grade, updated_at = NOW() WHERE id = :grade_id");
        $stmtInsertGrade = $dbh->prepare("INSERT INTO grades (user_id, course_id, grade, created_at) VALUES (:user_id, :course_id, :grade, NOW())");

        $stmtCheckCourseDetails = $dbh->prepare("SELECT year, semester FROM courses WHERE id = :course_id");

        $stmtCheckSummary = $dbh->prepare("SELECT id FROM grades_summary WHERE user_id = :user_id AND year = :year AND semester = :semester");
        $stmtUpdateSummary = $dbh->prepare("UPDATE grades_summary SET grade_id = :grade_id, total_units = :total_units, total_grade = :total_grade, gpa = :gpa, updated_at = NOW() WHERE id = :summary_id");
        $stmtInsertSummary = $dbh->prepare("INSERT INTO grades_summary (user_id, grade_id, year, semester, total_units, total_grade, gpa, created_at) VALUES (:user_id, :grade_id, :year, :semester, :total_units, :total_grade, :gpa, NOW())");

        foreach ($grades as $courseId => $newGrade) {
            $newGradeValue = ($newGrade === '') ? null : trim($newGrade);

            $stmtCheckGrade->execute([':user_id' => $userId, ':course_id' => $courseId]);
            $existingGrade = $stmtCheckGrade->fetch(PDO::FETCH_ASSOC);

            if ($existingGrade) {
                if ($existingGrade['grade'] !== $newGradeValue) {
                    $stmtUpdateGrade->execute([
                        ':grade_id' => $existingGrade['id'],
                        ':grade' => $newGradeValue,
                    ]);
                    $gradeId = $existingGrade['id'];
                } else {
                    $gradeId = $existingGrade['id'];
                }
            } elseif (!is_null($newGradeValue)) {
                $stmtInsertGrade->execute([
                    ':user_id' => $userId,
                    ':course_id' => $courseId,
                    ':grade' => $newGradeValue,
                ]);
                $gradeId = $dbh->lastInsertId();
            } else {
                continue;
            }

            $stmtCheckCourseDetails->execute([':course_id' => $courseId]);
            $course = $stmtCheckCourseDetails->fetch(PDO::FETCH_ASSOC);
            if ($course) {
                $yearSemesterKey = "{$course['year']}-{$course['semester']}";
                if (isset($totalUnits[$yearSemesterKey])) {
                    $units = $totalUnits[$yearSemesterKey] ?? 0;
                    $totalGrade = $totalGrades[$yearSemesterKey] ?? 0;
                    $gpa = $gpas[$yearSemesterKey] ?? 0;

                    $stmtCheckSummary->execute([
                        ':user_id' => $userId,
                        ':year' => $course['year'],
                        ':semester' => $course['semester']
                    ]);
                    $summaryId = $stmtCheckSummary->fetchColumn();

                    if ($summaryId) {
                        $stmtUpdateSummary->execute([
                            ':summary_id' => $summaryId,
                            ':grade_id' => $gradeId,
                            ':total_units' => $units,
                            ':total_grade' => $totalGrade,
                            ':gpa' => $gpa,
                        ]);
                    } else {
                        $stmtInsertSummary->execute([
                            ':user_id' => $userId,
                            ':grade_id' => $gradeId,
                            ':year' => $course['year'],
                            ':semester' => $course['semester'],
                            ':total_units' => $units,
                            ':total_grade' => $totalGrade,
                            ':gpa' => $gpa,
                        ]);
                        $summaryId = $dbh->lastInsertId();
                    }

                    $gradesSummaryIds[] = $summaryId;
                }
            }
        }

        // Update or insert into overall_summary
        $stmtCheckOverallSummary = $dbh->prepare("SELECT id FROM overall_summary WHERE user_id = :user_id");
        $stmtCheckOverallSummary->execute([':user_id' => $userId]);
        $overallSummaryId = $stmtCheckOverallSummary->fetchColumn();

        $gradesSummaryId = !empty($gradesSummaryIds) ? end($gradesSummaryIds) : null;

        if ($overallSummaryId) {
            $stmtUpdateOverallSummary = $dbh->prepare("UPDATE overall_summary SET total_units = :total_units, gwa = :gwa, grades_summary_id = :grades_summary_id, updated_at = NOW() WHERE id = :summary_id");
            $stmtUpdateOverallSummary->execute([
                ':summary_id' => $overallSummaryId,
                ':total_units' => $totalAllUnits,
                ':gwa' => $gwa,
                ':grades_summary_id' => $gradesSummaryId
            ]);
        } else {
            $stmtInsertOverallSummary = $dbh->prepare("INSERT INTO overall_summary (user_id, total_units, gwa, grades_summary_id, created_at) VALUES (:user_id, :total_units, :gwa, :grades_summary_id, NOW())");
            $stmtInsertOverallSummary->execute([
                ':user_id' => $userId,
                ':total_units' => $totalAllUnits,
                ':gwa' => $gwa,
                ':grades_summary_id' => $gradesSummaryId
            ]);
        }

        $dbh->commit();
        $_SESSION['success_message'] = 'Grades successfully updated!';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } catch (Exception $e) {
        $dbh->rollBack();
        error_log($e->getMessage());
        $_SESSION['error_message'] = 'An error occurred while saving the grades. Please try again later.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
