<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleUpdateStore')) {
    function handleUpdateStore($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $name = $data['name'] ?? null;
        $domain = $data['domain'] ?? null;
        $logo = $data['logo'] ?? null;
        $storeType = $data['storetype'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        if (!checkAuth($apiKey, 'admin', $db)) {
            apiResponse(false, null, 'Access restricted to admin users.', 403);
        }

        $query = "SELECT S.store_id 
                  FROM STORES S 
                  JOIN ADMINS A ON S.store_id = A.store_id 
                  JOIN USERS U ON A.id = U.id 
                  WHERE U.apikey = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare store check query.', 500);
        }
        $stmt->bind_param("s", $apiKey);
        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: Store check query failed.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            apiResponse(false, null, 'No store associated with this admin.', 404);
        }
        $store = $result->fetch_assoc();
        $storeId = $store['store_id'];
        $stmt->close();

        if (empty($name) && empty($domain) && empty($logo) && empty($storeType)) {
            apiResponse(false, null, 'At least one field (name, domain, logo, storetype) must be provided.', 400);
        }

        if (!empty($name)) {
            $query = "SELECT store_id FROM STORES WHERE name = ? AND store_id != ?";
            $stmt = $db->prepare($query);
            if (!$stmt) {
                apiResponse(false, null, 'Database error: Unable to prepare name check query.', 500);
            }
            $stmt->bind_param("si", $name, $storeId);
            if (!$stmt->execute()) {
                apiResponse(false, null, 'Database error: Name check query failed.', 500);
            }
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                apiResponse(false, null, 'Store name already in use.', 400);
            }
            $stmt->close();
        }

        $fields = [];
        $params = [];
        $types = '';

        if (!empty($name)) {
            $fields[] = "name = ?";
            $params[] = $name;
            $types .= 's';
        }
        if (!empty($domain)) {
            $fields[] = "domain = ?";
            $params[] = $domain;
            $types .= 's';
        }
        if (!empty($logo)) {
            $fields[] = "logo = ?";
            $params[] = $logo;
            $types .= 's';
        }
        if (!empty($storeType)) {
            $fields[] = "type = ?";
            $params[] = $storeType;
            $types .= 's';
        }

        $query = "UPDATE STORES SET " . implode(", ", $fields) . " WHERE store_id = ?";
        $params[] = $storeId;
        $types .= 'i';

        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare update query.', 500);
        }
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: Update operation failed.', 500);
        }
        $stmt->close();

        apiResponse(true, ['message' => 'Store updated successfully.'], null, 200);
    }
}
?>