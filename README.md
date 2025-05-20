# MQGA-COS221-Practical5

This repository is for a web application designed to allow users to compare book prices from various online stores. It features a frontend for user interaction and a backend API to manage data and business logic.

## Project Structure

The project is organised into two main folders: `FRONTEND` and `BACKEND`.

### `FRONTEND`

This folder contains the user interface of the application.

* **`CSS/`**: Holds all the CSS files for styling the various pages of the application. This includes styles for product listings (`products.css`), user login (`login.css`), registration (`register.css`), and more general styling rules (`styling.css`, `stylingJ.css`).
* **`PHP/`**: Contains the PHP files that generate the HTML content for the user. Key files include:
    * `launch.php`: The main landing or home page.
    * `dashboard.php`: Displays featured and highest-rated books.
    * `products.php`: Allows users to browse and view all available books.
    * `view.php`: Shows detailed information about a specific book, including prices from different stores and user reviews.
    * `login.php` & `register.php`: Handle user authentication.
    * `profile.php`: User profile page where users can manage their details and view their reviews.
    * `header.php` & `footer.php`: Common header and footer elements for the pages.
    * Admin-related pages:
        * `SuperMPanel.php`: Main control panel for super administrators.
        * `addProducts.php`: Allows administrators to add new books to the system.
        * `editProducts.php`: Enables administrators to modify existing book details.
        * `genres.php`: For managing book genres.
        * `managers.php`: Super admin page to manage store administrators.
        * `viewUsers.php`: Displays a list of all users.
* **`Images/`**: (Assumed based on references in PHP files like `dashboard.php` and `launch.php`) This directory would store images used throughout the frontend, such as book covers and promotional banners.

### `BACKEND`

This folder houses the server-side logic and API for the application.

* **`config/`**:
    * `config.php`: Loads environment variables.
    * `database.php`: Contains the function to establish a database connection.
* **`docs/`**:
    * `db_structure.sql`: SQL script defining the database schema, including tables for users, products (books), stores, categories, ratings, reviews, and admin associations.
* **`productGeneration/`**:
    * `APItoJson.py`: A Python script to fetch book data from the Google Books API and save it as a JSON file (`diverse_books_dataset_500.json`).
    * `jsonTOSQL.py`: A Python script to convert the JSON book data into SQL INSERT statements for populating the database.
* **`public/`**:
    * `index.php`: The main entry point for all API requests. It routes requests to the appropriate handlers based on the `type` parameter.
* **`src/`**:
    * **`handlers/`**: Contains PHP files that handle specific API actions. Each handler typically validates input, interacts with the database, and returns a JSON response. Examples include:
        * User management: `login_handler.php`, `register_handler.php`.
        * Product management: `add_product_handler.php`, `delete_product_handler.php`, `getallproducts_handler.php`, `getproduct_handler.php`, `updateProductAdmin_handler.php`, `getfeaturedproducts_handler.php`, `gethighestratedproducts_handler.php`.
        * Store and listing management: `AddInfoForStore_handler.php`, `delete_store_products_handler.php`, `get_store_products_handler.php`, `getalllistedproducts_handler.php`.
    * **`utils/`**:
        * `auth_utils.php`: Provides functions for checking user authentication and authorisation based on API keys and user types.
        * `response_utils.php`: Includes a function to standardise API JSON responses.
        * `sanitise_utils.php`: Contains functions for sanitising user input to prevent security vulnerabilities.

### Root Directory

* **`README.md`**: This file, providing an overview of the project.

## Key Features

* **User Authentication**: Users can register and log in to the platform.
* **Product Browse**: Users can view lists of books, including featured and highest-rated ones.
* **Product Details**: Users can view detailed information for each book, including descriptions, author, publisher, and availability in different stores with their respective prices and ratings.
* **Store Information**: The system manages information about different book stores and the products they offer.
* **Admin Functionality**:
    * Super admins can manage stores, users, and genres.
    * Admins and Super admins can add, update, and delete products.
    * Admins can add price and rating information for books in their assigned store.
* **API**: A backend API handles data operations and business logic, with distinct endpoints for various actions.
* **Data Generation**: Scripts are provided to populate the product database using the Google Books API.

