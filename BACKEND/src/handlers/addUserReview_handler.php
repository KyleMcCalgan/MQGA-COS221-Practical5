<?php

require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/userid_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';
require_once __DIR__ . '/../utils/response_utils.php';

if (!function_exists('handleAddUserReview')) {
    function handleAddUserReview($data, $db) {
        $apiKey = isset($data['apikey']) ? sanitiseInput($data['apikey']) : null;
        $bookIdInput = isset($data['book_id']) ? sanitiseInput($data['book_id']) : null;
        $reviewText = isset($data['review']) ? sanitiseInput($data['review']) : null;

        if (empty($apiKey) || $bookIdInput === null || $reviewText === null) {
            apiResponse(false, null, 'API key, book ID, and review text are required.', 400);
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

        $db->begin_transaction();

        try {
            $stmtDelete = $db->prepare("DELETE FROM REVIEWS WHERE user_id = ? AND book_id = ?");
            if (!$stmtDelete) {
                throw new Exception("Delete prepare failed: " . $db->error);
            }
            $stmtDelete->bind_param("ii", $userId, $bookId);
            if (!$stmtDelete->execute()) {
                throw new Exception("Delete execute failed: " . $stmtDelete->error);
            }
            $stmtDelete->close();

            $stmtInsert = $db->prepare("INSERT INTO REVIEWS (user_id, book_id, review) VALUES (?, ?, ?)");
            if (!$stmtInsert) {
                throw new Exception("Insert prepare failed: " . $db->error);
            }
            $stmtInsert->bind_param("iis", $userId, $bookId, $reviewText);
            if (!$stmtInsert->execute()) {
                throw new Exception("Insert execute failed: " . $stmtInsert->error);
            }
            $stmtInsert->close();

            $db->commit();
            apiResponse(true, "Review added/updated successfully.", null, 200);

        } catch (Exception $e) {
            $db->rollback();
            error_log("Transaction Error (AddUserReview): " . $e->getMessage());
            apiResponse(false, null, 'A server error occurred while processing the review.', 500);
        }
    }
}
?>