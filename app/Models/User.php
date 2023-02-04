<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'cellphone', 'email', 'birth_date','birth_city'];


    public function companies() {
        return $this->belongsToMany(Company::class);
    }
}
