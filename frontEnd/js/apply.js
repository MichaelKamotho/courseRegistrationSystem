$("#submitApplication").click(function() {
    let applicantId = 1;  // Assume this is retrieved from login session
    let selectedCourses = [1, 2];  // Assume these IDs are from user selection

    let applicationData = {
        applicant_id: applicantId,
        courses: selectedCourses
    };

    $.ajax({
        url: 'api/apply.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(applicationData),
        success: function(response) {
            alert(response.message);
        },
        error: function() {
            alert("Error in application submission.");
        }
    });
});
