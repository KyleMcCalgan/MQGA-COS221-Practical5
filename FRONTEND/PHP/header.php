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
                                                    } elseif ($currentPage == 'launch.php') {
                                                        echo '../CSS/styling.css';
                                                    } elseif ($currentPage == 'login.php') {
                                                        echo '../CSS/login.css';
                                                    } elseif ($currentPage == 'logout.php') {
                                                        echo '../CSS/logout.css';
                                                    } elseif ($currentPage == 'register.php' || $currentPage == 'addProducts.php') {
                                                        echo '../CSS/register.css';
                                                    } elseif ($currentPage == 'SuperMPanel.php') {
                                                        echo '../CSS/superMPanel.css';
                                                    } elseif ($currentPage == 'dashboard.php') {
                                                        echo '../CSS/dashboard.css';
                                                    }
                                                    ?>">
    <script src="../js/logout.js"></script>

</head>

<body>
    <div class="ribbon">

        <script>
            const isLoggedIn = sessionStorage.getItem('api_key') !== null;
            const userType = sessionStorage.getItem('user_type');
            const loginLogoutContainer = document.createElement('button');
            loginLogoutContainer.id = 'login-logout-container';
            loginLogoutContainer.className = '<?php echo ($currentPage == "login.php" || $currentPage == "register.php") ? "current-tab-btn" : ""; ?>';

            if (isLoggedIn) {
                loginLogoutContainer.textContent = 'Logout';
                loginLogoutContainer.onclick = function() {
                    logout();
                };
            } else {
                loginLogoutContainer.textContent = 'Login/Signup';
                loginLogoutContainer.onclick = function() {
                    location.href = 'login.php';
                };
            }
            document.currentScript.parentElement.prepend(loginLogoutContainer);

            const navButtons = [];
            if (!isLoggedIn) {
                navButtons.push({
                    id: 'home-btn',
                    text: 'Home',
                    href: 'launch.php',
                    active: '<?php echo ($currentPage == "launch.php") ? "current-tab-btn" : ""; ?>'
                });
            } else {
                if (userType === 'regular') {
                    navButtons.push({
                        id: 'home-btn',
                        text: 'Home',
                        href: 'launch.php',
                        active: '<?php echo ($currentPage == "launch.php") ? "current-tab-btn" : ""; ?>'
                    }, {
                        id: 'dashboard-btn',
                        text: 'Dashboard',
                        href: 'dashboard.php',
                        active: '<?php echo ($currentPage == "dashboard.php") ? "current-tab-btn" : ""; ?>'
                    }, {
                        id: 'products-btn',
                        text: 'Products',
                        href: 'products.php',
                        active: '<?php echo ($currentPage == "products.php") ? "current-tab-btn" : ""; ?>'
                    }, {
                        id: 'profile-btn',
                        text: 'Profile',
                        href: 'profile.php',
                        active: '<?php echo ($currentPage == "profile.php") ? "current-tab-btn" : ""; ?>'
                    });
                } else {
                    navButtons.push({
                        id: 'home-btn',
                        text: 'Home',
                        href: 'launch.php',
                        active: '<?php echo ($currentPage == "launch.php") ? "current-tab-btn" : ""; ?>'
                    }, {
                        id: 'dashboard-btn',
                        text: 'Dashboard',
                        href: 'dashboard.php',
                        active: '<?php echo ($currentPage == "dashboard.php") ? "current-tab-btn" : ""; ?>'
                    }, {
                        id: 'products-btn',
                        text: 'Products',
                        href: 'products.php',
                        active: '<?php echo ($currentPage == "products.php") ? "current-tab-btn" : ""; ?>'
                    }, {
                        id: 'control-panel-btn',
                        text: 'Control Panel',
                        href: 'SuperMPanel.php',
                        active: '<?php echo ($currentPage == "SuperMPanel.php") ? "current-tab-btn" : ""; ?>'
                    });
                }
            }

            navButtons.forEach(btn => {
                const button = document.createElement('button');
                button.id = btn.id;
                button.className = btn.active;
                button.textContent = btn.text;
                button.onclick = function() {
                    location.href = btn.href;
                };
                document.currentScript.parentElement.appendChild(button);
            });
        </script>
                <?php if ($currentPage == 'products.php') : ?>
            <input type="text" class="search-bar" placeholder="Search...">
        <?php endif; ?>
    </div>