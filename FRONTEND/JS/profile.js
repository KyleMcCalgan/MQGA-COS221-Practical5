const apiUrl = '../../BACKEND/public/index.php';
let currentUserData = {};
let currentReviewData = {};
let editingReviewData = null;
let selectedRating = 0;

document.addEventListener('DOMContentLoaded', function() {
    const apiKey = sessionStorage.getItem('api_key');
    if (!apiKey) {
        window.location.href = 'login.php';
        return;
    }
    
    const userType = sessionStorage.getItem('user_type');
    
    if (userType === 'regular') {
        initializeStarRating();
        loadUserReviews();
    }
    
    loadUserProfile();
    setupEventDelegation();
});

function setupEventDelegation() {
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

    document.addEventListener('click', function (event) {
        const modals = document.getElementsByClassName('modal');
        for (let modal of modals) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    });

    document.querySelectorAll('.modal-content').forEach(content => {
        content.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    });
}

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

function initializeStarRating() {
    const stars = document.querySelectorAll('.star-rating-input .star');
    const ratingText = document.getElementById('rating-text');
    
    if (!stars.length) return;
    
    stars.forEach((star, index) => {
        const rating = parseInt(star.dataset.rating);
        
        star.addEventListener('click', function() {
            selectedRating = rating;
            updateStarDisplay(rating);
            updateRatingText(rating);
        });
        
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
    if (!ratingText) return;
    
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

function displayUserProfile(userData) {
    document.getElementById('display-name').textContent = `${userData.name} ${userData.surname}`;
    document.getElementById('display-email').textContent = userData.email;
    
    const userNameDisplay = document.getElementById('user-name-display');
    if (userNameDisplay) {
        userNameDisplay.textContent = `${userData.name} ${userData.surname}`;
    }
}

async function loadUserReviews() {
    const userType = sessionStorage.getItem('user_type');
    if (userType !== 'regular') {
        return;
    }
    
    const apiKey = sessionStorage.getItem('api_key');
    const sortDropdown = document.getElementById('sort-dropdown');
    
    const sortBy = sortDropdown ? sortDropdown.value : 'newest';
    
    const loadingElement = document.getElementById('loading-reviews');
    const reviewsContainer = document.getElementById('reviews-container');
    const noReviewsMessage = document.getElementById('no-reviews-message');
    
    if (!loadingElement || !reviewsContainer || !noReviewsMessage) {
        return;
    }
    
    if (loadingElement) loadingElement.style.display = 'block';
    if (reviewsContainer) reviewsContainer.innerHTML = '';
    if (noReviewsMessage) noReviewsMessage.style.display = 'none';
    
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
            
            const reviewsWithImages = await fetchBookImagesForReviews(result.data.reviews);
            
            displayUserReviews(reviewsWithImages, result.data.stats);
        } else {
            showMessage('Failed to load reviews: ' + (result.message || 'Unknown error'), 'error');
            if (noReviewsMessage) noReviewsMessage.style.display = 'block';
        }
    } catch (error) {
        showMessage('Error loading reviews: ' + error.message, 'error');
        if (noReviewsMessage) noReviewsMessage.style.display = 'block';
    } finally {
        if (loadingElement) loadingElement.style.display = 'none';
    }
}

async function fetchBookImagesForReviews(reviews) {
    const apiKey = sessionStorage.getItem('api_key');
    const reviewsWithImages = [];
    
    for (const review of reviews) {
        try {
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
                const bookData = bookResult.data.find(book => book.title === review.book_name);
                
                if (bookData) {
                    reviewsWithImages.push({
                        ...review,
                        book_image: bookData.thumbnail || bookData.smallThumbnail || null,
                        book_id: bookData.id
                    });
                } else {
                    reviewsWithImages.push({
                        ...review,
                        book_image: null,
                        book_id: null
                    });
                }
            } else {
                reviewsWithImages.push({
                    ...review,
                    book_image: null,
                    book_id: null
                });
            }
        } catch (error) {
            reviewsWithImages.push({
                ...review,
                book_image: null,
                book_id: null
            });
        }
    }
    
    return reviewsWithImages;
}

