<?php
if (!function_exists('handleDeleteProduct')) {
    function handleDeleteProduct($inputData, $dbConnection) {
        // Validate required input parameters
        if (empty($inputData['apikey'])) {
            apiResponse(false, null, 'API key is required', 400);
            return;
        }
        
        if (empty($inputData['book_id'])) {
            apiResponse(false, null, 'Book ID is required', 400);
            return;
        }
        
        $apiKey = $inputData['apikey'];
        $bookId = (int)$inputData['book_id'];
        
        // Get user information by API key
        $stmt = $dbConnection->prepare("SELECT user_type FROM USERS WHERE apikey = ? LIMIT 1");
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
        
        $userType = $user['user_type'];
        
        // Only super admin can delete products
        if ($userType !== 'super') {
            apiResponse(false, null, 'You do not have access to do this. Only super admins can delete products.', 403);
            return;
        }
        
        // First check if the book exists
        $checkStmt = $dbConnection->prepare("SELECT id FROM PRODUCTS WHERE id = ? LIMIT 1");
        if (!$checkStmt) {
            apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
            return;
        }
        
        $checkStmt->bind_param("i", $bookId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $bookExists = $checkResult->fetch_assoc();
        $checkStmt->close();
        
        if (!$bookExists) {
            apiResponse(false, null, 'Book not found', 404);
            return;
        }
        
        // Delete the book from PRODUCTS table
        // Due to foreign key constraints with ON DELETE CASCADE,
        // this will also remove related entries in STORE_INFO, BOOK_CATS, etc.
        $deleteStmt = $dbConnection->prepare("DELETE FROM PRODUCTS WHERE id = ?");
        if (!$deleteStmt) {
            apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
            return;
        }
        
        $deleteStmt->bind_param("i", $bookId);
        $success = $deleteStmt->execute();
        $affectedRows = $deleteStmt->affected_rows;
        $deleteStmt->close();
        
        if (!$success) {
            apiResponse(false, null, 'Failed to delete the book: ' . $dbConnection->error, 500);
            return;
        }
        
        if ($affectedRows === 0) {
            apiResponse(false, null, 'No book was deleted. It may have been deleted already.', 404);
            return;
        }
        
        apiResponse(true, ['message' => 'Book successfully deleted from the database.']);
    }
}
?>