<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends BaseController
{
    public function __construct()
    {
        parent::__construct(new Company());
    }

    private array $RULES = [
        'name' => 'required|string',
        'cnpj' => 'required|string|unique_on_company',
        'address' => 'required|string',
        'users' => 'array'
    ];
    function create(Request $request): JsonResponse
    {
        $validated = $request->validate($this->RULES);
        $company = new Company();
        $company->name = $validated['name'];
        $company->address = $validated['address'];
        $company->cnpj = $validated['cnpj'];
        $company->save();
        return response()->json(['message' => 'Company created'], 201);

    }

    function update(Model $company, Request $request): JsonResponse
    {
        $validated = $request->validate($this->RULES);
        $company->name = $validated['name'];
        $company->address = $validated['address'];
        $company->cnpj = $validated['cnpj'];
        $company->save();
        return response()->json(['message' => 'Company updated'], 201);
    }
}