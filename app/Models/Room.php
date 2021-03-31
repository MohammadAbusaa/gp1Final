<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable=['name','subject','class','type','teacher_id'];
    use HasFactory;
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
