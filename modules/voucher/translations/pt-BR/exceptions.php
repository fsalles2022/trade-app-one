<?php

use Voucher\Exceptions\VoucherExceptions;

return [
    VoucherExceptions::NOT_FOUND => 'Serviço com transação especificada não encontrado.',
    VoucherExceptions::VOUCHER_BURNED => 'Este voucher não pode ser utilizado novamente.',
    VoucherExceptions::VOUCHER_INCORRECT_STATUS => 'Status da transação não permitido para queimar voucher.',
    VoucherExceptions::VOUCHER_NOT_BELONGS_TO_NETWORK => 'Você não pode usar um voucher de outra rede.',
    VoucherExceptions::VOUCHER_EXPIRED => 'Voucher expirado para uso, seu uso é restrito ao dia de sua geração.',
    VoucherExceptions::VOUCHER_NOT_BURNED_WHEN_TRYING_CANCEL => 'Vouchers não utilizados não podem ser cancelados.',
    VoucherExceptions::VOUCHER_INCORRECT_VALUES_FROM_METADATA => 'Metadados enviados não conferem.',
    VoucherExceptions::VOUCHER_TELECOMMUNICATION_DIFERENT_IMEI => 'O IMEI informado na venda difere do informado para uso do voucher.',
    VoucherExceptions::ONLY_OPERATOR_SALE_IS_ALLOWED => 'Apenas operações de operadoras são permitidas para esta funcionalidade.',
    VoucherExceptions::NO_TRIANGULATION_FOR_IMEI => 'Não existe nenhuma triangulação para o IMEI informado.',
    VoucherExceptions::NO_OTHER_TRIANGULATION_IN_OPERATOR => 'Todas as promoções do IMEI informado divergem do plano da venda.'
];
