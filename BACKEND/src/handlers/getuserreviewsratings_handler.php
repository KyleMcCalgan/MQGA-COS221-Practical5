<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleGetUserReviewsRatings')) {
    function handleGetUserReviewsRatings($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $bookName = isset($data['book_name']) ? sanitiseInput($data['book_name']) : null;
        $sort = isset($data['sort']) ? strtolower(sanitiseInput($data['sort'])) : 'newest';

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        $query = "SELECT id FROM USERS WHERE apikey = ? LIMIT 1";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get user reviews - select user): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("s", $apiKey);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get user reviews - select user): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            apiResponse(false, null, 'User not found.', 404);
        }
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        $stmt->close();

        $query = "SELECT R.id AS review_id, P.id AS book_id, P.title AS book_name, P.author, R.review, RA.rating 
                  FROM REVIEWS R 
                  JOIN PRODUCTS P ON R.book_id = P.id 
                  LEFT JOIN RATINGS RA ON R.book_id = RA.book_id AND RA.user_id = R.user_id 
                  WHERE R.user_id = ?";
        $params = [$userId];
        $types = 'i';

        if (!empty($bookName)) {
            $query .= " AND P.title LIKE ?";
            $params[] = "%$bookName%";
            $types .= 's';
        }

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
            error_log("Database prepare statement failed (get user reviews - select reviews): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get user reviews - select reviews): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = [
                'book_id' => $row['book_id'],
                'book_name' => $row['book_name'],
                'author' => $row['author'],
                'review' => $row['review'],
                'rating' => $row['rating'] ? number_format($row['rating'], 2) : null,
                'review_id' => $row['review_id']
            ];
        }
        $stmt->close();

        $query = "SELECT COUNT(*) AS number_of_reviews, COUNT(RA.rating) AS number_of_ratings, AVG(RA.rating) AS average_rating 
                  FROM REVIEWS R 
                  LEFT JOIN RATINGS RA ON R.book_id = RA.book_id AND RA.user_id = R.user_id 
                  WHERE R.user_id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get user reviews - stats): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get user reviews - stats): " . $stmt->error);
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