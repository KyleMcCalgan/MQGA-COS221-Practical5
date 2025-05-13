<?php include "header.php"?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>MTQGA</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../CSS/products.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    </head>
    <body>
<?php include "header.php"; ?>
    <style>
        
    </style>

        </br></br>
        <div class="content">
            <h1>Add Books</h1>
            <p>For Managers' eyes only</p>
        </div>

        <div class="smol-card">
            <form id="addProduct-form" method="POST">
                <div class="fg">
                    <label>Title</label>
                    <input>

                </div>

                <div class="fg">
                    <label>Author</label>
                    <input>

                </div>

                <div class="fg">
                    <label>Publisher</label>
                    <input>

                </div>

                <div class="fg">
                    <label>Published Date (Optional)</label>
                    <input>

                </div>

                <div class="fg">
                    <label>Description (Optional)</label>
                    <input>

                </div>

                <div class="fg">
                    <label>Page Count (Optional)</label>
                    <input>

                </div>

                <div class="fg">
                    <label>Maturity Rating (Optional)</label>
                    <input>

                </div>

                <div class="fg">
                    <label>Language (Optional)</label>
                    <input>

                </div>

                <div class="fg">
                    <label>Image (Optional)</label>
                    <button>Upload</button>

                </div>

                <div class="fg">
                    <label>Accessible In (Optional)</label>
                    <input>

                </div>

                <div class="fg">
                    <label>Ratings Count (Optional)</label>
                    <input>

                </div>

                <div class="fg">
                    <label>ISBN13 (Optional)</label>
                    <input>

                </div>
                
                <button type="submit" id="submit-button">Add book</button>

                <div id="frmMsg"></div>
                
            </form>
        </div>


<?php include "footer.php"?>