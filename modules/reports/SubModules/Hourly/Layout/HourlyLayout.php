<?php

namespace Reports\SubModules\Hourly\Layout;

use ReflectionClass;
use Reports\Exceptions\FailedReportBuildException;
use Reports\SubModules\Hourly\Constants\HourConstants;
use Reports\SubModules\Hourly\Constants\HourTextConstants;
use TradeAppOne\Domain\Components\Helpers\MoneyHelper;

class HourlyLayout
{
    const TOTAL    = 'RESUME';
    const LABEL    = 'LABEL';
    const POS_PAGO = 'POS_PAGO';
    const PRE_PAGO = 'PRE_PAGO';
    const GOALS    = 'GOALS';

    public $availableColumns = [];
    public $totalColumns     = [];
    public $blocks           = [];
    public $headersRows      = [];
    public $excludeHeader    = [];

    public $date;
    public $time;
    public $network;

    public function __construct(array $data, $options = [])
    {
        $this->excludeHeader = data_get($options, 'exclude');
        $this->date          = $data['DATE'] ?? null;
        $this->time          = $data['TIME'] ?? null;
        $this->network       = $data['NETWORK'] ?? null;
        $this->headersRows   = $data['HEADERS'] ?? null;
        $this->blocks        = $data['BODY'] ?? null;

        if ($this->date &&
            $this->time &&
            $this->network &&
            $this->headersRows
            && $this->blocks) {
            $this->totalColumns     = $this->getTotalOfColumns();
            $this->availableColumns = $this->getAvailableColumns($this->headersRows);

            return $this;
        }

        throw new FailedReportBuildException('Invalid Structure in Hourly');
    }

    private function getTotalOfColumns()
    {
        $total = 0;

        foreach ($this->getAvailableColumns($this->headersRows) as $group) {
            $total += count($group);
        }

        return $total;
    }

    public function getAvailableColumns($header)
    {
        $columns = [];

        $columns[self::LABEL]    = $this->getLabelColumn();
        $columns[self::TOTAL]    = $this->getTotalColumn();
        $columns[self::POS_PAGO] = array_keys($header['POS_PAGO']);
        $columns[self::PRE_PAGO] = array_keys($header['PRE_PAGO']);
        $columns[self::GOALS]    = $this->getGoalsColumns();

        if (is_array($this->excludeHeader) && count($this->excludeHeader)) {
            foreach ($this->excludeHeader as $header) {
                if ($header === HourConstants::VALUES) {
                    $position = array_search(HourConstants::VALUES, $columns[self::TOTAL]);
                    unset($columns[self::TOTAL][$position]);
                } else {
                    unset($columns[$header]);
                }
            }
        }

        return $columns;
    }

    private function getLabelColumn()
    {
        return [
            HourConstants::LABEL
        ];
    }

    private function getTotalColumn()
    {
        return [
            HourConstants::TOTAL,
            HourConstants::VALUES
        ];
    }

    private function getGoalsColumns()
    {
        return [
            HourConstants::TOTAL,
            HourConstants::PERCENT,
            HourConstants::GAP,
        ];
    }

    public function fillBodyRow($row, $rowName = '-')
    {
        $row[HourConstants::LABEL] = [HourConstants::LABEL => $rowName];
        foreach ($this->availableColumns as $groupOfColumn => $columns) {
            foreach ($columns as $column) {
                $value = $row[$groupOfColumn][$column] ?? '-';

                if ($column == HourConstants::VALUES && is_float($value)) {
                    $value = MoneyHelper::formatMoney($value);
                }

                $this->printCellText($value);
            }
        }
    }

    private function printCellText($text)
    {
        echo '<th>' . $text . '</th>';
    }

    public function fillHeaderGroupRow()
    {
        foreach ($this->availableColumns as $groupOfColumn => $columns) {
            $text = $this->getTextByConstant($groupOfColumn);
            $this->printWithColspan($text, count($columns));
        }
    }

    private function getTextByConstant($constant)
    {
        $class      = HourTextConstants::class;
        $reflection = new ReflectionClass($class);

        $value = $reflection->getConstant($constant);

        return $value ? $value : null;
    }

    private function printWithColspan($text, $colspan = '1')
    {
        echo '<th colspan=' . $colspan . '>' . $text . '</th>';
    }

    public function fillHeaderTitleRow(int $colspan = 3)
    {
        foreach ($this->availableColumns as $groupOfColumn => $columns) {
            foreach ($columns as $column) {
                if ($column == HourConstants::LABEL) {
                    echo '<th rowspan="' . $colspan . ' "> TOTAL REGIONAIS </th>';
                } else {
                    if ($col = $this->getTextByConstant($column)) {
                        echo '<th class="space">' . $col . '</th>';
                    } else {
                        echo '<th class="space">' . $column . '</th>';
                    }
                }
            }
        }
    }

    public function fillHeaderRow($cellValue)
    {
        foreach ($this->availableColumns as $groupOfColumn => $columns) {
            foreach ($columns as $column) {
                if ($column != HourConstants::LABEL) {
                    $value = $this->headersRows[$groupOfColumn][$column][$cellValue] ?? '-';

                    $cellThatWillBePrintedForValues = ($cellValue === HourConstants::QUANTITY && $column ===
                        HourConstants::VALUES);

                    if ($cellThatWillBePrintedForValues) {
                        $valueToHeader = $this->headersRows[$groupOfColumn][HourConstants::TOTAL][$column] ?? 0.0;
                        $value         = MoneyHelper::formatMoney($valueToHeader);
                    }

                    $this->printCellText($value);
                }
            }
        }
    }

    public function toHtml(): string
    {
        view()->addLocation(__DIR__);
        return view('hourly', ['report' => $this])->render();
    }
}
