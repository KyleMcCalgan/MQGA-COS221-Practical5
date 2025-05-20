<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- here -->
    <link rel="stylesheet" type="text/css" href="../CSS/products.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <script src="../JS/login.js"></script>

</head>

<body>
    <?php include "header.php"; ?>
    </br></br></br>
    <div class="content">
        <h1>Login to your account</h1>
        <p>Welcome Back!</p>
    </div>

    <div class="smol-card">
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

            <button type="submit" id="submit-button">Login</button>

            <div id="form-message"></div>

            <div class="signup-link">
                Don't have an account? <a href="register.php">Sign up here</a>
            </div>
        </form>
    </div>
    </br>

</body>

<?php include "footer.php" ?>