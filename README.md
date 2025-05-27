# MQGA-COS221-Practical5

This repository is for a web application designed to allow users to compare book prices from various online stores. It features a frontend for user interaction and a backend API to manage data and business logic.

## Project Structure

The project is organised into two main folders: `FRONTEND` and `BACKEND`.

### `FRONTEND`

This folder contains the user interface of the application.

* **`CSS/`**: 
    * Product-related pages: `products.css`, `view.css`, `addBook.css`, `addProducts.css`
    * User authentication: `login.css`, `register.css`
    * Admin panels: `superMPanel.css`, `editProducts.css`, `genres.css`, `managers.css`, `viewUsers.css`
    * General and other pages: `dashboard.css`, `profile.css`, `store_stats.css`, `styling.css`, `stylingJ.css`
    * Common elements: `header.css`, `footer.css`
* **`PHP/`**: 
    * `launch.php`: The main landing or home page.
    * `dashboard.php`: Displays featured and highest-rated books.
    * `products.php`: Allows users to browse and view all available books.
    * `view.php`: Shows detailed information about a specific book, including prices from different stores and user reviews.
    * `login.php` & `register.php`: Handle user authentication.
    * `profile.php`: User profile page where users can manage their details and view their reviews.
    * `stores_stats.php`: Displays statistics specific to stores.
    * `header.php` & `footer.php`: Common header and footer elements for the pages.
    * Admin-related pages:
        * `SuperMPanel.php`: Main control panel for super administrators and store admins.
        * `addProducts.php`: Allows administrators to add new books to the system.
        * `editProducts.php`: Enables administrators to modify existing book details and store-specific information.
        * `genres.php`: For managing book genres.
        * `managers.php`: Super admin page to manage store administrators and store details.
        * `viewUsers.php`: Displays a list of all users.
* **`Images/`**
* **`JS/`**: Contains JavaScript files for client-side interactivity. Key scripts include:
    * User authentication: `login.js`, `register.js`, `logout.js`
    * Page-specific logic: `launch_summary.js`, `dashboard.js`, `products.js`, `view.js`, `profile.js`, `store_stats.js`
    * Admin functionalities: `addProducts.js`, `editProducts.js`, `genres.js`, `managers.js`, `viewUsers.js`

### `BACKEND`

This folder houses the server-side logic and API for the application.

* **`config/`**:
    * `config.php`: Loads environment variables.
    * `database.php`: Contains the function to establish a database connection.
* **`docs/`**:
    * `db_structure.sql`: SQL script defining the database schema.
    * `sql_optimisation.sql`: SQL script for database optimisation examples.
* **`productGeneration/`**:
    * `APItoJson.py`: A Python script to fetch book data from the Google Books API and save it as a JSON file.
    * `jsonTOSQL.py`: A Python script to convert the JSON book data into SQL INSERT statements.
* **`pricesGeneration/`**:
    * `generate_ratings.php`: PHP script to generate sample rating data.
    * `generate_reviews_and_ratings.php`: PHP script to generate sample review and rating data.
    * `generate_store_info.php`: PHP script to generate sample store pricing and rating information.
* **`public/`**:
    * `index.php`: The main entry point for all API requests. It routes requests to the appropriate handlers.
* **`src/`**:
    * **`handlers/`**: Contains PHP files that handle specific API actions. Examples include:
        * User management: `login_handler.php`, `register_handler.php`, `updateuserinfo_handler.php`, `getusers_handler.php`, `removeusers_handler.php`, `removeStoreAdmin_handler.php`
        * Product management: `add_product_handler.php`, `delete_product_handler.php`, `getallproducts_handler.php`, `getproduct_handler.php`, `updateProductAdmin_handler.php`, `getfeaturedproducts_handler.php`, `gethighestratedproducts_handler.php`, `getallproductsrr_handler.php`
        * Store and listing management: `AddInfoForStore_handler.php`, `delete_store_products_handler.php`, `get_store_products_handler.php`, `getstoremissingbooks_handler.php`, `getalllistedproducts_handler.php`, `addstore_handler.php`, `deletestore_handler.php`, `updatestore_handler.php`, `getstores_handler.php`, `addstoreadmin_handler.php`
        * Review and Rating management: `addUserReview_handler.php`, `addUserRating_handler.php`, `getuserreviewsratings_handler.php`, `getbookreviewsratings_handler.php`, `getuserbookrating_handler.php`, `removeUserReview_handler.php`
        * Genre management: `add_genre_handler.php`, `get_genre_handler.php`, `update_genre_visibility_handler.php`
        * Utility/Summary: `websitesummary_handler.php`
    * **`utils/`**:
        * `auth_utils.php`: Provides functions for user authentication and authorisation.
        * `response_utils.php`: Includes a function to standardise API JSON responses.
        * `sanitise_utils.php`: Contains functions for sanitising user input.
        * `userid_utils.php`: Utility for retrieving user ID based on API key.

## Page Descriptions


* **Launch Page (`launch.php`)**:
    The launch page is the first page users will see before and after logging in. This page contains useful statistics accessible to all users. A button below the statistics leads users to statistics specific to stores. The header at the top of the page will be used for navigation.

