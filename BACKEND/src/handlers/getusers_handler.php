<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleGetUsers')) {
    function handleGetUsers($inputData, $dbConnection) {
        $apiKey = $inputData['api_key'] ?? null;
        $userType = $inputData['userType'] ?? null;
        
        if (empty($apiKey) || empty($userType)) {
            apiResponse(false, null, 'API key and userType are required.', 400);
        }

        $validUserTypes = ['super', 'regular', 'admin'];
        if (!in_array($userType, $validUserTypes)) {
            apiResponse(false, null, 'Invalid userType. Must be super, regular, or admin.', 400);
        }

        //what is this russian text for the api response?
        if (!checkAuth($apiKey, 'super', $dbConnection)) {
            apiResponse(false, nullзна, 'Unauthorised: Super admin access required.', 403);
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
    }
}
?>