<?php

namespace TradeAppOne\Domain\Components\Telegram;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class Telegram
{
    private $sender;

    public function __construct($token)
    {
        try {
            $this->sender = new Api($token);
        } catch (TelegramSDKException $e) {
            logger('telegram-sender', []);
        }
    }

    public function sendDocument($params)
    {
        $this->sender->sendDocument($params);
    }

    public function sendPdfToTelegramChat(string $filename, string $pdfContent, array $params): void
    {
        $filePath = storage_path($filename);
        file_put_contents($filePath, $pdfContent);

        $params['document'] = $filePath;
        $this->sendDocument($params);

        unlink($filePath);
    }

    public function sendMessage($params)
    {
        $this->sender->sendMessage($params);
    }
}
