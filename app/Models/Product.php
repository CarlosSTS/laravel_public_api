<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
    ];
    public function category()
    {
        // Um produto pertence a uma categoria (N-1)
        return $this->belongsTo(Category::class);
    }

    public function movements()
    {
        // Um produto pode ter muitos movimentos (1-N)
        return $this->hasMany(Movement::class);
    }
}
