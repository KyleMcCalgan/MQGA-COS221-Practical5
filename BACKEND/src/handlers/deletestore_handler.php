<?php
require_once __DIR__ . '/../utils/auth_utils.php';

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
            apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
        }
        $stmt->bind_param("i", $storeId);
        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: Query execution failed.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            apiResponse(false, null, 'Store not found.', 404);
        }
        $stmt->close();

        $query = "DELETE FROM STORES WHERE store_id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare delete query.', 500);
        }
        $stmt->bind_param("i", $storeId);
        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: Delete operation failed.', 500);
        }
        $stmt->close();

        apiResponse(true, ['message' => 'Store deleted successfully.'], null, 200);
    }
}
?>