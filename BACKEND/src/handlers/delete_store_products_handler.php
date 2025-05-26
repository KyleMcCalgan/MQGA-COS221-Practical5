<?php
if (!function_exists('handleDeleteStoreProducts')) {
    function handleDeleteStoreProducts($inputData, $dbConnection) {
        if (empty($inputData['api_key'])) {
            apiResponse(false, null, 'API key is required', 400);
            return;
        }
        
        if (empty($inputData['book_id'])) {
            apiResponse(false, null, 'Book ID is required', 400);
            return;
        }
        
        if (empty($inputData['store_id'])) {
            apiResponse(false, null, 'Store ID is required', 400);
            return;
        }
        
        $apiKey = $inputData['api_key'];
        $bookId = (int)$inputData['book_id'];
        $storeId = (int)$inputData['store_id'];
        
        $stmt = $dbConnection->prepare("SELECT id, user_type FROM USERS WHERE apikey = ? LIMIT 1");
        if (!$stmt) {
            apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
            return;
        }
        
        $stmt->bind_param("s", $apiKey);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!$user) {
            apiResponse(false, null, 'Invalid API key', 401);
            return;
        }
        
        $userId = $user['id'];
        $userType = $user['user_type'];
        
        $checkStmt = $dbConnection->prepare("SELECT * FROM STORE_INFO WHERE book_id = ? AND store_id = ? LIMIT 1");
        if (!$checkStmt) {
            apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
            return;
        }
        
        $checkStmt->bind_param("ii", $bookId, $storeId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $storeInfoExists = $checkResult->fetch_assoc();
        $checkStmt->close();
        
        if (!$storeInfoExists) {
            apiResponse(false, null, 'No price and rating information found for this book in the specified store', 404);
            return;
        }
        
        if ($userType === 'super') {
            performDeletion($dbConnection, $bookId, $storeId);
        } 
        elseif ($userType === 'admin') {
            $adminStoreQuery = "SELECT store_id FROM ADMINS WHERE id = ? LIMIT 1";
            
            $adminStmt = $dbConnection->prepare($adminStoreQuery);
            if (!$adminStmt) {
                apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
                return;
            }
            
            $adminStmt->bind_param("i", $userId);
            $adminStmt->execute();
            $adminResult = $adminStmt->get_result();
            $adminStore = $adminResult->fetch_assoc();
            $adminStmt->close();
            
            if (!$adminStore) {
                apiResponse(false, null, 'Admin is not associated with any store', 403);
                return;
            }
            
            if ($adminStore['store_id'] != $storeId) {
                apiResponse(false, null, "You don't have access to this store's book", 403);
                return;
            }
            
            performDeletion($dbConnection, $bookId, $storeId);
        } else {
            apiResponse(false, null, 'Unauthorised access. Only super admins and admins can use this endpoint', 403);
            return;
        }
    }
    
    function performDeletion($dbConnection, $bookId, $storeId) {
        $deleteStmt = $dbConnection->prepare("DELETE FROM STORE_INFO WHERE book_id = ? AND store_id = ?");
        if (!$deleteStmt) {
            apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
            return;
        }
        
        $deleteStmt->bind_param("ii", $bookId, $storeId);
        $success = $deleteStmt->execute();
        $affectedRows = $deleteStmt->affected_rows;
        $deleteStmt->close();
        
        if (!$success) {
            apiResponse(false, null, 'Failed to delete price and rating information: ' . $dbConnection->error, 500);
            return;
        }
        
        if ($affectedRows === 0) {
            apiResponse(false, null, 'No records were deleted. The specified book and store combination might not exist.', 404);
            return;
        }
        
        apiResponse(true, ['message' => 'Price and rating information successfully deleted for the specified book and store.']);
    }
}
?>