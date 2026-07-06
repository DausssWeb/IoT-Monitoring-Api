<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    protected $fillable = [
        'temperature',
        'humidity',
        'flame',
        'status'
    ];
}
