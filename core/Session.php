<?php

namespace Core;

class Session
{
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public static function flash($key, $value)
    {
        self::set('_flash_' . $key, $value);
    }

    public static function getFlash($key, $default = null)
    {
        $key = '_flash_' . $key;
        $value = self::get($key, $default);
        self::remove($key);
        return $value;
    }

    public static function regenerate()
    {
        session_regenerate_id(true);
    }

    public static function destroy()
    {
        session_destroy();
    }
}
