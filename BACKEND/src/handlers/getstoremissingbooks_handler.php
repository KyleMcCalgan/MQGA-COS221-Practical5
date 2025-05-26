<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleGetStoreMissingBooks')) {
    function handleGetStoreMissingBooks($data, $db) {
        $apiKey = isset($data['api_key']) ? sanitiseInput($data['api_key']) : null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 400);
        }

        if (!checkAuth($apiKey, 'admin', $db)) {
            apiResponse(false, null, 'Access restricted to admin users.', 403);
        }

        $query = "SELECT id FROM USERS WHERE apikey = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get store missing books - select user): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("s", $apiKey);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get store missing books - select user): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            apiResponse(false, null, 'User not found.', 401);
        }
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        $stmt->close();

        $query = "SELECT store_id FROM ADMINS WHERE id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get store missing books - select admin): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get store missing books - select admin): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            apiResponse(false, null, 'Admin is not associated with any store.', 403);
        }
        $admin = $result->fetch_assoc();
        $storeId = $admin['store_id'];
        $stmt->close();

        $query = "SELECT p.id, p.title, p.author, p.smallThumbnail, p.thumbnail
                  FROM PRODUCTS p
                  LEFT JOIN STORE_INFO si ON p.id = si.book_id AND si.store_id = ?
                  WHERE si.book_id IS NULL";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (get store missing books - select products): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("i", $storeId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (get store missing books - select products): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();

        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'author' => $row['author'],
                'smallThumbnail' => $row['smallThumbnail'],
                'thumbnail' => $row['thumbnail']
            ];
        }
        $stmt->close();

        if (empty($books)) {
            apiResponse(true, [], 'No books found without price or rating for your store.', 200);
        } else {
            apiResponse(true, $books, null, 200);
        }
    }
}
?>