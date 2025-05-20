<?php
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data) {
        if (is_string($data)) {
            $data = trim($data);
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = sanitizeInput($value);
            }
        }
        return $data;
    }
}
?>