<?php

namespace Buyback\Console;

use Buyback\Tests\Helpers\Builders\WaybillBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Domain\Services\UserService;

class WaybillGenerateCommand extends Command
{
    const DEFAULT_POINT_OF_SALE = 'matriz-rio-negro';
    const DEFAULT_SALESMAN_CPF  = '04629823190';

    protected $signature   = 'waybill:generate {--user=} {--pointOfSale=} {--operation=}';
    protected $description = 'Generate waybills in the test environment';

    public function handle(PointOfSaleService $pointOfSaleService, UserService $userService)
    {
        $this->validate();

        $pointOfSale = $this->pointOfSale($pointOfSaleService);
        $user        = $this->user($userService);
        $operation   = $this->operation();

        (new WaybillBuilder())
            ->withUser($user)
            ->withPointOfSale($pointOfSale)
            ->withOperation($operation)
            ->build();

        $this->info('Process Complete.');
    }

    private function validate()
    {
        if (Environments::TEST != App::environment()) {
            throw new \Exception('Não é possível executar o comando neste ambiente.');
        }

        Validator::make($this->options(), [
            'pointOfSale' => 'sometimes|nullable|exists:pointsOfSale,slug',
            'operation'   => ['sometimes', 'nullable', Rule::in(array_keys(Operations::TRADE_IN_OPERATORS[Operations::TRADE_IN_MOBILE]))]
        ])->validate();
    }

    private function pointOfSale(PointOfSaleService $service): PointOfSale
    {
        $slug = $this->option('pointOfSale');

        if (empty($slug)) {
            $slug = self::DEFAULT_POINT_OF_SALE;
        }

        return $service->findOneBySlug($slug);
    }

    private function operation(): string
    {
        $operation = $this->option('operation');

        if (empty($operation)) {
            return Operations::SALDAO_INFORMATICA;
        }

        return $operation;
    }

    private function user(UserService $service): User
    {
        $user_id = $this->option('user');

        if (empty($user_id)) {
            $user_id = self::DEFAULT_SALESMAN_CPF;
        }

        return $service->findOneByCpf($user_id);
    }
}
