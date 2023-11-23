<?php


namespace TradeAppOne\Domain\Services\Sale;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Domain\Models\Tables\User;

class ServiceOptionsFilter
{
    public const TRIANGULATION_DEVICE     = 'TRIANGULATION_DEVICE';
    public const ICCID_SEARCH             = 'ICCID_SEARCH';
    public const VIVO_CONTROLE_CARTAO_M4U = 'VIVO_CONTROLE_FACIL_M4U';
    public const DISABLED_AUTENTICA       = 'DISABLED_AUTENTICA';

    protected $filters;
    /** @var User $user */
    protected $user;
    /** @var Collection $serviceOptions */
    protected $serviceOptions;

    public function __construct(User $user, array $filters)
    {
        $this->user           = $user;
        $this->filters        = $filters;
        $this->serviceOptions = collect();
    }

    public static function make(User $user, array $filters): self
    {
        return new self($user, $filters);
    }

    public function filter(): array
    {
        return $this->serviceOptions->values()->toArray();
    }

    public function verifyM4uTradeUp(): self
    {
        $pointOfSale = $this->user->pointsOfSale->first();
        $options     = ServiceOption::findByPointOfSale(
            $pointOfSale,
            $this->filters
        );
        $options->each(function ($option) {
            if ($option->action === self::VIVO_CONTROLE_CARTAO_M4U) {
                $this->serviceOptions->push($option->action);
            }
        });
        return $this;
    }

    public function verifyCarteirizacao(): self
    {
        $pointOfSale          = $this->user->pointsOfSale->first();
        $this->serviceOptions = ServiceOption::findByPointOfSale(
            $pointOfSale,
            $this->filters
        )->pluck('action');

        $notAllowed = hasPermission(SalePermission::getFullName(SalePermission::ASSOCIATE));

        if ($notAllowed === false) {
            $this->serviceOptions = $this->serviceOptions->reject(static function ($item) {
                return $item === ServiceOption::CARTEIRIZACAO;
            });
        }
        $this->serviceOptions = $this->serviceOptions->unique();
        return $this;
    }

    public function verifyWithDevice(): self
    {
        $channel  = data_get($this->user->pointsOfSale()->first(), 'network.channel');
        $promoter = $this->user->isPromoter(true);
        if ($promoter && $channel === Channels::DISTRIBUICAO) {
            return $this;
        }
        $this->serviceOptions->push(self::TRIANGULATION_DEVICE);
        return $this;
    }

    public function verifyIccidSearch(): self
    {
        if ($this->user->isInovaPromoter()) {
            $this->serviceOptions->push(self::ICCID_SEARCH);
        }
        return $this;
    }

    public function verifyStatusDisabledAutentica(): self
    {
        if (((int) config('utils.autentica.isDisabled')) === 1) {
            $this->serviceOptions->push(self::DISABLED_AUTENTICA);
        }
        return $this;
    }
}
