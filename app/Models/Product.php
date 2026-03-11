<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}