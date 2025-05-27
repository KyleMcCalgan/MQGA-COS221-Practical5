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
            apiResponse(false, null, 'Invalid or Unauthorised API key for super admin access.', 401);
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
        
        $categoriesToUpdate = null;
        if (isset($bookDetails['categories']) && is_array($bookDetails['categories'])) {
            $categoriesToUpdate = array_map('intval', $bookDetails['categories']);
        }
        unset($bookDetails['id']);
        unset($bookDetails['categories']);


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
        $productDataProvided = false;

        foreach ($bookDetails as $field => $value) {
            if (array_key_exists($field, $allowedFieldTypes)) {
                $updateAssignments[] = "`" . $field . "` = ?";
                $params[] = $value;
                $paramTypes .= $allowedFieldTypes[$field];
                $productDataProvided = true;
            }
        }

        if (!$productDataProvided && $categoriesToUpdate === null) {
            apiResponse(false, null, 'No fields or categories provided for update.', 400);
            return;
        }

        $db->begin_transaction();
        $productUpdated = false;
        $categoriesUpdated = false;

        try {
            if ($productDataProvided) {
                $currentParams = $params;
                $currentParamTypes = $paramTypes;
                $currentParams[] = $productId;
                $currentParamTypes .= 'i'; 

                $sql = "UPDATE PRODUCTS SET " . implode(', ', $updateAssignments) . " WHERE id = ?";
                $stmt = $db->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Database error: Unable to prepare product update query. ' . $db->error);
                }
                if (!$stmt->bind_param($currentParamTypes, ...$currentParams)) {
                    throw new Exception('Database error: Unable to bind parameters for product update. ' . $stmt->error);
                }
                if (!$stmt->execute()) {
                    throw new Exception('Database error: Failed to execute product update. ' . $stmt->error);
                }
                if ($stmt->affected_rows > 0) {
                    $productUpdated = true;
                }
                $stmt->close();
            }

            if ($categoriesToUpdate !== null) {
                $deleteSql = "DELETE FROM BOOK_CATS WHERE book_id = ?";
                $stmtDel = $db->prepare($deleteSql);
                if (!$stmtDel) {
                    throw new Exception('Database error: Unable to prepare category delete query. ' . $db->error);
                }
                if (!$stmtDel->bind_param('i', $productId)) {
                    throw new Exception('Database error: Unable to bind parameters for category delete. ' . $stmtDel->error);
                }
                if (!$stmtDel->execute()) {
                    throw new Exception('Database error: Failed to delete existing categories. ' . $stmtDel->error);
                }
                $categoriesUpdated = true; 
                $stmtDel->close();

                if (!empty($categoriesToUpdate)) {
                    $insertSql = "INSERT INTO BOOK_CATS (book_id, category_id) VALUES (?, ?)";
                    $stmtIns = $db->prepare($insertSql);
                    if (!$stmtIns) {
                        throw new Exception('Database error: Unable to prepare category insert query. ' . $db->error);
                    }
                    foreach ($categoriesToUpdate as $categoryId) {
                        if (!$stmtIns->bind_param('ii', $productId, $categoryId)) {
                            throw new Exception('Database error: Unable to bind parameters for category insert. ' . $stmtIns->error);
                        }
                        if (!$stmtIns->execute()) {
                            throw new Exception('Database error: Failed to insert category. ' . $stmtIns->error);
                        }
                    }
                    $stmtIns->close();
                }
            }

            $db->commit();

            if ($productUpdated || $categoriesUpdated) {
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

        } catch (Exception $e) {
            $db->rollback();
            error_log("Transaction Error: " . $e->getMessage());
            apiResponse(false, null, 'Database error during update: ' . $e->getMessage(), 500);
        }
    }
}
?>