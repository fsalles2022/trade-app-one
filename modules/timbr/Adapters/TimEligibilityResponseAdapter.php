<?php

namespace TimBR\Adapters;

use Discount\Models\DeviceTim;
use ErrorException;
use Illuminate\Http\Response;
use TimBR\Services\TimBRMapPlansService;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;

class TimEligibilityResponseAdapter extends ResponseAdapterAbstract
{
    const CUSTOMER_WARNINGS = '-10001';

    public function __construct($response, $payload, ?DeviceTim $device = null)
    {
        $arrayResponse          = $response->toArray();
        $this->originalResponse = $arrayResponse;
        try {
            if (data_get($arrayResponse, 'eligibilityToken')) {
                $products             = data_get($arrayResponse, 'products');
                $operation            = data_get($payload, 'operation');
                $requireDeviceLoyalty = data_get($payload, 'requireDeviceLoyalty', false);

                $filteredPlans = TimBRMapPlansService::map($products, (string) $operation, (bool) $requireDeviceLoyalty, $device);

                if ($filteredPlans->isEmpty()) {
                    $content['message']      = trans('timBR::messages.eligibility.' . $operation . '.ineligible');
                    $content['ShortMessage'] = 'NotEligible_' . $operation;
                    $this->pushError($content, Response::HTTP_NOT_ACCEPTABLE);
                } else {
                    $this->adapted = $filteredPlans->toArray();
                }
            } else {
                $this->status    = Response::HTTP_MISDIRECTED_REQUEST;
                $originalMessage = isset($arrayResponse['message']) ? data_get($arrayResponse, 'message') : data_get($arrayResponse, 'provider.errorMessage');
                $originalCode    = data_get($arrayResponse, 'internalCode');

                $content['message'] = ! empty($originalMessage) ? $originalMessage : trans('timBR::messages.eligibility.empty_response');

                $status = $originalCode === self::CUSTOMER_WARNINGS
                    ? Response::HTTP_NOT_ACCEPTABLE
                    : Response::HTTP_PRECONDITION_FAILED;

                $this->pushError($content, $status);
            }
        } catch (ErrorException $exception) {
            $this->adapted = $arrayResponse;
        }
    }

    public function isSuccess(): bool
    {
        return data_get($this->originalResponse, 'eligibilityToken', false);
    }
}
