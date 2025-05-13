<?php 
$currentPage = basename($_SERVER['PHP_SELF']); 
include "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MTQGA</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php
    if ($currentPage == 'products.php') {
        echo '../CSS/products.css';
    } elseif ($currentPage == 'launch.php' || $currentPage == 'dashboard.php'){
        echo '../CSS/styling.css';
    } elseif ($currentPage == 'login.php'){
        echo '../CSS/login.css';
    } elseif ($currentPage == 'logout.php'){
        echo '../CSS/logout.css';
    } elseif ($currentPage == 'register.php' || $currentPage == 'addProducts.php'){
        echo '../CSS/register.css';
    } elseif ($currentPage == 'SuperMPanel.php'){
        echo '../CSS/superMPanel.css';
    }
    ?>">
</head>
<body>
    <div class="ribbon">

        <button class="<?php echo ($currentPage == 'login.php' || $currentPage == 'register.php') ? 'current-tab-btn' : ''; ?>">
            <a id="login-logout-link" href="login.php">Login/Register</a>
        </button>
        <button class="<?php echo ($currentPage == 'launch.php') ? 'current-tab-btn' : ''; ?>" onclick="location.href='launch.php'">Home</button>
        <button class="<?php echo ($currentPage == 'dashboard.php') ? 'current-tab-btn' : ''; ?>" onclick="location.href='dashboard.php'">Dashboard</button>
        <button class="<?php echo ($currentPage == 'products.php') ? 'current-tab-btn' : ''; ?>" onclick="location.href='products.php'">Products</button>
        <button class="<?php echo ($currentPage == 'addProducts.php') ? 'current-tab-btn' : ''; ?>" onclick="location.href='addProducts.php'">Add Books</button>
        <button class="<?php echo ($currentPage == 'SuperMPanel.php') ? 'current-tab-btn' : ''; ?>" onclick="location.href='SuperMPanel.php'">Control Panel</button>
        <button class="<?php echo ($currentPage == 'profile.php') ? 'current-tab-btn' : ''; ?>" onclick="location.href='profile.php'">Profile</button>
        <button class="<?php echo ($currentPage == 'editProducts.php') ? 'current-tab-btn' : ''; ?>" onclick="location.href='editProducts.php'">Edit Products</button>
        <?php if ($currentPage == 'products.php') : ?>
            <input type="text" class="search-bar" placeholder="Search...">    

        <?php endif; ?> 
    </div>