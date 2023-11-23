<?php

declare(strict_types=1);

namespace TimBR\Http\Requests;

use TradeAppOne\Domain\Enumerators\Permissions\ImportablePermission;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class RebateImportRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission(ImportablePermission::NAME . '.' . ImportablePermission::TTM_REBATE);
    }

    public function rules(): Array
    {
        return [];
    }
}
