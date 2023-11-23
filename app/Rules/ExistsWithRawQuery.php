<?php

declare(strict_types=1);

namespace TradeAppOne\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rule as RuleFacade;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Rules\Utils\ValueReplacerFormat;

class ExistsWithRawQuery extends RuleFacade implements Rule
{
    protected $raw;
    protected $table;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($raw, $table, $customValueName = ':value')
    {
        if (is_string($raw)) {
            $raw = new ValueReplacerFormat($raw, null, $customValueName);
        }

        if (! $raw instanceof ValueReplacerFormat) {
            throw new \InvalidArgumentException('$rawQueryModifier must be a "string" or a "' . ValueReplacerFormat::class . '" class instance.');
        }

        $this->raw   = $raw;
        $this->table = $table;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->table::query()->whereRaw(
            $this->raw->apply()
        )->first()->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
