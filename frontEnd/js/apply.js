$(document).ready(function() {
    let currentPage = 1;
    const itemsPerPage = 5;
    let applicantId = 1; // Assume this is retrieved from the login session

    function loadCourses(page = 1) {
        $.ajax({
            url: '../backEnd/api/courses.php',
            method: 'GET',
            dataType: 'json',
            success: function(courses) {
                displayCourses(courses, page);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching courses:', error);
                $('#coursesList').append('<tr><td colspan="2">Failed to load courses. Please try again later.</td></tr>');
            }
        });
    }

    function displayCourses(courses, page) {
        $('#coursesList').empty(); // Clear the course list
        let searchTerm = $('#searchInput').val().toLowerCase();
        let filteredCourses = courses.filter(course => 
            course.name.toLowerCase().includes(searchTerm) ||
            course.description.toLowerCase().includes(searchTerm)
        );

        let start = (page - 1) * itemsPerPage;
        let end = start + itemsPerPage;
        let paginatedCourses = filteredCourses.slice(start, end);

        if (paginatedCourses.length > 0) {
            paginatedCourses.forEach(course => {
                const courseRow = `
                    <tr data-id="${course.id}">
                        <td>${course.name}</td>
                        <td><button class="selectCourse">Add to cart</button></td>
                    </tr>
                `;
                $('#coursesList').append(courseRow);
            });
        } else {
            $('#coursesList').append('<tr><td colspan="2">No courses available.</td></tr>');
        }

        $('#pageNumber').text(page);
    }

    $('#searchInput').on('input', function() {
        currentPage = 1; // Reset to first page on new search
        loadCourses(currentPage);
    });

    $('#prevPage').click(function() {
        if (currentPage > 1) {
            currentPage--;
            loadCourses(currentPage);
        }
    });

    $('#nextPage').click(function() {
        currentPage++;
        loadCourses(currentPage);
    });

    loadCourses(); // Initial load of courses

    // Handle course selection
    $(document).on('click', '.selectCourse', function() {
        const courseElement = $(this).closest('tr'); // Find the closest table row (tr)
        const courseId = courseElement.data('id');
        const courseName = courseElement.find('td:first').text(); // Get the course name from the first table cell (td)

        // Check if the course is already selected
        if ($('#selectedCourses .selectedCourse[data-id="' + courseId + '"]').length === 0) {
            if ($('#selectedCourses .selectedCourse').length >= 3) {
                alert('You can only apply for a maximum of 3 courses.');
                return;
            }
            $('#selectedCourses').append(`
                <div class="selectedCourse" data-id="${courseId}">
                    <span>${courseName}</span>
                    <button class="removeCourse">Remove</button>
                </div>
            `);
        } else {
            alert(`Course "${courseName}" is already selected.`);
        }
    });

    $(document).on('click', '.removeCourse', function() {
        $(this).closest('.selectedCourse').remove();
    });
    //Submit an application 
    $('#submitApplication').click(function() {
        const selectedCourses = [];
        $('#selectedCourses .selectedCourse').each(function() {
            selectedCourses.push($(this).data('id'));
        });
        if (selectedCourses.length === 0) {
            alert('Please select at least one course before submitting.');
            return;
        }
        const applicationData = {
            applicant_id: applicantId,
            courses: selectedCourses
        };
        $.ajax({
            url: '../backEnd/api/apply.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(applicationData),
            success: function(response) {
                alert(response.message);
                $('#selectedCourses').empty(); // Clear selected courses after submission
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                                  ? xhr.responseJSON.message
                                  : "Error in application submission. Please try again.";
                alert(errorMessage);
            }
        });
    });
    // View Applications
    $('#viewApplications').click(function() {
        $.ajax({
            url: '../backEnd/api/apply.php', // Use the correct endpoint for applications
            method: 'GET',
            data: { applicant_id: applicantId },
            dataType: 'json',
            success: function(applications) {
                $('#coursesList').empty(); // Clear the course list before showing applications
                $('#coursesList').append('<h3>Your Applications</h3>');

                if (Array.isArray(applications) && applications.length > 0) {
                    let tableContent = '<table><tr><th>Course Name</th></tr>';
                    applications.forEach(application => {
                        tableContent += `<tr><td>${application.name}</td></tr>`;
                    });
                    tableContent += '</table>';
                    $('#coursesList').append(tableContent);
                } else {
                    $('#coursesList').append('<p>No applications found.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching applications:', error);
                $('#coursesList').append('<p>Failed to load applications. Please try again later.</p>');
            }
        });
    });
});
