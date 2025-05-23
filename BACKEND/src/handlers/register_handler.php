<?php

if (! function_exists('handleRegister')) {//marcel
    function handleRegister($data, $db)
    {
        $email    = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $name     = $data['name'] ?? null;
        $surname  = $data['surname'] ?? null;

        $userType = 'regular';

        if (empty($email) || empty($password) || empty($name) || empty($surname)) {
            apiResponse(false, null, 'Email, password, name, and surname are required.', 400);
            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            apiResponse(false, null, 'Invalid email format.', 400);
            return;
        }

        if (strlen($password) < 8 ||
            ! preg_match('/[A-Z]/', $password) ||
            ! preg_match('/[a-z]/', $password) ||
            ! preg_match('/[0-9]/', $password) ||
            ! preg_match('/[\W_]/', $password)
        ) {
            apiResponse(false, null, 'Password does not meet complexity requirements. It must be at least 8 characters long, and include an uppercase letter, a lowercase letter, a digit, and a symbol.', 400);
            return;
        }

        $stmt = $db->prepare("SELECT id FROM USERS WHERE email = ? LIMIT 1");
        if (! $stmt) {
            error_log("Database prepare statement failed (register - check email): " . $db->error);
            apiResponse(false, null, 'Database error: Unable to check email.', 500);
            return;
        }
        $stmt->bind_param("s", $email);
        if (! $stmt->execute()) {
            error_log("Database execute failed (register - check email): " . $stmt->error);
            $stmt->close();
            apiResponse(false, null, 'Database error: Unable to execute email check.', 500);
            return;
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            apiResponse(false, null, 'This email address is already registered.', 409);
            return;
        }
        $stmt->close();

        $userSalt             = bin2hex(random_bytes(32));
        $intermediatePassword = hash_hmac('sha256', $password, $userSalt, false);
        $finalHashedPassword  = password_hash($intermediatePassword, PASSWORD_DEFAULT);
        $apiKey               = bin2hex(random_bytes(32));

        $insertStmt = $db->prepare("INSERT INTO USERS (apikey, name, surname, email, password, salt, user_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
     

        $insertStmt->bind_param("sssssss", $apiKey, $name, $surname, $email, $finalHashedPassword, $userSalt, $userType);

        if ($insertStmt->execute()) {
            $responseData = [
                'api_key' => $apiKey,
                'name'    => $name,
                'surname' => $surname,
            ];
            apiResponse(true, $responseData, 'User registered successfully.', 201);
        } else {
            error_log("Database execute failed (register - insert user): " . $insertStmt->error);
            apiResponse(false, null, 'Failed to register user. Please try again.', 500);
        }
        $insertStmt->close();
    }
}
