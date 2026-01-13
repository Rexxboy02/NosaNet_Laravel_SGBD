<?php
// app/Models/JsonModel.php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

abstract class JsonModel
{
    protected static $filePath;
    
    protected static function getFilePath()
    {
        return database_path('json/' . static::$filePath);
    }
    
    protected static function readData(): array
    {
        $filePath = static::getFilePath();
        
        if (!File::exists($filePath)) {
            File::put($filePath, json_encode([]));
            return [];
        }
        
        $content = File::get($filePath);
        $data = json_decode($content, true);
        
        return is_array($data) ? $data : [];
    }
    
    protected static function writeData(array $data): bool
    {
        $filePath = static::getFilePath();
        File::put($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    
    public static function all(): Collection
    {
        return collect(static::readData());
    }
    
    public static function find($id): ?array
    {
        $data = static::readData();
        
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        
        return null;
    }
    
    public static function create(array $attributes): array
    {
        $data = static::readData();
        $data[] = $attributes;
        static::writeData($data);
        return $attributes;
    }
    
    public static function update($id, array $attributes): bool
    {
        $data = static::readData();
        $updated = false;
        
        foreach ($data as &$item) {
            if ($item['id'] == $id) {
                $item = array_merge($item, $attributes);
                $updated = true;
                break;
            }
        }
        
        if ($updated) {
            static::writeData($data);
        }
        
        return $updated;
    }
    
    public static function delete($id): bool
    {
        $data = static::readData();
        $filteredData = array_filter($data, function($item) use ($id) {
            return $item['id'] != $id;
        });
        
        if (count($data) !== count($filteredData)) {
            static::writeData(array_values($filteredData));
            return true;
        }
        
        return false;
    }
    
    public static function where($key, $value): Collection
    {
        $data = static::readData();
        $filteredData = array_filter($data, function($item) use ($key, $value) {
            return isset($item[$key]) && $item[$key] == $value;
        });
        
        return collect(array_values($filteredData));
    }
}