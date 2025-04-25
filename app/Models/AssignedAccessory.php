<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedAccessory extends Model
{
    protected $fillable = [
        'accessory_id',
        'employee_id',
        'assigned_date',
        'status',
    ];

    // Assigned accessory belongs to an accessory
    public function accessory()
    {
        return $this->belongsTo(Assesories::class, 'accessory_id');
    }

    // Belongs to a user (employee)
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
