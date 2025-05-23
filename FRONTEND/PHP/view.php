<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>View</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">
    <link rel="stylesheet" type="text/css" href="../CSS/view.css">
    <script src="../JS/view.js"></script>
</head>

<?php include "header.php" ?>

<body>
    <div class="viewcontent">

        <div class="details">
            <img class="Vimg" src="../Images/notfound.png" alt="Book Cover" id="book-image" />
            <div class="DetialsContainer" id="book-details">
                <h1 id="book-title">Loading...</h1>
                <h2 id="book-author"></h2>
                <p id="book-publisher"></p>
                <p id="book-categories"></p>
                <p id="book-pagecount"></p>
                <p id="book-isbn13"></p>
                <button class="blackbutton normalbutton viewb" id="details-button">All details</button>
            </div>
        </div>

        <div class="modal" id="book-details-modal">
            <div class="modal-content">
                <span class="close-button" id="close-modal">×</span>
                <h2 id="modal-title"></h2>
                <p id="modal-description"></p>
                <p id="modal-author"></p>
                <p id="modal-isbn13"></p>
                <p id="modal-published"></p>
                <p id="modal-publisher"></p>
                <p id="modal-pagecount"></p>
                <p id="modal-maturity"></p>
                <p id="modal-language"></p>
                <p id="modal-accessible"></p>
                <p id="modal-categories"></p>
            </div>
        </div>

        <div class="modal" id="review-modal">
            <div class="modal-content">
                <span class="close-button" id="close-review-modal">×</span>
                <h2>Rate/Review the Book</h2>
                <p>You can submit a rating, a review, or both.</p>
                <p id="review-message" style="font-size: 14px;">If you have an existing rating or review for this book, it will be replaced.</p>
                <form id="review-form">
                    <label for="review-input">Your Review:</label>
                    <textarea id="review-input" rows="5" style="width: 100%; margin-bottom: 10px;"></textarea>
                    <label>Rating:</label>
                    <div class="rating">
                        <input type="radio" id="star5" name="rate" value="5" />
                        <label for="star5" title="5 stars">
                            <svg viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg" class="star-solid">
                                <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                            </svg>
                        </label>
                        <input type="radio" id="star4" name="rate" value="4" />
                        <label for="star4" title="4 stars">
                            <svg viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg" class="star-solid">
                                <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                            </svg>
                        </label>
                        <input type="radio" id="star3" name="rate" value="3" />
                        <label for="star3" title="3 stars">
                            <svg viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg" class="star-solid">
                                <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                            </svg>
                        </label>
                        <input type="radio" id="star2" name="rate" value="2" />
                        <label for="star2" title="2 stars">
                            <svg viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg" class="star-solid">
                                <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                            </svg>
                        </label>
                        <input type="radio" id="star1" name="rate" value="1" />
                        <label for="star1" title="1 star">
                            <svg viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg" class="star-solid">
                                <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                            </svg>
                        </label>
                    </div>
                    <div class="centerbutton">
                        <button type="submit" class="blackbutton normalbutton">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="VTableContainer">

            <div class="comparesides">
                <div class="rowcontainer">
                    <div class="pricestopcontainer">
                        <h1>Price Comparison</h1>
                        <h3>Compare the prices at various stores!</h3>
                    </div>

                    <!-- <div class="storerow">
                        <h2>Store name</h2>
                        <h2>Rating⭐</h2>
                        <h2>Price</h2>
                    </div> -->
                </div>
                <img class="priceguy" src="../Images/cheapoakfinal.png">
            </div>
        </div>

        <div class="reviewcontainer">
            <div class="revtop">
                <h2>Ratings & Reviews</h2>
                <h3>See what MTQGA users thought of the book!</h3>
            </div>

            <div class="revtop functions">
                <button class="blackbutton normalbutton" id="review-button">Rate/Review book</button>
                <div class="sortheading">
                    <h4>Sort by: </h4>
                    <select class="revsort" id="review-sort">
                        <option value="newest">Newest</option>
                        <option value="oldest">Oldest</option>
                        <option value="highest rating">Highest Rating</option>
                        <option value="lowest rating">Lowest Rating</option>
                    </select>
                </div>
            </div>

            <div class="revouterc">
                <div class="revleft">
                    <h3 style="padding-left: 2%; padding-top: 2%;">Stats</h3>
                    <h5 class="statsavgR">Average rating: N/A</h5>
                    <h5 class="statsnoR">Number of ratings: 0</h5>
                    <h5 class="statsnoRe">Number of reviews: 0</h5>
                </div>

                <div class="revright">
                    <div class="accordion" id="reviewAccordion" style="width: 96%; margin: 0 auto;">
                    </div>
                    <div class="page" style="text-align: center; margin-top: 10px;">
                        <button class="page-button" id="prev-page" disabled>Previous</button>
                        <button class="page-button" id="next-page" disabled>Next</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include "footer.php" ?>

</html>