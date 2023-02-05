<?php

namespace App\Http\Controllers;

use App\Services\IService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class BaseController extends Controller implements ICrudController
{
    private IService $service;
    public function __construct(IService $service) {
        $this->service = $service;
    }
    public function list(Request $request): JsonResponse
    {
        Log::info($request->all());
        $data = $this->service->list([
            'searchField' => $request->get('searchField'),
            'searchValue' => $request->get('searchValue'),
            'searchOperation' => $request->get('searchOperator'),
            'extra' => $request->get("extraInfo")
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