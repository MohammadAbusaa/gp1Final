<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Authenticatable
{
    use hasApiTokens, HasFactory;
    protected $fillable=['name','email','password'];
    protected $table='teachers';
    public function getAuthPassword(){
        return $this->password;
    }
}
