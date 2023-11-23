<?php


namespace Outsourced\GPA\Hooks;

use Illuminate\Http\Response;
use Outsourced\GPA\Adapters\Request\ActivationAdapter;
use Outsourced\GPA\Connections\GPAConnection;
use Outsourced\GPA\Models\GPA;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHook;

class GPAHooks implements NetworkHook
{
    protected $gPAConnection;

    public function __construct(GPAConnection $gPAConnection)
    {
        $this->gPAConnection = $gPAConnection;
    }

    public function execute(Service $service, array $options = []): void
    {
        if ($service->status === ServiceStatus::APPROVED) {
            $this->send($service);
        }
    }

    private function send(Service $service): void
    {
        $payload  = (new ActivationAdapter($service))->toArray();
        $response = $this->gPAConnection->saleRegister($payload);
        $status   = $response->getStatus();

        if ($status !== Response::HTTP_CREATED) {
            GPA::updateAttributes($service, ['retrySend' => true]);
        }
    }
}
