<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleAddStore')) {
    function handleAddStore($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $storeName = $data['name'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        if (!checkAuth($apiKey, 'super', $db)) {
            apiResponse(false, null, 'Access restricted to super users.', 403);
        }

        if (empty($storeName)) {
            apiResponse(false, null, 'Store name is required.', 400);
        }

        $query = "INSERT INTO STORES (name) VALUES (?)";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
        }
        $stmt->bind_param("s", $storeName);
        try {
            if (!$stmt->execute()) {
                apiResponse(false, null, 'Database error: Failed to add store.', 500);
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry error
                apiResponse(false, null, 'Store name already exists.', 400);
            }
            apiResponse(false, null, 'Database error: ' . $e->getMessage(), 500);
        }
        $stmt->close();

        apiResponse(true, ['message' => 'Store added successfully.'], null, 200);
    }
}
?>