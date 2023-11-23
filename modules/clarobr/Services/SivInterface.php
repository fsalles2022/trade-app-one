<?php

namespace ClaroBR\Services;

interface SivInterface
{
    public function integrateService(string $serviceTransaction);

    public function activate($serviceEntity);
}
