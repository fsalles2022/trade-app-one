<?php

namespace TradeAppOne\Domain\Components\Helpers;

use Illuminate\Support\Facades\Validator;

final class ITUE164NumberStructure
{
    public static function pullAreaCode(string $number): string
    {
        $cleanNumber = str_replace(' ', '-', $number);
        $cleanNumber = preg_replace('/[^A-Za-z0-9\-]/', '', $cleanNumber);

        if (strlen($cleanNumber) >= 11 && strlen($cleanNumber) <= 15) {
            $extracted  = substr($cleanNumber, 0, 2);
            $validation = Validator::make(['areaCode' => $extracted], ['areaCode' => 'area_code_prefix']);

            if ($validation->fails()) {
                throw new \InvalidArgumentException('Invalid area code');
            }
            return $extracted;
        }
        throw new \InvalidArgumentException('To extract country code number must have 11 digits or more');
    }
}
