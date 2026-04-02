<?php

namespace Core;

class Auth
{
    public static function attempt($email, $password)
    {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL", [$email]);

        if ($user && password_verify($password, $user->password)) {
            Session::set('user_id', $user->id);
            Session::set('user_email', $user->email);
            Session::set('user_name', $user->first_name . ' ' . $user->last_name);
            
            $roles = $db->fetchAll("SELECT r.slug FROM roles r 
                                    JOIN user_roles ur ON r.id = ur.role_id 
                                    WHERE ur.user_id = ?", [$user->id]);
            Session::set('roles', array_column($roles, 'slug'));
            
            return true;
        }
        return false;
    }

    public static function check()
    {
        return Session::has('user_id');
    }

    public static function user()
    {
        if (!self::check()) return null;
        
        $db = Database::getInstance();
        return $db->fetch("SELECT * FROM users WHERE id = ?", [Session::get('user_id')]);
    }

    public static function hasRole($role)
    {
        $roles = Session::get('roles', []);
        return in_array($role, $roles);
    }

    public static function logout()
    {
        Session::destroy();
        Session::init();
    }

    public static function id()
    {
        return Session::get('user_id');
    }
}
