<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use Reports\Goals\Models\Goal;
use Reports\Goals\Models\GoalType;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class GoalBuilder
{
    protected $typeString;
    protected $pointOfSale;
    protected $value;
    protected $month;
    protected $type;

    public function withTypeString(string $type): GoalBuilder
    {
        $this->typeString = $type;
        return $this;
    }

    public function withType(GoalType $type): GoalBuilder
    {
        $this->type = $type;
        return $this;
    }

    public function withMonth(int $month): GoalBuilder
    {
        $this->month = $month;
        return $this;
    }

    public function withPointOfSale(PointOfSale $pointOfSale): GoalBuilder
    {
        $this->pointOfSale = $pointOfSale;
        return $this;
    }

    public function withValue($value): GoalBuilder
    {
        $this->value = $value;
        return $this;
    }

    public function build(): Goal
    {
        $goal = factory(Goal::class)->make([
            'goal'  => $this->value ?? rand(1, 1000),
            'month' => $this->month ?? rand(1, 12)
        ]);

        return $this->associations($goal);
    }

    private function getType(): GoalType
    {
        return $this->type ??
            ($this->typeString
                ? factory(GoalType::class)->create(['type' => $this->typeString])
                : factory(GoalType::class)->create());
    }

    private function getPointOfSale(): PointOfSale
    {
        return $this->pointOfSale ?? (new PointOfSaleBuilder())->build();
    }

    private function associations($goal): Goal
    {
        $type        = $this->getType();
        $pointOfSale = $this->getPointOfSale();

        $goal->goalType()->associate($type);
        $goal->pointOfSale()->associate($pointOfSale);
        $goal->save();

        return $goal;
    }
}
