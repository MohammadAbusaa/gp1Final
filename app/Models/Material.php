<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $fillable=['description','room_id','file_id'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function file()
    {
        return $this->hasOne(File::class);
    }
}
