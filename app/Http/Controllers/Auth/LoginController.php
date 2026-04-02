<?php

namespace App\Http\Controllers\Auth;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Database;

class LoginController extends Controller
{
    public function showForm()
    {
        return $this->view('auth.login');
    }

    public function login()
    {
        $email = filter_var($this->request->input('email'), FILTER_SANITIZE_EMAIL);
        $password = $this->request->input('password');

        if (!$email || !$password) {
            Session::flash('error', 'Email and password are required.');
            return $this->back();
        }

        if (Auth::attempt($email, $password)) {
            $user = Auth::user();

            if ($user->must_change_password == 1) {
                Session::flash('info', 'For security reasons, please change your password before continuing.');
                return $this->redirect('/change-password');
            }

            Session::flash('success', 'Welcome back, ' . $user->first_name . '!');
            return $this->redirect('/dashboard');
        }

        Session::flash('error', 'Invalid email or password.');
        return $this->back();
    }

    public function logout()
    {
        Auth::logout();
        Session::flash('success', 'You have been logged out successfully.');
        return $this->redirect('/');
    }
}
