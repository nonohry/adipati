<?php

namespace Core;

class Router
{
    protected $routes = [];
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($uri, $handler, $middleware = [])
    {
        $this->addRoute('GET', $uri, $handler, $middleware);
        return $this;
    }

    public function post($uri, $handler, $middleware = [])
    {
        $this->addRoute('POST', $uri, $handler, $middleware);
        return $this;
    }

    protected function addRoute($method, $uri, $handler, $middleware)
    {
        $uri = '/' . trim($uri, '/');
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function resolve()
    {
        $uri = $this->request->uri();
        $method = $this->request->method();

        if ($method !== 'GET') {
            $this->request->validateCsrf();
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                foreach ($route['middleware'] as $mw) {
                    $this->runMiddleware($mw);
                }
                return $this->executeHandler($route['handler'], $matches);
            }
        }

        http_response_code(404);
        return View::render('errors/404');
    }

    protected function runMiddleware($name)
    {
        $class = "\\App\\Http\\Middleware\\" . ucfirst($name) . "Middleware";
        if (class_exists($class)) {
            $middleware = new $class();
            $middleware->handle();
        } elseif ($name === 'guest' && Auth::check()) {
            redirect('/dashboard');
        } elseif ($name === 'auth' && !Auth::check()) {
            redirect('/login');
        }
    }

    protected function executeHandler($handler, $params)
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, array_filter($params, 'is_string'));
        }

        if (is_string($handler)) {
            list($controllerClass, $method) = explode('@', $handler);
            $controllerClass = "\\App\\Http\\Controllers\\" . $controllerClass;
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $method)) {
                    return call_user_func_array([$controller, $method], array_filter($params, 'is_string'));
                }
            }
        }

        throw new \Exception("Handler not found: " . (is_string($handler) ? $handler : 'Closure'));
    }
}
