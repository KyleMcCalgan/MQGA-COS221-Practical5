<?php include "header.php"; ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>MTQGA - Add Book</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../CSS/addBook.css"> 
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
        <script src="../JS/addProducts.js"></script>
    </head>
    <body>
        <div class="content">
            <h1>Add Books</h1>

            <div class="main-form-card">
                <form id="addProduct-form" method="POST" onsubmit="return false;"><!-- Ensure no normal form submission -->
                    <div class="form-columns">
                        <div class="smol-card">
                            <div class="fg">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title">
                            </div>
                            <div class="fg">
                                <label for="author">Author</label>
                                <input type="text" id="author" name="author">
                            </div>
                            <div class="fg">
                                <label for="publisher">Publisher</label>
                                <input type="text" id="publisher" name="publisher">
                            </div>
                            <div class="fg">
                                <label for="publishedDate">Published Date (Optional)</label>
                                <input type="date" id="publishedDate" name="publishedDate">
                            </div>
                            <div class="fg">
                                <label for="description">Description (Optional)</label>
                                <textarea id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="fg">
                                <label for="pageCount">Page Count (Optional)</label>
                                <input type="number" id="pageCount" name="pageCount">
                            </div>
                        </div>

                        <div class="smol-card">
                            <div class="fg">
                                <label for="maturityRating">Maturity Rating (Optional)</label>
                                <select id="maturityRating" name="maturityRating" class="form-control">
                                    <option value="">Select Maturity Rating</option>
                                    <option value="NOT_MATURE">Not Mature</option>
                                    <option value="MATURE">Mature</option>
                                    <option value="EVERYONE">Everyone</option>
                                    <option value="TEEN">Teen</option>
                                    <option value="ADULT">Adult</option>
                                </select>
                            </div>
                            <div class="fg">
                                <label for="language">Language (Optional)</label>
                                <input type="text" id="language" name="language">
                            </div>
                            <div class="fg">
                                <label for="thumbnail">Book Cover Image URL (Optional)</label>
                                <input type="url" id="thumbnail" name="thumbnail" placeholder="https://example.com/book-cover.jpg">
                                <small style="display: block; margin-top: 5px; color: #666; font-size: 12px;">
                                    Enter a direct URL to the book cover image. This will be used as the main book cover.
                                </small>
                            </div>
                            <div class="fg">
                                <label for="smallThumbnail">Small Thumbnail URL (Optional)</label>
                                <input type="url" id="smallThumbnail" name="smallThumbnail" placeholder="https://example.com/small-cover.jpg">
                                <small style="display: block; margin-top: 5px; color: #666; font-size: 12px;">
                                    Enter a URL for a smaller version of the cover image. If not provided, the main image will be used.
                                </small>
                            </div>
                            <div class="fg">
                                <label for="accessibleIn">Accessible In (Optional)</label>
                                <input type="text" id="accessibleIn" name="accessibleIn">
                            </div>
                            <div class="fg">
                                <label for="ratingsCount">Ratings Count (Optional)</label>
                                <input type="number" id="ratingsCount" name="ratingsCount">
                            </div>
                            <div class="fg">
                                <label for="isbn13">ISBN13 (Optional)</label>
                                <input type="text" id="isbn13" name="isbn13">
                            </div>
                        </div>
                    </div>

                    <div class="add-book-button-container">
                        <button type="submit" id="submit-button">Add book</button>
                    </div>
                    
                    <div id="frmMsg" style="margin-top: 20px; padding: 15px; display: none; text-align: center; font-weight: bold; border-radius: 5px;"></div>
                </form>
            </div>
        </div>

<?php include "footer.php"; ?>
    </body>
</html>