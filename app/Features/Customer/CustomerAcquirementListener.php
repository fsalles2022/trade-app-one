<?php

namespace TradeAppOne\Features\Customer;

use Illuminate\Support\Facades\Log;
use TradeAppOne\Events\PreAnalysisEvent;

class CustomerAcquirementListener
{
    public function handle(PreAnalysisEvent $event)
    {
        try {
            $customerService = new CustomerService(new CustomerRepository());
            $customerArray   = $event->data->adapt();

            $customerService->retainCustomer($customerArray);
        } catch (\Exception $exception) {
            Log::info('customer-acquirement-failed', [ 'exception' => $exception->getMessage()]);
        }
    }
}
