// Enhanced functionality for profile management
const apiUrl = '../../BACKEND/public/index.php';
let currentUserData = {};
let currentReviewData = {};
let editingReviewData = null;
let selectedRating = 0;

// Initialize profile on page load
document.addEventListener('DOMContentLoaded', function() {
    const apiKey = sessionStorage.getItem('api_key');
    if (!apiKey) {
        window.location.href = 'login.php';
        return;
    }
    
    initializeStarRating();
    loadUserProfile();
    loadUserReviews();
    setupEventDelegation();
});

// Set up event delegation for dynamically generated content
function setupEventDelegation() {
    // Event delegation for edit buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.edit-review-btn, .edit-review-btn *')) {
            const button = e.target.closest('.edit-review-btn');
            if (button) {
                const reviewIndex = parseInt(button.dataset.reviewIndex);
                const review = currentReviewData.reviews[reviewIndex];
                if (review) {
                    editReview(review.book_name, review.review, review.rating || 0);
                }
            }
        }
        
        // Event delegation for delete buttons
        if (e.target.matches('.delete-review-btn, .delete-review-btn *')) {
            const button = e.target.closest('.delete-review-btn');
            if (button) {
                const reviewIndex = parseInt(button.dataset.reviewIndex);
                const review = currentReviewData.reviews[reviewIndex];
                if (review) {
                    deleteReview(review.book_name);
                }
            }
        }
    });

    // Modal click outside to close
    document.addEventListener('click', function (event) {
        const modals = document.getElementsByClassName('modal');
        for (let modal of modals) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    });

    // Prevent closing when clicking inside modal content
    document.querySelectorAll('.modal-content').forEach(content => {
        content.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    });
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

function openNameModal() {
    const currentName = document.getElementById('display-name').textContent;
    const names = currentName.split(' ');
    document.getElementById('edit-name').value = names[0] || '';
    document.getElementById('edit-surname').value = names.slice(1).join(' ') || '';
    openModal('nameModal');
}

function openEmailModal() {
    document.getElementById('edit-email').value = document.getElementById('display-email').textContent;
    openModal('emailModal');
}

function openPasswordModal() {
    document.getElementById('old-password').value = '';
    document.getElementById('new-password').value = '';
    openModal('passwordModal');
}

// Star Rating System
function initializeStarRating() {
    const stars = document.querySelectorAll('.star-rating-input .star');
    const ratingText = document.getElementById('rating-text');
    
    stars.forEach((star, index) => {
        const rating = parseInt(star.dataset.rating);
        
        // Click handler
        star.addEventListener('click', function() {
            selectedRating = rating;
            updateStarDisplay(rating);
            updateRatingText(rating);
        });
        
        // Hover handlers
        star.addEventListener('mouseenter', function() {
            highlightStars(rating, true);
        });
        
        star.addEventListener('mouseleave', function() {
            highlightStars(selectedRating, false);
        });
    });
}

function updateStarDisplay(rating) {
    const stars = document.querySelectorAll('.star-rating-input .star');
    stars.forEach((star, index) => {
        const starRating = parseInt(star.dataset.rating);
        if (starRating <= rating) {
            star.classList.add('active');
            star.classList.remove('hovered');
        } else {
            star.classList.remove('active', 'hovered');
        }
    });
}

