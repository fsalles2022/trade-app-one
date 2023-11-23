<?php


namespace TradeAppOne\Domain\Components\Console;

use Illuminate\Support\Facades\Validator;

class OptionsValidator
{
    public static function validate($options, $rules)
    {
        $rules = array_merge($rules, [
            'help'           => 'boolean',
            'quiet'          => 'boolean',
            'verbose'        => 'boolean',
            'version'        => 'boolean',
            'ansi'           => 'boolean',
            'no-ansi'        => 'boolean',
            'no-interaction' => 'boolean',
        ]);
        $data  = [];
        foreach ($options as $option => $value) {
            if (is_array($value)) {
                foreach ($value as $key => $items) {
                    $rules[$option . $key] = $rules[$option];
                    $data[$option . $key]  = $items;
                }
            } else {
                $data[$option] = $value;
            }
        }

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new \RuntimeException(implode($validator->getMessageBag()->all(), ','));
        }

        return ! $validator->fails();
    }
}
