<?php

namespace Gateway\Components;

use Carbon\Carbon;
use Gateway\API\Brand;
use Gateway\Helpers\ValidateModelTrait;
use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Components\Helpers\DateConvertHelper;

class CreditCard
{
    use ValidateModelTrait;

    protected $attributes = [];

    protected $flag;
    protected $cardHolder;
    protected $cardNumber;
    protected $cardSecurityCode;
    protected $cardExpirationDate;
    public $softDescriptor;

    public static function fill(array $attributes): CreditCard
    {
        $card = new static;
        $card->validate($attributes);

        $card->attributes         = $attributes;
        $card->flag               = data_get($attributes, 'flag');
        $card->cardHolder         = $attributes['cardHolder'] ?? $attributes['name'];
        $card->cardNumber         = $attributes['cardNumber'] ?? $attributes['pan'];
        $card->cardSecurityCode   = $attributes['cardSecurityCode'] ?? $attributes['cvv'];
        $card->cardExpirationDate = DateConvertHelper::convertMonthYearToYearMonth($attributes['cardExpirationDate'] ?? "{$attributes['month']}/{$attributes['year']}");
        $card->softDescriptor     = data_get($attributes, 'softDescriptor');

        return $card;
    }

    private static function rules(): array
    {
        return [
            "flag" => ['required', 'string', Rule::in(array_values(Brand::getConstants()))],
            "cardHolder" => 'required_without:name|string|max:20',
            "name" => 'required_without:cardHolder|string|max:20',
            "cardNumber" => 'required_without:pan|string|min:16|max:18',
            "pan" => 'required_without:cardNumber|string|min:16|max:18',
            "cardSecurityCode" => 'required_without:cvv|string|size:3',
            "cvv" => 'required_without:cardSecurityCode|string|size:3',
            "cardExpirationDate" => 'required_without:month,year|date_format:"m/y"|after_or_equal:' . Carbon::now()->format('m/y'),
            "month" => 'required_without:cardExpirationDate|date_format:"m"',
            "year" => 'required_without:cardExpirationDate|date_format:"y"|after_or_equal:' . Carbon::now()->format('y'),
            'softDescriptor' => 'sometimes|string|min:2|max:10|nullable'
        ];
    }

    public function toArray(): array
    {
        return array_filter([
            'flag' => $this->flag,
            'cardHolder' => $this->cardHolder,
            'cardNumber' => $this->cardNumber,
            'cardSecurityCode' => $this->cardSecurityCode,
            'cardExpirationDate' => $this->cardExpirationDate,
            'softDescriptor'     => $this->softDescriptor
        ]);
    }
}
