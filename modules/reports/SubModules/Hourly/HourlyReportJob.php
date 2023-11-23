<?php

namespace Reports\SubModules\Hourly;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Reports\SubModules\Hourly\Layout\HourlyLayout;
use TradeAppOne\Domain\Components\Printer\PdfHelper;
use TradeAppOne\Domain\Components\Telegram\Telegram;
use TradeAppOne\Domain\Enumerators\MailConstant;

class HourlyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MESSAGE   = "Relatório Hora-Hora";
    const FROM_NAME = "Trade App One";

    private $options;
    private $filePath;

    public function __construct(array $options)
    {
        $this->options  = $options;
        $this->filePath = storage_path($this->options['network'] . '-report.pdf');
    }

    public function handle()
    {
        $reportData = $this->generateReport();
        $pdfContent = $this->renderReportInPdf($reportData);
        $this->writeFile($pdfContent);

        $this->sendPdfToTelegramChat();
        $this->sendPdfToMail();
        $this->deleteFile();
    }

    private function generateReport(): ?array
    {
        $date = $this->options['date'];
        if ($date) {
            $this->options['date'] = Carbon::createFromFormat('Y-m-d-H-i', $date);
        }

        $service = resolve(HourlyReportService::class);
        return $service->get($this->options);
    }

    private function renderReportInPdf($reportData): string
    {
        $hourlyLayout = new HourlyLayout($reportData, $this->options);
        $html         = $hourlyLayout->toHtml();

        $options   = [
            'paper' => 'A4',
            'orientation' => 'portrait',
        ];
        $pdfHelper = resolve(PdfHelper::class);
        return  $pdfHelper->fromHtmlToContent($html, $options);
    }

    private function deleteFile(): void
    {
        unlink($this->filePath);
    }

    private function writeFile($pdfContent): void
    {
        file_put_contents($this->filePath, $pdfContent);
    }

    private function sendPdfToTelegramChat(): void
    {
        $chatId = config($this->options['chatId']);

        $params   = [
            'chat_id' => $chatId,
            'document' => $this->filePath,
            'caption' => self::MESSAGE . ' ' . $this->options['network']
        ];
        $telegram = resolve(Telegram::class);
        $telegram->sendDocument($params);
    }

    private function sendPdfToMail(): void
    {
        Mail::send('emails.mail_report', $this->options, function ($mail) {
            $mail->from(MailConstant::CADASTRO, self::FROM_NAME);
            $mail->subject(self::MESSAGE);
            $mail->to(data_get($this->options, 'mailTo', []));
            $mail->cc(data_get($this->options, 'mailCC', []));
            $mail->bcc(MailConstant::TAO_RELATORIOS);
            $mail->attach($this->filePath);
        });
    }
}
