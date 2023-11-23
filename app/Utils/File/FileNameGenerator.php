<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\File;

class FileNameGenerator
{
    /**
     * The string that will come before unique identifier
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * The string that will come after unique identifier
     *
     * @var string
     */
    protected $postfix = '';

    /**
     * The extension of the file
     *
     * @var string
     */
    protected $extension = '';

    /**
     * The unique identifier
     *
     * @var string
     */
    protected $identifier = '';

    public function __construct(string $identifier = null)
    {
        $this->identifier = $identifier ?? (string) now()->getTimestamp();
    }

    public function genFileName(): string
    {
        return $this->prefix .
            $this->uniqueIdentifier() .
            $this->postfix . '.' . $this->extension;
    }

    public function uniqueIdentifier(): string
    {
        return $this->identifier;
    }

    public function setPrefix(string $prefix): FileNameGenerator
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setPostfix(string $postfix): FileNameGenerator
    {
        $this->postfix = $postfix;
        return $this;
    }

    public function setExtension(string $extension): FileNameGenerator
    {
        $this->extension = $extension;
        return $this;
    }
}
