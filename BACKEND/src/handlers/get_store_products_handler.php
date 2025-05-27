<?php

if (!function_exists('handleGetStoreProducts')) {
    function handleGetStoreProducts($inputData, $dbConnection) {
        if (empty($inputData['api_key'])) {
            apiResponse(false, null, 'API key is required', 400);
            return;
        }


        $apiKey = $inputData['api_key'];
        $storeNameFromInput = $inputData['store_name'] ?? null; 
        $searchTitle = $inputData['title'] ?? null;

        $userAuthStmt = $dbConnection->prepare("SELECT id, user_type FROM USERS WHERE apikey = ? LIMIT 1");
        if (!$userAuthStmt) {
            error_log("GetStoreProducts - DB Prepare Error (User Auth): " . $dbConnection->error);
            apiResponse(false, null, 'Database query preparation failed', 500);

            return;
        }
        $userAuthStmt->bind_param("s", $apiKey);
        if (!$userAuthStmt->execute()) {
            error_log("GetStoreProducts - DB Execute Error (User Auth): " . $userAuthStmt->error);
            $userAuthStmt->close();
            apiResponse(false, null, 'Database query execution failed', 500);
            return;
        }
        $userResult = $userAuthStmt->get_result();
        $user = $userResult->fetch_assoc();
        $userAuthStmt->close();

        if (!$user) {
            apiResponse(false, null, 'Invalid API key', 401);
            return;
        }

        $userId = $user['id'];
        $userType = $user['user_type'];

        $products = [];
        $targetStoreId = null;
        $targetStoreName = null;
        if ($userType === 'super') {
            if (empty($storeNameFromInput)) {
                apiResponse(false, null, 'Store name is required for super admins to fetch specific store products', 400);
                return;
            }
            $targetStoreName = $storeNameFromInput;

            $storeCheckStmt = $dbConnection->prepare("SELECT store_id FROM STORES WHERE name = ? LIMIT 1");
            if (!$storeCheckStmt) {
                error_log("GetStoreProducts - DB Prepare Error (Store Check): " . $dbConnection->error);
                apiResponse(false, null, 'Database query preparation failed (store check)', 500);
                return;
            }

            $storeCheckStmt->bind_param("s", $targetStoreName);
            if(!$storeCheckStmt->execute()){
                error_log("GetStoreProducts - DB Execute Error (Store Check): " . $storeCheckStmt->error);
                $storeCheckStmt->close();
                apiResponse(false, null, 'Database query execution failed (store check)', 500);

                return;
            }
            $storeResult = $storeCheckStmt->get_result();
            $storeData = $storeResult->fetch_assoc();
            $storeCheckStmt->close();

            if (!$storeData) {
                apiResponse(false, null, 'Store not found: ' . htmlspecialchars($targetStoreName), 404);
                return;
            }
            $targetStoreId = $storeData['store_id'];

        } elseif ($userType === 'admin') {
            $adminStoreQuery = "SELECT s.store_id, s.name FROM ADMINS a
                                JOIN STORES s ON a.store_id = s.store_id
                                WHERE a.id = ?";
            $adminStmt = $dbConnection->prepare($adminStoreQuery);
            if (!$adminStmt) {
                error_log("GetStoreProducts - DB Prepare Error (Admin Store Lookup): " . $dbConnection->error);
                apiResponse(false, null, 'Database query preparation failed (admin store)', 500);
                return;
            }
            $adminStmt->bind_param("i", $userId);
            if(!$adminStmt->execute()){ 
                error_log("GetStoreProducts - DB Execute Error (Admin Store Lookup): " . $adminStmt->error);
                $adminStmt->close();
                apiResponse(false, null, 'Database query execution failed (admin store)', 500);
                return;
            }
            $adminResult = $adminStmt->get_result();
            $adminLinkedStore = $adminResult->fetch_assoc();
            $adminStmt->close();

            if (!$adminLinkedStore) {
                apiResponse(false, null, 'Admin is not associated with any store', 403);
                return;
            }
            $targetStoreId = $adminLinkedStore['store_id'];
            $targetStoreName = $adminLinkedStore['name'];

            if (!empty($storeNameFromInput) && $storeNameFromInput !== $targetStoreName) {
                apiResponse(false, null, 'Admin can only access products for their own store (name mismatch).', 403);
                return;
            }
        } else {
            apiResponse(false, null, 'Unauthorised access. Only super admins and admins can use this endpoint', 403);
            return;
        }

        if ($targetStoreId === null || $targetStoreName === null) {
            apiResponse(false, null, 'Store context for product listing could not be reliably determined.', 500);
            return;
        }

        $sql = "SELECT
                    p.id, p.title, p.author, p.smallThumbnail, p.thumbnail,
                    si.price, 
                    si.rating
                FROM 
                    PRODUCTS p
                JOIN 
                    STORE_INFO si ON p.id = si.book_id AND si.store_id = ?";
            
        $params = [$targetStoreId];
        $paramTypes = "i";

        $whereClauses = [];

        if (!empty($searchTitle)) {
            $whereClauses[] = "p.title LIKE ?";
            $params[] = "%" . $searchTitle . "%";
            $paramTypes .= "s";
        }
        
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }
        $sql .= " ORDER BY p.title";
        
        $productQueryStmt = $dbConnection->prepare($sql);
        if (!$productQueryStmt) {
            error_log("GetStoreProducts - DB Prepare Error (Final Product Query): " . $dbConnection->error . " Query: " . $sql);
            apiResponse(false, null, 'Database query preparation failed', 500);
            return;
        }

        if (!empty($params)) {
            $productQueryStmt->bind_param($paramTypes, ...$params);
        }
        
        if (!$productQueryStmt->execute()) {
            error_log("GetStoreProducts - DB Execute Error (Final Product Query): " . $productQueryStmt->error);
            $productQueryStmt->close();
            apiResponse(false, null, 'Database query execution failed.', 500);
            return;
        }
        
        $result = $productQueryStmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $products[] = [
                'id' => $row['id'],
                'title' => htmlspecialchars($row['title']),
                'author' => htmlspecialchars($row['author']),
                'smallThumbnail' => $row['smallThumbnail'],
                'thumbnail' => $row['thumbnail'],
                'price' => $row['price'],
                'rating' => $row['rating'],
                'store' => [
                    'name' => htmlspecialchars($targetStoreName), 
                    'id' => $targetStoreId
                ]
            ];
        }
        $productQueryStmt->close();

        if (empty($products)) {
            $message = 'No books found matching your criteria.';
             if (empty($searchTitle)) {
                 $message = ($userType === 'super') 
                    ? 'No books found for the store: ' . htmlspecialchars($targetStoreName) 
                    : 'No books found for your store.';
             }
            apiResponse(true, [], $message);
        } else {
            apiResponse(true, $products, 'Products retrieved successfully.');
        }
    }
}
?>