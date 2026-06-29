<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    // Acrecentar o trait HasApiTokens para permitir que o modelo User utilize tokens de acesso pessoal do Laravel Sanctum
    use HasApiTokens;
}
