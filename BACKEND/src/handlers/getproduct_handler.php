<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleGetProduct')) {
    function handleGetProduct($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $bookId = $data['book_id'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        $query = "SELECT id FROM USERS WHERE apikey = ? LIMIT 1";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get all products - select user): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("s", $apiKey);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get all products - select user): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            apiResponse(false, null, 'Invalid or Unauthorised API key.', 401);
        }
        $stmt->close();


        if (empty($bookId) || !is_numeric($bookId)) {
            apiResponse(false, null, 'Valid book_id is required.', 400);
        }

        

        $query = "SELECT P.id, P.title, P.description, P.isbn13, P.publishedDate, P.publisher, P.author, P.pageCount, P.maturityRating, P.language, P.smallThumbnail, P.thumbnail, P.accessibleIn, P.ratingsCount, AVG(R.rating) as book_rating 
                  FROM PRODUCTS P 
                  LEFT JOIN RATINGS R ON P.id = R.book_id 
                  WHERE P.id = ? 
                  GROUP BY P.id";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare product query.', 500);
        }
        $stmt->bind_param("i", $bookId);
        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: Product query execution failed.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            apiResponse(false, null, 'Product not found.', 404);
        }
        $product = $result->fetch_assoc();
        $product['book_rating'] = $product['book_rating'] ? number_format($product['book_rating'], 2) : null;
        $stmt->close();

        $query = "SELECT C.genre 
                  FROM CATEGORIES C 
                  JOIN BOOK_CATS BC ON C.category_id = BC.category_id 
                  WHERE BC.book_id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare categories query.', 500);
        }
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['genre'];
        }
        $stmt->close();

        $query = "SELECT S.name, SI.price, SI.rating 
                  FROM STORES S 
                  JOIN STORE_INFO SI ON S.store_id = SI.store_id 
                  WHERE SI.book_id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare stores query.', 500);
        }
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stores = [];
        while ($row = $result->fetch_assoc()) {
            $row['price'] = number_format($row['price'], 2);
            $row['rating'] = $row['rating'] ? number_format($row['rating'], 2) : null;
            $stores[] = $row;
        }
        $stmt->close();

        $product['categories'] = $categories;
        $product['stores'] = $stores;

        apiResponse(true, $product, null, 200);
    }
}
?>