<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleGetHighestRatedProducts')) {
    function handleGetHighestRatedProducts($data, $db) {
        $apiKey = $data['api_key'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        // if (!checkAuth($apiKey, 'regular', $db)) {
        //     apiResponse(false, null, 'Invalid or unauthorized API key.', 401);
        // }

        $query = "SELECT P.id, P.title, P.description, P.isbn13, P.publishedDate, P.publisher, P.author, P.pageCount, P.maturityRating, P.language, P.smallThumbnail, P.thumbnail, P.accessibleIn, P.ratingsCount, AVG(R.rating) as book_rating 
                  FROM PRODUCTS P 
                  JOIN RATINGS R ON P.id = R.book_id 
                  GROUP BY P.id 
                  ORDER BY book_rating DESC 
                  LIMIT 5";

        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
        }

        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: Query execution failed.', 500);
        }

        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $row['book_rating'] = number_format($row['book_rating'], 2);
            $products[] = $row;
        }

        $stmt->close();

        apiResponse(true, $products, null, 200);
    }
}
?>