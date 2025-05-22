<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleGetGenre')) {
    function handleGetGenre($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $searchable = $data['searchable'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
            return;
        }

        $userStmt = $db->prepare("SELECT user_type FROM USERS WHERE apikey = ? LIMIT 1");
        if (!$userStmt) {
            error_log("DB Prepare Error (user check): " . $db->error);
            apiResponse(false, null, 'Database error: Unable to verify user.', 500);
            return;
        }

        $userStmt->bind_param("s", $apiKey);
        if (!$userStmt->execute()) {
            error_log("DB Execute Error (user check): " . $userStmt->error);
            apiResponse(false, null, 'Database error: Failed to verify user.', 500);
            $userStmt->close();
            return;
        }

        $userResult = $userStmt->get_result();
        $user = $userResult->fetch_assoc();
        $userStmt->close();

        if (!$user) {
            apiResponse(false, null, 'Invalid API key.', 401);
            return;
        }

        $userType = $user['user_type'];

        if ($searchable !== null) {
            $searchableValue = filter_var($searchable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            
            if ($searchableValue === null) {
                apiResponse(false, null, 'Searchable must be a boolean value (true/false).', 400);
                return;
            }

            if ($searchableValue === false && $userType !== 'admin' && $userType !== 'super') {
                apiResponse(false, null, 'Unauthorized. Only admins and super admins can view non-searchable genres.', 403);
                return;
            }

            $searchableInt = $searchableValue ? 1 : 0;

            $query = "SELECT category_id, genre, searchable FROM CATEGORIES WHERE searchable = ? ORDER BY genre ASC";
            $stmt = $db->prepare($query);
            if (!$stmt) {
                error_log("DB Prepare Error (filtered genres): " . $db->error);
                apiResponse(false, null, 'Database error: Unable to prepare genre query.', 500);
                return;
            }

            $stmt->bind_param("i", $searchableInt);
        } else {
            $query = "SELECT category_id, genre, searchable FROM CATEGORIES ORDER BY genre ASC";
            $stmt = $db->prepare($query);
            if (!$stmt) {
                error_log("DB Prepare Error (all genres): " . $db->error);
                apiResponse(false, null, 'Database error: Unable to prepare genre query.', 500);
                return;
            }
        }

        if (!$stmt->execute()) {
            error_log("DB Execute Error (genres): " . $stmt->error);
            apiResponse(false, null, 'Database error: Failed to retrieve genres.', 500);
            $stmt->close();
            return;
        }

        $result = $stmt->get_result();
        $genres = [];

        while ($row = $result->fetch_assoc()) {
            $genres[] = [
                'category_id' => (int)$row['category_id'],
                'genre' => $row['genre'],
                'searchable' => (bool)$row['searchable']
            ];
        }

        $stmt->close();
        $message = null;
        if ($searchable !== null) {
            $searchableText = $searchableValue ? 'searchable' : 'non-searchable';
            $message = 'Retrieved ' . count($genres) . ' ' . $searchableText . ' genre(s)';
        } else {
            $message = 'Retrieved ' . count($genres) . ' genre(s)';
        }

        apiResponse(true, [
            'genres' => $genres,
            'count' => count($genres),
            'message' => $message
        ], null, 200);
    }
}
?>

