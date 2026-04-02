<?php

namespace Core;

class Request
{
    public function uri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        
        if ($base !== '' && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        
        return '/' . trim($uri, '/');
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function all()
    {
        return array_merge($_GET, $_POST);
    }

    public function input($key, $default = null)
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? null;
        return $value ?? $default;
    }

    public function has($key)
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public function validateCsrf()
    {
        if (!config('security.csrf_enabled', true)) {
            return true;
        }

        if ($this->method() === 'POST' || $this->method() === 'PUT' || $this->method() === 'DELETE') {
            $token = $this->input('_token');
            if (!$token || $token !== Session::get('_token')) {
                http_response_code(403);
                view('errors/403', ['message' => 'CSRF Token Mismatch']);
                exit;
            }
        }
        return true;
    }

    public function file($key)
    {
        return $_FILES[$key] ?? null;
    }
}
