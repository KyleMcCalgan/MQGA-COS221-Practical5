<?php
if (!function_exists('handleRegister')) {
    function handleRegister($data, $db) {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $name = $data['name'] ?? null;
        $surname = $data['surname'] ?? null;
        
        $userType = 'regular';

        if (empty($email) || empty($password) || empty($name) || empty($surname)) {
            apiResponse(false, null, 'Email, password, name, and surname are required.', 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            apiResponse(false, null, 'Invalid email format.', 400);//not too sure how good phps internal checking is 
        }

        if (strlen($password) < 8) {
            apiResponse(false, null, 'Password must be at least 8 characters long.', 400);
        }

        $stmt = $db->prepare("SELECT id FROM USERS WHERE email = ? LIMIT 1");
        if (!$stmt) {
            apiResponse(false, null, 'Database error: Unable to check email.', 500);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            apiResponse(false, null, 'This email address is already registered.', 409);
        }
        $stmt->close();

        $userSalt = bin2hex(random_bytes(32));
        $passwordWithSalt = $userSalt . $password;
        $hashedPassword = password_hash($passwordWithSalt, PASSWORD_DEFAULT);//cailin,kyle password_hash is pretty cool because it does the salting already,but having a salt makes it extra secure 
        $apiKey = bin2hex(random_bytes(32)); 

        $insertStmt = $db->prepare("INSERT INTO USERS (apikey, name, surname, email, password, salt, user_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$insertStmt) {
            apiResponse(false, null, 'Database error: Unable to prepare user insertion.', 500);
        }
        
        $insertStmt->bind_param("sssssss", $apiKey, $name, $surname, $email, $hashedPassword, $userSalt, $userType);

        if ($insertStmt->execute()) {
            $responseData = [
                'api_key' => $apiKey,
                'name' => $name,
                'surname' => $surname
            ];
            apiResponse(true, $responseData, null, 201); 
        } else {
            apiResponse(false, null, 'Failed to register user. Please try again.', 500);
        }
        $insertStmt->close();
    }
}
?>
