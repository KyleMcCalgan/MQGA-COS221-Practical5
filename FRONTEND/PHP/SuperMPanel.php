<!DOCTYPE html>
<html lang="en">

<head>
    <title>SuperM pannel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../CSS/superMPanel.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">
</head>

<body>
    <?php include "header.php" ?>
    <div class="container">
        <h1>Control panel</h1>
        <div class="buttonsContainer">
            <button type="button" class="btn btn-outline-dark d-block mx-auto" style="width: 50%;" onclick="location.href='managers.php'">Stores</button>
            <button type="button" class="btn btn-outline-dark d-block mx-auto" style="width: 50%;" onclick="location.href='viewUsers.php'">Users</button>
            <button type="button" class="btn btn-outline-dark d-block mx-auto" style="width: 50%;" onclick="location.href='genres.php'">Genres</button>

        </div>


    </div>




</body>
<?php include "footer.php" ?>