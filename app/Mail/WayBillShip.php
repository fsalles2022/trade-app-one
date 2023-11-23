<?php

declare(strict_types=1);

namespace TradeAppOne\Mail;

use Buyback\Services\Waybill;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use stdClass;
use TradeAppOne\Domain\Enumerators\MailConstant;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Mail\Attachables\WaybillPDF;
use TradeAppOne\Utils\Mailing\AttacherMailer;

class WayBillShip extends AttacherMailer
{
    /**
     * @var Waybill
     */
    private $waybill;

    /**
     * All additional emails to send
     *
     * @var Collection|User[]|string[]
     */
    private $additional;

    /**
     * Create a new message instance.
     *
     * @param Waybill $waybill
     * @param User[]|Collection $additionalEmails
     * @return void
     */
    public function __construct(Waybill $waybill, $additionalEmails = [])
    {
        $this->waybill    = $waybill;
        $this->additional = $additionalEmails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): Mailable
    {
        $this->setDestinies()->mount();

        return parent::build();
    }

    /**
     * @param string $network
     * @return string[]
     */
    private function destinies(string $network): array
    {
        if ($network === NetworkEnum::RIACHUELO) {
            return $this->separate(config('mail.waybill.destinies_when_riachuelo'));
        }

        if ($network === NetworkEnum::FUJIOKA) {
            return $this->separate(config('mail.waybill.destinies_when_fujioka'));
        }

        if ($network === NetworkEnum::IPLACE) {
            return $this->separate(config('mail.waybill.destinies_when_iplace'));
        }

        return $this->separate(config('mail.waybill.destinies_default'));
    }

    private function setDestinies(): AttacherMailer
    {
        $destinies = collect(
            $this->destinies($this->waybill->pointOfSale->getSlugNetwork())
        )->merge($this->getAdditionalAsArray());

        $destinies = $this->getEmailsDestiniesFrom($destinies);

        return $this->to($destinies->all());
    }

    private function getEmailsDestiniesFrom(Collection $destinies): Collection
    {
        return $destinies->map(function ($elem) {
            return $this->getDestinyFrom($elem);
        })->values();
    }

    private function getDestinyFrom($elem): string
    {
        if ($elem instanceof User) {
            return $elem->email;
        }

        if (is_string($elem)) {
            return $elem;
        }

        if (is_array($elem) && isset($elem['email'])) {
            return $elem['email'];
        }

        if ($elem instanceof stdClass && isset($elem->email)) {
            return $elem->email;
        }

        throw new InvalidArgumentException("The destinies passed must be valid");
    }

    private function mount(): AttacherMailer
    {
        return $this->bcc([MailConstant::CADASTRO])
                     ->subject($this->genSubject())
                     ->view('emails.waybill', [ 'waybill' => $this->waybill ])
                     ->attachObject(
                         new WaybillPDF($this->waybill)
                     );
    }

    private function genSubject(): string
    {
        return "Romaneio - {$this->waybill->date->format('d/m/Y - H:i')} - {$this->waybill->services->count()} Aparelhos";
    }

    /**
     * @return string[]|User
     */
    private function getAdditionalAsArray(): array
    {
        $result = $this->additional;

        if ($result instanceof Collection) {
            $result = $result->values()->all();
        }

        return $result;
    }
}
