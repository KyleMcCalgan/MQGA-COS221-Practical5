<?php
require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleUpdateGenreVisibility')) {
    function handleUpdateGenreVisibility($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        $genreId = $data['genre_id'] ?? null;
        $genreName = $data['genre_name'] ?? null;
        $searchable = $data['searchable'] ?? null;

        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
            return;
        }

        if ($genreId === null && $genreName === null) {
            apiResponse(false, null, 'Either genre_id or genre_name is required.', 400);
            return;
        }

        if ($genreId !== null && !is_numeric($genreId)) {
            apiResponse(false, null, 'Genre ID must be a valid number.', 400);
            return;
        }

        if ($genreName !== null) {
            $genreName = trim($genreName);
            if (strlen($genreName) === 0) {
                apiResponse(false, null, 'Genre name cannot be empty.', 400);
                return;
            }
        }

        if ($searchable === null) {
            apiResponse(false, null, 'Searchable value is required (true/false).', 400);
            return;
        }

        if ($genreId !== null) {
            $genreId = (int)$genreId;
        }
        
        $searchableValue = filter_var($searchable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        
        if ($searchableValue === null) {
            apiResponse(false, null, 'Searchable must be a boolean value (true/false).', 400);
            return;
        }

        $searchableInt = $searchableValue ? 1 : 0;

        if (!checkAuth($apiKey, 'super', $db)) {
            apiResponse(false, null, 'Unauthorised. Only super admins can update genre visibility.', 403);
            return;
        }

        $existingGenre = null;
        
        if ($genreId !== null && $genreName !== null) {

            $checkStmt = $db->prepare("SELECT category_id, genre, searchable FROM CATEGORIES WHERE category_id = ? AND LOWER(genre) = LOWER(?) LIMIT 1");
            if (!$checkStmt) {
                error_log("DB Prepare Error (genre check both): " . $db->error);
                apiResponse(false, null, 'Database error: Unable to check genre.', 500);
                return;
            }
            
            $checkStmt->bind_param("is", $genreId, $genreName);
            if (!$checkStmt->execute()) {
                error_log("DB Execute Error (genre check both): " . $checkStmt->error);
                apiResponse(false, null, 'Database error: Failed to check genre.', 500);
                $checkStmt->close();
                return;
            }
            
            $checkResult = $checkStmt->get_result();
            $existingGenre = $checkResult->fetch_assoc();
            $checkStmt->close();
            
            if (!$existingGenre) {

                $idCheckStmt = $db->prepare("SELECT category_id, genre FROM CATEGORIES WHERE category_id = ? LIMIT 1");
                if ($idCheckStmt) {
                    $idCheckStmt->bind_param("i", $genreId);
                    $idCheckStmt->execute();
                    $idResult = $idCheckStmt->get_result();
                    $idGenre = $idResult->fetch_assoc();
                    $idCheckStmt->close();
                    
                    if ($idGenre) {
                        apiResponse(false, null, 'Genre ID and name do not match. ID ' . $genreId . ' corresponds to "' . $idGenre['genre'] . '", not "' . $genreName . '".', 400);
                        return;
                    }
                }
                apiResponse(false, null, 'Genre not found with the provided ID and name combination.', 404);
                return;
            }
        } elseif ($genreId !== null) {

            $checkStmt = $db->prepare("SELECT category_id, genre, searchable FROM CATEGORIES WHERE category_id = ? LIMIT 1");
            if (!$checkStmt) {
                error_log("DB Prepare Error (genre check ID): " . $db->error);
                apiResponse(false, null, 'Database error: Unable to check genre.', 500);
                return;
            }
            
            $checkStmt->bind_param("i", $genreId);
            if (!$checkStmt->execute()) {
                error_log("DB Execute Error (genre check ID): " . $checkStmt->error);
                apiResponse(false, null, 'Database error: Failed to check genre.', 500);
                $checkStmt->close();
                return;
            }
            
            $checkResult = $checkStmt->get_result();
            $existingGenre = $checkResult->fetch_assoc();
            $checkStmt->close();
            
            if (!$existingGenre) {
                apiResponse(false, null, 'Genre not found with ID: ' . $genreId, 404);
                return;
            }
        } else {

            $checkStmt = $db->prepare("SELECT category_id, genre, searchable FROM CATEGORIES WHERE LOWER(genre) = LOWER(?) LIMIT 1");
            if (!$checkStmt) {
                error_log("DB Prepare Error (genre check name): " . $db->error);
                apiResponse(false, null, 'Database error: Unable to check genre.', 500);
                return;
            }
            
            $checkStmt->bind_param("s", $genreName);
            if (!$checkStmt->execute()) {
                error_log("DB Execute Error (genre check name): " . $checkStmt->error);
                apiResponse(false, null, 'Database error: Failed to check genre.', 500);
                $checkStmt->close();
                return;
            }
            
            $checkResult = $checkStmt->get_result();
            $existingGenre = $checkResult->fetch_assoc();
            $checkStmt->close();
            
            if (!$existingGenre) {
                apiResponse(false, null, 'Genre not found with name: ' . htmlspecialchars($genreName), 404);
                return;
            }

            $genreId = (int)$existingGenre['category_id'];
        }

        if ((int)$existingGenre['searchable'] === $searchableInt) {
            apiResponse(true, [
                'message' => 'Genre visibility unchanged',
                'genre_id' => (int)$existingGenre['category_id'],
                'genre_name' => $existingGenre['genre'],
                'searchable' => $searchableValue
            ], null, 200);
            return;
        }

        $updateStmt = $db->prepare("UPDATE CATEGORIES SET searchable = ? WHERE category_id = ?");
        if (!$updateStmt) {
            error_log("DB Prepare Error (genre update): " . $db->error);
            apiResponse(false, null, 'Database error: Unable to prepare genre update.', 500);
            return;
        }

        $updateStmt->bind_param("ii", $searchableInt, $genreId);
        if ($updateStmt->execute()) {
            if ($updateStmt->affected_rows > 0) {
                $updateStmt->close();
                
                apiResponse(true, [
                    'message' => 'Genre visibility updated successfully',
                    'genre_id' => (int)$existingGenre['category_id'],
                    'genre_name' => $existingGenre['genre'],
                    'searchable' => $searchableValue
                ], null, 200);
            } else {
                $updateStmt->close();
                apiResponse(false, null, 'No changes were made to the genre visibility.', 400);
            }
        } else {
            error_log("DB Execute Error (genre update): " . $updateStmt->error);
            apiResponse(false, null, 'Database error: Failed to update genre visibility.', 500);
            $updateStmt->close();
        }
    }
}
?>