function displayUserReviews(reviews, stats) {
    const container = document.getElementById('reviews-container');
    
    const reviewCount = document.getElementById('review-count');
    const ratingCount = document.getElementById('rating-count');
    const averageRating = document.getElementById('average-rating');
    const totalReviews = document.getElementById('total-reviews');
    const totalRatings = document.getElementById('total-ratings');
    
    if (reviewCount) reviewCount.textContent = `${stats.number_of_reviews} Reviews`;
    if (ratingCount) ratingCount.textContent = `${stats.number_of_ratings} Ratings`;
    if (averageRating) averageRating.textContent = stats.average_rating || '0.0';
    if (totalReviews) totalReviews.textContent = stats.number_of_reviews;
    if (totalRatings) totalRatings.textContent = stats.number_of_ratings;
    
    if (!container) return;
    
    if (!reviews || reviews.length === 0) {
        const noReviewsMessage = document.getElementById('no-reviews-message');
        if (noReviewsMessage) noReviewsMessage.style.display = 'block';
        return;
    }
    
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

function editReview(bookName, reviewText, rating) {
    const reviewData = currentReviewData.reviews.find(r => r.book_name === bookName);
    if (!reviewData) {
        showMessage('Could not find review data', 'error');
        return;
    }
    
    editingReviewData = reviewData;
    
    const editReviewText = document.getElementById('edit-review-text');
    if (editReviewText) editReviewText.value = reviewText;
    
    const currentRating = rating && rating > 0 ? Math.floor(parseFloat(rating)) : 0;
    setInitialRating(currentRating);
    
    const editStatus = document.getElementById('review-edit-status');
    if (editStatus) editStatus.innerHTML = '';
    
    openModal('reviewEditModal');
}

async function deleteReview(bookName) {
    if (!confirm(`Are you sure you want to delete your review for "${bookName}"?`)) {
        return;
    }
    
    const reviewData = currentReviewData.reviews.find(r => r.book_name === bookName);
    if (!reviewData) {
        showMessage('Could not find review data for deletion', 'error');
        return;
    }
    
    const apiKey = sessionStorage.getItem('api_key');
    
    try {
        let bookId = reviewData.book_id;
        if (!bookId) {
            bookId = await findBookIdByName(bookName);
        }
        
        if (!bookId) {
            throw new Error('Could not find book ID for deletion');
        }
        
        
        const payload = {
            type: 'RemoveUserReview',
            apikey: apiKey,
            book_id: bookId
        };
        
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
            loadUserReviews();
        } else {
            showMessage('Failed to delete review: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error deleting review:', error);
        showMessage('Error deleting review: ' + error.message, 'error');
    }
}

async function saveReviewChanges() {
    const editReviewText = document.getElementById('edit-review-text');
    const reviewText = editReviewText ? editReviewText.value.trim() : '';
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
    
    if (saveButton) {
        saveButton.disabled = true;
        saveButton.textContent = 'Saving...';
    }
    showModalStatus('Updating review...', 'info');
    
    try {
        let bookId = editingReviewData.book_id;
        if (!bookId) {
            bookId = await findBookIdByName(editingReviewData.book_name);
        }
        
        if (!bookId) {
            throw new Error('Could not find book ID for review update');
        }
        
        let reviewUpdated = false;
        let ratingUpdated = false;
        
        if (reviewText !== editingReviewData.review) {
            const reviewPayload = {
                type: 'AddUserReview',
                apikey: apiKey,
                book_id: bookId,
                review: reviewText
            };
            
            const reviewResponse = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(reviewPayload)
            });

            const reviewResponseText = await reviewResponse.text();

            let reviewResult;
            try {
                reviewResult = JSON.parse(reviewResponseText);
            } catch (parseError) {
                console.error('Review JSON parse error:', parseError);
                throw new Error('Server returned invalid response for review update');
            }
            
            if (reviewResult.status !== 'success') {
                throw new Error('Failed to update review: ' + (reviewResult.message || 'Unknown error'));
            }
            reviewUpdated = true;
        }
        
        if (rating !== null) {
            const currentRating = editingReviewData.rating ? parseFloat(editingReviewData.rating) : null;
            
            // console.log('Rating comparison:', {
            //     newRating: rating,
            //     currentRating: currentRating,
            //     shouldUpdate: rating !== currentRating
            // });
            
            if (rating !== currentRating) {
                const ratingPayload = {
                    type: 'AddUserRating',
                    apikey: apiKey,
                    book_id: bookId,
                    rating: rating
                };
                
                const ratingResponse = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(ratingPayload)
                });

                
                const ratingResponseText = await ratingResponse.text();

                let ratingResult;
                try {
                    ratingResult = JSON.parse(ratingResponseText);
                } catch (parseError) {
                    // throw new Error('Server returned invalid response for rating update. Check console for details.');
                }
                
                
                if (ratingResult.status !== 'success') {
                    throw new Error('Failed to update rating: ' + (ratingResult.message || 'Unknown error'));
                }
                ratingUpdated = true;
            }
        }
        
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
            loadUserReviews();
            showMessage(successMessage, 'success');
        }, 1500);
        
    } catch (error) {
        showModalStatus('Error: ' + error.message, 'error');
    } finally {
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.textContent = 'Save';
        }
    }
}

