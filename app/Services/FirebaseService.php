<?php

namespace App\Services;

use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Support\Str;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $this->database = Firebase::database();
    }

    /**
     * Get a reference from the database.
     */
    public function getReference(string $path)
    {
        return $this->database->getReference($path);
    }

    /**
     * Get value from a path.
     */
    public function getValue(string $path)
    {
        return $this->getReference($path)->getValue();
    }

    /**
     * Set value at a path.
     */
    public function setValue(string $path, $value)
    {
        return $this->getReference($path)->set($value);
    }

    /**
     * Update value at a path.
     */
    public function updateValue(string $path, array $value)
    {
        return $this->getReference($path)->update($value);
    }

    /**
     * Delete value at a path.
     */
    public function deleteValue(string $path)
    {
        return $this->getReference($path)->remove();
    }

    /**
     * Push a new item into a list.
     */
    public function push(string $path, array $value)
    {
        $newPostKey = $this->getReference($path)->push()->getKey();
        $this->setValue($path . '/' . $newPostKey, array_merge(['id' => $newPostKey], $value));
        return $newPostKey;
    }
}
