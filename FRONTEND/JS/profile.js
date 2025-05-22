document.addEventListener('DOMContentLoaded', function() {
    const apiUrl = '../../BACKEND/public/index.php';
    const apiKey = sessionStorage.getItem('api_key');
    const userType = sessionStorage.getItem('user_type');

    // Check if user is logged in
    if (!apiKey) {
        window.location.href = 'login.php';
        return;
    }

    // Store current user data
    let currentUserData = {};

    // DOM Elements
    const loadingProfile = document.getElementById('loading-profile');
    const profileMessage = document.getElementById('profile-message');
    const profileCard = document.getElementById('profile-card');
    
    const loadingReviews = document.getElementById('loading-reviews');
    const reviewsMessage = document.getElementById('reviews-message');
    const reviewsCard = document.getElementById('reviews-card');
    const reviewsContainer = document.getElementById('reviews-container');
    const noReviews = document.getElementById('no-reviews');
    const sortDropdown = document.getElementById('sort-dropdown');

    // Form elements
    const nameForm = document.getElementById('name-form');
    const emailForm = document.getElementById('email-form');
    const passwordForm = document.getElementById('password-form');

    // Utility Functions
    function showMessage(element, message, isError = false) {
        element.textContent = message;
        element.style.display = 'block';
        element.className = isError ? 'message error' : 'message success';
        element.style.backgroundColor = isError ? 'rgba(255, 0, 0, 0.2)' : 'rgba(0, 255, 0, 0.2)';
        element.style.color = isError ? '#d32f2f' : '#008000';
        element.style.padding = '10px';
        element.style.borderRadius = '5px';
        element.style.marginBottom = '20px';
        
        setTimeout(() => {
            element.style.display = 'none';
        }, 5000);
    }

    function hideMessage(element) {
        element.style.display = 'none';
    }

    async function makeApiRequest(payload) {
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}`);
            }

            const result = await response.json();
            
            if (result.status === 'success') {
                return result.data;
            } else {
                throw new Error(result.message || 'API request failed');
            }
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Load User Profile Data
    async function loadUserProfile() {
        try {
            const userData = await makeApiRequest({
                type: 'GetUsers',
                api_key: apiKey
            });

            if (userData && userData.length > 0) {
                currentUserData = userData[0];
                
                // Populate display fields
                document.getElementById('display-name').value = `${currentUserData.name} ${currentUserData.surname}`;
                document.getElementById('display-email').value = currentUserData.email;
                
                // Populate modal fields
                document.getElementById('modal-name').value = currentUserData.name;
                document.getElementById('modal-surname').value = currentUserData.surname;
                document.getElementById('modal-email').value = currentUserData.email;
                
                // Update display name in reviews section
                document.getElementById('user-display-name').textContent = `${currentUserData.name} ${currentUserData.surname}`;
                
                loadingProfile.style.display = 'none';
                profileCard.style.display = 'block';
            } else {
                throw new Error('No user data received');
            }
        } catch (error) {
            loadingProfile.style.display = 'none';
            showMessage(profileMessage, `Failed to load profile: ${error.message}`, true);
        }
    }

    // Update User Profile
    async function updateUserProfile(updateData) {
        try {
            const updatePayload = {
                type: 'UpdateUserInfo',
                api_key: apiKey,
                ...updateData
            };

            await makeApiRequest(updatePayload);
            showMessage(profileMessage, 'Profile updated successfully!');
            
            // Reload profile to get updated data
            await loadUserProfile();
            
        } catch (error) {
            showMessage(profileMessage, `Failed to update profile: ${error.message}`, true);
        }
    }

    // Load User Reviews
    async function loadUserReviews(sortBy = 'newest') {
        try {
            const reviewData = await makeApiRequest({
                type: 'GetUserReviewsRatings',
                api_key: apiKey,
                sort: sortBy
            });

            if (reviewData) {
                updateReviewStats(reviewData.stats);
                displayReviews(reviewData.reviews);
                loadingReviews.style.display = 'none';
                reviewsCard.style.display = 'block';
            }
        } catch (error) {
            loadingReviews.style.display = 'none';
            showMessage(reviewsMessage, `Failed to load reviews: ${error.message}`, true);
        }
    }

    // Update Review Statistics
    function updateReviewStats(stats) {
        document.getElementById('review-count').textContent = `${stats.number_of_reviews} Reviews`;
        document.getElementById('avg-rating').textContent = stats.average_rating || '-';
        document.getElementById('total-reviews').textContent = stats.number_of_reviews || '0';
        document.getElementById('total-ratings').textContent = stats.number_of_ratings || '0';
    }

    // Display Reviews
    function displayReviews(reviews) {
        reviewsContainer.innerHTML = '';
        
        if (!reviews || reviews.length === 0) {
            noReviews.style.display = 'block';
            return;
        }

        noReviews.style.display = 'none';
        
        reviews.forEach(review => {
            const reviewElement = createReviewElement(review);
            reviewsContainer.appendChild(reviewElement);
        });
    }

    // Create Review Element
    function createReviewElement(review) {
        const reviewDiv = document.createElement('div');
        reviewDiv.className = 'review-item';
        
        // Create star rating display
        let starRating = '';
        if (review.rating) {
            const rating = parseFloat(review.rating);
            const fullStars = Math.floor(rating);
            const emptyStars = 5 - fullStars;
            starRating = '★'.repeat(fullStars) + '☆'.repeat(emptyStars);
        } else {
            starRating = 'No rating given';
        }
        
        reviewDiv.innerHTML = `
            <div class="product-info">
                <div class="product-details">
                    <h4 class="review-title">${review.book_name || 'Unknown Book'}</h4>
                    <span class="product-price">by ${review.author || 'Unknown Author'}</span>
                </div>
            </div>
            <div class="star-rating">${starRating}</div>
            <div class="review-content">${review.review || 'No review text provided'}</div>
            <div class="review-meta">
                <div class="review-actions">
                    <a href="#" class="remove-review-link" data-review-id="${review.review_id}" data-book-name="${review.book_name}">Delete</a>
                </div>
            </div>
        `;
        
        return reviewDiv;
    }

    // Remove Review
    async function removeReview(reviewId, bookName) {
        if (!confirm(`Are you sure you want to remove your review for "${bookName}"?`)) {
            return;
        }

        try {
            await makeApiRequest({
                type: 'RemoveUserReview',
                apikey: apiKey,
                book_id: reviewId // This might need adjustment based on your API
            });
            
            showMessage(reviewsMessage, 'Review removed successfully!');
            loadUserReviews(sortDropdown.value); // Reload reviews
        } catch (error) {
            showMessage(reviewsMessage, `Failed to remove review: ${error.message}`, true);
        }
    }

    // Event Listeners for Modal Forms
    nameForm.addEventListener('submit', function(e) {
        e.preventDefault();
        hideMessage(profileMessage);
        
        const formData = new FormData(this);
        const updateData = {
            name: formData.get('name'),
            surname: formData.get('surname')
        };
        
        updateUserProfile(updateData);
        closeModal('nameModal');
    });

    emailForm.addEventListener('submit', function(e) {
        e.preventDefault();
        hideMessage(profileMessage);
        
        const formData = new FormData(this);
        const updateData = {
            email: formData.get('email')
        };
        
        updateUserProfile(updateData);
        closeModal('emailModal');
    });

    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        hideMessage(profileMessage);
        
        const formData = new FormData(this);
        const updateData = {
            password: formData.get('password'),
            old_password: formData.get('old_password')
        };
        
        updateUserProfile(updateData);
        closeModal('passwordModal');
        
        // Clear password fields
        document.getElementById('modal-old-password').value = '';
        document.getElementById('modal-new-password').value = '';
    });

    // Sort dropdown event listener
    sortDropdown.addEventListener('change', function() {
        loadUserReviews(this.value);
    });

    // Handle review removal clicks
    reviewsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-review-link')) {
            e.preventDefault();
            const reviewId = e.target.getAttribute('data-review-id');
            const bookName = e.target.getAttribute('data-book-name');
            
            removeReview(reviewId, bookName);
        }
    });

    // Initialize page
    function initializePage() {
        loadUserProfile();
        loadUserReviews();
    }

    // Start the page
    initializePage();
});