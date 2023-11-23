<?php

namespace TradeAppOne\Domain\Services;

use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\ServiceOption;

class OperatorServiceService
{
    public function getAllServices(): array
    {
        $list     = [];
        $services = Service::select('id', 'sector', 'operator', 'operation')->get();

        foreach ($services as $service) {
            $labels = $this->getServiceLabels($service);

            isset($list[$labels['sector']])
                ? $list[$labels['sector']][$labels['operator']][$service->id] = $labels['operation']
                : $list[$labels['sector']]                                    = [$labels['operator'] => [$service->id => $labels['operation']]];
        };

        return $list;
    }

    public function getAllServiceOptions(): array
    {
        return ServiceOption::select('id', 'action as label')->get()->toArray();
    }

    public function getServicesAndOptionsByAttribution(int $id, string $field): array
    {
        $services = Service::selectRaw("
                services.id, 
                services.sector, 
                services.operator, 
                services.operation, 
                so.action as optionName, 
                so.id as optionId
            ")
            ->join('availableServices as as', 'services.id', 'as.serviceId')
            ->leftJoin('services_serviceOptions as sso', 'as.id', 'sso.availableServiceId')
            ->leftJoin('serviceOptions as so', 'sso.optionId', 'so.id')
            ->where('as.'.$field, $id)
            ->whereNull('as.deletedAt')
            ->get();

        return $this->organizeList($services);
    }

    private function organizeList(object $services): array
    {
        $list = [];

        foreach ($services as $service) {
            $labels = $this->getServiceLabels($service);

            isset($list[$labels['sector']])
                ? $list[$labels['sector']][$labels['operator']][$service->id] = ['label' => $labels['operation']]
                : $list[$labels['sector']]                                    = [$labels['operator'] => [$service->id => ['label' => $labels['operation']]]];

            if (! empty($service->optionId)) {
                $list[$labels['sector']][$labels['operator']][$service->id]['option'] = [
                    'label' => $service->optionName,
                    'id' => $service->optionId,
                ];
            }
        }

        return $list;
    }

    private function getServiceLabels(object $service): array
    {
        $serviceLabels = [];

        $serviceLabels['sector']    = __('operations.' . $service->sector);
        $serviceLabels['operator']  = __('operations.' . $service->operator);
        $serviceLabels['operation'] = __('operations.' . $service->operation)['label'] ?? $service->operation;

        if (strpos(__('operations.' . $service->operator), 'operations.' . $service->operator) !== false) {
            $serviceLabels['operator'] = $service->operator;
        }

        return $serviceLabels;
    }

    public function adapterServiceOptionsToExport(): array
    {
        $serviceOptions = $this->getAllServiceOptions();
        array_unshift($serviceOptions, ['id', 'label']);

        return $serviceOptions;
    }
}
