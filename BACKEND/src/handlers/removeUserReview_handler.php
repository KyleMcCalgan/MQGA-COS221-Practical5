<?php

require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/userid_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';
require_once __DIR__ . '/../utils/response_utils.php';

if (!function_exists('handleRemoveUserReview')) {
    function handleRemoveUserReview($data, $db) {
        $apiKey = isset($data['apikey']) ? sanitizeInput($data['apikey']) : null;
        $bookIdInput = isset($data['book_id']) ? sanitizeInput($data['book_id']) : null;

        if (empty($apiKey) || $bookIdInput === null) {
            apiResponse(false, null, 'API key and book ID are required.', 400);
            return;
        }

        if (!is_numeric($bookIdInput)) {
            apiResponse(false, null, 'Invalid book ID format.', 400);
            return;
        }
        $bookId = (int)$bookIdInput;

        if (!checkAuth($apiKey, 'regular', $db)) {
            apiResponse(false, null, 'Authorisation failed: Invalid API key or user type.', 401);
            return;
        }

        $userId = getUserIdUtil($apiKey, $db);
        if ($userId === null) {
            apiResponse(false, null, 'Authorisation failed: User not found.', 401);
            return;
        }

        $stmt = $db->prepare("DELETE FROM REVIEWS WHERE user_id = ? AND book_id = ?");
        if (!$stmt) {
            error_log("DB Prepare Error (REVIEWS delete): " . $db->error);
            apiResponse(false, null, 'A server error occurred while preparing to delete review.', 500);
            return;
        }

        $stmt->bind_param("ii", $userId, $bookId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                apiResponse(true, "Review removed successfully.", null, 200);
            } else {
                apiResponse(false, null, 'Review not found for this user and book, or already removed.', 404);
            }
        } else {
            error_log("DB Execute Error (REVIEWS delete): " . $stmt->error);
            apiResponse(false, null, 'A server error occurred while deleting review.', 500);
        }
        $stmt->close();
    }
}
?>