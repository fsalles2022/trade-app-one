<?php

namespace Banner\Models;

use Banner\Exceptions\ModelInvalidException;
use Illuminate\Support\Facades\Validator;

abstract class Model
{
    protected $rules = [];

    public function fill($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->{$attribute} = $value;
        }
    }

    public function validate()
    {
        $validator = Validator::make($this->attributesToArray(), $this->rules());
        if ($validator->fails()) {
            throw new ModelInvalidException($validator->errors()->first());
        }
        return true;
    }

    public function attributesToArray(): array
    {
        return get_object_vars($this);
    }

    abstract public function rules(): array;
}
