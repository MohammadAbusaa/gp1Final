<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Father extends Authenticatable
{
    use HasApiTokens, HasFactory;
    protected $fillable=['name','email','password'];
    protected $table='fathers';
    public function getAuthPassword(){
        return $this->password;
    }
}
