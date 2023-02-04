<?php

namespace App\Http\Controllers;

use App\Models\CompanyUserEnrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct(new User());
    }

    private array $RULES = [
        'name' => 'required|string',
        'email' => 'required|string|email|unique_on_user',
        'cellphone' => 'required|string',
        'birthCity' => 'required|string',
        'birthDate' => 'required|date',
        'companies' => 'array',
        'companies.*' => 'exists:company,id',
        'companiesToRemove' => 'array',
        'companiesToRemove.*' => 'exists:company,id',
        'companiesToAdd' => 'array',
        'companiesToAdd.*' => 'exists:company,id',
    ];
    public function create(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate($this->RULES);
            $user = new User();
            $user->name = $validated['name'];
            $user->birth_city = $validated['birthCity'];
            $user->birth_date = $validated['birthDate'];
            $user->email = $validated['email'];
            $user->cellphone = $validated['cellphone'];
            $id = $user->save();
            if (!empty ($validated['companies'])) $this->createUserCompanies($id, $validated['companies']);
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
            $validated = $request->validate($this->RULES);

            $user->name = $validated['name'];
            $user->birth_city = $validated['birth_city'];
            $user->birth_date = $validated['birth_date'];
            $user->email = $validated['email'];
            $user->cellphone = $validated['cellphone'];
            $user->save();

            if ($validated['companiesToAdd']) $this->createUserCompanies($user->id, $validated['companiesToAdd']);
            if ($validated['companiesToRemove']) $this->removeUserCompanies($user->id, $validated['companiesToRemove']);

            DB::commit();
            return response()->json(['message' => 'User updated'], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Internal Server error'], 500);
        }
    }

    private function createUserCompanies($idUser, $companies)
    {
        foreach ($companies as $idCompany) {
            CompanyUserEnrollment::create([
                'id_user' => $idUser,
                'id_company' => $idCompany
            ]);
        }
    }

    private function removeUserCompanies($idUser, $companies)
    {
        foreach ($companies as $idCompany) {
            CompanyUserEnrollment::where('id_user', $idUser)
                ->andWhere('id_company', $idCompany)
                ->delete();
        }
    }
}