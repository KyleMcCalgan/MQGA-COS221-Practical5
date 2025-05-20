document.addEventListener('DOMContentLoaded', function () {
    const signupForm = document.querySelector('.signup-form');
    const apiUrl = '../../BACKEND/public/index.php';

    signupForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const surname = document.getElementById('surname').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        const nameRegex = /^[a-zA-Z]{1,60}$/;

        document.querySelectorAll('.errMsg').forEach(el => el.textContent= '');
        document.getElementById('frmMsg').textContent = '';

        let isValid = true;
        if (!nameRegex.test(name)) {
            document.getElementById('name-error').textContent = '1-60 characters, no numbers or special characters';
            isValid = false;
        }
        if (!nameRegex.test(surname)) {
            document.getElementById('surname-error').textContent = '1-60 characters, no numbers or special characters';
            isValid = false;
        }
        if (!emailRegex.test(email) || email.length > 100) {
            document.getElementById('email-error').textContent = 'Enter a valid email address(max 100 characters)';
            isValid = false;
        }
        if (!passwordRegex.test(password)) {
            document.getElementById('password-error').textContent =
                'Password must be 8+ characters and include uppercase, number, and special character.';
            isValid = false;
        }
        if (!isValid) return;
        
        const requestPayload = {
            type: 'Register',
            name, 
            surname, 
            email, 
            password
        };

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestPayload)
            });

            if (!response.ok) {
                let errorData;
                try {
                    errorData = await response.json();
                } catch (e) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                const errorMessage = errorData && errorData.message ? errorData.message : `HTTP error ${response.status}`;
                throw new Error(errorMessage);
            }

            const result = await response.json();
            if (result.status=== 'success' && result.data) {
                sessionStorage.setItem('api_key', result.data.api_key)
                window.location.href = 'launch.php';
            } else document.getElementById('frmMsg').textContent = result.message;
            
        } catch (error) {
            document.getElementById('frmMsg').textContent = `Failed to register. ${error.message}`;
        }
    });
});