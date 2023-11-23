<?php

namespace TradeAppOne\Domain\Models\Collections;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use TradeAppOne\Exceptions\BusinessExceptions\ModelInvalidException;

class BaseModel extends Model
{
    use SoftDeletes;

    const CREATED_AT               = 'createdAt';
    const UPDATED_AT               = 'updatedAt';
    const DELETED_AT               = 'deletedAt';
    public static $snakeAttributes = false;
    protected $dates               = ['createdAt', 'createdAt', 'deletedAt'];

    /**
     * Error message bag
     *
     * @var MessageBag
     */
    protected $errors;

    /**
     * It allows you to save only if the model is valid
     */
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (! $model->validate()) {
                throw new ModelInvalidException($model->getErrors());
            }
        });
    }

    /**
     * Validates current attributes against rules
     */
    public function validate()
    {
        $validator = app('validator');

        $v = $validator->make($this->attributesToArray(), $this->rules(), $this->messages());

        $this->extendValidator($v);

        if ($v->passes()) {
            return true;
        }
        $this->setErrors($v->messages());

        return false;
    }

    public function rules(): array
    {
        return [];
    }

    /** Returns the custom messages for validation errors */
    public function messages()
    {
        return [];
    }

    /**
     * @param $v
     */
    public function extendValidator($v)
    {
        return;
    }

    /**
     * Retrieve error message bag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set error message bag
     *
     * @var MessageBag
     */
    protected function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Inverse of wasSaved
     */
    public function hasErrors()
    {
        return ! empty($this->errors);
    }
}
