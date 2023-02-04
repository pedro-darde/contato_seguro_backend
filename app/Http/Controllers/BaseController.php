<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseController extends Controller implements ICrudController
{
    private Model $model;
    public function __construct(Model $model) {
        $this->model = $model;
    }
    public function list(Request $request): JsonResponse
    {
        $data = $this->model->all();
        return response()->json(['data' => $data->all()]);
    }

    abstract function create(Request $request): JsonResponse;

    public function delete(Model $model): JsonResponse
    {
        $model->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    abstract function update(Model $model, Request $request): JsonResponse;
}