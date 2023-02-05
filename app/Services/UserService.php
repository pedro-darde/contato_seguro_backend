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
        'birth_city' => 'required|string',
        'birth_date' => 'required|date',
        'companies' => 'array',
        'companies.*' => 'exists:company,id'
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
        $user = User::create([
            'name' => $validated['name'],
            'birth_city' => $validated['birth_city'],
            'birth_date' => $validated['birth_date'],
            'cellphone' => $validated['cellphone'],
            'email'      => $validated['email'] 
        ]);
        if (!empty ($validated['companies'])) $user->companies()->attach($validated['companies']);
        
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
        $user->companies()->sync($validated['companies']);
        return [
            'success' => true,
            'errors' => []
        ];
    }


    public function makeValidator(array $data)
    {
       return Validator::make($data, $this->RULES);

    }

    public function list($options): array
    {
        @[
            'searchField' => $searchField,
            'searchValue' => $searchValue,
            'searchOperation' => $searchOperator,
            'extra' => $extra
        ] = $options;

        Log::info([$searchField, $searchValue, $searchOperator]);

        if ($searchField && $searchValue) {
            if ($searchOperator === "ilike") $searchValue = "$searchValue%";

            return User::with(['companies'])
                        ->where($searchField,  $searchOperator, $searchValue)
                        ->get()
                        ->all();
        }

        return User::with(['companies'])->get()->all();
    }

    public function delete(int $model): array
    {
        Log::info($model);
        $user = User::find($model);
        $user->companies()->sync([]);
        $user->delete();
        return [
            'ok' => true,
        ];
    }
}