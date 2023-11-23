<?php


use Voucher\Http\Requests\CancelVoucherFormRequest;
use Voucher\Http\Requests\UseVoucherFormRequest;

return [
    UseVoucherFormRequest::IMEI_REQUIRED => 'IMEI deve ser informado.',
    UseVoucherFormRequest::METADATA_REQUIRED => 'Metadados quando enviados devem conter as informações desejadas.',
    CancelVoucherFormRequest::METADATA_REQUIRED => 'Metadados quando enviados devem conter as informações desejadas.'
];
