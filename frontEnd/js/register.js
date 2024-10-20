$("#registerForm").submit(function(e) {
    e.preventDefault();  // Prevent default form submission

    let userData = {
        name: $("#name").val(),
        email: $("#email").val(),
        password: $("#password").val()
    };

    console.log(userData);  // Debug: Log the data being sent to the backend

    $.ajax({
        url: '../backEnd/api/register.php',  // Adjust the path if necessary
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(userData),  // Send data as JSON
        success: function(response) {
            console.log(response);  // Debug: Log response from the server
            alert(response.message);  // Display success message
        },
        error: function(xhr, status, error) {
            console.error("Error: " + status + " " + error);  // Log the error
            alert("Error occurred during registration.");
        }
    });
});
