<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\File;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

abstract class FileAutoGenerated extends Attachable
{
    /**
     * The attachable's options
     *
     * @var string[]
     */
    protected $attachableOptions = [];

    /**
     * The generator of the name of file that must be
     *
     * @var FileNameGenerator
     */
    protected $fileNameGenerator;

    public function __construct(IFileConvertable $convertable, bool $checkPath = true)
    {
        parent::__construct(
            $this->build($convertable),
            $checkPath
        );
    }

    public function build(IFileConvertable $convertable): string
    {
        Storage::disk('local')->put(
            $path = $this->fileNameGenerator->genFileName(),
            $convertable->toContents()
        );

        return Storage::path($path);
    }

    /**
     * @return string[]
     */
    public function options(): array
    {
        return $this->attachableOptions;
    }
}
