<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleGetBookReviewsRatings')) {
    function handleGetBookReviewsRatings($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $bookId = $data['book_id'] ?? null;
        $sort = isset($data['sort']) ? strtolower(sanitiseInput($data['sort'])) : 'newest';

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        $query = "SELECT id FROM USERS WHERE apikey = ? LIMIT 1";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get book reviews - select user): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("s", $apiKey);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get book reviews - select user): " . $stmt->error);
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

        $query = "SELECT id FROM PRODUCTS WHERE id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get book reviews - select product): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("i", $bookId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get book reviews - select product): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            apiResponse(false, null, 'Book not found.', 404);
        }
        $stmt->close();

        $query = "SELECT R.id AS review_id, 
                         COALESCE(CONCAT(U.name, ' ', U.surname), U.email) AS user_name, 
                         R.review, 
                         RA.rating 
                  FROM REVIEWS R 
                  JOIN USERS U ON R.user_id = U.id 
                  LEFT JOIN RATINGS RA ON R.book_id = RA.book_id AND R.user_id = RA.user_id 
                  WHERE R.book_id = ?";
        $params = [$bookId];
        $types = 'i';

        $sortOptions = [
            'newest' => 'R.id DESC',
            'oldest' => 'R.id ASC',
            'highest rating' => 'RA.rating DESC, R.id DESC',
            'lowest rating' => 'RA.rating ASC, R.id DESC'
        ];
        $sortOrder = $sortOptions['newest'];
        if (array_key_exists($sort, $sortOptions)) {
            $sortOrder = $sortOptions[$sort];
        }
        $query .= " ORDER BY $sortOrder";

        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get book reviews - select reviews): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get book reviews - select reviews): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = [
                'review_id' => $row['review_id'],
                'user_name' => $row['user_name'],
                'review' => $row['review'],
                'rating' => $row['rating'] ? number_format($row['rating'], 2) : null
            ];
        }
        $stmt->close();

        $query = "SELECT (SELECT COUNT(*) FROM REVIEWS WHERE book_id = ?) AS number_of_reviews, 
                         COUNT(rating) AS number_of_ratings, 
                         AVG(rating) AS average_rating 
                  FROM RATINGS 
                  WHERE book_id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get book reviews - stats): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("ii", $bookId, $bookId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get book reviews - stats): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        $stats['number_of_reviews'] = (int)$stats['number_of_reviews'];
        $stats['number_of_ratings'] = (int)$stats['number_of_ratings'];
        $stats['average_rating'] = $stats['average_rating'] ? number_format($stats['average_rating'], 2) : null;
        $stmt->close();

        $responseData = [
            'reviews' => $reviews,
            'stats' => $stats
        ];

        apiResponse(true, $responseData, null, 200);
    }
}
?>