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
            return;
        }

        $query = "SELECT id, email, password, salt FROM USERS WHERE apikey = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare user check query.', 500);
            return;
        }
        $stmt->bind_param("s", $apiKey);
        if (!$stmt->execute()) {
            $stmt->close();
            apiResponse(false, null, 'Database error: User check query failed.', 500);
            return;
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            apiResponse(false, null, 'User not found.', 404);
            return;
        }
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        $storedPassword = $user['password'];
        $storedSalt = $user['salt'];
        $stmt->close();

        if (empty($name) && empty($surname) && empty($email) && empty($password)) {
            apiResponse(false, null, 'At least one field (name, surname, email, password) must be provided.', 400);
            return;
        }

        if (!empty($password) && empty($oldPassword)) {
            apiResponse(false, null, 'Old password is required when updating password.', 400);
            return;
        }

        if (!empty($password)) {
            $intermediateOldPassword = hash_hmac('sha256', $oldPassword, $storedSalt, false);
            if (!password_verify($intermediateOldPassword, $storedPassword)) {
                apiResponse(false, null, 'Incorrect old password.', 401);
                return;
            }

            if (strlen($password) < 8 ||
                !preg_match('/[A-Z]/', $password) ||
                !preg_match('/[a-z]/', $password) ||
                !preg_match('/[0-9]/', $password) ||
                !preg_match('/[\W_]/', $password)) {
                apiResponse(false, null, 'Password does not meet complexity requirements. It must be at least 8 characters long, and include an uppercase letter, a lowercase letter, a digit, and a symbol.', 400);
                return;
            }
        }

        if (!empty($email) && $email !== $user['email']) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                apiResponse(false, null, 'Invalid new email format.', 400);
                return;
            }
            $query = "SELECT id FROM USERS WHERE email = ? AND id != ?";
            $stmt = $db->prepare($query);
            if (!$stmt) {
                apiResponse(false, null, 'Database error: Unable to prepare email check query.', 500);
                return;
            }
            $stmt->bind_param("si", $email, $userId);
            if (!$stmt->execute()) {
                $stmt->close();
                apiResponse(false, null, 'Database error: Email check query failed.', 500);
                return;
            }
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $stmt->close();
                apiResponse(false, null, 'Email already in use.', 400);
                return;
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
        if (!empty($email) && $email !== $user['email']) {
            $fields[] = "email = ?";
            $params[] = $email;
            $types .= 's';
        }

        if (!empty($password)) {
            $newSalt = bin2hex(random_bytes(32));
            $intermediateNewPassword = hash_hmac('sha256', $password, $newSalt, false);
            $hashedPassword = password_hash($intermediateNewPassword, PASSWORD_DEFAULT);
            
            $fields[] = "password = ?";
            $fields[] = "salt = ?";
            $params[] = $hashedPassword;
            $params[] = $newSalt;
            $types .= 'ss';
        }

        if (empty($fields)) {
            apiResponse(true, ['message' => 'No information was changed or no new valid values provided.'], null, 200);
            return;
        }

        $query = "UPDATE USERS SET " . implode(", ", $fields) . " WHERE id = ?";
        $params[] = $userId;
        $types .= 'i';

        $stmt = $db->prepare($query);
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to prepare update query.', 500);
            return;
        }
        
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            $stmt->close();
            apiResponse(false, null, 'Database error: Update operation failed.', 500);
            return;
        }
        
        $updated = $stmt->affected_rows > 0;
        $stmt->close();

        if ($updated) {
            apiResponse(true, ['message' => 'User information updated successfully.'], null, 200);
        } else {
            apiResponse(true, ['message' => 'No information was changed.'], null, 200);
        }
    }
}
?>