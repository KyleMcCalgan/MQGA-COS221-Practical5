<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleAddStoreAdmin')) {
    function handleAddStoreAdmin($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $storeId = $data['store_id'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        if (!checkAuth($apiKey, 'super', $db)) {
            apiResponse(false, null, 'Access restricted to super users.', 403);
        }

        if (empty($email)) {
            apiResponse(false, null, 'Email is required.', 400);
        }

        if (empty($password)) {
            apiResponse(false, null, 'Password is required.', 400);
        }

        if (empty($storeId) || !is_numeric($storeId)) {
            apiResponse(false, null, 'Valid store_id is required.', 400);
        }

        $query = "SELECT store_id FROM STORES WHERE store_id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (add store admin - select store): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("i", $storeId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (add store admin - select store): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            apiResponse(false, null, 'Store not found.', 404);
        }
        $stmt->close();

        $query = "SELECT id FROM USERS WHERE email = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (add store admin - select email): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            error_log("Database execute failed (add store admin - select email): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            apiResponse(false, null, 'Email already in use.', 400);
        }
        $stmt->close();

        $userSalt = bin2hex(random_bytes(32));
        $passwordWithSalt = $userSalt . $password;
        $hashedPassword = password_hash($passwordWithSalt, PASSWORD_DEFAULT);
        $newApiKey = bin2hex(random_bytes(32));
        $userType = 'admin';
        $name = "Default";
        $surname = "Default";

        $query = "INSERT INTO USERS (apikey, name, surname, email, password, salt, user_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (add store admin - insert user): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("sssssss", $newApiKey, $name, $surname, $email, $hashedPassword, $userSalt, $userType);
        if (!$stmt->execute()) {
            error_log("Database execute failed (add store admin - insert user): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $userId = $stmt->insert_id;
        $stmt->close();

        $query = "INSERT INTO ADMINS (id, store_id) VALUES (?, ?)";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            error_log("Database prepare statement failed (add store admin - insert admin): " . $db->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->bind_param("ii", $userId, $storeId);
        if (!$stmt->execute()) {
            error_log("Database execute failed (add store admin - insert admin): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        }
        $stmt->close();

        apiResponse(true, ['message' => 'Admin user added successfully.'], null, 200);
    }
}
?>