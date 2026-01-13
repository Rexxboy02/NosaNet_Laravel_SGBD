<?php
// app/Helpers/helpers.php

use Illuminate\Support\Facades\Session;

if (!function_exists('auth_check')) {
    function auth_check()
    {
        return Session::has('username');
    }
}

if (!function_exists('is_professor')) {
    function is_professor()
    {
        return Session::has('is_professor') && Session::get('is_professor') === 'True';
    }
}

if (!function_exists('current_user')) {
    function current_user()
    {
        return [
            'id' => Session::get('user_id'),
            'username' => Session::get('username'),
            'email' => Session::get('email'),
            'is_professor' => Session::get('is_professor'),
            'theme' => Session::get('theme', 'light')
        ];
    }
}