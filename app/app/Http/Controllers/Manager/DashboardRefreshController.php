<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardRefreshController extends Controller
{
    public function __invoke(DashboardService $dashboardService): JsonResponse
    {
        $data = $dashboardService->getManagerDashboardData();
        return response()->json($data);
    }
}
