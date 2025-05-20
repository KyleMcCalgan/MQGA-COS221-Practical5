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
    </head>
    <body>

        <br/><br/>
        <div class="content">
            <h1>Add Books</h1>
            <p>For Managers' eyes only</p>

            <div class="main-form-card">
                <form id="addProduct-form" method="POST">
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
                                <input type="text" id="maturityRating" name="maturityRating">
                            </div>
                            <div class="fg">
                                <label for="language">Language (Optional)</label>
                                <input type="text" id="language" name="language">
                            </div>
                            <div class="fg">
                                <label for="imageUpload">Image (Optional)</label>
                                <input type="file" id="imageUpload" name="imageUpload" style="display: none;">
                                <button type="button" class="upload-btn" onclick="document.getElementById('imageUpload').click();">Upload Image</button>
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
                    
                    <div id="frmMsg"></div>
                </form>
            </div>
        </div>

<?php include "footer.php"; ?>
    </body>
</html>