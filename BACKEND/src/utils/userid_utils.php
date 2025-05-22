<?php

if (!function_exists('getUserIdUtil')) {
    function getUserIdUtil(string $apiKey, mysqli $dbConnection): ?int
    {
        if (empty($apiKey)) {
            return null;
        }

        $stmt = $dbConnection->prepare("SELECT id FROM USERS WHERE apikey = ? LIMIT 1");

        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("s", $apiKey);

        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $result = $stmt->get_result();
        $userId = null;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (isset($row['id'])) {
                $userId = (int)$row['id'];
            }
        }

        $stmt->close();

        return $userId;
    }
}

?>