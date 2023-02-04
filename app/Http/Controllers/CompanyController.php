<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyUserEnrollment;
use App\Models\User;
use App\Rules\Cnpj;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends BaseController
{
    public function __construct()
    {
        parent::__construct(new Company());
    }

    private array $RULES = [
        'name' => 'required|string',
        'cnpj' => ['required','string','unique:company,cnpj'],
        'address' => 'required|string',
        'users' => 'array',
        'users.*' => 'exists:user,id',
        'usersToRemove' => 'array',
        'usersToRemove.*' => 'exists:user,id',
        'usersToAdd' => 'array',
        'usersToAdd.*' => 'exists:user,id',
    ];
    function create(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->RULES['cnpj'][] = new Cnpj;
            $validated = $request->validate($this->RULES);
            $company = new Company();
            $company->name = $validated['name'];
            $company->address = $validated['address'];
            $company->cnpj = $validated['cnpj'];
            $id = $company->save();

            if (!empty ($validated['users'])) {
               $this->createCompanyUsers($id, $validated['rules']);
            }
            DB::commit();
            return response()->json(['message' => 'Company created'], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Internal Server error'], 500);
        }

    }

    function update(Model $company, Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->RULES['cnpj'][] = new Cnpj;
            $validated = $request->validate($this->RULES);
            $company->name = $validated['name'];
            $company->address = $validated['address'];
            $company->cnpj = $validated['cnpj'];
            $company->save();
            if (!empty($validated['usersToAdd'])) $this->createCompanyUsers($company->id, $validated['usersToAdd']);
            if (!empty($validated['usersToRemove'])) $this->removeCompanyUsers($company->id, $validated['usersToRemove']);
            DB::commit();
            return response()->json(['message' => 'Company updated'], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Internal Server error'], 500);
        }
    }

    private function createCompanyUsers($idCompany, $users)
    {
        foreach ($users as $idUser) {
            CompanyUserEnrollment::create([
                'id_user' => $idUser,
                'id_company' => $idCompany
            ]);
        }
    }

    private function removeCompanyUsers($idCompany, $users)
    {
        foreach ($users as $idUser) {
            CompanyUserEnrollment::where('id_user', $idUser)
                ->andWhere('id_company', $idCompany)
                ->delete();
        }
    }
}