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
</head>

<body>
    <div class="container">
        <header>
            <h1>Profile</h1>
        </header>

        <!-- Loading indicator -->
        <div id="loading-profile" class="loading" style="display: block;">Loading profile...</div>
        
        <!-- Error/Success messages -->
        <div id="profile-message" class="message" style="display: none;"></div>

        <!-- Profile Card -->
        <div class="card" id="profile-card" style="display: none;">
            <div class="card-body">
                <form class="profile-form">
                    <div class="form-group">
                        <label for="display-name">Name</label>
                        <input type="text" id="display-name" name="name" readonly>
                        <button type="button" onclick="openNameModal()" style="margin-top: 5px;">Edit Name</button>
                    </div>

                    <div class="form-group">
                        <label for="display-email">Email</label>
                        <input type="email" id="display-email" name="email" readonly>
                        <button type="button" onclick="openEmailModal()" style="margin-top: 5px;">Edit Email</button>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" value="••••••••" readonly>
                        <button type="button" onclick="openPasswordModal()" style="margin-top: 5px;">Change Password</button>
                    </div>
                </form>
            </div>
        </div>

        <h1>My Reviews</h1>

        <!-- Loading indicator for reviews -->
        <div id="loading-reviews" class="loading" style="display: block;">Loading reviews...</div>
        
        <!-- Error message for reviews -->
        <div id="reviews-message" class="message" style="display: none;"></div>

        <!-- Reviews Card -->
        <div class="card" id="reviews-card" style="display: none;">
            <div class="card-body">
                <div class="user-summary">
                    <div class="user-info">
                        <h2 id="user-display-name">Loading...</h2>
                        <div class="user-stats">
                            <span id="review-count">0 Reviews</span>
                        </div>
                    </div>
                </div>

                <div class="review-stats">
                    <div class="stat-item">
                        <div class="stat-value" id="avg-rating">-</div>
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
                    <select class="filter-dropdown" id="sort-dropdown">
                        <option value="newest">Most Recent</option>
                        <option value="oldest">Oldest First</option>
                        <option value="highest rating">Highest Rating</option>
                        <option value="lowest rating">Lowest Rating</option>
                    </select>
                </div>

                <!-- Reviews Container -->
                <div id="reviews-container">
                    <!-- Reviews will be populated here -->
                </div>

                <!-- Empty state -->
                <div id="no-reviews" class="empty-wishlist" style="display: none;">
                    You haven't written any reviews yet.
                </div>
            </div>
        </div>
    </div>

    <!-- Name Modal -->
    <div id="nameModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="modal-content" style="background-color: white; margin: auto; padding: 20px; border-radius: 8px; width: 400px;">
            <h3>Edit Name</h3>
            <form id="name-form">
                <div class="form-group">
                    <label for="modal-name">First Name</label>
                    <input type="text" id="modal-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="modal-surname">Last Name</label>
                    <input type="text" id="modal-surname" name="surname" required>
                </div>
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" onclick="closeModal('nameModal')" style="margin-right: 10px; padding: 8px 16px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="emailModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="modal-content" style="background-color: white; margin: auto; padding: 20px; border-radius: 8px; width: 400px;">
            <h3>Edit Email</h3>
            <form id="email-form">
                <div class="form-group">
                    <label for="modal-email">Email Address</label>
                    <input type="email" id="modal-email" name="email" required>
                </div>
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" onclick="closeModal('emailModal')" style="margin-right: 10px; padding: 8px 16px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Modal -->
    <div id="passwordModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="modal-content" style="background-color: white; margin: auto; padding: 20px; border-radius: 8px; width: 400px;">
            <h3>Change Password</h3>
            <form id="password-form">
                <div class="form-group">
                    <label for="modal-old-password">Current Password</label>
                    <input type="password" id="modal-old-password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label for="modal-new-password">New Password</label>
                    <input type="password" id="modal-new-password" name="password" required>
                </div>
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" onclick="closeModal('passwordModal')" style="margin-right: 10px; padding: 8px 16px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Keep your existing modal functions
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
            openModal('nameModal');
        }
        function openEmailModal() {
            openModal('emailModal');
        }
        function openPasswordModal() {
            openModal('passwordModal');
        }
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
    </script>
    <script src="../JS/profile.js"></script>
</body>

<?php include "footer.php" ?>