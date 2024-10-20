$("#loginForm").submit(function(e) {
    e.preventDefault();  // Prevent the default form submission

    let loginData = {
        email: $("#email").val(),
        password: $("#password").val()
    };

    console.log(loginData);
    $.ajax({
        url: '../backEnd/api/login.php',  // Path to the backend login API
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(loginData),  // Send the data as JSON
        success: function(response) {
            let jsonResponse = JSON.parse(response);
            if (jsonResponse.message === "Login successful") {
                // Save user info (you could store this in localStorage or cookies)
                localStorage.setItem("applicant_id", jsonResponse.id);

                // Redirect to the dashboard
                window.location.href = "dashboard.html";
            } else {
                alert("Invalid login credentials.");
            }
        },
        error: function() {
            alert("An error occurred during login.");
        }
    });
});
