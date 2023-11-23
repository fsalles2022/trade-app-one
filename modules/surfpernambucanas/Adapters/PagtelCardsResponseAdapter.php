<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

use TradeAppOne\Domain\HttpClients\Responseable;
use Illuminate\Support\Arr;

class PagtelCardsResponseAdapter extends PagtelResponseAdapter
{
    protected const CODE_EMPTY_LIST = 'P01';

    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);

        $this->adapted = array_merge(
            $this->adaptCardsData(),
            $this->adapted
        );
    }

    /** @return mixed[] */
    protected function adaptCardsData(): array
    {
        $cards = $this->originalResponse->get('cardList', []);

        $cards = Collect($cards)->map(function (array $card): array {
            return [
                'paymentType' => Arr::get($card, 'paymentType'),
                'paymentId'   => Arr::get($card, 'paymentID'),
                'bin'         => Arr::get($card, 'bin'),
                'digFour'     => Arr::get($card, 'digFour'),
                'expiration'  => Arr::get($card, 'expiration'),
                'flag'        => Arr::get($card, 'flag'),
            ];
        });

        return [
            'cards' => $cards->toArray(),
        ];
    }

    /** @inheritdoc */
    protected function getOthersSuccessCodes(): array
    {
        return [
            self::CODE_EMPTY_LIST,
        ];
    }
}
