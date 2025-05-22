<!DOCTYPE html>
<html lang="en">

<head>
    <title>SuperM pannel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" type="text/css" href="../CSS/superMPanel.css"> -->
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">
    <!-- <link rel="stylesheet" type="text/css" href="../CSS/footer_header.css"> -->
    <link rel="stylesheet" type="text/css" href="../CSS/header.css">
</head>

<?php include "header.php" ?>
<body>
    <div class="container">
        <h1>Control panel</h1>
        <div class="buttonsContainer">
            <script>
                const buttons = [];

                if (userType === 'admin') {
                    buttons.push({
                        id: 'my-store-btn',
                        text: 'My Store',
                        href: 'managers.php'
                    },{
                        id: 'add-book-btn',
                        text: 'Add Book',
                        href: 'addProducts.php'
                    },{
                        id: 'edit-books-btn',
                        text: 'Edit Book',
                        href: 'editProducts.php'
                    });
                } else if (userType === 'super') {
                    buttons.push({
                        id: 'stores-btn',
                        text: 'Stores',
                        href: 'managers.php'
                    },{
                        id: 'users-btn',
                        text: 'Users',
                        href: 'viewUsers.php'
                    },{
                        id: 'genres-btn',
                        text: 'Genres',
                        href: 'genres.php'
                    },{
                        id: 'add-book-btn',
                        text: 'Add Book',
                        href: 'addProducts.php'
                    },{
                        id: 'edit-books-btn',
                        text: 'Edit Books',
                        href: 'editProducts.php'
                    });
                }

                buttons.forEach(btn => {
                    const button = document.createElement('button');
                    button.id = btn.id;
                    button.className='mbtn blackbutton'
                    button.textContent = btn.text;
                    button.onclick = function() {
                        location.href = btn.href;
                    };
                    document.currentScript.parentElement.appendChild(button);
                });
            </script>


        </div>


    </div>




</body>
<?php include "footer.php" ?>