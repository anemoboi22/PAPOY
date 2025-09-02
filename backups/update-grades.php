<?php
include('../db/dbconnection.php');
session_start();

$userId = $_SESSION['userid'];

// Handle grades submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $dbh->beginTransaction();

        // Grades data
        $grades = $_POST['grades'] ?? [];
        $totalGrades = [];
        $weightedGrades = [];

        // Update or insert grades in the database
        $stmtCheckGrade = $dbh->prepare("SELECT id FROM grades WHERE user_id = :user_id AND course_id = :course_id");
        $stmtUpdateGrade = $dbh->prepare("UPDATE grades SET grade = :grade WHERE id = :id");
        $stmtInsertGrade = $dbh->prepare("INSERT INTO grades (user_id, course_id, grade) VALUES (:user_id, :course_id, :grade)");

        foreach ($grades as $courseId => $grade) {
            $grade = !empty($grade) ? floatval($grade) : null; // Convert to float or null if empty

            $stmtCheckGrade->execute([':user_id' => $userId, ':course_id' => $courseId]);
            $existingGrade = $stmtCheckGrade->fetch(PDO::FETCH_ASSOC);

            if ($existingGrade) {
                // Update existing grade
                $stmtUpdateGrade->execute([':grade' => $grade, ':id' => $existingGrade['id']]);
            } else {
                // Insert new grade
                $stmtInsertGrade->execute([':user_id' => $userId, ':course_id' => $courseId, ':grade' => $grade]);
            }
        }

        // Recalculate total and weighted grades based on updated course grades
        $stmtFetchCourses = $dbh->prepare("SELECT c.year, c.semester, c.units, g.grade FROM courses c LEFT JOIN grades g ON c.id = g.course_id WHERE g.user_id = :user_id");
        $stmtFetchCourses->execute([':user_id' => $userId]);
        $courseDetails = $stmtFetchCourses->fetchAll(PDO::FETCH_ASSOC);

        foreach ($courseDetails as $courseDetail) {
            $year = $courseDetail['year'];
            $semester = $courseDetail['semester'];
            $units = $courseDetail['units'];
            $grade = $courseDetail['grade'] ?? 0;

            if (!isset($totalGrades[$year][$semester])) {
                $totalGrades[$year][$semester] = 0;
                $weightedGrades[$year][$semester] = 0;
            }

            $totalGrades[$year][$semester] += $grade;
            $weightedGrades[$year][$semester] += $grade * $units;
        }

        // Calculate the final weighted grade (GPA)
        foreach ($weightedGrades as $year => $semesters) {
            foreach ($semesters as $semester => $weightedGrade) {
                $units = array_sum(array_column(array_filter($courseDetails, function ($course) use ($year, $semester) {
                    return $course['year'] === $year && $course['semester'] === $semester;
                }), 'units'));

                if ($units > 0) {
                    $weightedGrades[$year][$semester] = $weightedGrade / $units;
                }
            }
        }

        // Update or insert semester grades in the database
        $stmtCheckSemester = $dbh->prepare("SELECT id FROM semester_grades WHERE user_id = :user_id AND year = :year AND semester = :semester");
        $stmtUpdateSemester = $dbh->prepare("UPDATE semester_grades SET total_grade = :total_grade, weighted_grade = :weighted_grade WHERE id = :id");
        $stmtInsertSemester = $dbh->prepare("INSERT INTO semester_grades (user_id, year, semester, total_grade, weighted_grade) VALUES (:user_id, :year, :semester, :total_grade, :weighted_grade)");

        foreach ($totalGrades as $year => $semesters) {
            foreach ($semesters as $semester => $totalGrade) {
                $weightedGrade = $weightedGrades[$year][$semester] ?? 0;

                // Only proceed if the total grade is greater than zero
                if ($totalGrade > 0) {
                    $stmtCheckSemester->execute([':user_id' => $userId, ':year' => $year, ':semester' => $semester]);
                    $existingSemesterGrade = $stmtCheckSemester->fetch(PDO::FETCH_ASSOC);

                    if ($existingSemesterGrade) {
                        // Update existing semester grade
                        $stmtUpdateSemester->execute([
                            ':total_grade' => $totalGrade,
                            ':weighted_grade' => $weightedGrade,
                            ':id' => $existingSemesterGrade['id']
                        ]);
                    } else {
                        // Insert new semester grade
                        $stmtInsertSemester->execute([
                            ':user_id' => $userId,
                            ':year' => $year,
                            ':semester' => $semester,
                            ':total_grade' => $totalGrade,
                            ':weighted_grade' => $weightedGrade
                        ]);
                    }
                }
            }
        }

        $dbh->commit();

        // Redirect back to the evaluation page after saving
        header("Location: view-subEvaluation.php");
        exit();
    } catch (Exception $e) {
        $dbh->rollBack();
        error_log("Failed to save grades: " . $e->getMessage());
        echo "An error occurred while saving grades. Please try again later.";
    }
}
?>
