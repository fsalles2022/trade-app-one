<?php

namespace Outsourced\Crafts\Devices;

class OutsourcedDeviceDTO
{
    public $sku;
    public $model;
    public $label;

    public function __construct(?string $identifier = null, ?string $model = null, ?string $label = null)
    {
        $this->sku   = $identifier;
        $this->model = $model;
        $this->label = $label;
    }

    public function toArray(): array
    {
        return array_filter([
          'sku'   => $this->sku,
          'model' => $this->model,
          'label' => $this->label
        ]);
    }
}
