<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable=['body','teacher_id'];
    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
}
