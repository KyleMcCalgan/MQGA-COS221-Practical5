<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleGetUsers')) {
    function handleGetUsers($inputData, $dbConnection) {
        $apiKey = $inputData['api_key'] ?? null;
        $userType = $inputData['userType'] ?? null;
        
        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 400);
        }

        $requestingUserStmt = $dbConnection->prepare("SELECT id, user_type FROM USERS WHERE apikey = ? LIMIT 1");
        if (!$requestingUserStmt) {
            apiResponse(false, null, 'Database error: Unable to prepare user lookup query.', 500);
        }

        $requestingUserStmt->bind_param("s", $apiKey);
        if (!$requestingUserStmt->execute()) {
            apiResponse(false, null, 'Database error: User lookup query failed.', 500);
        }

        $requestingUserResult = $requestingUserStmt->get_result();
        if ($requestingUserResult->num_rows === 0) {
            apiResponse(false, null, 'Invalid or Unauthorised API key.', 401);
        }

        $requestingUser = $requestingUserResult->fetch_assoc();
        $requestingUserId = $requestingUser['id'];
        $requestingUserType = $requestingUser['user_type'];
        $requestingUserStmt->close();

        if ($requestingUserType === 'regular') {
            $stmt = $dbConnection->prepare("SELECT id, name, surname, email, user_type FROM USERS WHERE id = ?");
            if (!$stmt) {
                apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
            }

            $stmt->bind_param("i", $requestingUserId);
            if (!$stmt->execute()) {
                $stmt->close();
                apiResponse(false, null, 'Database error: Query execution failed.', 500);
            }

            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user) {
                apiResponse(true, [$user], 'Your user information retrieved successfully.', 200);
            } else {
                apiResponse(false, null, 'User information not found.', 404);
            }

        } elseif ($requestingUserType === 'admin' || $requestingUserType === 'super') {
            if (empty($userType)) {
                apiResponse(false, null, 'userType is required for admin and super users.', 400);
            }

            $validUserTypes = ['super', 'regular', 'admin'];
            if (!in_array($userType, $validUserTypes)) {
                apiResponse(false, null, 'Invalid userType. Must be super, regular, or admin.', 400);
            }

            $stmt = $dbConnection->prepare("SELECT id, name, surname, email, user_type FROM USERS WHERE user_type = ?");
            if (!$stmt) {
                apiResponse(false, null, 'Database error: Unable to prepare query.', 500);
            }

            $stmt->bind_param("s", $userType);
            if (!$stmt->execute()) {
                $stmt->close();
                apiResponse(false, null, 'Database error: Query execution failed.', 500);
            }

            $result = $stmt->get_result();
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'surname' => $row['surname'],
                    'email' => $row['email'],
                    'user_type' => $row['user_type']
                ];
            }

            $stmt->close();

            if (empty($users)) {
                apiResponse(true, [], 'No users found for the specified user type.', 200);
            } else {
                apiResponse(true, $users, null, 200);
            }

        } else {
            apiResponse(false, null, 'Invalid user type for this operation.', 403);
        }
    }
}
?>