<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyUserEnrollment;
use App\Rules\Cnpj;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class CompanyService implements IService
{

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

    public function list($options): array
    {
        @[
            'searchField' => $searchField,
            'searchValue' => $searchValue,
            'searchOperator' => $searchOperator
        ] = $options;

        $companies = new Company();

        if ($searchField && $searchValue) {
            $companies->where($searchField, $searchValue);
        }

        return $companies->get()->all();
    }

    public function delete(Model $model): array
    {
        $model->delete();
        return [
            'ok' => true
        ];
    }

    public function createCompany(array $data)
    {
        $this->RULES['cnpj'][] = new Cnpj;

        $validator = $this->makeValidator($data);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        $validated = $validator->validated();

        $company = new Company();
        $company->name = $validated['name'];
        $company->address = $validated['address'];
        $company->cnpj = $validated['cnpj'];
        $id = $company->save();

        if (!empty ($validated['users'])) {
            $this->createCompanyUsers($id, $validated['rules']);
        }

        return [
            'success' => true,
            'errors' => []
        ];
    }

    public function updateCompany(Company $company, array $data)
    {
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
        if (!empty($validated['usersToAdd'])) $this->createCompanyUsers($company->id, $validated['usersToAdd']);
        if (!empty($validated['usersToRemove'])) $this->removeCompanyUsers($company->id, $validated['usersToRemove']);

        return [
            'success' => true,
            'errors' => []
        ];
    }

    private function makeValidator(array $data)
    {
        return Validator::make($data, $this->RULES);

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