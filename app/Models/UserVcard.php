<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vcard;
use App\Models\User;

class UserVcard extends Model
{
    use HasFactory;
    protected $table = 'user_vcard';
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function vCard()
    {
        return $this->belongsTo(Vcard::class, 'vcardid');
    }
}
