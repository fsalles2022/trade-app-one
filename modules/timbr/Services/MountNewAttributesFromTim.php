<?php

namespace TimBR\Services;

use Discount\Services\DeviceTimService;
use Discount\Services\DiscountTimService;
use ErrorException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use TimBR\Connection\TimBRConnection;
use TimBR\Enumerators\TimBRCacheables;
use TimBR\Enumerators\TimBRDefaultPackages;
use TimBR\Enumerators\TimBRServices;
use TimBR\Exceptions\EligibilityNotFound;
use TimBR\Exceptions\TimBRCepNotFound;
use TimBR\Exceptions\TimBRInvalidDevice;
use TimBR\Models\Eligibility;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;

class MountNewAttributesFromTim implements MountNewAttributesService
{
    /** @var TimBRConnection */
    protected $connection;

    /** @var DeviceTimService */
    protected $deviceTimService;

    /** @var DiscountTimService */
    protected $discountTimService;

    public function __construct(
        TimBRConnection $connection,
        DeviceTimService $deviceTimService,
        DiscountTimService $discountTimService
    ) {
        $this->connection = $connection;
        $this->deviceTimService = $deviceTimService;
        $this->discountTimService = $discountTimService;
    }

    public function getAttributes(array $service): array
    {
        $user                   = Auth::user();
        $networkSlug            = $user->pointsOfSale->first()->network->slug;
        $cpf                    = $user->cpf;
        $customer               = $service['customer'];

        [
            $requireDeviceLoyalty,
            $device
        ] = $this->getDeviceByService($service, $user);

        $eligibility    = $this->getEligibility($customer['cpf']);
        $mappedProducts = TimBRMapPlansService::map($eligibility->products->toArray(), $service['operation'], $requireDeviceLoyalty, $device);

        $customer = $this->requestTimAddress($networkSlug, $cpf, $customer);
        $areaCode = $this->extractAreaCode($service);

        $price             = TimMappedProduct::getPrice($mappedProducts, $service);
        $product           = $this->getProduct($mappedProducts, $service);
        $loyalty           = $this->getLoyalty($mappedProducts, $service);
        $timServices       = $this->getTimServices($mappedProducts, $service);
        $package           = $this->getPackage($mappedProducts, $service);
        $automaticPackages = $this->getAutomaticPackages($mappedProducts, $service);
        $timPackages       = $this->getSelectedPackages($mappedProducts, $service);

        $label       = data_get($product, 'label');
        $productName = data_get($product, 'tim.plan.name');

        foreach ($timPackages as $timPackage) {
            $price = $this->appendTimServicePrice($price, $timPackage);
            $label = $this->appendTimServiceLabel($label, $timPackage);
        }

        foreach ($timServices as $timService) {
            $price = $this->appendTimServicePrice($price, $timService);
            $label = $this->appendTimServiceLabel($label, $timService);
        }

        $msisdn = $this->getMsisdn($service, $networkSlug, $cpf);

        $operatorsIdentifiers = [
            'protocol' => $this->generateProtocol($service, $msisdn, $networkSlug, $cpf),
        ];

        $eligibilityToken = $eligibility->eligibilityToken;

        return array_filter([
            'areaCode'              => $areaCode,
            'customer'              => $customer,
            'price'                 => $price,
            'eligibilityToken'      => $eligibilityToken,
            'label'                 => $label,
            'productName'           => $productName,
            'loyalty'               => $loyalty,
            'package'               => $package,
            'automaticPackages'     => $automaticPackages,
            'selectedPackages'      => $timPackages,
            'selectedServices'      => $timServices,
            'msisdn'                => $msisdn,
            'operatorIdentifiers'   => $operatorsIdentifiers,
        ]);
    }

    public function getEligibility($customerCpf): Eligibility
    {
        $eligibility = Cache::get(TimBRCacheables::ELIGIBILITY . $customerCpf);
        throw_if(is_null($eligibility), new EligibilityNotFound());
        return $eligibility;
    }

