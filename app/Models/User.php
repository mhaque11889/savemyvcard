<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Vcard;
use App\Models\User_Subscription;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $primaryKey = 'id';

    public function vCards()
    {
        return $this->belongsToMany(Vcard::class, 'user_vcard', 'userid', 'vcardid');
    }

    public function userSubscription()
    {
        return $this->hasOne(User_Subscription::class, 'userid');
    }
}
