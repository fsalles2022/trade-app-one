<?php

namespace Buyback\Services;

class EvaluationProducerFromQuestions
{
    public $price;
    public $deviceNote;
    public $questions;

    public function __construct($priceAndNote, $questionsAnswer)
    {
        $this->price      = $priceAndNote['devicePrice'];
        $this->deviceNote = $priceAndNote['deviceNote'];
        $this->questions  = $questionsAnswer;
    }

    public function toArray() :array
    {
        return [
            'price'      => $this->price,
            'deviceNote' => $this->deviceNote,
            'questions' => $this->questions,
        ];
    }
}
