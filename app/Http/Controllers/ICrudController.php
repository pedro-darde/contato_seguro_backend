<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ICrudController
{
    public function list(Request $request): JsonResponse;
    public function create(Request $request): JsonResponse;
    public function delete(int $id): JsonResponse;
    public function update(int $id, Request $request): JsonResponse;
}