<?php include "header.php" ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My profile</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">

    <script src="../JS/profile.js"></script>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>Profile</h1>
            <div class="card-body">
                <form class="profile-form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <div class="display-field">
                            <span id="display-name">Loading...</span>
                            <button type="button" class="editBtn" onclick="openNameModal()">
                                <svg height="1em" viewBox="0 0 512 512">
                                    <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="display-field">
                            <span id="display-email">Loading...</span>
                            <button type="button" class="editBtn" onclick="openEmailModal()">
                                <svg height="1em" viewBox="0 0 512 512">
                                    <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <button type="button" class="save-btn" onclick="openPasswordModal()">Change Password</button>
                    </div>

                
                    <div id="nameModal" class="modal">
                        <div class="modal-content">
                            <h3>Edit Name</h3>
                            <input type="text" id="edit-name" placeholder="New Name" class="modal-input">
                            <input type="text" id="edit-surname" placeholder="New Surname" class="modal-input">
                            <button class="save-btn" onclick="saveNameChanges()">Save</button>
                            <button class="save-btn" onclick="closeModal('nameModal')">Cancel</button>
                        </div>
                    </div>

                    <div id="emailModal" class="modal">
                        <div class="modal-content">
                            <h3>Edit Email</h3>
                            <input type="email" id="edit-email" placeholder="New Email" class="modal-input">
                            <button class="save-btn" onclick="saveEmailChanges()">Save</button>
                            <button class="save-btn" onclick="closeModal('emailModal')">Cancel</button>
                        </div>
                    </div>

                    <div id="passwordModal" class="modal">
                        <div class="modal-content">
                            <h3>Change Password</h3>
                            <input type="password" id="old-password" placeholder="Current Password" class="modal-input">
                            <input type="password" id="new-password" placeholder="New Password" class="modal-input">
                            <button class="save-btn" onclick="savePasswordChanges()">Save</button>
                            <button class="save-btn" onclick="closeModal('passwordModal')">Cancel</button>
                        </div>
                    </div>

                  
                    <div id="reviewEditModal" class="modal">
                        <div class="modal-content">
                            <h3>Edit Review</h3>
                            <div class="modal-section">
                                <label for="edit-rating">Rating:</label>
                                <div class="star-rating-input">
                                    <span class="star" data-rating="1">★</span>
                                    <span class="star" data-rating="2">★</span>
                                    <span class="star" data-rating="3">★</span>
                                    <span class="star" data-rating="4">★</span>
                                    <span class="star" data-rating="5">★</span>
                                </div>
                                <div class="rating-display">
                                    <span id="rating-text">No rating selected</span>
                                </div>
                            </div>
                            <div class="modal-section">
                                <label for="edit-review-text">Review:</label>
                                <textarea id="edit-review-text" placeholder="Your review..." class="modal-input review-textarea"></textarea>
                            </div>
                            <div id="review-edit-status" class="modal-status"></div>
                            <button class="save-btn" onclick="saveReviewChanges()" id="save-review-btn">Save</button>
                            <button class="save-btn" onclick="closeModal('reviewEditModal')">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card">
            <h1>My Reviews</h1>
            <div class="card-body">
                <div class="user-summary">
                    <div class="user-info">
                        <h2 id="user-name-display">Loading...</h2>
                        <div class="user-stats">
                            <span id="review-count">0 Reviews</span>
                            <span>•</span>
                            <span id="rating-count">0 Ratings</span>
                        </div>
                    </div>
                </div>

                <div class="review-stats">
                    <div class="stat-item">
                        <div class="stat-value" id="average-rating">0.0</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="total-reviews">0</div>
                        <div class="stat-label">Total Reviews</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="total-ratings">0</div>
                        <div class="stat-label">Total Ratings</div>
                    </div>
                </div>

                <div class="reviews-header">
                    <h3>Reviews</h3>
                    <select class="filter-dropdown" id="sort-dropdown" onchange="loadUserReviews()">
                        <option value="newest">Most Recent</option>
                        <option value="oldest">Oldest First</option>
                        <option value="highest rating">Highest Rating</option>
                        <option value="lowest rating">Lowest Rating</option>
                    </select>
                </div>

                <div id="reviews-container">
                  
                </div>

                <div id="no-reviews-message" style="display: none;" class="empty-reviews">
                    <p>You haven't written any reviews yet.</p>
                </div>

                <div id="loading-reviews" class="loading-reviews">
                    <p>Loading your reviews...</p>
                </div>
            </div>
        </div>
    </div>

    <div id="profile-message" class="profile-message" style="display: none;"></div>

</body>

<?php include "footer.php" ?>