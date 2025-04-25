<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $fillable = [
        'category_id', 'accessory_no', 'model', 'condition', 'issue_date', 'note','status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
