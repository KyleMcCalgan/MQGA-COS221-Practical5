<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleGetStores')) {
    function handleGetStores($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $storeId = $data['store_id'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        if (checkAuth($apiKey, 'regular', $db)) {
            apiResponse(false, null, 'Access restricted to admin or super users.', 403);
        }

        if (!checkAuth($apiKey, 'admin', $db) && !checkAuth($apiKey, 'super', $db)) {
            apiResponse(false, null, 'Invalid or unauthorized API key.', 401);
        }

        if (checkAuth($apiKey, 'admin', $db)) {
            $query = "SELECT S.store_id, S.name, S.logo, S.domain, S.type 
                      FROM STORES S 
                      JOIN ADMINS A ON S.store_id = A.store_id 
                      JOIN USERS U ON A.id = U.id 
                      WHERE U.apikey = ?";
            $stmt = $db->prepare($query);
            if (!$stmt) {
                apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
            }
            $stmt->bind_param("s", $apiKey);
            if (!$stmt->execute()) {
                apiResponse(false, null, 'Database error: Query execution failed.', 500);
            }
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                apiResponse(false, null, 'No store associated with this admin.', 404);
            }
            $store = $result->fetch_assoc();
            $stmt->close();

            apiResponse(true, $store, null, 200);
        } else {
            if ($storeId !== null) {
                if (!is_numeric($storeId)) {
                    apiResponse(false, null, 'Valid store_id is required.', 400);
                }

                $query = "SELECT store_id, name, logo, domain, type FROM STORES WHERE store_id = ?";
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
                $store = $result->fetch_assoc();
                $stmt->close();

                apiResponse(true, $store, null, 200);
            } else {
                $query = "SELECT store_id, name, logo, domain, type FROM STORES";
                $stmt = $db->prepare($query);
                if (!$stmt) {
                    apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
                }
                if (!$stmt->execute()) {
                    apiResponse(false, null, 'Database error: Query execution failed.', 500);
                }
                $result = $stmt->get_result();
                $stores = [];
                while ($row = $result->fetch_assoc()) {
                    $stores[] = $row;
                }
                $stmt->close();

                apiResponse(true, $stores, null, 200);
            }
        }
    }
}
?>