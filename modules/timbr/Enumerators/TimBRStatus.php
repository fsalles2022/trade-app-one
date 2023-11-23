<?php

namespace TimBR\Enumerators;

class TimBRStatus
{
    public const ACCEPTED  = [
        'Em andamento',
        'Pendente portabilidade',
        'Pendente Portabilidade',
        'Aguardando Aceite',
        'Criação da ordem em andamento',
        'Protocolo não localizado'
    ];
    public const CANCELED  = [
        'Cancelado',
        'Erro na criacao da ordem Ja existe uma ordem para este acesso',
        'Erro na criacao da ordem Acesso ja existe na base',
        'Erro na criacao da ordem Nao existe transacao autorizada.'
    ];
    public const APPROVED  = ['Concluído'];
    public const REJECTED  = ['Erro no processamento'];
    public const NOT_FOUND = ['Protocolo não localizado'];
}
