<?php
require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (!function_exists('handleUpdateUserInfo')) {
    function handleUpdateUserInfo($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $name = $data['name'] ?? null;
        $surname = $data['surname'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $oldPassword = $data['old_password'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
        }

        $query = "SELECT id, email, password, salt FROM USERS WHERE apikey = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare user check query.', 500);
        }
        $stmt->bind_param("s", $apiKey);
        if (!$stmt->execute()) {
            apiResponse(false, null, 'Database error: User check query failed.', 500);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            apiResponse(false, null, 'User not found.', 404);
        }
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        $storedPassword = $user['password'];
        $storedSalt = $user['salt'];
        $stmt->close();

        if (empty($name) && empty($surname) && empty($email) && empty($password)) {
            apiResponse(false, null, 'At least one field (name, surname, email, password) must be provided.', 400);
        }

        if (!empty($password) && empty($oldPassword)) {
            apiResponse(false, null, 'Old password is required when updating password.', 400);
        }

        if (!empty($password)) {
            $passwordWithSalt = $storedSalt . $oldPassword;
            if (!password_verify($passwordWithSalt, $storedPassword)) {
                apiResponse(false, null, 'Incorrect old password.', 401);
            }
        }

        if (!empty($email)) {
            $query = "SELECT id FROM USERS WHERE email = ? AND id != ?";
            $stmt = $db->prepare($query);
            if (!$stmt) {
                apiResponse(false, null, 'Database error: Unable to prepare email check query.', 500);
            }
            $stmt->bind_param("si", $email, $userId);
            if (!$stmt->execute()) {
                apiResponse(false, null, 'Database error: Email check query failed.', 500);
            }
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                apiResponse(false, null, 'Email already in use.', 400);
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
        if (!empty($surname)) {
            $fields[] = "surname = ?";
            $params[] = $surname;
            $types .= 's';
        }
        if (!empty($email)) {
            $fields[] = "email = ?";
            $params[] = $email;
            $types .= 's';
        }
        if (!empty($password)) {
            $newSalt = bin2hex(random_bytes(32));
            $passwordWithSalt = $newSalt . $password;
            $hashedPassword = password_hash($passwordWithSalt, PASSWORD_DEFAULT);
            $fields[] = "password = ?";
            $fields[] = "salt = ?";
            $params[] = $hashedPassword;
            $params[] = $newSalt;
            $types .= 'ss';
        }

        $query = "UPDATE USERS SET " . implode(", ", $fields) . " WHERE id = ?";
        $params[] = $userId;
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

        apiResponse(true, ['message' => 'User information updated successfully.'], null, 200);
    }
}
?>