document.addEventListener('DOMContentLoaded', function() {  
    // Generate PDF button click event
    document.querySelector('#generate-report-btn').addEventListener('click', function() {
        window.open('functions/generate-self-evaluation.php', '_blank');
    });

    calculateAll();
    applyPrerequisiteRules(); // Apply rules on page load
});

document.querySelectorAll('.grade-select').forEach(function(select) {
    select.addEventListener('change', function() {
        const previousGrade = select.getAttribute('data-previous-grade');
        const newGrade = select.value;
        const courseTitle = select.closest('tr').querySelector('td:nth-child(1)').textContent.trim();

        // Handle change from passing to failing
        if (!['INC', 'DRP', 'NA', 'NG', '3.1', '3.2', '3.3', '3.4', '3.5'].includes(previousGrade) && ['INC', 'DRP', 'NA', 'NG', '3.1', '3.2', '3.3', '3.4', '3.5'].includes(newGrade)) {
            const affectedSubjects = [];

            document.querySelectorAll('.grade-select').forEach(function(otherSelect) {
                const otherRow = otherSelect.closest('tr');
                const prerequisites = otherRow.querySelector('td:nth-child(3)').textContent.split(/[,;]/).map(prereq => prereq.trim());

                if (prerequisites.includes(courseTitle)) {
                    affectedSubjects.push(otherRow.querySelector('td:nth-child(1)').textContent.trim());
                }
            });

            const affectedSubjectsText = affectedSubjects.length > 0 ? `This change will affect the following subjects: ${affectedSubjects.join(', ')}` : 'No linked prerequisites will be affected.';

            Swal.fire({
                title: 'Are you sure?',
                text: `You are changing the grade from ${previousGrade} to ${newGrade}. ${affectedSubjectsText}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    select.setAttribute('data-previous-grade', newGrade);
                    calculateAll();
                    applyPrerequisiteRules();
                } else {
                    select.value = previousGrade; // Revert to the previous grade
                }
            });
        } else {
            select.setAttribute('data-previous-grade', newGrade);
            calculateAll();
            applyPrerequisiteRules();
        }
    });
    // Initialize the data-previous-grade attribute with the current value
    select.setAttribute('data-previous-grade', select.value);
});

document.querySelector('#grades-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    document.querySelectorAll('.grade-select').forEach(function(select) {
        const courseId = select.closest('tr').querySelector('td:first-child').textContent.trim();
        formData.append('grades[' + courseId + ']', select.value);
    });

    fetch('./functions/update-grades.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('success')) {
            Swal.fire('Success', 'Grades updated successfully!', 'success');
            setTimeout(() => {
                location.reload(); // Reload the page to reflect changes
            }, 500);
        } else {
            Swal.fire('Error', 'Failed to update grades. Please try again.', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'An unexpected error occurred.', 'error');
        console.error('Error:', error);
    });
});

function calculateAll() {
    let totalAllUnits = 0;
    let totalAllGrades = 0;
    let totalUnitsForGWA = 0;
    let hasIncompleteGradesByYear = {
        'First Year': false,
        'Second Year': false,
        'Third Year': false,
        'Fourth Year': false
    };

    document.querySelectorAll('.tab-pane').forEach(function(yearTab) {
        let yearLabel = yearTab.querySelector('h5').textContent;
        let yearHasIncompleteGrades = false;
        let yearTotalUnits = 0;
        let yearTotalGrades = 0;
        let yearTotalUnitsForGWA = 0;

        yearTab.querySelectorAll('.semester-section').forEach(function(semesterSection) {
            var totalUnits = 0;
            var weightedGrade = 0;
            var yearSemester = semesterSection.querySelector('.total-units').getAttribute('data-year-semester');

            semesterSection.querySelectorAll('.grade-select').forEach(function(select) {
                var gradeValue = select.value.trim();
                var units = parseFloat(select.closest('tr').querySelector('td:nth-child(4)').textContent.trim());
                totalUnits += units;
                totalAllUnits += units;
                yearTotalUnits += units;

                if (['DRP', 'NA', 'NG', ''].includes(gradeValue)) {
                    yearHasIncompleteGrades = true;
                    hasIncompleteGradesByYear[yearLabel] = true;
                    return; // Skip the current course if grade is invalid or non-numeric
                }

                var grade = gradeValue === 'INC' ? 5.0 : parseFloat(gradeValue) || 0; // Treat INC as 5.0
                weightedGrade += grade * units;
                yearTotalGrades += grade * units;

                // Calculate GWA values
                yearTotalUnitsForGWA += units;
                totalUnitsForGWA += units;
                totalAllGrades += grade * units;
            });

            var gpa = totalUnits ? (weightedGrade / totalUnits) : 0;

            document.querySelector('.total-units[data-year-semester="' + yearSemester + '"]').textContent = totalUnits.toFixed(0);
            document.querySelector('.total-grade[data-year-semester="' + yearSemester + '"]').textContent = weightedGrade.toFixed(1);
            document.querySelector('.weighted-grade[data-year-semester="' + yearSemester + '"]').textContent = yearHasIncompleteGrades ? 'N/A' : gpa.toFixed(1);

            // Update hidden input fields with the calculated values
            document.querySelector('input.hidden-total-units[name="total_units[' + yearSemester + ']"]').value = totalUnits.toFixed(0);
            document.querySelector('input.hidden-total-grade[name="total_grades[' + yearSemester + ']"]').value = weightedGrade.toFixed(1);
            document.querySelector('input.hidden-weighted-grade[name="gpas[' + yearSemester + ']"]').value = yearHasIncompleteGrades ? 'N/A' : gpa.toFixed(1);
        });
    });

    // Determine if overall GWA should be N/A
    let hasOverallIncompleteGrades = Object.values(hasIncompleteGradesByYear).some(val => val);

    // Calculate Overall GWA
    let gwa = totalUnitsForGWA ? (totalAllGrades / totalUnitsForGWA) : 0;

    // Update GWA and Total Units in the DOM
    if (hasOverallIncompleteGrades) {
        document.getElementById('gwa').textContent = 'GWA: N/A';
        document.getElementById('gwa').setAttribute('title', 'GWA is not available because there are incomplete or missing grades in some year levels.');
    } else {
        document.getElementById('gwa').textContent = 'GWA: ' + gwa.toFixed(1);
        document.getElementById('gwa').removeAttribute('title');
    }

    document.getElementById('totalAllUnits').textContent = 'Total Units: ' + totalAllUnits;

    // Update hidden input fields
    document.getElementById('hiddenTotalAllUnits').value = totalAllUnits;
    document.getElementById('hiddenGwa').value = hasOverallIncompleteGrades ? 'N/A' : gwa.toFixed(1);
}

function applyPrerequisiteRules() {
    // First, remove all previous styles and reset attributes
    document.querySelectorAll('.grade-select').forEach(function(select) {
        const courseRow = select.closest('tr');
        courseRow.classList.remove('text-danger', 'fw-bold');
        select.disabled = false;
        // Remove any existing tooltip icons
        const existingIcon = courseRow.querySelector('.bi-info-circle');
        if (existingIcon) {
            existingIcon.remove();
        }
    });

    // Recursive function to mark dependent courses
    function markDependentCourses(courseTitle, isDirectPrereq = false) {
        document.querySelectorAll('.grade-select').forEach(function(select) {
            var linkedCourseRow = select.closest('tr');
            var linkedPrerequisiteText = linkedCourseRow.querySelector('td:nth-child(3)').textContent;

            // Split prerequisites by comma, semicolon, or similar delimiter
            var prerequisites = linkedPrerequisiteText.split(/[,;]/).map(function(prereq) {
                return prereq.trim();
            });

            if (prerequisites.some(function(prereq) {
                return prereq === courseTitle; // Exact match check
            })) {
                // Mark the dependent course row and disable input
                linkedCourseRow.classList.add('text-danger', 'fw-bold');
                select.disabled = true;

                if (isDirectPrereq) {
                    // Set tooltip indicating which prerequisite is causing the restriction
                    // Check if an icon already exists to avoid adding multiple icons
                    let existingIcon = linkedCourseRow.querySelector('.bi-info-circle');
                    if (!existingIcon) {
                        let tooltipText = `You cannot enroll in this course because you have a failed grade (such as INC, DRP, NG, NA, or a grade of 3.1 to 3.5) in the prerequisite: ${courseTitle}`;
                        let infoIcon = document.createElement('i');
                        infoIcon.className = 'bi bi-info-circle';
                        infoIcon.setAttribute('data-bs-toggle', 'tooltip');
                        infoIcon.setAttribute('title', tooltipText);

                        // Add a space before the icon to ensure it’s not stuck to the text
                        linkedCourseRow.querySelector('td:first-child').appendChild(document.createTextNode(' '));
                        linkedCourseRow.querySelector('td:first-child').appendChild(infoIcon);
                    }
                }

                // Get the title of the dependent course
                var dependentCourseTitle = linkedCourseRow.querySelector('td:nth-child(1)').textContent.trim();

                // Recursively check for courses that depend on this one, but they are not direct prerequisites
                markDependentCourses(dependentCourseTitle, false);
            }
        });
    }

    // Go through all selects and mark prerequisites for INC or 5 grades
    document.querySelectorAll('.grade-select').forEach(function(select) {
        var gradeValue = select.value.trim();
        var courseRow = select.closest('tr');

        if (['INC', 'DRP', 'NG', 'NA', '3.1', '3.2', '3.3', '3.4', '3.5'].includes(gradeValue)) {
            // Mark the current course as having an INC or failing grade
            courseRow.classList.add('text-danger', 'fw-bold');

            // Get the title of the current course
            var courseTitle = courseRow.querySelector('td:nth-child(1)').textContent.trim();

            // Recursively mark all dependent courses, starting with direct prerequisites
            markDependentCourses(courseTitle, true);
        }

        // if (['DRP', 'NG', 'NA', '3.1', '3.2', '3.3', '3.4', '3.5'].includes(gradeValue)) {
        //     // If grade is 3.1 mark the row in red but do not disable input 
        //     courseRow.classList.add('text-danger', 'fw-bold');
        // }
    });
}

// Remember to initialize tooltips after applying the rules
document.addEventListener('DOMContentLoaded', function() {
    applyPrerequisiteRules();
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function filterGrades(filter) {
    document.querySelectorAll('.course-row').forEach(function(row) {
        const grade = row.getAttribute('data-grade');
        let showRow = false;

        // Convert grade to a number for comparison where possible
        const numericGrade = parseFloat(grade);

        if (filter === 'all') {
            showRow = true;
        } else if (filter === 'honor-dq') {
            // Filter for Honor DQ (grades between 2.6 and 3.0)
            showRow = numericGrade >= 2.6 && numericGrade <= 3.0;
        } else if (filter === 'none-passing') {
            // Filter for None Passing: grades between 3.1 and 3.5 or special grades (INC, DRP, NA, NG)
            showRow = (numericGrade >= 3.1 && numericGrade <= 3.5) || ['INC', 'DRP', 'NA', 'NG'].includes(grade);
        } else if (filter === 'blank') {
            // Filter for Blank: rows with empty grades
            showRow = !grade || grade.trim() === '';
        }

        // Show or hide the row based on the filter
        row.style.display = showRow ? '' : 'none';
    });
}