    /**
     * @param mixed[] $service
     * @return mixed[]
     * @throws TimBRInvalidDevice
     */
    private function getDeviceByService(array $service, User $user): array
    {
        $requireDeviceLoyalty   = null;
        $device                 = null;

        $operation = data_get($service, 'operation');

        if ($this->discountTimService->shouldUseDiscountByOperation($user, $operation) === false) {
            return [
                $requireDeviceLoyalty,
                $device
            ];
        }

        $deviceId = data_get($service, 'device.id');

        if (!empty($deviceId)) {
            $device = $this->deviceTimService->findById((int) $deviceId);
            $requireDeviceLoyalty = true;

            if (empty($device)) {
                throw new TimBRInvalidDevice();
            }
        }

        return [
            $requireDeviceLoyalty,
            $device
        ];
    }

    private function requestTimAddress(string $networkSlug, string $cpf, $customer): array
    {
        try {
            $addressFromTim = $this->connection
                ->getCep($networkSlug, $cpf, $customer['zipCode'])
                ->toArray();

            $addressAttributes = $addressFromTim;

            $customer['zipCode']      = $addressAttributes['postCode'];
            $customer['localId']      = $addressAttributes['streetType'] ?? $customer['localId'];
            $customer['local']        = $addressAttributes['streetName'] ?? $customer['local'];
            $customer['neighborhood'] = $addressAttributes['locality'] ?? $customer['neighborhood'];
            $customer['city']         = $addressAttributes['city'];
            $customer['state']        = $addressAttributes['stateOrProvince'];

            return $customer;
        } catch (ErrorException $exception) {
            throw new TimBRCepNotFound();
        }
    }

    private function extractAreaCode(array $service): string
    {
        if (isset($service['msisdn'])) {
            return substr($service['msisdn'], 0, 2);
        }

        if ($service['mode'] == Modes::PORTABILITY) {
            return substr($service['portedNumber'], 0, 2);
        }

        return $service['areaCode'];
    }

    private function getProduct(Collection $eligibility, $service): ?array
    {
        if ($loyalty = data_get($service, 'loyalty.id')) {
            $plan = $eligibility
                ->where('product', $service['product'])
                ->where('loyalty.id', $loyalty)
                ->first();
        } else {
            $plan = $eligibility->where('product', $service['product'])->where('loyalty', null)->first();
        }
        return $plan;
    }

    private function getLoyalty(Collection $eligibility, $service)
    {
        if ($loyalty = data_get($service, 'loyalty.id')) {
            $plan = $eligibility->where('loyalty.id', $loyalty)->first();
            return data_get($plan, 'loyalty');
        }
        return null;
    }

    /**
     * @param mixed[] $service
     * @return mixed[]|null
     */
    private function getPlanByElegibilityAndService(Collection $eligibility, array $service): ?array
    {
        $loyalty = data_get($service, 'loyalty.id');

        if ($loyalty !== null) {
            return $eligibility->where('loyalty.id', $loyalty)->first();
        }

        $planId = data_get($service, 'product');

        return $eligibility->where('product', $planId)->first();
    }

    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    private function getPackage(Collection $eligibility, array $service): array
    {
        $plan = $this->getPlanByElegibilityAndService($eligibility, $service);

        $package = collect(data_get($plan, 'tim.packageGroups.groups.*.packages.*', []))
            ->whereIn('id', TimBRDefaultPackages::AVAILABLE_PACKAGES)
            ->first();

        if ($package === null) {
            return [];
        }

        return [
            'id'    => data_get($package, 'id'),
            'name'  => data_get($package, 'name'),
            'price' => (float) data_get($package, 'price.amount', 0.00),
        ];
    }

