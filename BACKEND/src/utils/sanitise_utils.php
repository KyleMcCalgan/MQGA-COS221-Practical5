<?php
if (!function_exists('sanitiseInput')) {//marcel
    function sanitiseInput($data) {
        if (is_string($data)) {
            $data = trim($data);
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = sanitiseInput($value);
            }
        }
        return $data;
    }
}
?>