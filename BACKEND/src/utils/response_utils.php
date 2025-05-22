<?php
if (!function_exists('apiResponse')) {//marcel
    function apiResponse($isSuccess, $data = null, $errorMessage = null, $httpStatusCode = 200) {
        if (!headers_sent()) {
            http_response_code($httpStatusCode);
            header('Content-Type: application/json');
        }

        $response = ['status' => $isSuccess ? 'success' : 'error'];

        if (!$isSuccess && $errorMessage !== null) {
            $response['message'] = $errorMessage;
        }
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit;
    }
}
?>