<?php
if (!function_exists('checkAuth')) {
    function checkAuth($apiKey, $expectedType, $dbConnection = null) {
        if (empty($apiKey) || empty($expectedType)) {
            return false;
        }

        $internalDbConnection = false;
        if ($dbConnection === null) {
            if (!function_exists('getDbConnection')) {
                require_once __DIR__ . '/../../config/database.php';
            }
            $dbConnection = getDbConnection();
            $internalDbConnection = true;
        }

        $stmt = $dbConnection->prepare("SELECT user_type FROM users WHERE apikey = ? LIMIT 1");
        if (!$stmt) {
            if ($internalDbConnection) $dbConnection->close();
            return false; 
        }

        $stmt->bind_param("s", $apiKey);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($internalDbConnection) {
            $dbConnection->close();
        }

        if ($user && $user['user_type'] === $expectedType) {
            return true;
        }

        return false;
    }
}
?>


