<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\File;

use Illuminate\Http\File;
use Illuminate\Support\Facades\File as FileManager;

class TemporalInstance
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $files;

    public function __construct()
    {
        $this->files = collect();
    }

    public function push(File $file): void
    {
        $this->files->push($file);
    }

    public function done(): void
    {
        foreach ($this->files->values()->all() as $file) {
            if (! ($file instanceof File)) {
                throw new \RuntimeException('Object in file is not instance of File');
            }

            FileManager::delete($file->getPath());
        }
    }
}
