<?php

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return dirname(__DIR__) . '/' . ltrim($path, '/');
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        static $configs = [];
        
        $parts = explode('.', $key);
        $file = $parts[0];
        $nestedKey = $parts[1] ?? null;
        
        if (!isset($configs[$file])) {
            $filePath = base_path("config/{$file}.php");
            if (file_exists($filePath)) {
                $configs[$file] = require $filePath;
            } else {
                return $default;
            }
        }
        
        if ($nestedKey) {
            return $configs[$file][$nestedKey] ?? $default;
        }
        
        return $configs[$file] ?? $default;
    }
}

if (!function_exists('url')) {
    function url($path = '') {
        $baseUrl = config('app.url', 'http://localhost');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        return url('public/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = $_SESSION['_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['_token'] = $token;
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION['_token'] ?? bin2hex(random_bytes(32));
    }
}

if (!function_exists('old')) {
    function old($key, $default = '') {
        return $_POST[$key] ?? $default;
    }
}

if (!function_exists('session_flash')) {
    function session_flash($key, $default = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $flashKey = '_flash_' . $key;
        $value = $_SESSION[$flashKey] ?? $default;
        unset($_SESSION[$flashKey]);
        return $value;
    }
}

if (!function_exists('auth')) {
    function auth() {
        return \Core\Auth::user();
    }
}
