<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleRemoveUsers')) {
    function handleRemoveUsers($inputData, $dbConnection) {
        $apiKey = $inputData['api_key'] ?? null;
        $userId = $inputData['id'] ?? null;

        if (empty($apiKey) || empty($userId)) {
            apiResponse(false, null, 'API key and user ID are required.', 400);
        }

        if (!is_numeric($userId) || $userId <= 0) {
            apiResponse(false, null, 'Invalid user ID.', 400);
        }

        if (!checkAuth($apiKey, 'super', $dbConnection)) {
            apiResponse(false, null, 'Unauthorised: Super admin access required.', 403);
        }

        $stmt = $dbConnection->prepare("DELETE FROM USERS WHERE id = ?");
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
        }

        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            $stmt->close();
            apiResponse(false, null, 'Database error: Query execution failed.', 500);
        }

        if ($stmt->affected_rows === 0) {
            $stmt->close();
            apiResponse(false, null, 'No user found with the specified ID.', 404);
        }

        $stmt->close();
        apiResponse(true, null, 'User deleted successfully.', 200);
    }
}
?>