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
        $searchField = $request->get('searchField');
        $searchValue = $request->get('searchValue');

        if ($searchValue && $searchField) {
            $this->model->where($searchField, $searchValue);
        }

        $data = $this->model->get()->all();
        return response()->json(['data' => $data]);
    }

    abstract function create(Request $request): JsonResponse;

    public function delete(Model $model): JsonResponse
    {
        $model->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    abstract function update(Model $model, Request $request): JsonResponse;
}