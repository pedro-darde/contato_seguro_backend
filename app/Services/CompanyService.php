<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyUserEnrollment;
use App\Rules\Cnpj;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CompanyService implements IService
{

    private array $RULES = [
        'name' => 'required|string',
        'cnpj' => ['required', 'string'],
        'address' => 'required|string',
        'users' => 'array',
        'users.*' => 'exists:user,id',
    ];

    public function list($options): array
    {
        @[
            'searchField' => $searchField,
            'searchValue' => $searchValue,
            'searchOperation' => $searchOperator
        ] = $options;

        if ($searchField && $searchValue) {
            if ($searchOperator === "ilike") $searchValue = "$searchValue%";

            Log::info([$searchValue, $searchOperator, $searchField]);
            return Company::with(['users'])
                ->where($searchField, $searchOperator, $searchValue)
                ->get()
                ->all();
        }

        return Company::with(['users'])->get()->all();
    }

    public function delete(int $model): array
    {
        $company = Company::find($model);
        $company->users()->sync([]);
        $company->delete();
        return [
            'ok' => true
        ];
    }

    public function createCompany(array $data)
    {
        $this->RULES['cnpj'][] = new Cnpj;
        $this->RULES['cnpj'][] = 'unique:company,cnpj';

        $validator = $this->makeValidator($data);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        $validated = $validator->validated();

        $company = Company::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'cnpj' => $validated['cnpj']
        ]);

        if (!empty($validated['users'])) {
            $company->users()->attach($validated["users"]);
        }

        return [
            'success' => true,
            'errors' => []
        ];
    }

    public function updateCompany(Company $company, array $data)
    {
        if ($company->cnpj !== @$data['cnpj']) {
            $this->RULES['cnpj'][] = 'unique:company,cnpj';
        }
        $this->RULES['cnpj'][] = new Cnpj;
        $validator = $this->makeValidator($data);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        $validated = $validator->validated();

        $company->name = $validated['name'];
        $company->address = $validated['address'];
        $company->cnpj = $validated['cnpj'];
        $company->save();
        $company->users()->sync($validated['users']);

        return [
            'success' => true,
            'errors' => []
        ];
    }

    private function makeValidator(array $data)
    {
        return Validator::make($data, $this->RULES);
    }
}
