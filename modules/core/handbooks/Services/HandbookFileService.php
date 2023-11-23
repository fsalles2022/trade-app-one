<?php

namespace Core\HandBooks\Services;

use Core\HandBooks\Models\Handbook;
use Core\HandBooks\Models\HandbookRequest;
use TradeAppOne\Facades\S3;
use TradeAppOne\Facades\Uniqid;

class HandbookFileService
{
    public const DEFAULT_DIR = 'handbooks';

    public function save(HandbookRequest $data, ?Handbook $handbook = null): string
    {
        return $handbook === null
           ? $this->create($data)
           : $this->update($data, $handbook);
    }

    public function create(HandbookRequest $data): string
    {
        $path = self::generatePath($data->module, $data->title, $data->getType());

        S3::put($path, file_get_contents($data->getFile()));
        return $path;
    }

    public function update(HandbookRequest $data, Handbook $handbook): string
    {
        if ($data->getFile() === null) {
            return '';
        }

        $title = $data->title ?? $handbook->title;

        $path = self::generatePath($handbook->module, $title, $data->getType());

        S3::delete($handbook->file);
        S3::put($path, file_get_contents($data->getFile()));
        return $path;
    }

    public static function generatePath(string $module, string $title, string $type): string
    {
        $module = str_slug($module);
        $name   = Uniqid::generate() . '-' . str_slug($title);

        return self::DEFAULT_DIR . "/$module/$type/$name.$type";
    }
}
