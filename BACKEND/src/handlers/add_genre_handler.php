<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleAddGenre')) {
    function handleAddGenre($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $genreName = $data['genre_name'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
            return;
        }

        if (empty($genreName)) {
            apiResponse(false, null, 'Genre name is required.', 400);
            return;
        }

        $genreName = trim($genreName);
        if (strlen($genreName) === 0) {
            apiResponse(false, null, 'Genre name cannot be empty.', 400);
            return;
        }

        if (strlen($genreName) > 100) {
            apiResponse(false, null, 'Genre name cannot exceed 100 characters.', 400);
            return;
        }

        if (!checkAuth($apiKey, 'super', $db)) {
            apiResponse(false, null, 'Unauthorized. Only super admins can add genres.', 403);
            return;
        }

        $checkStmt = $db->prepare("SELECT category_id FROM CATEGORIES WHERE LOWER(genre) = LOWER(?) LIMIT 1");
        if (!$checkStmt) {
            error_log("DB Prepare Error (genre check): " . $db->error);
            apiResponse(false, null, 'Database error: Unable to check if genre exists.', 500);
            return;
        }

        $checkStmt->bind_param("s", $genreName);
        if (!$checkStmt->execute()) {
            error_log("DB Execute Error (genre check): " . $checkStmt->error);
            apiResponse(false, null, 'Database error: Failed to check genre existence.', 500);
            $checkStmt->close();
            return;
        }

        $checkResult = $checkStmt->get_result();
        $existingGenre = $checkResult->fetch_assoc();
        $checkStmt->close();

        if ($existingGenre) {
            apiResponse(false, null, 'Genre already exists: ' . htmlspecialchars($genreName), 409);
            return;
        }

        $insertStmt = $db->prepare("INSERT INTO CATEGORIES (genre, searchable) VALUES (?, 1)");
        if (!$insertStmt) {
            error_log("DB Prepare Error (genre insert): " . $db->error);
            apiResponse(false, null, 'Database error: Unable to prepare genre insertion.', 500);
            return;
        }

        $insertStmt->bind_param("s", $genreName);
        if ($insertStmt->execute()) {
            $newGenreId = $insertStmt->insert_id;
            $insertStmt->close();
            
            apiResponse(true, [
                'message' => 'Genre added successfully',
                'genre_id' => $newGenreId,
                'genre_name' => $genreName,
                'searchable' => true
            ], null, 201);
        } else {
            error_log("DB Execute Error (genre insert): " . $insertStmt->error);
            apiResponse(false, null, 'Database error: Failed to add genre.', 500);
            $insertStmt->close();
        }
    }
}
?>