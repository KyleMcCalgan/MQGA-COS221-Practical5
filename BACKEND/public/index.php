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
    if (!empty($jsonPayload)) {
        $decodedJson = json_decode($jsonPayload, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $inputData = $decodedJson;
        }
    }
    if (empty($inputData) && !empty($_POST)) {
        $inputData = $_POST;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $inputData = $_GET;
}

if (!empty($inputData)) {
    $inputData = sanitizeInput($inputData);
}

$actionType = $inputData['type'] ?? null;
$dbConnection = null;

if ($actionType) {
    $dbConnection = getDbConnection();
}

switch ($actionType) {
    case 'Register':
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

    case 'Login':
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

    case 'GetAllProducts':
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

    case 'GetHighestRatedProducts':
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

    case 'GetFeaturedProducts':
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

    case 'GetProduct':
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
        
    case 'GetAllListedProducts':
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
?>
