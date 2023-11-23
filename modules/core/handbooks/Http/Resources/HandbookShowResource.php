<?php

namespace Core\HandBooks\Http\Resources;

use Core\HandBooks\Exceptions\HandbookExceptions;
use Core\HandBooks\Models\Handbook;
use TradeAppOne\Domain\Enumerators\Files\FileTypes;
use TradeAppOne\Domain\Enumerators\FilterModes;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Facades\S3;

class HandbookShowResource
{
    public static function edit(Handbook $handbook): array
    {
        return [
            'id' => $handbook->id,
            'title' => $handbook->title,
            'file' => self::filename($handbook->file),
            'networksFilterMode' => $handbook->networksFilterMode,
            'rolesFilterMode' => $handbook->rolesFilterMode,
            'module' => [
                'slug' => $handbook->module,
                'label' => trans("operations.$handbook->module"),
            ],
            'category' => [
                'slug' => $handbook->getOriginal('category'),
                'label' => $handbook->category
            ],
            'networks' => self::networks($handbook),
            'roles' => self::roles($handbook)
        ];
    }

    public static function show(Handbook $handbook)
    {
        switch ($handbook->type) {
            case FileTypes::VIDEO:
                return ['link' => S3::url($handbook->file)];

            case FileTypes::DOCUMENT:
                return S3::download($handbook->file);

            default:
                throw HandbookExceptions::typeInvalid();
        }
    }

    private static function networks(Handbook $handbook): array
    {
        if ($handbook->networksFilterMode === FilterModes::CHOSEN) {
            $handbook->load('networks');

            return $handbook->networks->map(function (Network $network) {
                return $network->only(['id', 'slug', 'label']);
            })->toArray();
        }

        return [];
    }

    private static function roles(Handbook $handbook): array
    {
        if ($handbook->rolesFilterMode === FilterModes::CHOSEN) {
            $handbook->load('roles');

            return $handbook->roles->map(static function (Role $role) {
                return $role->only(['id', 'slug', 'name']);
            })->toArray();
        }

        return [];
    }

    private static function filename(string $url): string
    {
        $explode = explode('/', $url);
        return end($explode);
    }
}
