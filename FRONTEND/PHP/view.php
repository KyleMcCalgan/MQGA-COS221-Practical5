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
            <table class="VStoresTable">
                <tr class="vStoreRow">
                    <td>
                        <h2>Store name</h2>
                    </td>
                    <td>
                        <h3>Price</h3>
                    </td>

                    <td>
                        <div class="rating">
                            <input type="radio" id="star5" name="rate" value="5" />
                            <label for="star5" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input type="radio" id="star4" name="rate" value="4" />
                            <label for="star4" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input checked="" type="radio" id="star3" name="rate" value="3" />
                            <label for="star3" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input type="radio" id="star2" name="rate" value="2" />
                            <label for="star2" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input type="radio" id="star1" name="rate" value="1" />
                            <label for="star1" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                        </div>
                    </td>

                </tr>

                <tr class="vStoreRow">
                    <td>
                        <h2>Store name</h2>
                    </td>
                    <td>
                        <h3>Price</h3>
                    </td>

                    <td>
                        <div class="rating">
                            <input type="radio" id="star5" name="rate" value="5" />
                            <label for="star5" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input type="radio" id="star4" name="rate" value="4" />
                            <label for="star4" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input checked="" type="radio" id="star3" name="rate" value="3" />
                            <label for="star3" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input type="radio" id="star2" name="rate" value="2" />
                            <label for="star2" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input type="radio" id="star1" name="rate" value="1" />
                            <label for="star1" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                        </div>
                    </td>

                </tr>

                <tr class="vStoreRow">
                    <td>
                        <h2>Store name</h2>
                    </td>
                    <td>
                        <h3>Price</h3>
                    </td>

                    <td>
                        <div class="rating">
                            <input type="radio" id="star5" name="rate" value="5" />
                            <label for="star5" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input type="radio" id="star4" name="rate" value="4" />
                            <label for="star4" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input checked="" type="radio" id="star3" name="rate" value="3" />
                            <label for="star3" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input type="radio" id="star2" name="rate" value="2" />
                            <label for="star2" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                            <input type="radio" id="star1" name="rate" value="1" />
                            <label for="star1" title="text"><svg
                                    viewBox="0 0 576 512"
                                    height="1em"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="star-solid">
                                    <path
                                        d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                </svg></label>
                        </div>
                    </td>

                </tr>

            </table>

        </div>


        <div class="reviewcontainer">
            <h2>Reviews</h2>





            <!-- <div class="Revcard">
            <span class="title">Leave a review</span>
            <form class="form">
                <div class="group">
                    <textarea placeholder="" id="comment" name="comment" rows="5" required=""></textarea>
                </div>
                <button type="submit">Submit</button>
            </form>
        </div> -->

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

        <button class="btn btn-primary">Review Book</button>




    </div>
    <?php include "footer.php" ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>