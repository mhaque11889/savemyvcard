<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookData extends Model
{
    use HasFactory;

    protected $table = 'webhook_data';
    protected $primaryKey = 'id';
}
