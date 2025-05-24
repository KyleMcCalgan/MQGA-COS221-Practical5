<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleDeleteStore')) {
    function handleDeleteStore($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $storeId = $data['store_id'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        if (!checkAuth($apiKey, 'super', $db)) {
            apiResponse(false, null, 'Access restricted to super users.', 403);
        }

        if (empty($storeId) || !is_numeric($storeId)) {
            apiResponse(false, null, 'Valid store_id is required.', 400);
        }

        $query = "SELECT store_id FROM STORES WHERE store_id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (delete store - select store): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("i", $storeId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (delete store - select store): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            apiResponse(false, null, 'Store not found.', 404);
        }
        $stmt->close();

        $db->begin_transaction();

        $query = "DELETE FROM USERS WHERE id IN (SELECT id FROM ADMINS WHERE store_id = ?)";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (delete store - delete users): " . $db->error);
            $db->rollback();
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("i", $storeId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (delete store - delete users): " . $stmt->error);
            $db->rollback();
            $stmt->close();
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->close();

        $query = "DELETE FROM STORES WHERE store_id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (delete store - delete store): " . $db->error);
            $db->rollback();
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("i", $storeId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (delete store - delete store): " . $stmt->error);
            $db->rollback();
            $stmt->close();
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->close();

        $db->commit();

        apiResponse(true, ['message' => 'Store and associated admin users deleted successfully.'], null, 200);
    }
}
?>