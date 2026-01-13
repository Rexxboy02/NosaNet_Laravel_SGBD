<?php
// app/Models/User.php

namespace App\Models;

class User extends JsonModel
{
    protected static $filePath = 'users.json';
    
    public static function findByUsername($username)
    {
        $data = static::readData();
        
        foreach ($data as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        
        return null;
    }
    
    public static function findByEmail($email)
    {
        $data = static::readData();
        
        foreach ($data as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        
        return null;
    }
}