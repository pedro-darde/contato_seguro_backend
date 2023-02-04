<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        'birth_city' => 'required|string',
        'birth_date' => 'required|date',
        'companies' => 'array'
    ];
    function create(Request $request): JsonResponse
    {
       $validated = $request->validate($this->RULES);

        $user = new User();
        $user->name = $validated['name'];
        $user->birth_city = $validated['birth_city'];
        $user->birth_date = $validated['birth_date'];
        $user->email = $validated['email'];
        $user->cellphone = $validated['cellphone'];
        $user->save();

        return response()->json(['message' => 'User created'], 201);

    }

    function update(Model $user, Request $request): JsonResponse
    {
        $validated = $request->validate($this->RULES);

        $user->name = $validated['name'];
        $user->birth_city = $validated['birth_city'];
        $user->birth_date = $validated['birth_date'];
        $user->email = $validated['email'];
        $user->cellphone = $validated['cellphone'];
        $user->save();

        return response()->json(['message' => 'User updated'], 201);
    }
}