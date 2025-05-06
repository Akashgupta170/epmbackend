<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessoryAssign extends Model
{
    protected $fillable = [
        'user_id', 'accessory_id', 'assigned_at','status'
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
