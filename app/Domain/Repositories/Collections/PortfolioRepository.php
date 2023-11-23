<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use TradeAppOne\Domain\Models\Collections\Portfolio\Portfolio;

class PortfolioRepository extends BaseRepository
{
    protected $model = Portfolio::class;

    public function filter(array $parameters = [])
    {
        $portfolio = $this->createModel()->newQuery();

        foreach ($parameters as $key => $value) {
            $portfolio = $portfolio->where($key, 'like', "{$value}%");
        }
        return $portfolio->get();
    }
}
