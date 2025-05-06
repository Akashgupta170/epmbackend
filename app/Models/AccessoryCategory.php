<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessoryCategory extends Model
{
    protected $fillable = ['name','category_code','instock'];

    public function accessories()
    {
        return $this->hasMany(Accessory::class);
    }
}
