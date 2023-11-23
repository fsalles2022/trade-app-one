<?php

namespace Core\HandBooks\Models;

use Core\HandBooks\Http\Requests\HandbookFormRequest;
use Core\HandBooks\Policies\HandbookPolicies;
use Illuminate\Http\UploadedFile;
use TradeAppOne\Domain\Enumerators\Files\FileTypes;
use TradeAppOne\Domain\Enumerators\FilterModes;
use TradeAppOne\Domain\Models\Tables\User;

class HandbookRequest extends HandbookPolicies
{
    public $networks;
    public $module;
    public $roles;
    public $networksFilterMode;
    public $rolesFilterMode;
    public $user;

    public $title;
    public $description;
    public $category;
    public $path;
    public $file;

    public function __construct(HandbookFormRequest $request)
    {
        $data = $request->validated();

        $this->user               = $request->user();
        $this->title              = data_get($data, 'title');
        $this->description        = data_get($data, 'description');
        $this->module             = data_get($data, 'module');
        $this->category           = data_get($data, 'category');
        $this->networks           = data_get($data, 'networks');
        $this->file               = data_get($data, 'file');
        $this->roles              = data_get($data, 'roles');
        $this->networksFilterMode = data_get($data, 'networksFilterMode');
        $this->rolesFilterMode    = data_get($data, 'rolesFilterMode');
    }

    public function getType(): string
    {
        $file = $this->getFile();

        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalExtension();
        }

        return '';
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function getNetworksIds(): array
    {
        return $this->networks->pluck('id')->toArray();
    }

    public function getRolesIds(): array
    {
        return $this->roles->pluck('id')->toArray();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
        return $this;
    }

    public function networkModeIsChosen(): bool
    {
        return $this->networksFilterMode === FilterModes::CHOSEN;
    }

    public function roleModeIsChosen(): bool
    {
        return $this->rolesFilterMode === FilterModes::CHOSEN;
    }

    public function toArray(): array
    {
        return array_filter([
            'userId' => $this->getUser()->id,
            'title'  => $this->title,
            'description' => $this->description,
            'type' => data_get(FileTypes::TYPES, $this->getType()),
            'file' => $this->path,
            'module' => $this->module,
            'category' => $this->category,
            'networksFilterMode' => $this->networksFilterMode,
            'rolesFilterMode' => $this->rolesFilterMode
        ]);
    }
}
