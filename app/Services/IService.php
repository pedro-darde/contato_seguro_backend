<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

interface IService
{
    public function list($options): array;
    public function delete(int $model): array;
}