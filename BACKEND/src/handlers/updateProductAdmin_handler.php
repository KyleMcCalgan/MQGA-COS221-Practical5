<?php

require_once __DIR__ . '/../utils/auth_utils.php';

if (!function_exists('handleUpdateProductAdmin')) {
    function handleUpdateProductAdmin($data, $db) {
        $apiKey = $data['api_key'] ?? null;
        if (empty($apiKey)) {
            apiResponse(false, null, 'API key is required.', 401);
            return;
        }

        if (!checkAuth($apiKey, 'super', $db)) {
            apiResponse(false, null, 'Invalid or unauthorized API key for super admin access.', 401);
            return;
        }

        $bookDetails = (array) ($data['Book'] ?? null);

        if (empty($bookDetails) || !isset($bookDetails['id'])) {
            apiResponse(false, null, 'Product data or Product ID is missing in "Book" object.', 400);
            return;
        }

        $productId = filter_var($bookDetails['id'], FILTER_VALIDATE_INT);
        if ($productId === false || $productId <= 0) {
            apiResponse(false, null, 'Invalid Product ID. Must be a positive integer.', 400);
            return;
        }
        
        unset($bookDetails['id']);

        if (empty($bookDetails)) {
            apiResponse(false, null, 'No fields provided for update.', 400);
            return;
        }

        $allowedFieldTypes = [
            'tempID' => 's',          
            'title' => 's',           
            'description' => 's',     
            'isbn13' => 's',
            'publishedDate' => 's',   
            'publisher' => 's',
            'author' => 's',
            'pageCount' => 'i',
            'maturityRating' => 's',
            'language' => 's',
            'smallThumbnail' => 's',
            'thumbnail' => 's',
            'accessibleIn' => 's',   
            'ratingsCount' => 'i'     
        ];

        $updateAssignments = [];
        $params = [];
        $paramTypes = '';

        foreach ($bookDetails as $field => $value) {
            if (array_key_exists($field, $allowedFieldTypes)) {
                $updateAssignments[] = "`" . $field . "` = ?";
                $params[] = $value;
                $paramTypes .= $allowedFieldTypes[$field];
            }
        }

        if (empty($updateAssignments)) {
            apiResponse(false, null, 'No valid fields provided for update.', 400);
            return;
        }

        $params[] = $productId;
        $paramTypes .= 'i'; 

        $sql = "UPDATE PRODUCTS SET " . implode(', ', $updateAssignments) . " WHERE id = ?";

        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log("DB Prepare Error: " . $db->error);
            apiResponse(false, null, 'Database error: Unable to prepare update query.', 500);
            return;
        }

        if (!$stmt->bind_param($paramTypes, ...$params)) {
            error_log("DB Bind Param Error: " . $stmt->error);
            apiResponse(false, null, 'Database error: Unable to bind parameters for update.', 500);
            $stmt->close();
            return;
        }

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                apiResponse(true, "Book has been updated :)", null, 200);
            } else {
                $checkStmt = $db->prepare("SELECT id FROM PRODUCTS WHERE id = ?");
                if ($checkStmt) {
                    $checkStmt->bind_param('i', $productId);
                    $checkStmt->execute();
                    $checkStmt->store_result();
                    if ($checkStmt->num_rows > 0) {
                        apiResponse(true, "Book data was identical, no changes made.", null, 200);
                    } else {
                        apiResponse(false, null, 'Book not found with the provided ID.', 404);
                    }
                    $checkStmt->close();
                } else {
                    error_log("DB Prepare Error (check stmt): " . $db->error);
                    apiResponse(false, null, 'Book not updated. It may not exist or data was unchanged.', 404);
                }
            }
        } else {
            error_log("DB Execute Error: " . $stmt->error);
            apiResponse(false, null, 'Database error: Failed to execute update.', 500);
        }

        $stmt->close();
    }
}
?>