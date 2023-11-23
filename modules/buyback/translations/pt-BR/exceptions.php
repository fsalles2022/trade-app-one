<?php

use Buyback\Exceptions\QuizExceptions;
use Buyback\Exceptions\TradeInExceptions;

return [
    'device_not_found'     => [
        'message' => 'Não foi encontrado nenhum aparelho'
    ],
    'device_not_sould_by_network' => [
        'message' => 'Este aparelho não é comercializado por esta rede'
    ],
    'questions_not_found'  => [
        'message' => 'Não foram encontradas perguntas associadas a este aparelho'
    ],
    'number_of_questions_other_than_answers' => [
        'message' => 'O número de respostas enviadas é diferente do número de perguntas feitas'
    ],
    'device_not_accepted' => [
        'message' => 'Não aceitamos o aparelho nas condições especificadas'
    ],
    'generator_voucher_exception' => [
        'message' => 'Não foi possível gerar o voucher, entre em contato com o suporte'
    ],
    'evaluation_not_found_exception' => [
        'message' => 'Não foram encontrado avaliacões disponíveis para este aparelho nessa rede'
    ],
    'revaluation_already_done_exception'     => [
        'message' => 'Avaliação não pode ser realizada'
    ],
    'waybill_empty_exception'     => [
        'message' => 'Não existem dispositivos para serem recolhidos'
    ],
    'quiz_not_found_exception' => [
        'message' => 'Não foi encontrado nenhum quiz com este identificador'
    ],
    TradeInExceptions::VOUCHER_ALREADY_CANCELED => 'O voucher já foi cancelado.',
    'can_not_change_voucher_current_status' => [
        'message' => 'Para alterar o voucher é necessário que o serviço esteja em análise, status atual é :status'
    ],
    TradeInExceptions::VOUCHER_NOT_BELONGS_TO_OPERATION => 'O processo de voucher não pertence para operação: :operations.',
    TradeInExceptions::DEVICE_NOT_BELONG_TO_NETWORK => 'O dispositivo não pertence a Rede específicada.',
    QuizExceptions::NETWORK_ALREADY_QUIZ => 'A rede já possui um questionário cadastrado.',
    QuizExceptions::NOT_FOUND => 'Questionário não encontrado.'
];
