document.addEventListener('DOMContentLoaded', function() {
    const apiKey = sessionStorage.getItem('api_key');
    const userType = sessionStorage.getItem('user_type');

    if (!apiKey || (userType !== 'admin' && userType !== 'super')) {
        window.location.href = 'login.php';
        return;
    }

    const addProductForm = document.getElementById('addProduct-form');
    const submitButton = document.getElementById('submit-button');
    const formMessage = document.getElementById('frmMsg');
    const apiUrl = '../../BACKEND/public/index.php';

    if (!formMessage.style.padding) {
        formMessage.style.padding = '10px';
        formMessage.style.marginTop = '20px';
        formMessage.style.borderRadius = '5px';
        formMessage.style.display = 'none';
    }

    function showLoading() {
        const existingOverlay = document.getElementById('loading-screen');
        if (existingOverlay) {
            existingOverlay.remove();
        }
        const loadingScreen = document.createElement('div');
        loadingScreen.id = 'loading-screen';
        loadingScreen.innerHTML = '<div>Processing your request...</div>';
        document.body.appendChild(loadingScreen);
    }

    function hideLoading() {
        const loadingScreen = document.getElementById('loading-screen');
        if (loadingScreen) {
            loadingScreen.remove();
        }
    }

    function showMessage(message, isError) {
        if (isError) {
            alert('Error: ' + message);
            formMessage.style.display = 'none';
        } else {
            formMessage.textContent = message;
            formMessage.style.display = 'block';
            formMessage.className = 'success';
            formMessage.style.backgroundColor = 'rgba(0, 255, 0, 0.2)';
            formMessage.style.color = '#008000';
        }
    }

    if (!addProductForm || !submitButton) {
        return;
    }

    submitButton.addEventListener('click', function(e) {
        e.preventDefault();
        formMessage.textContent = '';
        formMessage.style.display = 'none';

        const tempID = 'tmp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const title = document.getElementById('title').value.trim();
        const author = document.getElementById('author').value.trim();
        const publisher = document.getElementById('publisher').value.trim();
        const publishedDate = document.getElementById('publishedDate').value;
        const description = document.getElementById('description').value.trim();
        const pageCount = document.getElementById('pageCount').value ? parseInt(document.getElementById('pageCount').value) : null;
        const maturityRating = document.getElementById('maturityRating').value.trim();
        const language = document.getElementById('language').value.trim();
        const accessibleIn = document.getElementById('accessibleIn').value.trim();
        const ratingsCount = document.getElementById('ratingsCount').value ? parseInt(document.getElementById('ratingsCount').value) : 0;
        const isbn13 = document.getElementById('isbn13').value.trim();

        if (!title) {
            showMessage('Title is required', true);
            return;
        }

        if (publishedDate && !/^\d{4}-\d{2}-\d{2}$/.test(publishedDate)) {
            showMessage('Published Date must be in YYYY-MM-DD format', true);
            return;
        }

        if (isbn13 && isbn13.length !== 13) {
            showMessage('ISBN13 must be exactly 13 characters', true);
            return;
        }

        if (publisher && publisher.length > 255) {
            showMessage('Publisher name cannot exceed 255 characters', true);
            return;
        }

        if (author && author.length > 255) {
            showMessage('Author name cannot exceed 255 characters', true);
            return;
        }

        if (language && language.length > 50) {
            showMessage('Language cannot exceed 50 characters', true);
            return;
        }

        const validMaturityRatings = ['NOT_MATURE', 'MATURE', 'EVERYONE', 'TEEN', 'ADULT'];
        if (maturityRating && !validMaturityRatings.includes(maturityRating)) {
            showMessage('Maturity Rating must be one of: ' + validMaturityRatings.join(', '), true);
            return;
        }

        if (accessibleIn && accessibleIn.length > 100) {
            showMessage('Accessible In field cannot exceed 100 characters', true);
            return;
        }

        const requestPayload = {
            type: 'AddProduct',
            apikey: apiKey,
            tempID: tempID,
            title: title,
            author: author || null,
            publisher: publisher || null,
            publishedDate: publishedDate || null,
            description: description || null,
            pageCount: pageCount,
            maturityRating: maturityRating || null,
            language: language || null,
            accessibleIn: accessibleIn || null,
            ratingsCount: ratingsCount,
            isbn13: isbn13 || null,
            thumbnail: null,
            smallThumbnail: null
        };

        showLoading();
        submitForm(requestPayload);
    });

    async function submitForm(requestPayload) {
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestPayload)
            });

            const responseText = await response.text();

            if (responseText.includes('CONSTRAINT') && responseText.includes('failed')) {
                const constraintMatch = responseText.match(/CONSTRAINT [^`]*`([^`]+)` failed/);
                if (constraintMatch && constraintMatch[1]) {
                    const fieldName = constraintMatch[1];
                    showMessage(`Database constraint error: The value for "${fieldName}" is not valid according to database rules.`, true);
                    return;
                }
            }

            if (responseText.includes('mysqli_stmt->bind_param()')) {
                showMessage('Database binding error: Check that all field values are of the correct type.', true);
                return;
            }

            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                if (responseText.includes('Uncaught mysqli_sql_exception')) {
                    showMessage('Database error occurred. Please check your input values or contact support.', true);
                } else {
                    showMessage('The server returned an invalid response. This could indicate a PHP error on the server.', true);
                }
                return;
            }

            if (result.status === 'success') {
                const successMessage = result.data && result.data.message
                    ? result.data.message
                    : 'Book added successfully!';
                showMessage(successMessage, false);
                addProductForm.reset();
            } else {
                const errorMessage = result.message || 'Failed to add book. Please try again.';
                showMessage(errorMessage, true);
            }
        } catch (error) {
            showMessage('An error occurred: ' + error.message, true);
        } finally {
            hideLoading();
        }
    }
});