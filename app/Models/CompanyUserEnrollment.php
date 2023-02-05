<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyUserEnrollment extends Model
{
    public const TABLE = "company_user_enrollment";
    protected $fillable = ["id_user", "id_company"];
    protected $table = self::TABLE;
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;
}