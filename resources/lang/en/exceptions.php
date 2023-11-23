<?php

use Reports\Exceptions\ReportExceptions;

return [
    'imei_already_exists'                  => 'Imei already exists',
    ReportExceptions::FAILED_REPORT_BUILD  => 'Unable to finish extraction of analytical report',
    'failed_report_build'                  => 'Unable to finish extraction of analytical report',
    'third_party_unavailable'              => 'Partner :service is unavailable now',
    'siv_invalid_credentials'              => 'Invalid credentials',
    'no_access_to_siv'                     => 'Unfortunately you do not have access to the services of Claro',
    'point_of_sale_without_siv'            => 'The selected point of sale does not have Siv credentials',
    'sale_no_exists'                       => 'This sale transaction does not exist',
    'service_no_exists'                    => 'This service has invalid format',
    'invalid_service_status'               => ':status is not a valid status for service',
    'service_non_integrated'               => 'The selected service has not yet been integrated',
    'protocol_no_exists'                   => 'There is no protocol number for this sale',
    'tim_status_no_exists'                 => 'Status on TIM endpoint returns empty',
    'operator_not_found'                   => 'Operator not found in our portfolio',
    'user_doesnt_belongs_to_point_of_sale' => 'You are not registered at this point of sale',
    'user'                                 => [
        'not_found' => 'User not found',
        'already_has_active_reset_request' => 'Wait for your manager to approve your request'
    ],
    'pos'                                  => [
        'network_no_exists' => 'Network not found to attach to this Point of Sale.',
        'not_found'         => 'Point of Sale no exists'
    ],
    'network'                              => [
        'not_found' => 'Network not found'
    ],
    'role'                                 => [
        'not_found' => 'Role not found'
    ],
    'general'                              => [
        'model_invalid'   => 'Invalid attributes for this model',
        'service_invalid' => 'This service does not exist!',
    ],
    'third_party'                          => [
        'default' => 'Could not complete process',
    ],
    'assistance'                           => [
        'not_found' => 'Could not complete process'
    ],
    'importable'                           => [
        'not_found' => 'There are no methods to import this data'
    ],
    'sign_in'                              => [
        'exceeded_sign_in_attempts' => 'Login attempts exceeded'
    ],
    'export' => [
        'user_not_found_on_this_network_for_this_operator' => 'Nenhum usuário encontrado nessa rede para esta operadora'
    ]
];
