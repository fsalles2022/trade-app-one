<?php

namespace Buyback\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class QuizFormRequest extends FormRequestAbstract
{
    const QUESTIONS = 'questions';
    const STORE     = 'store';
    const UPDATE    = 'update';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case self::QUESTIONS:
                return $this->onQuestions();

            case self::STORE:
                return $this->onStore();

            case self::UPDATE:
                return $this->onUpdate();

            default:
                return [];
        }
    }

    private function onQuestions(): array
    {
        return [
            'deviceId'  => 'required|Numeric'
        ];
    }

    private function onStore(): array
    {
        return [
            'network'                 => 'required|exists:networks,slug',

            'questions'               => 'required|array',
            'questions.*.question'    => 'required|string',
            'questions.*.weight'      => 'sometimes',
            'questions.*.order'       => 'required|numeric',
            'questions.*.blocker'     => 'required|boolean',
            'questions.*.description' => 'sometimes'
        ];
    }

    private function onUpdate(): array
    {
        return [
            'questions'               => 'required|sometimes',
            'questions.*.question'    => 'required|sometimes|string',
            'questions.*.weight'      => 'sometimes',
            'questions.*.order'       => 'required|sometimes|numeric',
            'questions.*.blocker'     => 'required|sometimes|boolean',
            'questions.*.description' => 'sometimes'
        ];
    }
}
