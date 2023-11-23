<?php

namespace TradeAppOne\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Components\Helpers\ZipHelper;
use TradeAppOne\Domain\Enumerators\MailConstant;
use Illuminate\Foundation\Bus\Dispatchable;
use TradeAppOne\Exports\UsersExportToOperators;

class MailRegistrations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    private $query;
    private $columns;
    private $emails;
    private $emails_cc;

    private static $network;

    public const FROM_NAME = 'Trade App One';
    public const SUBJECT   = 'Cadastro de Vendedores ';

    public function __construct(string $query, array $columns, array $emails, array $emails_cc)
    {
        $this->query     = $query;
        $this->columns   = $columns;
        $this->emails    = $emails;
        $this->emails_cc = $emails_cc;
    }

    /** @throws */
    public function handle(): void
    {
        try {
            $csvFileName     = $this->getCsvFileName();
            $usersCollection = collect(DB::select($this->query))->map(static function ($row) {
                self::$network = $row->REDE;
                return (array) $row;
            })->unique('CPF');

            (new UsersExportToOperators($usersCollection, $this->columns))->store($csvFileName);

            $zipFilePath = storage_path('app/') . 'base_usuarios_' . self::$network . '.zip';

            $generatedZipFile = $this->zipCsvFile($zipFilePath, $csvFileName);

            Mail::send(
                'emails.send_registrations',
                ['network' => self::$network],
                function ($mail) use ($zipFilePath, $generatedZipFile) {
                    $mail->from(MailConstant::CADASTRO, self::FROM_NAME);
                    $mail->to($this->emails);
                    $mail->cc($this->emails_cc);
                    $mail->subject(self::SUBJECT . self::$network);
                    $mail->bcc(MailConstant::TAO_RELATORIOS);
                    $mail->attach($generatedZipFile);
                }
            );

            Storage::delete($csvFileName);
        } catch (\Exception $exception) {
            Log::info('MailRegistrations', ['Exception' => $exception->getMessage()]);
        }
    }

    private function getCsvFileName(): string
    {
        $nowDate    = Carbon::now()->format('d-m-Y');
        $identifier = uniqid('', false);

        return "base-usuarios-$nowDate-$identifier.csv";
    }

    private function zipCsvFile(string $zipFileName, string $csvFileName): string
    {
        $csvFilePath = storage_path('app/');

        $zip = ZipHelper::create($zipFileName);

        $zip->addFile($csvFilePath . $csvFileName, $csvFileName);

        $generatedFileName = $zip->filename;

        $zip->close();

        return $generatedFileName;
    }
}
