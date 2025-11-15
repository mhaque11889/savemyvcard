<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataReceived extends Model
{
    use HasFactory;
    protected $table = 'dataReceived';
    protected $primaryKey = 'id';
}
