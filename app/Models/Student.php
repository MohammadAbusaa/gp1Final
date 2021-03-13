<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use hasApiTokens, HasFactory;
    protected $fillable=['name','email','password'];
    protected $hidden=['remember_token','password'];
    protected $table='students';
    public function getAuthPassword(){
        return $this->password;
    }
    public function rooms(){
        return $this->belongsToMany(Room::class)->withTimeStamps();
    }
}
