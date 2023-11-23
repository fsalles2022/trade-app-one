<?php

namespace TradeAppOne\Features\Customer;

use Illuminate\Support\Facades\Validator;

class CustomerService
{
    private $customerRepository;

    //TODO: Inject Customer Repository
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function retainCustomer(array $customerData): ?Customer
    {
        $cpf = $customerData['cpf'];

        if ($this->isInvalidCpf($cpf)) {
            return null;
        }

        $customer = $this->fetchCustomer($cpf);

        $sanitizedCustomer = $this->purgeInvalidValues($customerData);

        if ($customer instanceof Customer) {
            $customer->fill($sanitizedCustomer);
            $customer->update();
            return $customer;
        }

        $customer = new Customer();
        $customer->fill($sanitizedCustomer);
        $customer->save();

        return $customer;
    }

    public function fetchCustomer(?string $cpf): ?Customer
    {
        return $this->customerRepository->where('cpf', $cpf)->first();
    }

    private function purgeInvalidValues(array $data): array
    {
        $customer = [];
        $rules    = (new Customer())->rules();

        $customer['cpf']       = $this->validateValue('cpf', $data, $rules);
        $customer['firstName'] = $this->validateValue('firstName', $data, $rules);
        $customer['lastName']  = $this->validateValue('lastName', $data, $rules);

        $customer['email']   = $this->validateValue('email', $data, $rules);
        $customer['zipCode'] = $this->validateValue('zipCode', $data, $rules);

        $customer['birthday']  = $this->validateValue('birthday', $data, $rules);
        $customer['filiation'] = $this->validateValue('filiation', $data, $rules);

        $customer['rgLocal'] = $this->validateValue('rgLocal', $data, $rules);
        $customer['rgDate']  = $this->validateValue('rgDate', $data, $rules);
        $customer['rgState'] = $this->validateValue('rgState', $data, $rules);
        $customer['gender']  = $this->validateValue('gender', $data, $rules);

        $customer['mainPhone']      = $this->validateValue('mainPhone', $data, $rules);
        $customer['secondaryPhone'] = $this->validateValue('secondaryPhone', $data, $rules);
        $customer['rg']             = $this->validateValue('rg', $data, $rules);
        $customer['neighborhood']   = $this->validateValue('neighborhood', $data, $rules);
        $customer['local']          = $this->validateValue('local', $data, $rules);
        $customer['city']           = $this->validateValue('city', $data, $rules);
        $customer['state']          = $this->validateValue('state', $data, $rules);
        $customer['complement']     = $this->validateValue('complement', $data, $rules);
        $customer['number']         = $this->validateValue('number', $data, $rules);

        return array_filter($customer);
    }

    private function isInvalidCpf(?string $cpf) :bool
    {
        $validation = Validator::make(['cpf' => $cpf], ['cpf' => 'cpf']);
        return (bool) $validation->fails();
    }

    private function validateValue(?string $key, ?array $data, ?array $rules) :?string
    {
        $value      = data_get($data, $key);
        $keyRules   = data_get($rules, $key);
        $validation = Validator::make([$key => $value], [$key => $keyRules]);

        return $validation->passes() ? $value : null;
    }
}
