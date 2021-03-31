<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable=['major'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    //protected $primaryKey='user_id';
}
