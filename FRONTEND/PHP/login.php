<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- here -->
    <!-- <link rel="stylesheet" type="text/css" href="../CSS/products.css"> -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" type="text/css" href="../CSS/header.css"> -->
    <link rel="stylesheet" type="text/css" href="../CSS/stylingJ.css">
    <script src="../JS/login.js"></script>

</head>

<?php include "header.php"; ?>

<body>
    <div class="logcontainer">
        <div class="logcontcontainer">
            <!-- <img class="bookman" src="../Images/cheapoakfinal.png"> -->

            <!-- <div class="vertical-line"></div> -->

            <div class="logright">

                <h1>Welcome back, fellow bookworm!</h1>


                <div class="smol-card">


                    <p class="loginwelcome">Login to your account</p>

                    <form id="login-form" method="POST">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email">
                            <div class="error-message" id="email-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password">
                            <div class="error-message" id="password-error"></div>
                        </div>

                        <button class="blackbutton normalbutton" type="submit" id="submit-button">Login</button>

                        <div id="form-message"></div>

                        <div class="signup-link">
                            Don't have an account? <a href="register.php">Sign up here</a>
                        </div>
                    </form>
                </div>

            </div>



        </div>


    </div>

    <?php include "footer.php" ?>
</body>