<?php

namespace App\Http\Controllers;

use App\Services\IService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseController extends Controller implements ICrudController
{
    private Model $model;
    private IService $service;
    public function __construct(Model $model, IService $service) {
        $this->model = $model;
        $this->service = $service;
    }
    public function list(Request $request): JsonResponse
    {
        $data = $this->service->list([
            'searchField' => $request->get('searchField'),
            'searchValue' => $request->get('searchValue'),
            'searchOperator' => $request->get('searchOperator')
        ]);

        return response()->json(['data' => $data]);
    }

    abstract function create(Request $request): JsonResponse;

    public function delete(Model $model): JsonResponse
    {
        $this->service->delete($model);
        return response()->json(['message' => 'Deleted successfully']);
    }

    abstract function update(Model $model, Request $request): JsonResponse;
}