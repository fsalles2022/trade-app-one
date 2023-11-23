<?php

namespace Buyback\Services;

class DeviceRated
{
    public $price;
    public $note;
    public $tierNote;


    public function __construct($price, $note, $tierNote)
    {
        $this->price    = $price;
        $this->note     = $note;
        $this->tierNote = $tierNote;
    }

    public function toArray()
    {
        return [
            'price'    => $this->price,
            'note'     => $this->note,
            'tierNote' => $this->tierNote,
        ];
    }
}
