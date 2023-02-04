<?php

namespace App\Services;

use App\Models\CompanyUserEnrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class UserService implements IService
{
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

    public function createUser(array $data)
    {
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
        $id = $user->save();
        if (!empty ($validated['companies'])) $this->createUserCompanies($id, $validated['companies']);

        return [
            'success' => true,
            'errors' => []
        ];
    }

    public function updateUser(User $user, array $data) {
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

        if ($validated['companiesToAdd']) $this->createUserCompanies($user->id, $validated['companiesToAdd']);
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

    public function delete(Model $model): array
    {
        $model->delete();
        return [
            'ok' => true,
        ];
    }
}