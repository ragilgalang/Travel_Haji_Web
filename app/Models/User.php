<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    public $id;
    public $uuid;
    public $name;
    public $email;
    public $username;
    public $password;
    public $remember_token;
    public $role;

    public function __construct(array $attributes = [])
    {
        $this->id = $attributes['id'] ?? null;
        $this->uuid = $attributes['uuid'] ?? null;
        $this->name = $attributes['name'] ?? null;
        $this->email = $attributes['email'] ?? null;
        $this->username = $attributes['username'] ?? null;
        $this->password = $attributes['password'] ?? null;
        $this->role = $attributes['role'] ?? 'user';
    }

    public function getAuthIdentifierName() { return 'uuid'; }
    public function getAuthIdentifier() { return $this->uuid; }
    public function getAuthPassword() { return $this->password; }
    public function getAuthPasswordName() { return 'password'; }
    public function getRememberToken() { return $this->remember_token; }
    public function setRememberToken($value) { $this->remember_token = $value; }
    public function getRememberTokenName() { return 'remember_token'; }
}
