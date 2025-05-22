<?php
if (!function_exists('handleGetStoreProducts')) {
    function handleGetStoreProducts($inputData, $dbConnection) {
        if (empty($inputData['apikey'])) {
            apiResponse(false, null, 'API key is required', 400);
            return;
        }

        $apiKey = $inputData['apikey'];
        $storeName = $inputData['store_name'] ?? null;
        
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
        
        if ($userType === 'super') {
            if (empty($storeName)) {
                apiResponse(false, null, 'Store name is required even for super admins', 400);
                return;
            }
            
            $storeCheckStmt = $dbConnection->prepare("SELECT store_id FROM STORES WHERE name = ? LIMIT 1");
            if (!$storeCheckStmt) {
                apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
                return;
            }
            
            $storeCheckStmt->bind_param("s", $storeName);
            $storeCheckStmt->execute();
            $storeResult = $storeCheckStmt->get_result();
            $storeExists = $storeResult->fetch_assoc();
            $storeCheckStmt->close();
            
            if (!$storeExists) {
                apiResponse(false, null, 'Store not found: ' . htmlspecialchars($storeName), 404);
                return;
            }
            
            $query = "SELECT 
                        p.id, p.title, p.author, p.smallThumbnail, p.thumbnail, 
                        si.price, si.rating, s.name as store_name, s.store_id
                      FROM PRODUCTS p
                      JOIN STORE_INFO si ON p.id = si.book_id
                      JOIN STORES s ON si.store_id = s.store_id
                      WHERE s.name = ?";
            
            $stmt = $dbConnection->prepare($query);
            if (!$stmt) {
                apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
                return;
            }
            
            $stmt->bind_param("s", $storeName);
            
        } 
        elseif ($userType === 'admin') {
          
            $adminStoreQuery = "SELECT s.store_id, s.name FROM ADMINS a
                               JOIN STORES s ON a.store_id = s.store_id
                               WHERE a.id = ?";
            
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
            
            if (!empty($storeName) && $storeName !== $adminStore['name']) {
                apiResponse(false, null, 'Admin can only access their own store', 403);
                return;
            }
            
            $query = "SELECT 
                        p.id, p.title, p.author, p.smallThumbnail, p.thumbnail, 
                        si.price, si.rating, s.name as store_name, s.store_id
                      FROM PRODUCTS p
                      JOIN STORE_INFO si ON p.id = si.book_id
                      JOIN STORES s ON si.store_id = s.store_id
                      WHERE s.store_id = ?";
            
            $stmt = $dbConnection->prepare($query);
            if (!$stmt) {
                apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
                return;
            }
            
            $stmt->bind_param("i", $adminStore['store_id']);
            
        } else {
            apiResponse(false, null, 'Unauthorized access. Only super admins and admins can use this endpoint', 403);
            return;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'author' => $row['author'],
                'smallThumbnail' => $row['smallThumbnail'],
                'thumbnail' => $row['thumbnail'],
                'price' => $row['price'],
                'rating' => $row['rating'],
                'store' => [
                    'name' => $row['store_name'],
                    'id' => $row['store_id']
                ]
            ];
        }
        
        $stmt->close();
        
        if (empty($books)) {
            if ($userType === 'super') {
                apiResponse(true, [], 'No books found for the specified store');
            } else {
                apiResponse(true, [], 'No books found for your store');
            }
            return;
        }
        
        apiResponse(true, $books);
    }
}