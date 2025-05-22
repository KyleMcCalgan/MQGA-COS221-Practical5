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
    <link rel="stylesheet" type="text/css" href="../CSS/profile.css">
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">

    <script src="../JS/profile.js" ></script>
</head>

<body>
    <div class="container">
        <h1>Profile</h1>
        <div class="card">
            <div class="card-body">
                <form class="profile-form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <div class="display-field">
                            <span>Cait Smith</span>
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
                            <span>cait.smith@example.com</span>
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

                    <!-- Modals -->
                    <div id="nameModal" class="modal">
                        <div class="modal-content">
                            <h3>Edit Name</h3>
                            <input type="text" placeholder="New Name" class="modal-input">
                            <input type="text" placeholder="New Surname" class="modal-input">
                            <button class="save-btn">Save</button>
                            <button class="save-btn" onclick="closeModal('nameModal')">Cancel</button>
                        </div>
                    </div>

                    <div id="emailModal" class="modal">
                        <div class="modal-content">
                            <h3>Edit Email</h3>
                            <input type="email" placeholder="New Email" class="modal-input">
                            <button class="save-btn">Save</button>
                            <button class="save-btn" onclick="closeModal('emailModal')">Cancel</button>
                        </div>
                    </div>

                    <div id="passwordModal" class="modal">
                        <div class="modal-content">
                            <h3>Change Password</h3>
                            <input type="password" placeholder="Current Password" class="modal-input">
                            <input type="password" placeholder="New Password" class="modal-input">
                            <button class="save-btn">Save</button>
                            <button class="save-btn" onclick="closeModal('passwordModal')">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>



        <h1>My Reviews</h1>

        <div class="card">
            <div class="card-body">
                <div class="user-summary">
                    <div class="user-info">
                        <h2>Cait Smith</h2>
                        <div class="user-stats">
                            <span>12 Reviews</span>
                            <span>•</span>
                            <span>27 Orders</span>
                        </div>
                    </div>
                </div>

                <div class="review-stats">
                    <div class="stat-item">
                        <div class="stat-value">4.2</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">12</div>
                        <div class="stat-label">Total Reviews</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">8</div>
                        <div class="stat-label">Verified Purchases</div>
                    </div>
                </div>

                <div class="reviews-header">
                    <h3>Reviews</h3>
                    <select class="filter-dropdown">
                        <option value="recent">Most Recent</option>
                        <option value="all">All reviews</option>
                    </select>
                </div>

                <div class="review-item">
                    <div class="product-info">
                        <img src="../Images/book.jpg" alt="Product Image" class="product-image">
                        <div class="product-details">
                            <h3>Book for math</h3>
                            <span class="product-price">$349.99</span>
                            <p class="review-content">Nice book, used all of the pages for stuff</p>

                            <div class="star-rating">★★★★☆</div>

                            <div class="review-meta">
                                <div class="review-actions">
                                    <a href="#">Edit</a>
                                    <a href="#">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>


    </div>

</body>



<?php include "footer.php" ?>