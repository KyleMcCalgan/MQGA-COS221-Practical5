<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleGetUserBookRating')) {
    function handleGetUserBookRating($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $bookId = $data['book_id'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        if (!checkAuth($apiKey, 'regular', $db)) {
            apiResponse(false, null, 'Access restricted to regular users.', 403);
        }

        if (empty($bookId) || !is_numeric($bookId)) {
            apiResponse(false, null, 'Valid book_id is required.', 400);
        }
        
        $query = "SELECT id FROM USERS WHERE apikey = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get user book rating - select user): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("s", $apiKey);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get user book rating - select user): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        $stmt->close();

        $query = "SELECT id FROM PRODUCTS WHERE id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get user book rating - select product): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("i", $bookId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get user book rating - select product): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            apiResponse(false, null, 'Book not found.', 404);
        }
        $stmt->close();

        $query = "SELECT rating FROM RATINGS WHERE user_id = ? AND book_id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get user book rating - select rating): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("ii", $userId, $bookId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get user book rating - select rating): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        $rating = null;
        if ($row = $result->fetch_assoc()) {
            $rating = number_format($row['rating'], 2);
        }
        $stmt->close();

        apiResponse(true, ['rating' => $rating], null, 200);
    }
}
?>