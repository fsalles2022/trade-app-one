<?php

namespace TradeAppOne\Features\Customer;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use TradeAppOne\Http\Controllers\Controller;

class CustomerController extends Controller
{
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function get($cpf)
    {
        $validation = Validator::make(['cpf' => $cpf], ['cpf' => 'cpf']);

        if ($validation->fails()) {
            return response([], Response::HTTP_OK);
        }

        $customer = $this->customerService->fetchCustomer($cpf);

        return $customer;
    }
}
