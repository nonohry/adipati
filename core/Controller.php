<?php

namespace Core;

class Controller
{
    protected $request;
    protected $response;
    protected $db;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->db = Database::getInstance();
    }

    protected function view($view, $data = [])
    {
        return $this->response->view($view, $data);
    }

    protected function redirect($url)
    {
        return $this->response->redirect($url);
    }

    protected function back()
    {
        return $this->response->back();
    }

    protected function json($data)
    {
        return $this->response->json($data);
    }

    protected function abort($code, $message = '')
    {
        http_response_code($code);
        View::render('errors/' . $code, ['message' => $message]);
        exit;
    }
}
