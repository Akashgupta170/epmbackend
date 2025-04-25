<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessoryCategory extends Model
{
    protected $fillable = ['name'];
    public function assesories()
    {
        return $this->hasMany(Assesories::class, 'category_id');
    }
}
