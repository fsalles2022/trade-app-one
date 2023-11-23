<?php

namespace TradeAppOne\Domain\Importables;

use Carbon\Carbon;
use TradeAppOne\Domain\Models\Tables\User;

class ImportablePath
{
    const IMPORTABLE_DIR = 'imports';

    public static function generate(User $user, string $type, string $status, $id)
    {
        $typeSlug    = str_slug($type);
        $userCpf     = $user->cpf;
        $networkSlug = strtolower($user->getNetwork()->slug);

        $date     = Carbon::now();
        $date->tz = config('app.timezone');
        $year     = $date->format('Y');
        $month    = $date->format('m');
        $day      = $date->format('d');

        $fileName = "$userCpf-$id-$status.csv";

        return self::IMPORTABLE_DIR . "/$typeSlug/$networkSlug/$year/$month/$day/$fileName";
    }
}
