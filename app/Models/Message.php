<?php
// app/Models/Message.php

namespace App\Models;

use Illuminate\Support\Collection;

class Message extends JsonModel
{
    protected static $filePath = 'messages.json';
    
    public static function getPending(): Collection
    {
        $data = parent::where('approved', 'pending')
                      ->where('status', 'active');
        
        return collect($data);
    }
    
     public static function getApproved()
    {
        $data = parent::where('approved', 'true')
                      ->where('status', 'active');
        
        // DEBUG: Ver qué retorna
        // dd('getApproved return type:', gettype($data), 'value:', $data);
        
        return $data;
    }
    
    public static function getDeleted(): Collection
    {
        $data = parent::where('status', 'deleted');
        
        return collect($data);
    }
    
    public static function getUserMessages($username): Collection
    {
        $data = parent::where('user', $username);
        
        return collect($data);
    }
    
    // Sobrescribir el método where para retornar colecciones
    public static function where($key, $value): Collection
    {
        $data = static::readData();
        $filteredData = array_filter($data, function($item) use ($key, $value) {
            return isset($item[$key]) && $item[$key] == $value;
        });
        
        return collect(array_values($filteredData));
    }
    
    // Sobrescribir el método all para retornar colecciones
    public static function all(): Collection
    {
        return collect(parent::all());
    }
}