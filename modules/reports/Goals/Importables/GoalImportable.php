<?php

namespace Reports\Goals\Importables;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Reports\Goals\Services\GoalService;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\ImportableInterface;
use TradeAppOne\Domain\Services\PointOfSaleService;

class GoalImportable implements ImportableInterface
{
    protected $goalService;
    protected $pointsOfSaleAvailable;
    protected $line;
    protected $typesAvailable;

    public function __construct(GoalService $goalService, Collection $pointsOfSale, Collection $goalsTypes)
    {
        $this->goalService           = $goalService;
        $this->typesAvailable        = $goalsTypes;
        $this->pointsOfSaleAvailable = $pointsOfSale;
    }

    public function getExample()
    {
        $columnsDefault = $this->getColumns();
        $columnsTypes   = $this->typesAvailable->pluck('type')->toArray();

        $lines = [
            '36721217000138',
            now()->year,
            now()->month
        ];

        foreach ($columnsTypes as $key => $type) {
            $columnsTypes[$key] = $this->translateTypeToExport($type);
            array_push($lines, rand(20, 200));
        }

        $columns = array_merge($columnsDefault, $columnsTypes);

        return [$columns, $lines];
    }

    public function getColumns()
    {
        return  [
            'cnpj'  => 'cnpj',
            'year'  => 'ano',
            'month' => 'mes',
        ];
    }

    /** @throws */
    public function processLine($line)
    {
        $year  = data_get($line, 'year');
        $month = data_get($line, 'month');
        $cnpj  = data_get($line, 'cnpj');
        $types = data_get($line, 'extra_columns');

        $this->line = $line;
        unset($this->line['extra_columns']);

        $this->cnpjExists($cnpj)
             ->hasAuthorizationUnderCnpj($cnpj)
             ->goalHasValidDate($year, $month);

        foreach ($types as $type => $goal) {
            if ($this->isNotEmpty($goal)) {
                $this->isNumeric($goal)
                    ->typeExists($type)
                    ->saveLine();
            }
        }
    }

    public function typeExists($type)
    {
        $typeTranslated = $this->translateTypeToImport($type);
        $typeFound      = $this->typesAvailable->where('type', $typeTranslated)->first();

        if ($typeFound) {
            $this->line['goalTypeId'] = $typeFound->id;
            return $this;
        }

        throw new \InvalidArgumentException(trans('goal::exceptions.goal.invalid_type', ['type' => $type]));
    }

    protected function goalHasValidDate($year, $month)
    {
        if ($month > 0 && $month <= 12 && $year == now()->year) {
            return $this;
        }

        throw new \InvalidArgumentException(trans('goal::exceptions.goal.invalid_date'));
    }

    public function isNumeric($goal)
    {
        if (is_numeric($goal)) {
            $this->line['goal'] = $goal;
            return $this;
        }

        throw new \InvalidArgumentException(trans('goal::exceptions.goal.invalid_goal', ['type' => $goal]));
    }

    public function saveLine()
    {
        return $this->goalService->persist($this->line);
    }

    public function isNotEmpty($goal)
    {
        if (empty($goal) or $goal == 0) {
            return false;
        }

        return true;
    }

    public function cnpjExists($cnpj)
    {
        $pointOfSaleService = resolve(PointOfSaleService::class);
        $pointOfSaleService->findOneByCnpj($cnpj);

        return $this;
    }

    public function hasAuthorizationUnderCnpj($cnpj)
    {
        $contains = $this->pointsOfSaleAvailable->contains('cnpj', $cnpj);

        if ($contains) {
            return $this;
        }

        throw new \InvalidArgumentException(trans('goal::exceptions.goal.pdv_not_authorized'));
    }

    public function translateTypeToImport($type)
    {
        $translations = Lang::get('goals::types');
        $flip         = array_flip($translations);

        if (array_key_exists($type, $flip)) {
            return $flip[$type];
        }
        return $type;
    }

    public function translateTypeToExport($type)
    {
        $translations = Lang::get('goals::types');

        if (array_key_exists($type, $translations)) {
            return $translations[$type];
        }
        return $type;
    }

    public function getType()
    {
        return Importables::GOALS;
    }
}
