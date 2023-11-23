<?php


use Uol\Exceptions\UolExceptions;

return [
    UolExceptions::UOL_UNAVAILABLE => 'Parceiro Uol esta fora do ar neste momento',
    UolExceptions::UOL_ERROR_GENERATING_PASSPORT => 'Erro ao gerar o código passaporte, por favor, tente novamente',
    UolExceptions::UOL_PRICE_NOT_FOUND => 'Erro ao confirmar o preço informado.',
    UolExceptions::UOL_ERROR_CANCEL => 'Ocorreu um erro ao cancelar o Passaporte. :message'
];
