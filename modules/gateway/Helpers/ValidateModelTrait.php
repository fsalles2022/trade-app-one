<?php

namespace Gateway\Helpers;

use Illuminate\Support\Facades\Validator;
use TradeAppOne\Exceptions\BusinessExceptions\ModelInvalidException;

trait ValidateModelTrait
{
    public function validate(array $attributes = null)
    {
        $attr = $attributes ?? $this->attributes;

        $validator = Validator::make($attr, self::rules());
        if ($validator->fails()) {
            throw new ModelInvalidException($validator->errors()->first());
        }

        return $this;
    }
}
