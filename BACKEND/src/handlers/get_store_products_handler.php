<?php

if (!function_exists('handleGetStoreProducts')) {
    function handleGetStoreProducts($inputData, $dbConnection) {
        if (empty($inputData['api_key'])) {
            apiResponse(false, null, 'API key is required', 400);
            return;
        }

        $apiKey = $inputData['api_key'];
        $storeName = $inputData['store_name'] ?? null;
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
        
        $baseQuery = "SELECT
                            p.id, p.title, p.author, p.smallThumbnail, p.thumbnail,
                            si.price, si.rating, s.name as store_name, s.store_id
                          FROM PRODUCTS p
                          JOIN STORE_INFO si ON p.id = si.book_id
                          JOIN STORES s ON si.store_id = s.store_id";
        
        $whereClauses = [];
        $params = [];
        $paramTypes = "";

        if ($userType === 'super') {
            if (empty($storeName)) {
                apiResponse(false, null, 'Store name is required for super admins to fetch specific store products', 400);
                return;
            }

            $storeCheckStmt = $dbConnection->prepare("SELECT store_id FROM STORES WHERE name = ? LIMIT 1");
            if (!$storeCheckStmt) {
                error_log("GetStoreProducts - DB Prepare Error (Store Check): " . $dbConnection->error);
                apiResponse(false, null, 'Database query preparation failed', 500);
                return;
            }
            $storeCheckStmt->bind_param("s", $storeName);
            if(!$storeCheckStmt->execute()){
                 error_log("GetStoreProducts - DB Execute Error (Store Check): " . $storeCheckStmt->error);
                 $storeCheckStmt->close();
                 apiResponse(false, null, 'Database query execution failed', 500);
                 return;
            }
            $storeResult = $storeCheckStmt->get_result();
            $storeExists = $storeResult->fetch_assoc();
            $storeCheckStmt->close();

            if (!$storeExists) {
                apiResponse(false, null, 'Store not found: ' . htmlspecialchars($storeName), 404);
                return;
            }
            $whereClauses[] = "s.name = ?";
            $params[] = $storeName;
            $paramTypes .= "s";

        } elseif ($userType === 'admin') {
            $adminStoreQuery = "SELECT s.store_id, s.name FROM ADMINS a
                                 JOIN STORES s ON a.store_id = s.store_id
                                 WHERE a.id = ?";
            $adminStmt = $dbConnection->prepare($adminStoreQuery);
            if (!$adminStmt) {
                 error_log("GetStoreProducts - DB Prepare Error (Admin Store Lookup): " . $dbConnection->error);
                apiResponse(false, null, 'Database query preparation failed', 500);
                return;
            }
            $adminStmt->bind_param("i", $userId);
            if(!$adminStmt->execute()){
                error_log("GetStoreProducts - DB Execute Error (Admin Store Lookup): " . $adminStmt->error);
                $adminStmt->close();
                apiResponse(false, null, 'Database query execution failed', 500);
                return;
            }
            $adminResult = $adminStmt->get_result();
            $adminStore = $adminResult->fetch_assoc();
            $adminStmt->close();

            if (!$adminStore) {
                apiResponse(false, null, 'Admin is not associated with any store', 403);
                return;
            }
            
            if (!empty($storeName) && $storeName !== $adminStore['name']) {
                apiResponse(false, null, 'Admin can only access products for their own store (name mismatch).', 403);
                return;
            }

            $whereClauses[] = "s.store_id = ?";
            $params[] = $adminStore['store_id'];
            $paramTypes .= "i";
        } else {
            apiResponse(false, null, 'Unauthorised access. Only super admins and admins can use this endpoint', 403);
            return;
        }

        if (!empty($searchTitle)) {
            $whereClauses[] = "p.title LIKE ?";
            $params[] = "%" . $searchTitle . "%";
            $paramTypes .= "s";
        }

        $finalQuery = $baseQuery;
        if (!empty($whereClauses)) {
            $finalQuery .= " WHERE " . implode(" AND ", $whereClauses);
        } else {
            apiResponse(false, null, 'Store context is required to fetch products.', 400);
            return;
        }
        
        $productQueryStmt = $dbConnection->prepare($finalQuery);
        if (!$productQueryStmt) {
            error_log("GetStoreProducts - DB Prepare Error (Final Product Query): " . $dbConnection->error . " Query: " . $finalQuery);
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
                    'name' => htmlspecialchars($row['store_name']),
                    'id' => $row['store_id']
                ]
            ];
        }
        $productQueryStmt->close();

        if (empty($products)) {
            $message = 'No books found matching your criteria.';
            if (empty($searchTitle)) {
                if ($userType === 'super' && !empty($storeName)) {
                    $message = 'No books found for the specified store: ' . htmlspecialchars($storeName);
                } elseif ($userType === 'admin') {
                    $message = 'No books found for your store.';
                }
            }
            apiResponse(true, [], $message);
        } else {
            apiResponse(true, $products, 'Products retrieved successfully.');
        }
    }
}
?>