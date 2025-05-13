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
        <h1>Profile</h1>
        <div class="card">
            <div class="card-body">
                <form class="profile-form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Your Name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Your Email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="New Password">
                    </div>

                    <button type="submit" class="save-btn">Save Changes</button>
                </form>
            </div>
        </div>


        <h1>My Reviews</h1>

        <div class="card">
            <div class="card-body">
                <div class="user-summary">
                    <!-- <img src="/api/placeholder/400/400" alt="User Avatar" class="user-avatar"> -->
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
                        </div>
                    </div>
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

</body>



<?php include "footer.php" ?>