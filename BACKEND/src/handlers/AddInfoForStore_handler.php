<?php

require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleAddInfoForStore')) {//marcel
    function handleAddInfoForStore($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $bookIdInput = $data['book_id'] ?? null;
        $storeIdInput = $data['store_id'] ?? null;
        $priceInput = $data['Price'] ?? null;
        $ratingInput = $data['Rating'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'Authentication failed.', 401);
            return;
        }

        if ($bookIdInput === null || $storeIdInput === null || $priceInput === null || $ratingInput === null ||
            !is_numeric($bookIdInput) || !is_numeric($storeIdInput) || !is_numeric($priceInput) || !is_numeric($ratingInput)) {
            apiResponse(false, null, 'Invalid request data: ensure all fields are present and numeric where expected.', 400);
            return;
        }

        $bookId = (int)$bookIdInput;
        $storeId = (int)$storeIdInput;
        $price = floatval($priceInput);
        $rating = floatval($ratingInput);

        if ($bookId <= 0 || $storeId <= 0 || $price < 0 || ($rating < 0 || $rating > 9.99)) {
            apiResponse(false, null, 'Invalid request data: values out of allowed range.', 400);
            return;
        }

        if (!checkAuth($apiKey, 'admin', $db)) {
            apiResponse(false, null, 'Authorisation failed.', 403);
            return;
        }

        $userId = null;
        $stmtUser = $db->prepare("SELECT id FROM USERS WHERE apikey = ?");
        $stmtUser->bind_param("s", $apiKey);
        if (!$stmtUser->execute()) {
            error_log("DB Execute Error (USERS select): " . $stmtUser->error);
            apiResponse(false, null, 'A server error occurred.', 500);
            $stmtUser->close();
            return;
        }
        $resultUser = $stmtUser->get_result();
        if ($rowUser = $resultUser->fetch_assoc()) {
            $userId = (int)$rowUser['id'];
        }
        $stmtUser->close();

        if ($userId === null) {
            apiResponse(false, null, 'Authorisation failed.', 403);
            return;
        }

        $checkAUth = false;
    
        $stmtAuthStore = $db->prepare("SELECT id FROM ADMINS WHERE id = ? AND store_id = ?");
        if (!$stmtAuthStore) {
            error_log("DB Prepare Error (ADMINS select): " . $db->error);
            apiResponse(false, null, 'A server error occurred.', 500);
            return;
        }
        $stmtAuthStore->bind_param("ii", $userId, $storeId);
        if (!$stmtAuthStore->execute()) {
            error_log("DB Execute Error (ADMINS select): " . $stmtAuthStore->error);
            apiResponse(false, null, 'A server error occurred.', 500);
            $stmtAuthStore->close();
            return;
        }
        $resultAuthStore = $stmtAuthStore->get_result();
        if ($resultAuthStore->num_rows > 0) {
            $checkAUth = true;
        }
        $stmtAuthStore->close();

        if (!$checkAUth) {
            apiResponse(false, null, 'Authorisation failed.', 403);
            return;
        }

        $storeItemsTable = 'STORE_INFO';

        $stmtCheck = $db->prepare("SELECT book_id FROM $storeItemsTable WHERE book_id = ? AND store_id = ?");
        $stmtCheck->bind_param("ii", $bookId, $storeId);

        if (!$stmtCheck->execute()) { 
            error_log("DB Execute Error ($storeItemsTable select check): " . $stmtCheck->error);
            apiResponse(false, null, "A server error occurred.", 500);
            $stmtCheck->close(); 
            return;
        }
        $resultCheck = $stmtCheck->get_result();
        $entryExists = $resultCheck->num_rows > 0;
        $stmtCheck->close();

        $successMessage = "Book added to store successfully"; 
        $stmtUpsert = null;

        if ($entryExists) {
            $stmtUpsert = $db->prepare("UPDATE $storeItemsTable SET price = ?, rating = ? WHERE book_id = ? AND store_id = ?");
            if ($stmtUpsert) {
                $stmtUpsert->bind_param("ddii", $price, $rating, $bookId, $storeId);
            }
        } else {
            $stmtUpsert = $db->prepare("INSERT INTO $storeItemsTable (book_id, store_id, price, rating) VALUES (?, ?, ?, ?)");
            if ($stmtUpsert) {
                $stmtUpsert->bind_param("iidd", $bookId, $storeId, $price, $rating);
            }
        }

        if (!$stmtUpsert) {
            error_log("DB Prepare Error ($storeItemsTable upsert): " . $db->error);
            apiResponse(false, null, "A server error occurred.", 500);
            return;
        }

        if ($stmtUpsert->execute()) {
            apiResponse(true, $successMessage, null, 200);
        } else {
            error_log("DB Upsert Error on $storeItemsTable: " . $stmtUpsert->error);
            apiResponse(false, null, "A server error occurred.", 500);
        }
        $stmtUpsert->close();
    }
}
?>