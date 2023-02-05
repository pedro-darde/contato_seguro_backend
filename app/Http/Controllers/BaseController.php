<?php

namespace App\Http\Controllers;

use App\Services\IService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseController extends Controller implements ICrudController
{
    private IService $service;
    public function __construct(IService $service) {
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

    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Deleted successfully']);
    }

    abstract function update(int $id, Request $request): JsonResponse;
}