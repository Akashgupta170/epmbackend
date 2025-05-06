<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $fillable = [
        'accessory_no',
        'brand_name',
        'category_id',
        'vendor_name',
        'purchase_date',
        'amount',
        'condition',
        'images',
        'note',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(AccessoryCategory::class, 'category_id');
    }

    public function assigns()
    {
        return $this->hasMany(AccessoryAssign::class);
    }
}
