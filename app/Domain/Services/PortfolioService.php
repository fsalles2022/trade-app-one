<?php

namespace TradeAppOne\Domain\Services;

use TradeAppOne\Domain\Repositories\Collections\PortfolioRepository;

class PortfolioService extends BaseService
{
    protected $repository;

    public function __construct(PortfolioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function filter($parameters = [])
    {
        return $this->repository->filter($parameters);
    }
}
