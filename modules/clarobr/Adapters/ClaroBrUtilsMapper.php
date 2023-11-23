<?php

namespace ClaroBR\Adapters;

class ClaroBrUtilsMapper
{
    public static function map(array $utils): array
    {
        $local   = self::adaptLocal($utils);
        $dueDate = self::adaptDueDate($utils);
        $banks   = self::adaptBanks($utils);

        return [
            'local' => $local,
            'dueDate' => $dueDate,
            'banks' => $banks,
        ];
    }

    private static function adaptLocal(array $utils)
    {
        $locals = data_get($utils, 'logradouros', []);

        return collect($locals)
            ->map(function ($local) {
                $id    = data_get($local, 'id');
                $label = data_get($local, 'nome');

                return [
                    'id' => $id,
                    'label' => $label
                ];
            })
            ->filter(static function ($local) {
                return is_scalar($local['id'] ?? null) && is_scalar($local['label'] ?? null);
            })
            ->toArray();
    }

    private static function adaptDueDate(array $utils)
    {
        $dueDates = data_get($utils, 'vencimentos', []);

        return collect($dueDates)
            ->map(function ($dueDate) {
                return [
                    'id' => $dueDate['id'] ?? null,
                    'dueDay' => $dueDate['vencimento'] ?? null,
                    'closingDay' => $dueDate['fechamento'] ?? null
                ];
            })
            ->filter(function ($local) {
                $allValuesAreScalar = (is_scalar($local['id'] ?? null)
                    && is_scalar($local['dueDay'] ?? null)
                    && is_scalar($local['closingDay'] ?? null));

                return $allValuesAreScalar;
            })
            ->toArray();
    }

    private static function adaptBanks(array $utils)
    {
        $banks = data_get($utils, 'bancos', []);

        return collect($banks)
            ->map(function ($bank) {
                return [
                    'id' => $bank['id'] ?? null,
                    'label' => $bank['nome'] ?? null
                ];
            })
            ->filter(function ($local) {
                $allValuesAreScalar = is_scalar($local['id'] ?? null) && is_scalar($local['label'] ?? null);
                return $allValuesAreScalar;
            })
            ->toArray();
    }
}
