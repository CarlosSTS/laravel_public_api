<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function products()
    {
        // Uma categoria pode ter muitos produtos (1-N)
        return $this->hasMany(Product::class);
    }
}
