<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public const TABLE = "user";
    protected $table = self::TABLE;
    protected $fillable = ['name', 'cellphone', 'email', 'birth_date', 'birth_city'];


    public function companies()
    {
        return $this->belongsToMany(Company::class, CompanyUserEnrollment::TABLE, "id_user", "id_company");
    }

    static function withNoCompany()
    {
        $idsUser = CompanyUserEnrollment::all()->pluck("id_user")->all();
        return self::whereNotIn("id", $idsUser)->get()->all();
    }
}
