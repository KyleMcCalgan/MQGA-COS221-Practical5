-- Create USERS table
CREATE TABLE users (
    user_id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100),
    surname VARCHAR(100),
    password VARCHAR(255),
    email VARCHAR(100),
    salt VARCHAR(100),
    user_type VARCHAR(50),
    apikey VARCHAR(100)
);

-- Create ADMINS table
CREATE TABLE admins (
    id VARCHAR(50) PRIMARY KEY,
    store_name VARCHAR(100),
    user_id VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create PRODUCTS table with tempID as primary key
CREATE TABLE products (
    tempID INT AUTO_INCREMENT PRIMARY KEY,
    id VARCHAR(50) UNIQUE,
    title TEXT NOT NULL,  -- Changed from VARCHAR(255) to TEXT
    description TEXT,
    isbn13 VARCHAR(13),
    publishedDate VARCHAR(50),
    publisher VARCHAR(100),
    author VARCHAR(100),
    pageCount INT CHECK (pageCount IS NULL OR pageCount >= 0),
    maturityRating VARCHAR(50),
    language VARCHAR(50) NOT NULL,
    smallThumbnail VARCHAR(255),
    thumbnail VARCHAR(255),
    accessibleIn VARCHAR(100),
    ratingsCount INT CHECK (ratingsCount >= 0)
);

-- Create CATEGORIES table
CREATE TABLE categories (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100),
    searchable BOOLEAN
);

-- Create RATINGS table
CREATE TABLE ratings (
    user_id VARCHAR(50),
    book_id VARCHAR(50),
    rating INT CHECK (rating >= 1 AND rating <= 5),
    PRIMARY KEY (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES products(id)  -- This now references a UNIQUE column, not a PK
);

-- Create REVIEWS table
CREATE TABLE reviews (
    user_id VARCHAR(50),
    book_id VARCHAR(50),
    review TEXT,
    PRIMARY KEY (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES products(id)  -- This now references a UNIQUE column, not a PK
);

-- Create STORES table
CREATE TABLE stores (
    store_id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100),
    logo VARCHAR(255),
    domain VARCHAR(100),
    type VARCHAR(50)
);

-- Create product_category join table for many-to-many relationship
CREATE TABLE product_category (
    product_id VARCHAR(50),
    category_id VARCHAR(50),
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES products(id),  -- This now references a UNIQUE column, not a PK
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Create product_store join table for many-to-many relationship (SELL)
CREATE TABLE product_store (
    product_id VARCHAR(50),
    store_id VARCHAR(50),
    price DECIMAL(10,2) CHECK (price > 0),
    rating DECIMAL(3,1) CHECK (rating IS NULL OR (rating >= 0 AND rating <= 5.0)),
    PRIMARY KEY (product_id, store_id),
    FOREIGN KEY (product_id) REFERENCES products(id),  -- This now references a UNIQUE column, not a PK
    FOREIGN KEY (store_id) REFERENCES stores(store_id)
);