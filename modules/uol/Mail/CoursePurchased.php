<?php

namespace Uol\Mail;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use TradeAppOne\Domain\Components\Helpers\DateConvertHelper;
use TradeAppOne\Domain\Models\Collections\Service;
use Uol\Models\UolPassport;

class CoursePurchased extends Mailable
{
    protected $service;
    protected $passport;

    public function __construct(Service $service, UolPassport $passport)
    {
        $this->service  = $service;
        $this->passport = $passport;
    }

    public function build()
    {
        return $this
            ->from('noreply@tradeupgroup.com', 'Uol Cursos')
            ->subject("Parabéns! Você acaba de adquirir um de nossos cursos.")
            ->view('uol::course_purchased')
            ->with([
                'plan'     => $this->service->label,
                'name'     => "{$this->service->customer['firstName']} {$this->service->customer['lastName']}",
                'passport' => $this->passport->number,
                'saleDate' => DateConvertHelper::convertToStringFormat(Carbon::now(), 'd/m/y'),
            ]);
    }
}
