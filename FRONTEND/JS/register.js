document.getElementById('signup-form').addEventListener('submit', function (e) {
    e.preventDefault(); 

    const name = document.getElementById('name').value.trim();
    const surname = document.getElementById('surname').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const accountType = document.getElementById('type').value;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{9,20}$/;
    const nameRegex = /^[a-zA-Z]{1,60}$/;

    if (name === '' || name.length > 60) {
        alert('First name must be filled in and under 60 characters.');
        return;
    }

    if (surname === '' || surname.length > 60) {
        alert('Last name must be filled in and under 60 characters.');
        return;
    }

    if (!emailRegex.test(email) || email.length > 100) {
        alert('Enter a valid email address (max 100 characters).');
        return;
    }

    if (!passwordRegex.test(password)) {
        alert('Password must be 9-20 characters long, and include:\n- Uppercase\n- Lowercase\n- Digit\n- Symbol');
        return;
    }

    console.log("Form validated successfully!");

    var formData = {
        type: "Register",
        name: name,
        surname: surname,
        email: email,
        password: password,
        user_type: accountType 
    };

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            try {
                console.log("Raw response:", xhr.responseText);
                var response = JSON.parse(xhr.responseText);
                console.log(response.data);
        
                if (xhr.status === 200 && response.status === "success") {
                    alert("Registration successful! Your API key is " + response.data.apikey);
                    window.location.href = 'login.php';
                } else {
                    alert("Error: " + response.data);
                }
            } catch (err) {
                console.error("Error parsing the server response:", err);
                console.error("Raw server response:", xhr.responseText);
                alert("Unexpected error occurred. Please try again.");
            }
        }
    };

    xhr.send(JSON.stringify(formData));
});