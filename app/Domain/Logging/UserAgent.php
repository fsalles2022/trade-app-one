<?php

namespace TradeAppOne\Domain\Logging;

use WhichBrowser\Parser;

class UserAgent
{
    public $raw;
    public $browserName;
    public $browserVersion;
    public $engine;
    public $osName;
    public $osVersion;
    public $device;
    public $deviceType;
    public $subType;
    public $osAlias;
    public $osVersionName;

    public function __construct(string $userAgent)
    {
        $this->raw = $userAgent;
        $result    = new Parser($userAgent);

        $this->browserName    = $result->browser->name ?? null;
        $this->browserVersion = $result->browser->version->value ?? null;
        $this->engine         = $result->engine->name ?? null;

        $this->osName        = $result->os->name ?? null;
        $this->osAlias       = $result->os->alias ?? null;
        $this->osVersion     = $result->os->version->value ?? null;
        $this->osVersionName = $result->os->version->nickname ?? null;

        $this->device     = $result->device->model ?? null;
        $this->deviceType = $result->device->type ?? null;
        $this->subType    = $result->device->subtype ?? null;
    }
}
