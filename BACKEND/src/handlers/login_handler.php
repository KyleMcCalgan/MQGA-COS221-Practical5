<?php
if (! function_exists('handleLogin')) {//marcel
    function handleLogin($inputData)
    {
        $conn = getDbConnection();
        if (! $conn) {
            apiResponse(false, null, 'Database connection failed.', 500);
            return;
        }

        $email           = isset($inputData['email']) ? sanitiseInput($inputData['email']) : null;
        $passwordAttempt = isset($inputData['password']) ? $inputData['password'] : null;

        if (empty($email) || empty($passwordAttempt)) {
            apiResponse(false, null, 'Email and password are required.', 400);
            $conn->close();
            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            apiResponse(false, null, 'Invalid email format.', 400);
            $conn->close();
            return;
        }

        $stmt = $conn->prepare("SELECT id, email, apikey, password, salt, user_type FROM USERS WHERE email = ? LIMIT 1");

  

        $stmt->bind_param("s", $email);

        if (! $stmt->execute()) {
            error_log("Database execute failed (login - select user): " . $stmt->error);
            apiResponse(false, null, 'An internal error occurred. Please try again later.', 500);
            $stmt->close();
            $conn->close();
            return;
        }

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            $userSaltFromDB            = $user['salt'];
            $storedFinalHashedPassword = $user['password'];
            $user_type                 = $user['user_type'];

            $intermediatePasswordAttempt = hash_hmac('sha256', $passwordAttempt, $userSaltFromDB, false);

            if (password_verify($intermediatePasswordAttempt, $storedFinalHashedPassword)) {
                try {
                    $userApiKey   = $user['apikey'];
                    $responseData = [
                        'api_key'   => $userApiKey,
                        'user_type' => $user_type,
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
}
