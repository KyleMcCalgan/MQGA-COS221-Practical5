<?php
include "header.php";
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTQGA</title>
    <link rel="stylesheet" type="text/css" href="../CSS/store_stats.css">
    <link rel="stylesheet" type="text/css" href="../CSS/styling.css">

    <div class="page-container" style="padding-top: 100px; padding-left: 20px; padding-right: 20px;"> <header class="page-header">
            <h1>Specific Store Statistics</h1>
            <p>Average Ratings and Prices for Each Store</p>
        </header>

        <main id="store-stats-main-content">
            <div id="store-stats-display-area">
                <p class="loading-message">Loading store stats...</p>
                </div>
        </main>

    </div>

    <?php include "footer.php";?>

    <script src="../js/store_stats.js" defer></script> 
