<?php

declare(strict_types=1);

namespace Core\PowerBi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CheckPowerBiAvailabilityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isOffline() === true) {
            return response()->json([
                'type' => 'report',
                'accessToken' => '',
                'embedUrl' => '',
                'id' => '',
                'offline' => true,
                'offlineImage' => $this->getWarningImagePathDashboard()
            ]);
        }

        return $next($request);
    }

    private function isOffline(): bool
    {
        return $this->checkAvailabilityDateTime() === false
            && $this->checkPowerBiDashboardOnline() === false;
    }

    private function checkAvailabilityDateTime(): bool
    {
        $datetime = (int) now()->format('H');

        return $datetime >= 5 && $datetime <= 22;
    }

    private function checkPowerBiDashboardOnline(): bool
    {
        $isPowerBiOffline = config('pbi.isPowerBiOffline');
        
        if (is_bool($isPowerBiOffline) === true) {
            return $isPowerBiOffline === false;
        }
        
        return $isPowerBiOffline === 'true' ? false : true;
    }

    private function getWarningImagePathDashboard(): string
    {
        $powerBiOfflineImage = config('pbi.powerBiOfflineImage');

        if (preg_match('/^\//', $powerBiOfflineImage)) {
            $powerBiOfflineImage = preg_replace('/^\//', '', $powerBiOfflineImage, 1);
        }

        return Storage::disk('s3')->url($powerBiOfflineImage);
    }
}
