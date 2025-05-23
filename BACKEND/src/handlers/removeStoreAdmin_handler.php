<?php

require_once __DIR__ . '/../utils/auth_utils.php';
require_once __DIR__ . '/../utils/userid_utils.php';
require_once __DIR__ . '/../utils/sanitise_utils.php';
require_once __DIR__ . '/../utils/response_utils.php';

if (!function_exists('handleRemoveStoreAdmin')) {//marcel
    function handleRemoveStoreAdmin($data, $db) {
        $requestingApiKey = isset($data['api_key']) ? sanitiseInput($data['api_key']) : null;
        $userIdToDeleteInput = isset($data['user_id']) ? sanitiseInput($data['user_id']) : null;

        if (empty($requestingApiKey) || $userIdToDeleteInput === null) {
            apiResponse(false, null, 'API key and user ID to delete are required.', 400);
            return;
        }

        if (!is_numeric($userIdToDeleteInput)) {
            apiResponse(false, null, 'User ID to delete must be numeric.', 400);
            return;
        }
        $userIdToDelete = (int)$userIdToDeleteInput;

        if (!checkAuth($requestingApiKey, 'super', $db)) {
            apiResponse(false, null, 'Authorisation failed: Requesting user is not a super admin or API key is invalid.', 403);
            return;
        }

        $requestingSuperAdminId = getUserIdUtil($requestingApiKey, $db);
        if ($requestingSuperAdminId === null) {
            apiResponse(false, null, 'Authorisation failed: Could not verify requesting super admin.', 403);
            return;
        }

        if ($requestingSuperAdminId === $userIdToDelete) {
            apiResponse(false, null, 'Super admins cannot remove themselves using this endpoint.', 400);
            return;
        }
        
        $db->begin_transaction();

        try {
           
            $stmtUsers = $db->prepare("DELETE FROM USERS WHERE id = ?");
            if (!$stmtUsers) {
                throw new Exception("DB Prepare Error (USERS delete): " . $db->error);
            }
            $stmtUsers->bind_param("i", $userIdToDelete);
            if (!$stmtUsers->execute()) {
                throw new Exception("DB Execute Error (USERS delete): " . $stmtUsers->error);
            }

            $affectedRows = $stmtUsers->affected_rows;
            $stmtUsers->close();

            if ($affectedRows === 0) {
                apiResponse(false, null, "User with ID {$userIdToDelete} not found for removal.", 404);
                return;
            }

            $db->commit();
            apiResponse(true, "User account removed successfully (admin roles cascaded).", null, 200);

        } catch (Exception $e) {
            $db->rollback();
            error_log($e->getMessage());
            apiResponse(false, null, 'A server error occurred while removing the user.', 500);
        }
    }
}
?>