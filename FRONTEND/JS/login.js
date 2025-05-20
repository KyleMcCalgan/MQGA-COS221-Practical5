document.addEventListener('DOMContentLoaded', function () {
    const apiUrl = '../../BACKEND/public/index.php';
    document.getElementById('login-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        document.querySelectorAll('.error-message').forEach(el => el.textContent= '');
        document.getElementById('form-message').textContent = '';

     
        let isValid= true;
        if (!emailRegex.test(email) || email.length > 100) {
            document.getElementById('email-error').textContent ='Enter a valid email address (max 100 characters)';
            isValid = false;
        }
        if (!passwordRegex.test(password)) {
            document.getElementById('password-error').textContent ='Password must be 8+ characters and include uppercase, number, and special character.';
            isValid= false;
        }
        if (!isValid) return;
        
        const requestPayload={
            type: 'Login',
            email,
            password
        };

        try {
            const response= await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestPayload)
            });

            if (!response.ok) {
                let errorData;
                try {
                    errorData= await response.json();
                } catch (e) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                const errorMessage= errorData && errorData.message ? errorData.message : `HTTP error ${response.status}`;
                throw new Error(errorMessage);
            }

            const result = await response.json();
            if (result.status === 'success' && result.data) {
                sessionStorage.setItem('api_key', result.data.api_key);
                // sessionStorage.setItem('user_type', result.data.user_type);
                sessionStorage.setItem('user_type','super');
                window.location.href = 'launch.php';
            } else document.getElementById('form-message').textContent= result.message;
            
        } catch (error) {
            document.getElementById('form-message').textContent=`Failed to login. ${error.message}`;
        }
    });
});