<?php

declare(strict_types=1);

namespace Outsourced\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Outsourced\Assistance\OutsourcedFactory;
use Outsourced\Enums\Crafts;

class OutsourcedCustomerController
{
    public function validate(string $cpf): JsonResponse
    {
        $user    = Auth::user();
        $network = $user->getNetwork();

        $instance = OutsourcedFactory::make($network->slug, Crafts::CUSTOMER);
        $customer = $instance->validatePartnerEmployee($cpf);

        return response()->json($customer, Response::HTTP_OK);
    }
}