* **Login Page (`login.php`)**:
    The login page allows users to enter their email and password to log in to the website. Access to certain web pages will depend on their user type.

* **Register Page (`register.php`)**:
    The register page allows new users to register an account to gain access to the website and all its features. Only regular users will make use of this page, as store admins need to be registered via the super manager management page. This extends the database.

* **Header (`header.php`)**:
    The header acts as the navigation bar for all user types.
    * Regular users will see: Home, Dashboard, Products, and Profile buttons.
    * Admins and the Super Admin will see an additional button: Control Panel.

* **Specific Store Statistics (`stores_stats.php`)**:
    All user types can access this page. It displays the average price and rating of all books in a store, for all stores in the database.

* **Dashboard Page (`dashboard.php`)**:
    This page contains featured books and the highest-rated books. Clicking on a book card will lead users to the view page for that specific product.

* **Products Page (`products.php`)**:
    Similar in design to the dashboard, but this page contains all products. These products can be sorted and filtered by name, author, rating, and/or genre. Searching for a book by its title is also possible. Clicking on a book card will lead users to the view page for that specific product.

* **View Page (`view.php`)**:
    The view page allows both admins and users to look at a specific book, all its details, and the prices at various stores. It displays prices in order from cheapest to most expensive. Users are also able to view all the ratings and reviews of a book.
    * Only users are able to give a review or rating for a book. They are limited to one review and rating per book, however, they are able to replace their existing ones.
    * This page allows users to rate and/or review products.
    * This page allows all different types of users to view a product, and display an image of the product, the list of prices at different retailers (stockists), ratings, and reviews.

* **Control Panel (`SuperMPanel.php` and related admin pages)**:
    The control panel is only visible to Admins and the Super Admin.
    * **Store Admins** have the option to navigate to their store page (`managers.php`), add a book to their store (`addProducts.php`, using `AddInfoForStore_handler.php` for existing books or `add_product_handler.php` for new books to the system), and manage books in their store (`editProducts.php`).
    * **Super Admins** have broader privileges detailed under "Edit Books" and "Management Page".

* **Edit Books Page (`editProducts.php`)**:
    Accessible by Admins and the Super Admin via the control panel. This page’s functionality changes depending on the type of admin logged in. Admins can view books not yet in their store and add them.
    * **Super Admin privileges**:
        * Deleting books from all stores (system-wide via `delete_product_handler.php`) or specific store listings (`delete_store_products_handler.php`).
        * Editing the information of all books from all stores (`updateProductAdmin_handler.php`).
    * **Store Admin privileges**:
        * Deleting books from their store (`delete_store_products_handler.php`).
        * Editing the price and the rating of books in their store (`AddInfoForStore_handler.php`).

* **Management Page (`managers.php`, `genres.php`, `viewUsers.php`)**:
    * **Super Admin**: This page allows the super manager to add and delete both stores (`addstore_handler.php`, `deletestore_handler.php`) and admins for specific stores (`addstoreadmin_handler.php`, `removeStoreAdmin_handler.php`), and see the store information of all stores (`getstores_handler.php`). They can also manage genres (add/update visibility via `add_genre_handler.php`, `update_genre_visibility_handler.php`, view via `get_genre_handler.php` on `genres.php`) and users (view/remove via `getusers_handler.php`, `removeusers_handler.php` on `viewUsers.php`). This page meets the criteria to manage stockists. This updates, extends, and deletes from the database.
    * **Store Admins**: Managers are able to view their specific store and edit its information (`updatestore_handler.php`) via their control panel options leading to `managers.php`.

* **Profile Page (`profile.php`)**:
    This page allows Admins, the Super Admin, and Users to change their personal information (`updateuserinfo_handler.php`). It also allows users to review their past reviews and ratings (`getuserreviewsratings_handler.php`) and change them (`addUserReview_handler.php`, `addUserRating_handler.php`, `removeUserReview_handler.php`). This allows users to rate and/or review products. This would update the database tables.

---

## Functionality

The application provides different functionalities based on user roles:

### Customers are able to:
* Sign up and log in.
* View the dashboard.
* View products and filter and sort them by various filters including categories and more.
* Rate and review books and see others’ ratings and reviews on various books.
* View and edit their profile, past ratings, and reviews.

### Store Admins are able to:
* Log in.
* View the dashboard and products page.
* View and change their profile information.
* View and change their store information.
* Add new books to the system (if not existing) or add their own store’s price for existing books.
* Edit their store’s price and rating for a book.
* View books listed in the system that are not yet in their store's inventory and add them.

### Super Admin is able to:
* Log in.
* View the dashboard and products page.
* View and change their profile information.
* Add, delete, and manage stores and their admins.
* View and remove users (including store admins).
* Add and delete books from the system or specific stores.
* Edit book information across all stores.
* Manage genres (add, update visibility, view).

---

## Default Logins

For testing and demonstration purposes, the following default accounts are available:

* **Super admin**:
    * Email: `mqga@gmail.com`
    * Password: `Admin123!`
* **Store admin**:
    * Email: `mike@gmail.com`
    * Password: `mikePass!1`
* **Normal user**:
    * Email: `cailin@gmail.com`
    * Password: `CaiPas123@`

---