async function findBookIdByName(bookName) {
    const apiKey = sessionStorage.getItem('api_key');
    
    try {
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
          
            const exactMatch = result.data.find(book => {
                return book.title === bookName;
            });
            
            if (exactMatch) {
                return exactMatch.id;
            }
            
            const lenientMatch = result.data.find(book => {
                const normalizedBookTitle = book.title.replace(/['']/g, "'").replace(/[""]/g, '"');
                const normalizedSearchTitle = bookName.replace(/['']/g, "'").replace(/[""]/g, '"');
                return normalizedBookTitle === normalizedSearchTitle;
            });
            
            if (lenientMatch) {
                return lenientMatch.id;
            }
            
        }
    } catch (error) {
    }
    
    return null;
}

async function saveNameChanges() {
    const editName = document.getElementById('edit-name');
    const editSurname = document.getElementById('edit-surname');
    const name = editName ? editName.value.trim() : '';
    const surname = editSurname ? editSurname.value.trim() : '';
    
    if (!name || !surname) {
        showMessage('Both name and surname are required', 'error');
        return;
    }
    
    await updateUserInfo({ name, surname });
    closeModal('nameModal');
}

async function saveEmailChanges() {
    const editEmail = document.getElementById('edit-email');
    const email = editEmail ? editEmail.value.trim() : '';
    
    if (!email || !isValidEmail(email)) {
        showMessage('Please enter a valid email address', 'error');
        return;
    }
    
    await updateUserInfo({ email });
    closeModal('emailModal');
}

async function savePasswordChanges() {
    const oldPasswordElement = document.getElementById('old-password');
    const newPasswordElement = document.getElementById('new-password');
    const oldPassword = oldPasswordElement ? oldPasswordElement.value : '';
    const newPassword = newPasswordElement ? newPasswordElement.value : '';
    
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
            loadUserProfile();
        } else {
            showMessage('Failed to update profile: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        showMessage('Error updating profile: ' + error.message, 'error');
    }
}

function showModalStatus(message, type) {
    const statusDiv = document.getElementById('review-edit-status');
    if (statusDiv) {
        statusDiv.textContent = message;
        statusDiv.className = `modal-status ${type}`;
        statusDiv.style.display = 'block';
    }
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
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = `profile-message ${type}`;
        messageDiv.style.display = 'block';
        
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 3000);
    }
}