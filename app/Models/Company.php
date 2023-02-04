<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'cnpj', 'address'];

    public function users() {
        return $this->belongsToMany(User::class);
    }
}