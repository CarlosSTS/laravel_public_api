<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    public function product()
    {
        // Um movimento pertence a um produto (N-1)
        return $this->belongsTo(Product::class);
    }
}
