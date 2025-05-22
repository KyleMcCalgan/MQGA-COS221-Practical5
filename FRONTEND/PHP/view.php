<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>View</title>
    <meta charset="UTF-8">
    <!-- <link rel="stylesheet" type="text/css" href="../CSS/view.css"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">

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

        <!-- Modal for all details -->
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

        <!-- table to store the different stores, their pricing and ratign -->
        <div class="VTableContainer">
            <div class="pricestopcontainer">
                <h1>Price Comparisson</h1>
                <h3>Compare the prices at various stores!</h3>
            </div>

            <div class="comparesides">
                <div class="rowcontainer">
                    <div class="storerow">
                        <h2>Store name</h2>
                        <h2>Rating⭐</h2>
                        <h2>Price</h2>
                    </div>

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
                <button>Rate/Review book</button>

                <div class="sortheading">
                    <h4>Sort by:</h4>
                    <select>
                        <option>Newest</option>
                        <option>Oldest</option>
                        <option>Highest rating</option>
                        <option>Lowwest Rating</option>
                    </select>
                </div>

            </div>

            <div class="revouterc">
                <div class="revleft">
                    <h3 style="padding-left: 2%; padding-top: 2%;">Stats</h3>
                    <h5>Average rating:4.50⭐</h5>
                    <h5>Number of ratings:2</h5>
                    <h5>Number of reviews:4</h5>
                </div>

                <div class="revright">

                    <div class="accordion" id="myAccordion" style="width: 96%; margin: 0 auto;">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    Name Rating: 4.7 ★
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#myAccordion">
                                <div class="accordion-body">
                                    This is the first item’s accordion body.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    Accordion Item #2
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#myAccordion">
                                <div class="accordion-body">
                                    This is the second item’s accordion body.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>
    <?php include "footer.php" ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>