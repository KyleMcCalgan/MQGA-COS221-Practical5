<?php

require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/userid_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';
require_once __DIR__ . '/../utils/response_utils.php';

if (!function_exists('handleAddUserRating')) {
    function handleAddUserRating($data, $db) {
        $apiKey = isset($data['apikey']) ? sanitiseInput($data['apikey']) : null;
        $bookIdInput = isset($data['book_id']) ? sanitiseInput($data['book_id']) : null;
        $ratingInput = isset($data['rating']) ? sanitiseInput($data['rating']) : null;

        if (empty($apiKey) || $bookIdInput === null || $ratingInput === null) {
            apiResponse(false, null, 'API key, book ID, and rating are required.', 400);
            return;
        }

        if (!is_numeric($bookIdInput) || !is_numeric($ratingInput)) {
            apiResponse(false, null, 'Book ID and rating must be numeric.', 400);
            return;
        }

        $bookId = (int)$bookIdInput;
        $rating = (int)$ratingInput;

        if ($rating < 1 || $rating > 5) {
            apiResponse(false, null, 'Invalid rating value. Must be between 1 and 5.', 400);
            return;
        }

        if (!checkAuth($apiKey, 'regular', $db)) {
            apiResponse(false, null, 'Authorisation failed: Invalid API key or user type.', 401);
            return;
        }

        $userId = getUserIdUtil($apiKey, $db);
        if ($userId === null) {
            apiResponse(false, null, 'Authorisation failed: User not found.', 401);
            return;
        }

        $db->begin_transaction();

        try {
            $stmtRatings = $db->prepare("INSERT INTO RATINGS (user_id, book_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating)");
            if (!$stmtRatings) {
                throw new Exception("DB Prepare Error (RATINGS upsert): " . $db->error);
            }

            $stmtRatings->bind_param("iii", $userId, $bookId, $rating);

            if (!$stmtRatings->execute()) {
                throw new Exception("DB Execute Error (RATINGS upsert): " . $stmtRatings->error);
            }

            $affectedRowsRatings = $stmtRatings->affected_rows;
            $stmtRatings->close();

            if ($affectedRowsRatings === 1) {
                $stmtProducts = $db->prepare("UPDATE PRODUCTS SET ratingsCount = ratingsCount + 1 WHERE id = ?");
                if (!$stmtProducts) {
                    throw new Exception("DB Prepare Error (PRODUCTS update ratingsCount): " . $db->error);
                }
                $stmtProducts->bind_param("i", $bookId);
                if (!$stmtProducts->execute()) {
                    throw new Exception("DB Execute Error (PRODUCTS update ratingsCount): " . $stmtProducts->error);
                }
                if ($stmtProducts->affected_rows === 0) {
                    error_log("Warning: ratingsCount for book_id {$bookId} was not incremented as product was not found.");
                }
                $stmtProducts->close();
            }

            $db->commit();
            apiResponse(true, "Rating added/updated successfully.", null, 200);

        } catch (Exception $e) {
            $db->rollback();
            error_log($e->getMessage());
            apiResponse(false, null, 'A server error occurred while processing the rating.', 500);
        }
    }
}
?>