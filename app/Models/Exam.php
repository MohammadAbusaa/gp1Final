<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $fillable=['name','room_id','start_date','enabled'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function student()
    {
        return $this->belongsToMany(Student::class)->withPivot('mark','feedback')->withTimestamps();
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
