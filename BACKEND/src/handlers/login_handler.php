<?php

function handleLogin($inputData) {
    $conn = getDbConnection();

    $email = isset($inputData['email']) ? sanitizeInput($inputData['email']) : null;
    $password = isset($inputData['password']) ? $inputData['password'] : null;

    if (empty($email) || empty($password)) {
        apiResponse(false, null, 'Email and password are required.', 400);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        apiResponse(false, null, 'Invalid email format.', 400);
        return;
    }

    $stmt = $conn->prepare("SELECT id, email, apikey, password, salt,user_type FROM USERS WHERE email = ? LIMIT 1");

    if (!$stmt) {
        error_log("Database prepare statement failed (login - select user): " . $conn->error);
        apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        $conn->close();
        return;
    }

    $stmt->bind_param("s", $email);

    if (!$stmt->execute()) {
        error_log("Database execute failed (login - select user): " . $stmt->error);
        apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
        $stmt->close();
        $conn->close();
        return;
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $userSalt = $user['salt'];
        $storedHashedPassword = $user['password'];
        $user_type=$user['user_type'];

        $passwordWithSalt = $userSalt . $password;

        if (password_verify($passwordWithSalt, $storedHashedPassword)) {
            try {
                $userApiKey = $user['apikey'];


                $responseData = [
                    'api_key' => $userApiKey,
                    'user_type'=>$user_type
                ];
                
                apiResponse(true, $responseData, 'Login successful.', 200);
            } catch (Exception $e) {
                error_log("Error preparing success response: " . $e->getMessage());
                apiResponse(false, null, 'Login succeeded but could not prepare session data.', 500);
            }
        } else {
            apiResponse(false, null, 'Invalid email or password.', 401);
        }
    } else {
        apiResponse(false, null, 'Invalid email or password.', 401);
    }

    $stmt->close();
    $conn->close();
}

?>