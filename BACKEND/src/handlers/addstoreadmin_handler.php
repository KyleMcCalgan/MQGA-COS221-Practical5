<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleAddStoreAdmin')) {
    function handleAddStoreAdmin($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

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

        $query = "SELECT id FROM USERS WHERE email = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare email check query.', 500);
        }
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: Email check query failed.', 500);
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
            apiResponse(false, null, 'Database error: Unable to prepare user insertion query.', 500);
        }
        $stmt->bind_param("sssssss", $newApiKey, $name, $surname, $email, $hashedPassword, $userSalt, $userType);
        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: Failed to add admin user.', 500);
        }
        $stmt->close();

        apiResponse(true, ['message' => 'Admin user added successfully.'], null, 200);
    }
}
?>