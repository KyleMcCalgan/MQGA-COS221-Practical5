<?php
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleWebsiteSummary')) {
    function handleWebsiteSummary($data, $db) {
        $query = "SELECT 
                     (SELECT COUNT(store_id) FROM STORES) AS total_stores,
                     (SELECT COUNT(id) FROM PRODUCTS) AS total_books,
                     (SELECT COUNT(*) FROM USERS WHERE user_type = 'regular') AS total_regular_users,
                     (SELECT COUNT(*) FROM REVIEWS) AS total_reviews,
                     (SELECT COUNT(*) FROM RATINGS) AS total_ratings,
                     (SELECT COUNT(*) FROM STORE_INFO) AS total_prices,
                     (SELECT ROUND(MIN(price), 2) FROM STORE_INFO WHERE price > 0) AS cheapest_price,
                     (SELECT ROUND(MAX(price), 2) FROM STORE_INFO WHERE price > 0) AS most_expensive_price,
                     (SELECT ROUND(AVG(rating), 2) FROM RATINGS) AS average_rating,
                     (SELECT ROUND(AVG(price), 2) FROM STORE_INFO WHERE price > 0) AS average_price,
                     (SELECT COUNT(*) FROM CATEGORIES) AS total_categories";
        
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (website summary): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }

        if (!$stmt->execute()) {
            error_log("Database execute failed (website summary): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }

        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        
        $responseData = [
            'total_stores' => (int)$stats['total_stores'],
            'total_books' => (int)$stats['total_books'],
            'total_regular_users' => (int)$stats['total_regular_users'],
            'total_reviews' => (int)$stats['total_reviews'],
            'total_ratings' => (int)$stats['total_ratings'],
            'total_prices' => (int)$stats['total_prices'],
            'cheapest_price' => $stats['cheapest_price'] !== null ? number_format($stats['cheapest_price'], 2) : null,
            'most_expensive_price' => $stats['most_expensive_price'] !== null ? number_format($stats['most_expensive_price'], 2) : null,
            'average_rating' => $stats['average_rating'] !== null ? number_format($stats['average_rating'], 2) : null,
            'average_price' => $stats['average_price'] !== null ? number_format($stats['average_price'], 2) : null,
            'total_categories' => (int)$stats['total_categories']
        ];

        $stmt->close();

        apiResponse(true, $responseData, null, 200);
    }
}
?>