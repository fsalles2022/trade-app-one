<?php

declare(strict_types=1);

namespace SurfPernambucanas\Repositories;

use Outsourced\Enums\Outsourced;
use SurfPernambucanas\Enumerators\PagtelPlan;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Repositories\Collections\UserRepository;

class PagtelCustomerRepository
{
    /** @var UserRepository */
    private $userRepository;

    /** @var SaleRepository */
    private $saleRepository;

    private $cpf;
    private $customer;
    private $sales;

    /**
     * @param UserRepository $userRepository
     * @param SaleRepository $saleRepository
     */
    public function __construct(UserRepository $userRepository, SaleRepository $saleRepository)
    {
        $this->customer       = collect([]);
        $this->userRepository = $userRepository;
        $this->saleRepository = $saleRepository;
    }

    /**
     * @param string $cpf
     * @return self
     */
    public function validate(string $cpf): PagtelCustomerRepository
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function partner(): PagtelCustomerRepository
    {
        $user = $this->userRepository->whereBy('cpf', $this->cpf)->first();

        if (($user instanceof  User) && ($user->getNetwork()->slug === Outsourced::SURF_PERNAMBUCANAS)) {
            $this->customer->push($user);

            return $this;
        }

        return $this;
    }

    public function sale(): PagtelCustomerRepository
    {
        $sales = $this->saleRepository->findByCustomer($this->cpf)
            ->where('services.type', PagtelPlan::COLLABORATIVE)
            ->with('services')->get();

        if ($sales->isEmpty()) {
            $this->sales = $sales->push(['count' => 0]);

            return $this;
        }

        $sales->transform(function (Sale $sale) {
            $services = $sale->services->where('status', 'APPROVED')
                ->where('type', PagtelPlan::COLLABORATIVE);

            return [
                'count' => $services->count()
            ];
        });

        $this->sales = $sales;

        return $this;
    }

    /** @return mixed[] */
    public function get(): array
    {
        $sum = $this->sales->sum('count');

        return  [
            'customer' => $this->customer->isNotEmpty() && $sum < 3,
            'sales' => $sum
        ];
    }
}
