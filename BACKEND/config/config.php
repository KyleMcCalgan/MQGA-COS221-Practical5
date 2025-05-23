<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!function_exists('loadEnv')) {//marcel
    function loadEnv($file) {
        if (!file_exists($file)) return;
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2) + [null, null];
            if ($name === null) continue;
            $value = trim(preg_match('/^["\'](.*)["\']$/', trim($value ?? ''), $m) ? $m[1] : trim($value ?? ''));
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

loadEnv(__DIR__ . '/../.env');
?>