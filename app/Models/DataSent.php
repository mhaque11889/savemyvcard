<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSent extends Model
{
    use HasFactory;
    protected $table = 'dataSent';
    protected $primaryKey = 'id';
}
