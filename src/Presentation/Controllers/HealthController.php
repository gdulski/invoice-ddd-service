<?php

namespace Src\Presentation\Controllers;

use Src\Domain\Services\HealthService;
use Illuminate\Http\JsonResponse;

class HealthController
{
    public function __construct(
        private HealthService $healthService
    ) {}

    public function check(): JsonResponse
    {
        $status = $this->healthService->getStatus();
        
        return response()->json($status);
    }
}
