<?php

namespace App\Http\Controllers;

use App\Models\CompanyUserEnrollment;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    private UserService $service;
    public function __construct()
    {
        $this->service = new UserService();
        parent::__construct(new User(), $this->service);
    }

    public function create(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $ok = $this->service->createUser($request->all());

            if (!$ok['success']) {
                return response()->json($ok['errors'], 422);
            }

            DB::commit();
            return response()->json(['message' => 'User created'], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Internal Server error'], 500);
        }

    }

    public function update(Model $user, Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $ok = $this->service->updateUser($user, $request->all());
            if (!$ok['success']) {
                return response()->json($ok['errors'], 422);
            }
            DB::commit();
            return response()->json(['message' => 'User updated'], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Internal Server error'], 500);
        }
    }
}