<?php

namespace App\Models;
 
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Faculty;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table ='accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
   

     protected $fillable = [
        'id',
        'email',
        'password',
        'user_type',
        'otp',
        'otp_expiry',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function faculties()
    {
        return $this->hasMany(Faculty::class);
    }
   
}
