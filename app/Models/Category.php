<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // fillable é usado para definir quais campos podem ser preenchidos em massa (mass assignment)
    protected $fillable = ['name', 'description'];
    public function products()
    {
        // Uma categoria pode ter muitos produtos (1-N)
        return $this->hasMany(Product::class);
    }
}
