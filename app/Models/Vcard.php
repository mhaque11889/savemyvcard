<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Vcard extends Model
{
    use HasFactory;
    protected $table = 'vcard';
    protected $primaryKey = 'id';

    protected $fillable = [
        'premiumCode','uniqueCode','tentCardCode','mobileStickerCode'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_vcard', 'vcardid', 'userid');
    }
}
