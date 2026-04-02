<?php

namespace Core;

class View
{
    public static function render($view, $data = [])
    {
        extract($data);
        
        $viewPath = base_path("resources/views/{$view}.php");
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }

        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        echo $content;
    }

    public static function renderLayout($layout, $content, $data = [])
    {
        extract($data);
        $layoutPath = base_path("resources/views/layouts/{$layout}.php");
        
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout not found: {$layout}");
        }

        include $layoutPath;
    }
    
    public static function partial($name, $data = []) {
        extract($data);
        $path = base_path("resources/views/partials/{$name}.php");
        if(file_exists($path)) {
            include $path;
        }
    }
}
