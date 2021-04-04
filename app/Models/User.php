<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }
    public function student()
    {
        return $this->hasOne(Student::class);
    }
    public function father()
    {
        return $this->hasOne(Father::class);
    }
    public function files()
    {
        return $this->hasMany(File::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