function highlightStars(rating, isHover) {
    const stars = document.querySelectorAll('.star-rating-input .star');
    stars.forEach((star) => {
        const starRating = parseInt(star.dataset.rating);
        if (isHover) {
            if (starRating <= rating) {
                star.classList.add('hovered');
            } else {
                star.classList.remove('hovered');
            }
        } else {
            star.classList.remove('hovered');
            if (starRating <= selectedRating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        }
    });
}

function updateRatingText(rating) {
    const ratingText = document.getElementById('rating-text');
    const ratingLabels = {
        1: '1 star - Poor',
        2: '2 stars - Fair', 
        3: '3 stars - Good',
        4: '4 stars - Very Good',
        5: '5 stars - Excellent'
    };
    
    if (rating > 0) {
        ratingText.textContent = ratingLabels[rating];
    } else {
        ratingText.textContent = 'No rating selected';
    }
}

function setInitialRating(rating) {
    selectedRating = rating;
    updateStarDisplay(rating);
    updateRatingText(rating);
}

// Load user profile information
async function loadUserProfile() {
    const apiKey = sessionStorage.getItem('api_key');
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'GetUsers',
                api_key: apiKey
            })
        });

        const result = await response.json();
        
        if (result.status === 'success' && result.data && result.data.length > 0) {
            currentUserData = result.data[0];
            displayUserProfile(currentUserData);
        } else {
            showMessage('Failed to load profile information: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        showMessage('Error loading profile: ' + error.message, 'error');
    }
}

// Display user profile information
function displayUserProfile(userData) {
    document.getElementById('display-name').textContent = `${userData.name} ${userData.surname}`;
    document.getElementById('display-email').textContent = userData.email;
    document.getElementById('user-name-display').textContent = `${userData.name} ${userData.surname}`;
}

// Load user reviews and ratings with book images
async function loadUserReviews() {
    const apiKey = sessionStorage.getItem('api_key');
    const sortBy = document.getElementById('sort-dropdown').value;
    
    // Show loading state
    document.getElementById('loading-reviews').style.display = 'block';
    document.getElementById('reviews-container').innerHTML = '';
    document.getElementById('no-reviews-message').style.display = 'none';
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'GetUserReviewsRatings',
                api_key: apiKey,
                sort: sortBy
            })
        });

        const result = await response.json();
        
        if (result.status === 'success') {
            currentReviewData = result.data;
            
            // Fetch book images for each review
            const reviewsWithImages = await fetchBookImagesForReviews(result.data.reviews);
            
            displayUserReviews(reviewsWithImages, result.data.stats);
        } else {
            showMessage('Failed to load reviews: ' + (result.message || 'Unknown error'), 'error');
            document.getElementById('no-reviews-message').style.display = 'block';
        }
    } catch (error) {
        showMessage('Error loading reviews: ' + error.message, 'error');
        document.getElementById('no-reviews-message').style.display = 'block';
    } finally {
        document.getElementById('loading-reviews').style.display = 'none';
    }
}

// Fetch book images for reviews using existing endpoints
async function fetchBookImagesForReviews(reviews) {
    const apiKey = sessionStorage.getItem('api_key');
    const reviewsWithImages = [];
    
    for (const review of reviews) {
        try {
            // Get all books and find the one that matches our review
            const bookResponse = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: 'GetAllProducts',
                    api_key: apiKey
                })
            });

            const bookResult = await bookResponse.json();
            
            if (bookResult.status === 'success' && bookResult.data && bookResult.data.length > 0) {
                // Find exact match or use first result
                const bookData = bookResult.data.find(book => book.title === review.book_name);
                
                if (bookData) {
                    reviewsWithImages.push({
                        ...review,
                        book_image: bookData.thumbnail || bookData.smallThumbnail || null,
                        book_id: bookData.id // Store the book ID for later use
                    });
                } else {
                    // If no exact match found, add review without image but log the issue
                    console.warn('No book found for review:', review.book_name);
                    reviewsWithImages.push({
                        ...review,
                        book_image: null,
                        book_id: null
                    });
                }
            } else {
                // If no books found, add review without image
                reviewsWithImages.push({
                    ...review,
                    book_image: null,
                    book_id: null
                });
            }
        } catch (error) {
            console.error('Error fetching book image for:', review.book_name, error);
            // Add review without image if fetch fails
            reviewsWithImages.push({
                ...review,
                book_image: null,
                book_id: null
            });
        }
    }
    
    return reviewsWithImages;
}

