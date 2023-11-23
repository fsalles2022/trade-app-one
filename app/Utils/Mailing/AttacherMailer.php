<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\Mailing;

use Illuminate\Support\Facades\File;
use TradeAppOne\Utils\File\IAttachable;

abstract class AttacherMailer extends BaseMailer
{
    public function attachObject(IAttachable $attachable): AttacherMailer
    {
        return $this->attach(
            $attachable->path(),
            $attachable->options()
        );
    }

    public function clearAttachs(bool $delete = true): AttacherMailer
    {
        if ($delete) {
            foreach ($this->attachments as $attachment) {
                File::delete($attachment["file"]);
            }
        }

        $this->attachments = [];
        return $this;
    }
}
