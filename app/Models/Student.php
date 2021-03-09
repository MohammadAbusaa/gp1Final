<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory;
    protected $fillable=['name','email','password'];
    protected $hidden=['remember_token','password'];
    protected $table='students';
    public function getAuthPassword(){
        return $this->password;
    }
}
