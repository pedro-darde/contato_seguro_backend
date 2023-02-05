<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\CompanyService;
use App\Services\IService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyController extends BaseController
{
    private IService $service;
    public function __construct()
    {
        $this->service = new CompanyService();
        parent::__construct($this->service);
    }


    public function create(Request $request): JsonResponse
    {

        try {
            DB::beginTransaction();

            $ok = $this->service->createCompany($request->all());

            if (!$ok['success']) {
                return response()->json($ok['errors'], 422);
            }

            DB::commit();
            return response()->json(['message' => 'Company created'], 201);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Internal Server error'], 500);
        }

    }

    function update(int $idCompany, Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $company = Company::find($idCompany);
            $ok = $this->service->updateCompany($company, $request->all());

            if (!$ok['success']) {
                return response()->json($ok['errors'], 422);
            }

            DB::commit();
            return response()->json(['message' => 'Company updated'], 201);
        } catch (\Throwable $e) {

            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Internal Server error'], 500);
        }
    }


}