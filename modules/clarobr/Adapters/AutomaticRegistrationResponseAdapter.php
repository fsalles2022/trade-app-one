<?php

namespace ClaroBR\Adapters;

use Carbon\Carbon;
use TradeAppOne\Domain\Models\Tables\User;

class AutomaticRegistrationResponseAdapter
{
    /**
     * @param mixed[] $requestInput
     * @return mixed[]
     */
    public static function adaptReceivedSuccess(array $requestInput): array
    {
        return [
            'protocol' => $requestInput['usuario']['autenticacaoAlternativa'] ?? data_get($requestInput, 'usuario.cpf'),
            'message' => trans('siv::messages.automaticRegistration.received'),
        ];
    }

    /** @return mixed[] */
    public static function adaptNotFound(string $protocol): array
    {
        return [
            'protocol' => $protocol,
            'message' => trans('siv::messages.automaticRegistration.notFound'),
        ];
    }

    /**
     * @param mixed[] $sivUser
     * @return mixed[]
     */
    public static function adaptCreationSuccessful(User $user, array $sivUser, string $protocol): array
    {
        $role    = $user->role;
        $network = $user->getNetwork();

        return [
            'protocol' => $protocol,
            'user'     => [
                'cpf'           => data_get($user, 'cpf'),
                'nome'          => data_get($user, 'firstName') . ' ' . data_get($user, 'lastName'),
                'funcao'        => data_get($role, 'slug'),
                'rede'          => data_get($network, 'label'),
                'canal'         => data_get($sivUser, 'canal'),
                'funcao_claro'  => data_get($sivUser, 'funcao'),
                'pdv_codigo'    => data_get($sivUser, 'pdv_codigo'),
                'data_cadastro' => Carbon::parse(data_get($user, 'createdAt', now()))->format('d/m/Y à\s H:i:s'),
            ],
            'message' => trans('siv::messages.automaticRegistration.success'),
        ];
    }
}
