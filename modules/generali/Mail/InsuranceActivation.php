<?php

namespace Generali\Mail;

use Generali\resources\contracts\InsuranceTicketTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Enumerators\MailConstant;
use TradeAppOne\Domain\Models\Collections\Service;

class InsuranceActivation extends Mailable
{
    public const FILE_NAME = 'ticket.pdf';

    use Queueable, SerializesModels;

    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function build(): InsuranceActivation
    {
        $insuranceTicket = (new InsuranceTicketTemplate($this->service))->layout()->toPdf();

        Storage::disk('local')->put(self::FILE_NAME, $insuranceTicket);

        $mailSent = $this->from('noreply@generali.com', 'Generali Seguros')
            ->subject('Seguro enviado para ativação.')
            ->bcc(MailConstant::OCTADESK)
            ->attachFromStorageDisk('local', self::FILE_NAME, null, [
                'as'   => self::FILE_NAME,
                'mime' => 'application/pdf'
            ])
            ->view('generaliViews::activation_mail_generali')
            ->with([
                'customerFirstName' => data_get($this->service->customer, 'firstName'),
                'customerLastName'  => data_get($this->service->customer, 'lastName'),
                'deviceModel'       => data_get($this->service->device, 'model'),
                'devicePrice'       => data_get($this->service->device, 'price'),
                'label'             => ucwords(strtolower(data_get($this->service, 'product.label'))),
                'price'             => data_get($this->service, 'price'),
                'assinatura'        => $this->getImage('logo-generali-alpha'),
                'header'            => $this->getImage('logo-generali2-removed-bg')
            ]);

        Storage::disk('local')->delete(self::FILE_NAME);

        return $mailSent;
    }

    public function getImage(string $path, string $extension = 'png'): string
    {
        return (__DIR__ . '/../resources/contracts/' . $path . '.' . $extension);
    }
}
