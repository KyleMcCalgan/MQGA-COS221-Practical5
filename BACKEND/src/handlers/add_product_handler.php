<?php
if (!function_exists('handleAddProduct')) {
    function handleAddProduct($inputData, $dbConnection) {
        if (empty($inputData['apikey'])) {
            apiResponse(false, null, 'API key is required', 400);
            return;
        }
        
        if (empty($inputData['tempID'])) {
            apiResponse(false, null, 'Book tempID is required', 400);
            return;
        }
        
        if (empty($inputData['title'])) {
            apiResponse(false, null, 'Book title is required', 400);
            return;
        }
        
        $apiKey = $inputData['apikey'];
        
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
        
        if ($userType !== 'super' && $userType !== 'admin') {
            apiResponse(false, null, 'Unauthorized. Only super admins and store admins can add products.', 403);
            return;
        }
        
        if ($userType === 'admin') {
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
        }
        
        $checkStmt = $dbConnection->prepare("SELECT id FROM PRODUCTS WHERE tempID = ? LIMIT 1");
        if (!$checkStmt) {
            apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
            return;
        }
        
        $tempID = $inputData['tempID'];
        $checkStmt->bind_param("s", $tempID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $existingBook = $checkResult->fetch_assoc();
        $checkStmt->close();
        
        if ($existingBook) {
            apiResponse(false, null, 'A book with this tempID already exists', 409);
            return;
        }
        
        if (!empty($inputData['isbn13'])) {
            $checkIsbnStmt = $dbConnection->prepare("SELECT id FROM PRODUCTS WHERE isbn13 = ? LIMIT 1");
            if (!$checkIsbnStmt) {
                apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
                return;
            }
            
            $isbn13 = $inputData['isbn13'];
            $checkIsbnStmt->bind_param("s", $isbn13);
            $checkIsbnStmt->execute();
            $checkIsbnResult = $checkIsbnStmt->get_result();
            $existingIsbn = $checkIsbnResult->fetch_assoc();
            $checkIsbnStmt->close();
            
            if ($existingIsbn) {
                apiResponse(false, null, 'A book with this ISBN13 already exists', 409);
                return;
            }
        }
        
        $query = "INSERT INTO PRODUCTS (
                    tempID, 
                    title, 
                    description, 
                    isbn13, 
                    publishedDate, 
                    publisher, 
                    author, 
                    pageCount, 
                    maturityRating, 
                    language, 
                    smallThumbnail, 
                    thumbnail, 
                    accessibleIn, 
                    ratingsCount
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insertStmt = $dbConnection->prepare($query);
        if (!$insertStmt) {
            apiResponse(false, null, 'Database query preparation failed: ' . $dbConnection->error, 500);
            return;
        }
        
        $title = $inputData['title'];
        $description = $inputData['description'] ?? null;
        $isbn13 = $inputData['isbn13'] ?? null;
        $publishedDate = !empty($inputData['publishedDate']) ? $inputData['publishedDate'] : null;
        $publisher = $inputData['publisher'] ?? null;
        $author = $inputData['author'] ?? null;
        $pageCount = isset($inputData['pageCount']) ? (int)$inputData['pageCount'] : null;
        $maturityRating = $inputData['maturityRating'] ?? null;
        $language = $inputData['language'] ?? null;
        $smallThumbnail = $inputData['smallThumbnail'] ?? null;
        $thumbnail = $inputData['thumbnail'] ?? null;
        $accessibleIn = $inputData['accessibleIn'] ?? null;
        $ratingsCount = isset($inputData['ratingsCount']) ? (int)$inputData['ratingsCount'] : 0;
        
     
        
        $insertStmt->bind_param(
            "sssssssisssssi", 
            $tempID,
            $title,
            $description,
            $isbn13,
            $publishedDate,
            $publisher,
            $author,
            $pageCount,
            $maturityRating,
            $language,
            $smallThumbnail,
            $thumbnail,
            $accessibleIn,
            $ratingsCount
        );
        
        $success = $insertStmt->execute();
        $newBookId = $insertStmt->insert_id;
        $insertStmt->close();
        
        if (!$success) {
            apiResponse(false, null, 'Failed to add the book: ' . $dbConnection->error, 500);
            return;
        }
        
   
        if ($userType === 'admin' && !empty($inputData['price'])) {
            $storeInfoQuery = "INSERT INTO STORE_INFO (book_id, store_id, price, rating) VALUES (?, ?, ?, NULL)";
            
            $storeInfoStmt = $dbConnection->prepare($storeInfoQuery);
            if (!$storeInfoStmt) {
                apiResponse(true, [
                    'message' => 'Book added successfully but failed to associate with your store.',
                    'book_id' => $newBookId
                ], null, 201);
                return;
            }
            
            $price = (float)$inputData['price'];
            $storeId = $adminStore['store_id'];
            
            $storeInfoStmt->bind_param("iid", $newBookId, $storeId, $price);
            $storeInfoSuccess = $storeInfoStmt->execute();
            $storeInfoStmt->close();
            
            if (!$storeInfoSuccess) {
                apiResponse(true, [
                    'message' => 'Book added successfully but failed to associate with your store: ' . $dbConnection->error,
                    'book_id' => $newBookId
                ], null, 201);
                return;
            }
        }
        
        if (!empty($inputData['categories']) && is_array($inputData['categories'])) {
            $categorySuccess = true;
            $categoryErrors = [];
            
            foreach ($inputData['categories'] as $categoryId) {
                $checkCategoryStmt = $dbConnection->prepare("SELECT category_id FROM CATEGORIES WHERE category_id = ? LIMIT 1");
                if (!$checkCategoryStmt) {
                    $categorySuccess = false;
                    $categoryErrors[] = "Failed to validate category ID: " . $dbConnection->error;
                    continue;
                }
                
                $categoryId = (int)$categoryId;
                $checkCategoryStmt->bind_param("i", $categoryId);
                $checkCategoryStmt->execute();
                $checkCategoryResult = $checkCategoryStmt->get_result();
                $categoryExists = $checkCategoryResult->fetch_assoc();
                $checkCategoryStmt->close();
                
                if (!$categoryExists) {
                    $categorySuccess = false;
                    $categoryErrors[] = "Category ID $categoryId does not exist";
                    continue;
                }
                
                $bookCatQuery = "INSERT INTO BOOK_CATS (book_id, category_id) VALUES (?, ?)";
                
                $bookCatStmt = $dbConnection->prepare($bookCatQuery);
                if (!$bookCatStmt) {
                    $categorySuccess = false;
                    $categoryErrors[] = "Failed to prepare category association query: " . $dbConnection->error;
                    continue;
                }
                
                $bookCatStmt->bind_param("ii", $newBookId, $categoryId);
                $bookCatSuccess = $bookCatStmt->execute();
                $bookCatStmt->close();
                
                if (!$bookCatSuccess) {
                    $categorySuccess = false;
                    $categoryErrors[] = "Failed to associate book with category $categoryId: " . $dbConnection->error;
                }
            }
            
            if (!$categorySuccess) {
                apiResponse(true, [
                    'message' => 'Book added successfully but some category associations failed',
                    'book_id' => $newBookId,
                    'category_errors' => $categoryErrors
                ], null, 201);
                return;
            }
        }
        
        apiResponse(true, [
            'message' => 'Book added successfully',
            'book_id' => $newBookId
        ], null, 201);
    }
}
?>