<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable=['name','assignment_details','start_date','due_date','teacher_id','assignment_file'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
}
