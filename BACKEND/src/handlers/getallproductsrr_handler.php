<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleGetAllProductsRR')) {
    function handleGetAllProductsRR($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $title = $data['title'] ?? null;
        $category = $data['category'] ?? null;
        $sort = filter_var($data['sort'] ?? false, FILTER_VALIDATE_BOOLEAN);

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


        $query = "SELECT P.id, P.title, P.description, P.isbn13, P.publishedDate, P.publisher, P.author, P.pageCount, P.maturityRating, P.language, P.smallThumbnail, P.thumbnail, P.accessibleIn, P.ratingsCount, AVG(R.rating) as book_rating 
                  FROM PRODUCTS P 
                  INNER JOIN RATINGS R ON P.id = R.book_id 
                  INNER JOIN REVIEWS RV ON P.id = RV.book_id";
        $params = [];
        $types = '';

        if ($category) {
            $query .= " JOIN BOOK_CATS BC ON P.id = BC.book_id JOIN CATEGORIES C ON BC.category_id = C.category_id WHERE C.genre = ?";
            $params[] = $category;
            $types .= 's';
        } else {
            $query .= " WHERE 1=1";
        }

        if ($title) {
            $query .= " AND P.title LIKE ?";
            $params[] = "%$title%";
            $types .= 's';
        }

        $query .= " GROUP BY P.id";

        if ($sort) {
            $query .= " ORDER BY P.title";
        }

        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: Query execution failed.', 500);
        }

        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $row['book_rating'] = $row['book_rating'] ? number_format($row['book_rating'], 2) : null;
            $products[] = $row;
        }

        $stmt->close();

        apiResponse(true, $products, null, 200);
    }
}
?>