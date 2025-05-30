<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/utils/response_utils.php';
require_once __DIR__ . '/../src/utils/sanitise_utils.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$inputData = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $jsonPayload = file_get_contents('php://input');
    if (! empty($jsonPayload)) {
        $decodedJson = json_decode($jsonPayload, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $inputData = $decodedJson;
        }
    }
    if (empty($inputData) && ! empty($_POST)) {
        $inputData = $_POST;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $inputData = $_GET;
}

if (! empty($inputData)) {
    $inputData =sanitiseInput($inputData);
}

$actionType   = $inputData['type'] ?? null;
$dbConnection = null;

if ($actionType) {
    $dbConnection = getDbConnection();
}

switch ($actionType) {
    case 'Register'://marcel
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for Register. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/register_handler.php')) {
            require_once __DIR__ . '/../src/handlers/register_handler.php';
            handleRegister($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'Register handler not found.', 500);
        }
        break;

    case 'Login'://marcel
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for Login. Use POST.', 405);
            exit;
        }
        if (file_exists(__DIR__ . '/../src/handlers/login_handler.php')) {
            require_once __DIR__ . '/../src//handlers/login_handler.php';
            handleLogin($inputData);
        } else {
            error_log("Login handler not found at " . __DIR__ . '/handlers/login_handler.php');
            apiResponse(false, null, 'Endpoint configuration error (Login).', 500);
        }
        break;
    
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Kyle Section


        case 'AddGenre':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for AddGenre. Use POST.', 405);
        }   
        if (file_exists(__DIR__ . '/../src/handlers/add_genre_handler.php')) {
            require_once __DIR__ . '/../src/handlers/add_genre_handler.php';
            handleAddGenre($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'AddGenre handler not found.', 500);
        }
        break;


        case 'UpdateGenreVisibility':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for UpdateGenreVisibility. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/update_genre_visibility_handler.php')) {
            require_once __DIR__ . '/../src/handlers/update_genre_visibility_handler.php';
            handleUpdateGenreVisibility($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'UpdateGenreVisibility handler not found.', 500);
        }
        break;

        case 'GetGenre':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetGenre. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/get_genre_handler.php')) {
            require_once __DIR__ . '/../src/handlers/get_genre_handler.php';
            handleGetGenre($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetGenre handler not found.', 500);
        }
        break;


        case 'GetStoreProducts':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetStoreProducts. Use GET or POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/get_store_products_handler.php')) {
            require_once __DIR__ . '/../src/handlers/get_store_products_handler.php';
            handleGetStoreProducts($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetStoreProducts handler not found.', 500);
        }
        break;

        case 'DeleteStoreProducts':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            apiResponse(false, null, 'Invalid request method for DeleteStoreProducts. Use POST or DELETE.', 405);
        }   
        if (file_exists(__DIR__ . '/../src/handlers/delete_store_products_handler.php')) {
            require_once __DIR__ . '/../src/handlers/delete_store_products_handler.php';
            handleDeleteStoreProducts($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'DeleteStoreProducts handler not found.', 500);
        }
        break;

        case 'DeleteProduct':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            apiResponse(false, null, 'Invalid request method for DeleteProduct. Use POST or DELETE.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/delete_product_handler.php')) {
            require_once __DIR__ . '/../src/handlers/delete_product_handler.php';
            handleDeleteProduct($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'DeleteProduct handler not found.', 500);
        }
        break;

        case 'AddProduct':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for AddProduct. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/add_product_handler.php')) {
            require_once __DIR__ . '/../src/handlers/add_product_handler.php';
            handleAddProduct($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'AddProduct handler not found.', 500);
        }
        break;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    case 'GetAllProducts': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for getAllProducts. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getallproducts_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getallproducts_handler.php';
            handleGetAllProducts($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetAllProducts handler not found.', 500);
        }
        break;

    case 'GetHighestRatedProducts': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetHighestRatedProducts. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/gethighestratedproducts_handler.php')) {
            require_once __DIR__ . '/../src/handlers/gethighestratedproducts_handler.php';
            handleGetHighestRatedProducts($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetHighestRatedProducts handler not found.', 500);
        }
        break;


    case 'GetFeaturedProducts': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetFeaturedProducts. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getfeaturedproducts_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getfeaturedproducts_handler.php';
            handleGetFeaturedProducts($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetFeaturedProducts handler not found.', 500);
        }
        break;

    case 'GetProduct': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetProduct. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getproduct_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getproduct_handler.php';
            handleGetProduct($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetProduct handler not found.', 500);
        }
        break;

    case 'GetAllListedProducts': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetAllListedProducts. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getalllistedproducts_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getalllistedproducts_handler.php';
            handleGetAllListedProducts($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetAllListedProducts handler not found.', 500);
        }
        break;
  case 'UpdateProductSuper': //marcel
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for UpdateProduct. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/updateProductAdmin_handler.php')) {
            require_once __DIR__ . '/../src/handlers/updateProductAdmin_handler.php';
            handleUpdateProductAdmin($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'UpdateProductAdmin handler not found.', 500);
        }
        break;


    case 'GetStores': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetStores. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getstores_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getstores_handler.php';
            handleGetStores($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetStores handler not found.', 500);
        }
        break;

    case 'DeleteStore': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for DeleteStore. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/deletestore_handler.php')) {
            require_once __DIR__ . '/../src/handlers/deletestore_handler.php';
            handleDeleteStore($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'DeleteStore handler not found.', 500);
        }
        break;

    case 'AddStore': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for AddStore. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/addstore_handler.php')) {
            require_once __DIR__ . '/../src/handlers/addstore_handler.php';
            handleAddStore($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'AddStore handler not found.', 500);
        }
        break;

    case 'AddStoreAdmin': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for AddStoreAdmin. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/addstoreadmin_handler.php')) {
            require_once __DIR__ . '/../src/handlers/addstoreadmin_handler.php';
            handleAddStoreAdmin($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'AddStoreAdmin handler not found.', 500);
        }
        break;
    
     case 'AddInfoForStore': //marcel
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for AddInfoForStore. Use POST.', 405);
        }
        
        if (file_exists(__DIR__ . '/../src/handlers/AddInfoForStore_handler.php')) {
            require_once __DIR__ . '/../src/handlers/AddInfoForStore_handler.php';
            handleAddInfoForStore($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'AddInfoForStore handler not found.', 500);
        }
        break;

    case 'UpdateStore': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for UpdateStore. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/updatestore_handler.php')) {
            require_once __DIR__ . '/../src/handlers/updatestore_handler.php';
            handleUpdateStore($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'UpdateStore handler not found.', 500);
        }
        break;

    case 'UpdateUserInfo': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for UpdateUserInfo. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/updateuserinfo_handler.php')) {
            require_once __DIR__ . '/../src/handlers/updateuserinfo_handler.php';
            handleUpdateUserInfo($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'UpdateUserInfo handler not found.', 500);
        }
        break;

    case 'GetUserReviewsRatings': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetUserReviewsRatings. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getuserreviewsratings_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getuserreviewsratings_handler.php';
            handleGetUserReviewsRatings($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetUserReviewsRatings handler not found.', 500);
        }
        break;

    case 'GetAllProductsRR': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetAllProductsRR. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getallproductsrr_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getallproductsrr_handler.php';
            handleGetAllProductsRR($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetAllProductsRR handler not found.', 500);
        }
        break;

    case 'GetBookReviewsRatings': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetBookReviewsRatings. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getbookreviewsratings_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getbookreviewsratings_handler.php';
            handleGetBookReviewsRatings($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetBookReviewsRatings handler not found.', 500);
        }
        break;

    case 'GetUsers': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetUsers. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getusers_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getusers_handler.php';
            handleGetUsers($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetUsers handler not found.', 500);
        }
        break;

    case 'RemoveUsers': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for RemoveUsers. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/removeusers_handler.php')) {
            require_once __DIR__ . '/../src/handlers/removeusers_handler.php';
            handleRemoveUsers($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'RemoveUsers handler not found.', 500);
        }
        break;
    
      case 'RemoveUserReview'://marcel
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
            apiResponse(false, null, 'Invalid request method for RemoveUserReview. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/removeUserReview_handler.php')) {
            require_once __DIR__ . '/../src/handlers/removeUserReview_handler.php';
            handleRemoveUserReview($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'RemoveUserReview handler not found.', 500);
        }
        break;

    case 'AddUserRating'://marcel
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for AddUserRating. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/addUserRating_handler.php')) {
            require_once __DIR__ . '/../src/handlers/addUserRating_handler.php';
            handleAddUserRating($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'AddUserRating handler not found.', 500);
        }
        break;

    case 'AddUserReview': //marcel
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for AddUserReview. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/addUserReview_handler.php')) {
            require_once __DIR__ . '/../src/handlers/addUserReview_handler.php';
            handleAddUserReview($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'AddUserReview handler not found.', 500);
        }
     break;

     case 'RemoveStoreAdmin'://marcel
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
            apiResponse(false, null, 'Invalid request method for RemoveStoreAdmin. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/removeStoreAdmin_handler.php')) {
            require_once __DIR__ . '/../src/handlers/removeStoreAdmin_handler.php';
            handleRemoveStoreAdmin($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'RemoveStoreAdmin handler not found.', 500);
        }
        break;
    

    case 'WebsiteSummary': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for WebsiteSummary. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/websitesummary_handler.php')) {
            require_once __DIR__ . '/../src/handlers/websitesummary_handler.php';
            handleWebsiteSummary($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'WebsiteSummary handler not found.', 500);
        }
        break;

    case 'GetUserBookRating': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetUserBookRating. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getuserbookrating_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getuserbookrating_handler.php';
            handleGetUserBookRating($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetUserBookRating handler not found.', 500);
        }
        break;

    case 'GetStoreMissingBooks': // Cailin
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            apiResponse(false, null, 'Invalid request method for GetStoreMissingBooks. Use POST.', 405);
        }
        if (file_exists(__DIR__ . '/../src/handlers/getstoremissingbooks_handler.php')) {
            require_once __DIR__ . '/../src/handlers/getstoremissingbooks_handler.php';
            handleGetStoreMissingBooks($inputData, $dbConnection);
        } else {
            apiResponse(false, null, 'GetStoreMissingBooks handler not found.', 500);
        }
        break;

    case null:
        apiResponse(true, ['info' => 'API is operational. Please specify a type.'], null);
        break;

    default:
        apiResponse(false, null, 'Unknown action type: ' . htmlspecialchars($actionType ?? 'N/A'), 400);
        break;
}

if ($dbConnection) {
    $dbConnection->close();
}
