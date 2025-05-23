<?php include "header.php"?>

        <div class="content">
            <h1>Browse Books</h1>
        </div>

        <div class="filters-container">
            <select id="sort-name" class="filter-select">
                <option value="default">Sort by Name (Default)</option>
                <option value="name_asc">Name (A-Z)</option>
                <option value="name_desc">Name (Z-A)</option>
            </select>
            <select id="sort-author" class="filter-select">
                <option value="default">Sort by Author (Default)</option>
                <option value="author_asc">Author (A-Z)</option>
                <option value="author_desc">Author (Z-A)</option>
            </select>
            <select id="sort-rating" class="filter-select">
                <option value="default">Sort by Rating (Default)</option>
                <option value="rating_asc">Rating (Low to High)</option>
                <option value="rating_desc">Rating (High to Low)</option>
            </select>
            <select id="filter-genre" class="filter-select">
                <option value="default">All Genres</option>
                </select>
        </div>
        </br>
        <div class="range"> 
                    <div class="card">
            <a href="view.php">
                <img src="../Images/percy1.jpg" alt="Watch" class="card-image">
                <h2>Percy Jackson: The Lightning Thief</h2>
            </a>
            <div class="card-content">
                <p>Rick Riordan</p>
                <p>4.9 ⭐</p>
                <p>R450.00</p>
                
            </div>
        </div>

       
    </div>
</br></br>
<script src="../JS/products.js"></script> </body>
<?php include "footer.php"?>

