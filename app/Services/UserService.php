<?php

namespace App\Services;

use App\Models\CompanyUserEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserService implements IService
{
    private array $RULES = [
        'name' => 'required|string',
        'email' => 'required|string|email',
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

    public function createUser(array $data)
    {
        $this->RULES['email'] .= "|unique:user,email";
        $validator = $this->makeValidator($data);
        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        $validated = $validator->validated();
        $user = new User();
        $user->name = $validated['name'];
        $user->birth_city = $validated['birthCity'];
        $user->birth_date = $validated['birthDate'];
        $user->email = $validated['email'];
        $user->cellphone = $validated['cellphone'];
        if (!empty ($validated['companies'])) $user->companies()->attach($validated['companies']);
        $user->save();

        return [
            'success' => true,
            'errors' => []
        ];
    }

    public function updateUser(User $user, array $data) {
        if ($user->email !== $data['email']) {
            $this->RULES['email'] .= "|unique:user,email";
        }

        $validator = $this->makeValidator($data);
        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }
        $validated = $validator->validated();
        $user->name = $validated['name'];
        $user->birth_city = $validated['birth_city'];
        $user->birth_date = $validated['birth_date'];
        $user->email = $validated['email'];
        $user->cellphone = $validated['cellphone'];
        $user->save();

        if ($validated['companiesToAdd']) $this->createUserCompanies($user, $validated['companiesToAdd']);
        if ($validated['companiesToRemove']) $this->removeUserCompanies($user->id, $validated['companiesToRemove']);

        return [
            'success' => true,
            'errors' => []
        ];
    }


    public function makeValidator(array $data)
    {
       return Validator::make($data, $this->RULES);

    }

    private function createUserCompanies($user, $companies)
    {
        foreach ($companies as $idCompany) {
            $user->companies()->save(new CompanyUserEnrollment([
                'id_user' => $user->id,
                'id_company' => $idCompany
            ]));
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

    public function list($options): array
    {
        @[
            'searchField' => $searchField,
            'searchValue' => $searchValue,
            'searchOperator' => $searchOperator
        ] = $options;

        $users = new User();

        if ($searchField && $searchValue) {
            $users->where($searchField, $searchValue);
        }

        return $users->get()->all();
    }

    public function delete(int $model): array
    {
        CompanyUserEnrollment::where('id_user', $model)->delete();
        User::find($model)->delete();
        return [
            'ok' => true,
        ];
    }
}