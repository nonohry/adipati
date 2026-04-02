<?php

namespace Core;

class Response
{
    public function setStatusCode($code)
    {
        http_response_code($code);
        return $this;
    }

    public function header($key, $value)
    {
        header("$key: $value");
        return $this;
    }

    public function json($data)
    {
        $this->header('Content-Type', 'application/json');
        echo json_encode($data);
        exit;
    }

    public function view($view, $data = [])
    {
        return View::render($view, $data);
    }

    public function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    public function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        return $this->redirect($referer);
    }

    public function download($filePath, $fileName)
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            exit;
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
