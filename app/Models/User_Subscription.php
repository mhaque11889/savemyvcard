<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;

class User_Subscription extends Model
{
    use HasFactory;
    protected $table = 'user_subscription';
    protected $primaryKey = 'id';

    protected $fillable = [
        'userid',
        'subscriptionid',
        'start_date',
        'end_date',
        'vcard_count',
        'is_active'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscriptionid');
    }

    public function isValid()
    {
        return $this->is_active && now()->between($this->start_date, $this->end_date);
    }

    public function canCreateVcard()
    {
        return $this->isValid() && $this->vcard_count < $this->subscription->vcardAllowed;
    }  
}
