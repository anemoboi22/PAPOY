document.addEventListener('DOMContentLoaded', function() {
    const courseList = document.getElementById('course-list');
    const addCourseButton = document.getElementById('add-course');
    let courseCount = 0;

    function updateCourseNumbers() {
        const courseItems = courseList.querySelectorAll('.course-item');
        courseItems.forEach((item, index) => {
            const label = item.querySelector('.course-label');
            const input = item.querySelector('input');

            // Update the course number in the label
            label.textContent = `Course ${index + 1}`;
            input.name = `course${index + 1}`;
            input.id = `course${index + 1}`;
        });

        courseCount = courseItems.length; // Update courseCount to reflect the number of items
    }

    addCourseButton.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default button behavior

        const courseInput = document.getElementById('course');
        const courseName = courseInput.value;

        if (courseName) {
            courseCount++;
            const courseItem = document.createElement('div');
            courseItem.classList.add('course-item');
            courseItem.innerHTML = `
                <span class="course-label">Course ${courseCount}</span>
                <input type="text" value="${courseName}" name="course${courseCount}" id="course${courseCount}" class="course-input" required />
                <button type="button" class="remove-course"><i class="bi bi-trash"></i></button>
            `;
            
            courseList.appendChild(courseItem);
            courseInput.value = '';

            courseItem.querySelector('.remove-course').addEventListener('click', function() {
                courseList.removeChild(courseItem);
                updateCourseNumbers(); // Update course numbers after removing an item
            });

            updateCourseNumbers(); // Update course numbers after adding a new item
        }
    });
});
