<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assesories extends Model
{
    protected $fillable = [
        'category_id',
        'accessory_no',
        'name',
        'description',
    ];

    // Belongs to a category
    public function category()
    {
        return $this->belongsTo(AccessoryCategory::class, 'category_id');
    }

    // Has many assignments
    public function assignments()
    {
        return $this->hasMany(AssignedAccessory::class, 'accessory_id');
    }
}
