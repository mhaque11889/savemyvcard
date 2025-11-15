<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryStateCities extends Model
{
    use HasFactory;
    protected $table = 'cities';
    protected $primaryKey = 'id';
}
