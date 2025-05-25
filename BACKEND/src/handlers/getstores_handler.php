<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('_getAdminsForStore')) {
    function _getAdminsForStore($storeId, $db) {
        $admins = [];
        $adminQuery = "SELECT U.id, U.name, U.surname, U.email 
                       FROM USERS U 
                       JOIN ADMINS A ON U.id = A.id 
                       WHERE A.store_id = ?";
        $stmt = $db->prepare($adminQuery);
        if (!$stmt) {
            error_log("Database error (get admins for store " . $storeId . "): Unable to prepare query. " . $db->error);
            return $admins;
        }
        $stmt->bind_param("i", $storeId);
        if (!$stmt->execute()) {
            error_log("Database error (get admins for store " . $storeId . "): Query execution failed. " . $stmt->error);
            $stmt->close();
            return $admins;
        }
        $result = $stmt->get_result();
        while ($adminRow = $result->fetch_assoc()) {
            $admins[] = [
                'id' => (int)$adminRow['id'],
                'name' => trim(($adminRow['name'] ?? '') . ' ' . ($adminRow['surname'] ?? '')),
                'email' => $adminRow['email']
            ];
        }
        $stmt->close();
        return $admins;
    }
}

if (!function_exists('handleGetStores')) {
    function handleGetStores($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $storeIdInput = $data['store_id'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        $isRegularUser = checkAuth($apiKey, 'regular', $db);
        $isAdminUser = checkAuth($apiKey, 'admin', $db);
        $isSuperUser = checkAuth($apiKey, 'super', $db);

        if ($isRegularUser) {
            apiResponse(false, null, 'Access restricted to admin or super users.', 403);
        }

        if (!$isAdminUser && !$isSuperUser) {
            apiResponse(false, null, 'Invalid or Unauthorised API key.', 401);
        }

        if ($isAdminUser) {
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
                $stmt->close();
                apiResponse(false, null, 'Database error: Query execution failed.', 500);
            }
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                apiResponse(false, null, 'No store associated with this admin.', 404);
            }
            $store = $result->fetch_assoc();
            $stmt->close();

            if ($store && isset($store['store_id'])) {
                $store['admins'] = _getAdminsForStore((int)$store['store_id'], $db);
            } else {
                $store['admins'] = [];
            }
            
            apiResponse(true, $store, null, 200);

        } else { 
            if ($storeIdInput !== null) {
                if (!is_numeric($storeIdInput)) {
                    apiResponse(false, null, 'Valid store_id is required.', 400);
                }
                $storeIdInput = (int)$storeIdInput;

                $query = "SELECT store_id, name, logo, domain, type FROM STORES WHERE store_id = ?";
                $stmt = $db->prepare($query);
                if (!$stmt) {
                    apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
                }
                $stmt->bind_param("i", $storeIdInput);
                if (!$stmt->execute()) {
                    $stmt->close();
                    apiResponse(false, null, 'Database error: Query execution failed.', 500);
                }
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    $stmt->close();
                    apiResponse(false, null, 'Store not found.', 404);
                }
                $store = $result->fetch_assoc();
                $stmt->close();

                if ($store && isset($store['store_id'])) {
                     $store['admins'] = _getAdminsForStore((int)$store['store_id'], $db);
                } else {
                    $store['admins'] = [];
                }

                apiResponse(true, $store, null, 200);
            } else {
                $query = "SELECT store_id, name, logo, domain, type FROM STORES ORDER BY store_id ASC";
                $stmt = $db->prepare($query);
                if (!$stmt) {
                    apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
                }
                if (!$stmt->execute()) {
                    $stmt->close();
                    apiResponse(false, null, 'Database error: Query execution failed.', 500);
                }
                $result = $stmt->get_result();
                $stores = [];
                while ($row = $result->fetch_assoc()) {
                    $currentStoreId = (int)$row['store_id'];
                    $row['admins'] = _getAdminsForStore($currentStoreId, $db);
                    $stores[] = $row;
                }
                $stmt->close();

                apiResponse(true, $stores, null, 200);
            }
        }
    }
}
?>