// Display user reviews with book images and proper event handling
function displayUserReviews(reviews, stats) {
    const container = document.getElementById('reviews-container');
    
    // Update stats
    document.getElementById('review-count').textContent = `${stats.number_of_reviews} Reviews`;
    document.getElementById('rating-count').textContent = `${stats.number_of_ratings} Ratings`;
    document.getElementById('average-rating').textContent = stats.average_rating || '0.0';
    document.getElementById('total-reviews').textContent = stats.number_of_reviews;
    document.getElementById('total-ratings').textContent = stats.number_of_ratings;
    
    if (!reviews || reviews.length === 0) {
        document.getElementById('no-reviews-message').style.display = 'block';
        return;
    }
    
    // Generate HTML for each review with proper data attributes
    container.innerHTML = reviews.map((review, index) => `
        <div class="review-item" data-review-id="${review.review_id}" data-review-index="${index}">
            <div class="product-info">
                <img src="${review.book_image || '../Images/notfound.png'}" 
                     alt="${escapeHtml(review.book_name)}" 
                     class="product-image"
                     style="width: 4rem; height: 5rem; object-fit: cover; border-radius: 0.25rem; border: 1px solid #eee; flex-shrink: 0;"
                     onerror="this.src='../Images/notfound.png'">
                <div class="product-details">
                    <h3>${escapeHtml(review.book_name)}</h3>
                    <p class="review-author">by ${escapeHtml(review.author || 'Unknown Author')}</p>
                    <p class="review-content">${escapeHtml(review.review)}</p>
                    ${review.rating ? `<div class="star-rating">${generateStars(parseFloat(review.rating))}</div>` : ''}
                </div>
            </div>
            <div class="review-meta">
                <div class="review-actions">
                    <button type="button" 
                            class="edit-review-btn" 
                            data-review-index="${index}">
                        Edit
                    </button>
                    <button type="button" 
                            class="delete-review-btn" 
                            data-review-index="${index}">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Generate star rating display
function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
    
    let stars = '';
    for (let i = 0; i < fullStars; i++) stars += '★';
    if (halfStar) stars += '☆';
    for (let i = 0; i < emptyStars; i++) stars += '☆';
    
    return stars;
}

// Enhanced edit review functionality
function editReview(bookName, reviewText, rating) {
    // Find the review data we need
    const reviewData = currentReviewData.reviews.find(r => r.book_name === bookName);
    if (!reviewData) {
        showMessage('Could not find review data', 'error');
        return;
    }
    
    editingReviewData = reviewData;
    
    // Set the review text
    document.getElementById('edit-review-text').value = reviewText;
    
    // Set the rating using our star system
    const currentRating = rating && rating > 0 ? Math.floor(parseFloat(rating)) : 0;
    setInitialRating(currentRating);
    
    // Clear any previous status messages
    document.getElementById('review-edit-status').innerHTML = '';
    
    openModal('reviewEditModal');
}

// Enhanced delete review functionality
async function deleteReview(bookName) {
    if (!confirm(`Are you sure you want to delete your review for "${bookName}"?`)) {
        return;
    }
    
    // Find the review data we need
    const reviewData = currentReviewData.reviews.find(r => r.book_name === bookName);
    if (!reviewData) {
        showMessage('Could not find review data for deletion', 'error');
        return;
    }
    
    const apiKey = sessionStorage.getItem('api_key');
    
    try {
        // Use the stored book_id if available, otherwise try to find it
        let bookId = reviewData.book_id;
        if (!bookId) {
            bookId = await findBookIdByName(bookName);
        }
        
        if (!bookId) {
            throw new Error('Could not find book ID for deletion');
        }
        
        console.log('Deleting review for book ID:', bookId);
        
        const payload = {
            type: 'RemoveUserReview',
            apikey: apiKey,  // Note: this endpoint uses 'apikey' not 'api_key'
            book_id: bookId
        };
        
        console.log('Sending delete payload:', payload);
        
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();
        console.log('Delete response:', result);
        
        if (result.status === 'success') {
            showMessage('Review deleted successfully!', 'success');
            loadUserReviews(); // Reload reviews
        } else {
            showMessage('Failed to delete review: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error deleting review:', error);
        showMessage('Error deleting review: ' + error.message, 'error');
    }
}

// Enhanced save review changes with new star rating system
async function saveReviewChanges() {
    const reviewText = document.getElementById('edit-review-text').value.trim();
    const rating = selectedRating > 0 ? selectedRating : null;
    
    if (!reviewText) {
        showModalStatus('Review text is required', 'error');
        return;
    }
    
    if (!editingReviewData) {
        showModalStatus('Error: No review data found', 'error');
        return;
    }
    
    const apiKey = sessionStorage.getItem('api_key');
    const saveButton = document.getElementById('save-review-btn');
    
    // Show loading state
    saveButton.disabled = true;
    saveButton.textContent = 'Saving...';
    showModalStatus('Updating review...', 'info');
    
    try {
        // Use the stored book_id if available, otherwise try to find it
        let bookId = editingReviewData.book_id;
        if (!bookId) {
            bookId = await findBookIdByName(editingReviewData.book_name);
        }
        
        if (!bookId) {
            throw new Error('Could not find book ID for review update');
        }
        
        console.log('Updating review for book ID:', bookId);
        console.log('Review text:', reviewText);
        console.log('New rating:', rating);
        console.log('Current rating:', editingReviewData.rating);
        
        let reviewUpdated = false;
        let ratingUpdated = false;
        
        // Update the review text if it changed
        if (reviewText !== editingReviewData.review) {
            const reviewPayload = {
                type: 'AddUserReview',
                apikey: apiKey,
                book_id: bookId,
                review: reviewText
            };
            
            console.log('Sending review payload:', reviewPayload);
            
            const reviewResponse = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(reviewPayload)
            });

            const reviewResponseText = await reviewResponse.text();
            console.log('Review raw response text:', reviewResponseText);

            let reviewResult;
            try {
                reviewResult = JSON.parse(reviewResponseText);
            } catch (parseError) {
                console.error('Review JSON parse error:', parseError);
                throw new Error('Server returned invalid response for review update');
            }
            
            console.log('Review update response:', reviewResult);
            
            if (reviewResult.status !== 'success') {
                throw new Error('Failed to update review: ' + (reviewResult.message || 'Unknown error'));
            }
            reviewUpdated = true;
        }
        
        // Update the rating if provided and different from current rating
        if (rating !== null) {
            const currentRating = editingReviewData.rating ? parseFloat(editingReviewData.rating) : null;
            
            console.log('Rating comparison:', {
                newRating: rating,
                currentRating: currentRating,
                shouldUpdate: rating !== currentRating
            });
            
            if (rating !== currentRating) {
                const ratingPayload = {
                    type: 'AddUserRating',
                    apikey: apiKey,
                    book_id: bookId,
                    rating: rating
                };
                
                console.log('Sending rating payload:', ratingPayload);
                
                const ratingResponse = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(ratingPayload)
                });

                console.log('Rating response status:', ratingResponse.status);
                
                const ratingResponseText = await ratingResponse.text();
                console.log('Rating raw response text:', ratingResponseText);

                let ratingResult;
                try {
                    ratingResult = JSON.parse(ratingResponseText);
                } catch (parseError) {
                    console.error('Rating JSON parse error:', parseError);
                    console.error('Raw rating response:', ratingResponseText);
                    throw new Error('Server returned invalid response for rating update. Check console for details.');
                }
                
                console.log('Rating update response:', ratingResult);
                
                if (ratingResult.status !== 'success') {
                    throw new Error('Failed to update rating: ' + (ratingResult.message || 'Unknown error'));
                }
                ratingUpdated = true;
            }
        }
        
        // Show appropriate success message
        let successMessage = 'Updated successfully!';
        if (reviewUpdated && ratingUpdated) {
            successMessage = 'Review and rating updated successfully!';
        } else if (reviewUpdated) {
            successMessage = 'Review updated successfully!';
        } else if (ratingUpdated) {
            successMessage = 'Rating updated successfully!';
        } else {
            successMessage = 'No changes were made.';
        }
        
        showModalStatus(successMessage, 'success');
        setTimeout(() => {
            closeModal('reviewEditModal');
            loadUserReviews(); // Reload reviews
            showMessage(successMessage, 'success');
        }, 1500);
        
    } catch (error) {
        console.error('Error updating review:', error);
        showModalStatus('Error: ' + error.message, 'error');
    } finally {
        saveButton.disabled = false;
        saveButton.textContent = 'Save';
    }
}

// Helper function to find book ID by name
async function findBookIdByName(bookName) {
    const apiKey = sessionStorage.getItem('api_key');
    
    try {
        // First try exact title search
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'GetAllProducts',
                api_key: apiKey
            })
        });

        const result = await response.json();
        
        if (result.status === 'success' && result.data && result.data.length > 0) {
            // Find exact match by comparing titles directly
            console.log('Searching for book:', bookName);
            console.log('Available books:', result.data.map(book => book.title));
            
            const exactMatch = result.data.find(book => {
                console.log('Comparing:', book.title, 'vs', bookName, 'Match:', book.title === bookName);
                return book.title === bookName;
            });
            
            if (exactMatch) {
                console.log('Found exact match:', exactMatch.id);
                return exactMatch.id;
            }
            
            // If no exact match, try a more lenient comparison
            const lenientMatch = result.data.find(book => {
                const normalizedBookTitle = book.title.replace(/['']/g, "'").replace(/[""]/g, '"');
                const normalizedSearchTitle = bookName.replace(/['']/g, "'").replace(/[""]/g, '"');
                return normalizedBookTitle === normalizedSearchTitle;
            });
            
            if (lenientMatch) {
                console.log('Found lenient match:', lenientMatch.id);
                return lenientMatch.id;
            }
            
            console.log('No matches found for:', bookName);
        }
    } catch (error) {
        console.error('Error finding book ID:', error);
    }
    
    return null;
}

// Save profile changes functions
async function saveNameChanges() {
    const name = document.getElementById('edit-name').value.trim();
    const surname = document.getElementById('edit-surname').value.trim();
    
    if (!name || !surname) {
        showMessage('Both name and surname are required', 'error');
        return;
    }
    
    await updateUserInfo({ name, surname });
    closeModal('nameModal');
}

async function saveEmailChanges() {
    const email = document.getElementById('edit-email').value.trim();
    
    if (!email || !isValidEmail(email)) {
        showMessage('Please enter a valid email address', 'error');
        return;
    }
    
    await updateUserInfo({ email });
    closeModal('emailModal');
}

async function savePasswordChanges() {
    const oldPassword = document.getElementById('old-password').value;
    const newPassword = document.getElementById('new-password').value;
    
    if (!oldPassword || !newPassword) {
        showMessage('Both current and new passwords are required', 'error');
        return;
    }
    
    if (newPassword.length < 8) {
        showMessage('New password must be at least 8 characters long', 'error');
        return;
    }
    
    await updateUserInfo({ password: newPassword, old_password: oldPassword });
    closeModal('passwordModal');
}

// Update user information
async function updateUserInfo(updateData) {
    const apiKey = sessionStorage.getItem('api_key');
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'UpdateUserInfo',
                api_key: apiKey,
                ...updateData
            })
        });

        const result = await response.json();
        
        if (result.status === 'success') {
            showMessage('Profile updated successfully!', 'success');
            loadUserProfile(); // Reload profile
        } else {
            showMessage('Failed to update profile: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        showMessage('Error updating profile: ' + error.message, 'error');
    }
}

// Helper functions
function showModalStatus(message, type) {
    const statusDiv = document.getElementById('review-edit-status');
    statusDiv.textContent = message;
    statusDiv.className = `modal-status ${type}`;
    statusDiv.style.display = 'block';
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showMessage(message, type) {
    const messageDiv = document.getElementById('profile-message');
    messageDiv.textContent = message;
    messageDiv.className = `profile-message ${type}`;
    messageDiv.style.display = 'block';
    
    // Hide message after 5 seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}