<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardExtractionDetails extends Model
{
    use HasFactory;
    protected $table = 'card_extraction_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'isEditedByUser', 'cardName', 'jobTitle', 'companyName', 'contactNo1', 'contactNo2', 'contactNo3', 'contactNo4', 'contactNo5', 'email1', 'email2', 'website1', 'website2', 'address'
    ];
}
