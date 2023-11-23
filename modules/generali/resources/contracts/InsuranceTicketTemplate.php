<?php


namespace Generali\resources\contracts;

use Carbon\Carbon;
use Generali\Enumerators\GeneraliProductsEnumerators;
use Jenssegers\Date\Date;
use stdClass;
use TradeAppOne\Domain\Components\Helpers\BrazilianDocuments;
use TradeAppOne\Domain\Components\Helpers\FormatHelper;
use TradeAppOne\Domain\Components\Helpers\MoneyHelper;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Components\Printer\PdfHelper;
use TradeAppOne\Domain\Models\Collections\Service;

class InsuranceTicketTemplate
{
    public $ticket;
    public $service;

    public function __construct(Service $service)
    {
        $this->ticket  = new stdClass();
        $this->service = $service;
    }

    public function layout(): InsuranceTicketTemplate
    {
        $service = $this->service->toArray();
        $sale    = $this->service->sale;

        $this->ticket->transactionId   = data_get($service, 'serviceTransaction');
        $this->ticket->city            = ucwords(mb_strtolower($sale->pointOfSale['city']));
        $this->ticket->date            = (new Date($sale->updatedAt))->format('d \d\e F \d\e Y');
        $this->ticket->dateF           = (new Date($sale->updatedAt))->format('d/m/Y');
        $this->ticket->salesman        = data_get($sale, 'user.firstName'). '' .data_get($sale, 'user.lasttName');
        $this->ticket->device          = data_get($service, 'device');
        $this->ticket->device['price'] = MoneyHelper::formatMoney($this->ticket->device['price']);
        $this->ticket->price           = data_get($service, 'price');
        $this->ticket->customer        = $this->formatCustomer();
        $this->ticket->premium         = data_get($service, 'premium');
        $this->ticket->ticketNumber    = data_get($service, 'policyId');
        $this->ticket->product         = data_get($service, 'product');
        $this->ticket->payment         = (int) data_get($service, 'payment.times');

        return $this;
    }

    public function formatCustomer(): array
    {
        $customer = data_get($this->service->toArray(), 'customer');

        $customer['cpf']         = FormatHelper::mask($customer['cpf'], '###.###.###-##');
        $customer['mainPhone']   = $this->mainPhoneFormat($customer);
        $customer['fullName']    = $customer['firstName'] . ' ' . $customer['lastName'];
        $customer['zipCode']     = FormatHelper::mask($customer['zipCode'], '#####-###');
        $customer['birthday']    = Carbon::createFromFormat('Y-m-d', $customer['birthday'])->format('d/m/Y');
        $customer['rg']          = BrazilianDocuments::unmask(data_get($customer, 'rg'));
        $customer['genderFull']  = ($customer['gender'] === 'M') ? 'Masculino' : 'Feminino';
        $customer['fullAddress'] = ($customer['localId'] ?? '') . ' ' . ($customer['local'] ?? '') . ', ' . ($customer['number'] ?? '') . ' - ' . ($customer['complement'] ?? '');

        return $customer;
    }

    private function mainPhoneFormat(array $customer): string
    {
        $customerPhone = MsisdnHelper::removeCountryCode(MsisdnHelper::BR, $customer['mainPhone']);
        return FormatHelper::mask($customerPhone, '(##) #####-#####');
    }

    public function toPdf(): string
    {
        $html      = $this->toHtml();
        $options   = ['paper' => 'A4', 'orientation' => 'portrait'];
        $pdfHelper = resolve(PdfHelper::class);

        return $pdfHelper->fromHtmlToContent($html, $options);
    }

    public function toHtml(): string
    {
        view()->addLocation(__DIR__);

        return (data_get($this->ticket->product, 'slug') === GeneraliProductsEnumerators::GE)
            ? view('insuranceTicketGELayout', ['ticket' => $this->ticket, 'layout'=>$this])->render()
            : view('insuranceTicketRFQLayout', ['ticket' => $this->ticket, 'layout'=>$this])->render();
    }
    public function getImage(string $path, string $extension = 'png')
    {
        return (__DIR__ . '/' . $path . '.' . $extension);
    }
}
