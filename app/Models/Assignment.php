<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'user_id', 'accessory_id', 'quantity'
    ];

    public function accessory()
    {
        return $this->belongsTo(Accessory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
