<?php

namespace Buyback\Services;

use Buyback\Resources\contracts\Waybill\WaybillLayout;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Swift_TransportException;
use TradeAppOne\Domain\Components\Telegram\Telegram;
use TradeAppOne\Domain\Enumerators\MailConstant;
use TradeAppOne\Exceptions\SystemExceptions\ErrorSendingEmailException;

class WaybillJob
{
    const MESSAGE = "Romaneio";

    public function downloadAsPdf(Waybill $waybill): string
    {
        return (new WaybillLayout($waybill))->toPdf();
    }

    public function sendWithTelegram(Waybill $waybill)
    {
        $pdf      = (new WaybillLayout($waybill))->toPdf();
        $telegram = $this->getTelegramInstance();

        $chatId = config('telegram.developer');
        $params = [
            'chat_id' => $chatId,
            'caption' => self::MESSAGE
        ];

        $telegram->sendPdfToTelegramChat('waybill-report.pdf', $pdf, $params);
    }

    public function sendWithEmail(Waybill $waybill, array $emails)
    {
        $this->validateEmails($emails);

        try {
            $this->sendEmail($waybill, $emails);
        } catch (Swift_TransportException $exception) {
            throw new ErrorSendingEmailException();
        }
    }

    private function sendEmail(Waybill $waybill, $emails)
    {
        $pdfContent        = (new WaybillLayout($waybill))->toPdf();
        $dateSubject       = $waybill->date->format('d/m/Y - H:i');
        $dateFilename      = $waybill->date->format('d-m-Y-H-i');
        $quantityOfDevices = $waybill->services->count();
        $pointOfSale       = $waybill->pointOfSale->slug . ' - ' . $waybill->pointOfSale->label;

        $filename = "romaneio-$dateFilename.pdf";
        $filePath = storage_path($filename);

        $body = "Segue Romaneio:
        
        Lote: $waybill->id
        Data: $dateSubject
        Ponto de venda: $pointOfSale
        Quantidade de aparelhos: $quantityOfDevices";

        $subject = "Romaneio - $dateSubject - $quantityOfDevices Aparelhos";

        file_put_contents($filePath, $pdfContent);
        Mail::raw($body, function ($message) use ($subject, $filename, $filePath, $pdfContent, $emails) {
            $message
                ->to($emails)
                ->bcc([MailConstant::CADASTRO])
                ->subject($subject)
                ->attach($filePath, [
                    'as'   => $filename,
                    'mime' => 'application/pdf',
                ]);
        });
        unlink($filePath);
    }

    private function getTelegramInstance(): Telegram
    {
        return resolve(Telegram::class);
    }

    public function validateEmails(array $emails): void
    {
        $validation = Validator::make(['email' => $emails], [
            'email.*' => 'required|email|max:255'
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors()->first();
            throw new \InvalidArgumentException($errors);
        }
    }
}
