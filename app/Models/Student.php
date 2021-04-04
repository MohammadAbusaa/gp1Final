<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable=['class'];
    public function father()
    {
        return $this->belongsTo(Father::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
    //protected $primaryKey='user_id';
}
