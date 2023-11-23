<?php

namespace TradeAppOne\Domain\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Enumerators\ImportHistoryStatus;
use TradeAppOne\Domain\Importables\ImportablePath;
use TradeAppOne\Domain\Models\Tables\ImportHistory;
use TradeAppOne\Domain\Models\Tables\User;

class ImportHistoryRegistry
{
    private $importHistory;
    private $user;
    private $type;

    public function __construct()
    {
        $this->importHistory = new ImportHistory();
        $this->user          = auth()->user();
    }

    public function user(User $user): ImportHistoryRegistry
    {
        $this->user = $user;
        return $this;
    }

    public function type(string $type): ImportHistoryRegistry
    {
        $this->type = $type;
        return $this;
    }

    public function success()
    {
        $this->importHistory->status = ImportHistoryStatus::SUCCESS;
        $this->importHistory->save();
    }

    public function savePendingFile(?UploadedFile $file)
    {
        $this->importHistory->type   = $this->type;
        $this->importHistory->status = ImportHistoryStatus::PENDING;
        $this->importHistory->user()->associate($this->user);
        $this->importHistory->save();

        $s3Path = $this->registryFile(file_get_contents($file), 'INPUT');

        $this->importHistory->inputFile = $s3Path;
        $this->importHistory->save();

        return $this;
    }

    public function saveErrorFile($file)
    {
        $this->importHistory->status = ImportHistoryStatus::ERROR;
        $this->importHistory->save();

        $s3Path = $this->registryFile($file, 'OUTPUT');

        $this->importHistory->outputFile = $s3Path;
        $this->importHistory->save();
    }

    private function registryFile($file, $source)
    {
        $filePath = ImportablePath::generate(
            $this->user,
            $this->type,
            $source,
            $this->importHistory->id
        );

        try {
            Storage::disk('s3')->put($filePath, $file);
            return Storage::disk('s3')->url($filePath);
        } catch (Exception $exception) {
            logger()->alert('Invalid sending to S3' . $exception->getMessage());
        }

        return null;
    }
}