    /**
     * Packages Selected by Customer
     *
     * @param mixed[] $service
     * @return mixed[]
     */
    private function getSelectedPackages(Collection $eligibility, array $service): array
    {
        $plan = $this->getPlanByElegibilityAndService($eligibility, $service);

        $selectedPackages = (array) data_get($service, 'packages.*.id', []);

        $packages = collect(data_get($plan, 'packages', []))->whereIn('id', $selectedPackages);

        if ($packages->isEmpty()) {
            return [];
        }

        return $packages->map(function ($package): array {
            return [
                'id'    => data_get($package, 'id'),
                'label'  => data_get($package, 'label'),
                'price' => (float) data_get($package, 'price', 0.00),
            ];
        })->toArray();
    }

    /**
     * @param mixed[] $service
     * @return array[]|null
     */
    private function getAutomaticPackages(Collection $eligibility, array $service): ?array
    {
        $plan = $this->getPlanByElegibilityAndService($eligibility, $service);

        $packages = collect(data_get($plan, 'tim.packageGroups.groups.*.packages.*', []))
            ->where('isReadOnly', true)
            ->where('activationType', 'Automatica')
            ->map(function ($package) {
                return [
                    'id' => $package['id'] ?? null
                ];
            });

        return $packages->isEmpty() ? null : $packages->all();
    }

    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    public function getTimServices(Collection $eligibility, array $service): array
    {
        $plan = $this->getPlanByElegibilityAndService($eligibility, $service);

        $selectedServices = (array) data_get($service, 'service.*.id', []);

        $services = collect(data_get($plan, 'services', []))->whereIn('id', $selectedServices);

        if ($services->isEmpty()) {
            return [];
        }

        return $services->map(function ($service): array {
            $id = data_get($service, 'id');
            $type = in_array($id, TimBRServices::DEEZER_SERVICES) ? TimBRServices::DEEZER_TYPE : TimBRServices::PLUGIN_TYPE;

            return [
                'id'            => $id,
                'label'         => data_get($service, 'label'),
                'description'   => data_get($service, 'description'),
                'price'         => (float) data_get($service, 'price', 0.00),
                'type'          => $type,
            ];
        })
            ->values()
            ->toArray();
    }

    public function getPriceFromEligibility(string $customerCpf, ?string $product, array $loyalty = [])
    {
        $plan = $this->getEligibility($customerCpf)->products->where('plan.id', $product)->first();

        throw_if(is_null($plan), new ProductNotFoundException());
        if (empty($loyalty)) {
            return $plan['plan']['price']['amount'];
        } else {
            $planPrice    = $plan['plan']['price']['amount'];
            $loyaltyPrice = collect($plan['loyalties'])->where('id', $loyalty['id'])->first()['price']['amount'];
            return $planPrice + $loyaltyPrice;
        }
    }

    /** @param mixed[] $timService */
    private function appendTimServicePrice(float $price, array $timService): float
    {
        $timServicePrice = (float) data_get($timService, 'price', 0);

        return $price + $timServicePrice;
    }

    /** @param mixed[] $timService */
    private function appendTimServiceLabel(string $label, array $timService): string
    {
        $timServiceLabel = data_get($timService, 'label');

        if (empty($timServiceLabel)) {
            return $label;
        }

        return "{$label} + {$timServiceLabel}";
    }

    private function getMsisdn(array $service, string $networkSlug, string $cpf): ?string
    {
        if ($service['mode'] === Modes::MIGRATION) {
            return $service['msisdn'] ?? null;
        }

        $payload = TimBRMapSimCardActivationService::map($service);

        // Burn SimCard
        $response = $this->connection->simCardActivation($networkSlug, $cpf, $payload);

        return MsisdnHelper::addCountryCode(CountryAbbreviation::BR, $response->get('device.msisdn'));
    }

    /** @param Mixed[] $service */
    private function generateProtocol(array $service, string $msisdn, string $networkSlug, string $cpf): string
    {
        $service['msisdn'] = MsisdnHelper::removeCountryCode(CountryAbbreviation::BR, $msisdn);

        $payload = TimBRMapGenerateProtocolService::map($service);

        $response = $this->connection->generateProtocol($networkSlug, $cpf, $payload);

        return $response->get('interactionProtocol');
    }
}
