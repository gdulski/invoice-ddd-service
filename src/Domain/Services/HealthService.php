<?php

namespace Src\Domain\Services;

class HealthService
{
    public function getStatus(): array
    {
        return [
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'Invoice DDD Service',
            'version' => '1.0.0'
        ];
    }
}
