<?php

namespace OiBR\Enumerators;

class OiBRStatus
{
    const ACCEPTED = [
        'PreCadastro',
        'PendenteRecargaAvulsa',
    ];
    const CANCELED = ['PreCadastroExpirado', 'FalhaHabilitacaoRecargaAvulsa',];
    const APPROVED = ['Concluido'];
    const REJECTED = ['FalhaCriacaoAssinatura'];
}
