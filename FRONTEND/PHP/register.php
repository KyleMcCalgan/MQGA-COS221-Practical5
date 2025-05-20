<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" type="text/css" href="../CSS/products.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <script src="../JS/register.js"></script>

</head>

<?php include "header.php"; ?>
<body>

    <div class="content">
        <h1>Create An Account</h1>
        <p>Join the Book worm Community</p>
    </div>

    <div id="smol-card">
        <form class="signup-container" method="POST">
            <div class="fg">
                <label for="name">First name</label>
                <input type="text" id="name" name="name">
                <div class="errMsg" id="name-error"></div>
            </div>

            <div class="fg">
                <label for="surname">Last name</label>
                <input type="text" id="surname" name="surname">
                <div class="errMsg" id="surname-error"></div>
            </div>

            <div class="fg">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
                <div class="errMsg" id="email-error"></div>
            </div>

            <div class="fg">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password">
                <div class="errMsg" id="password-error"></div>


                <div class="password-requirements">
                    Password must: Be longer than 8 characters, contain an uppercase and lowercase letter, a digit and a symbol.
                </div>
            </div>


            <button type="submit" id="submit-button">Register</button>

            <div id="frmMsg"></div>

            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </form>
    </div>

</body>
<?php include "footer.php"; ?>