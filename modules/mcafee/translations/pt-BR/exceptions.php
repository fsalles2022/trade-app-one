<?php

use McAfee\Exceptions\McAfeeExceptions;

return [
    McAfeeExceptions::MCAFEE_ERROR_ACTIVATING_THE_SALE => 'Não foi possível contratar o serviço, entre em contato com o suporte, código de erro: :code',
    McAfeeExceptions::MCAFEE_SERVICE_IS_NOT_APPROVED_TO_CANCEL => 'Você não pode cancelar um serviço que não está aprovado',
    McAfeeExceptions::MCAFEE_DATE_IS_GREATER_THAN_SEVEN_DAYS_TO_CANCEL => 'O prazo de 7 dias para cancelamento desse item foi ultrapassado',
    McAfeeExceptions::MCAFEE_ERROR_CANCELING_SUBSCRIPTION => 'Ocorreu um erro ao cancelar o serviço, tente novamente',
    McAfeeExceptions::MCAFEE_ERROR_DISCONNECTING_DEVICES => 'Ocorreu um erro ao desconectar os aparelhos vinculado a conta do cliente, tente novamente'
];
