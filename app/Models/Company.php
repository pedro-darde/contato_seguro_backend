<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'cnpj', 'address'];
    protected $table = 'company';
    public function users()
    {
        return $this->belongsToMany(User::class, CompanyUserEnrollment::TABLE, "id_user", "id_company");
    }
}
