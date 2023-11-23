<?php

return [
    'third_party_unavailable'              => 'We are already working to solve, the help desk is ready',
    'siv_invalid_credentials'              => 'Your Siv credentials are invalid',
    'no_access_to_siv'                     => 'Request a registration in the Siv system to access',
    'sale_no_exists'                       => 'Verify that the transaction number is correct, it is a number consisting of 16 digits example: 2018031515524786',
    'service_no_exists'                    => 'Verify that the number is correct, it is a number consisting of 16 digits, dash followed by 3 digits example: 2018031515524786-000',
    'service_non_integrated'               => 'Do activation first method put : sale/,  before activating',
    'protocol_no_exists'                   => 'Check that the protocol number exists for the sale you submitted',
    'tim_status_no_exists'                 => 'Check the return from the TIM endpoint',
    'operator_not_found'                   => 'Check the name of the Operator to enable the correct service',
    'user_doesnt_belongs_to_point_of_sale' => 'Check the point of sale identifier you submitted',
    'user'                                 => [
        'not_found' => 'It\'s necessary send a existent User.',
        'already_has_active_reset_request' => 'Your manager needs to approve your request'
    ],
    'pos'                                  => [
        'network_no_exists' => 'It\'s necessary send a existent network to attach with a Point of Sale!',
        'not_found'         => 'It\'s necessary send a existent Point of Sale.'
    ],
    'role'                                 => [
        'not_found' => 'It\'s necessary send a existent Role.'
    ],
    'network'                              => [
        'not_found' => 'It\'s necessary send a existent Network.'
    ],
    'sun'                                  => [
        'error' => 'An internal communication error has occurred. Please try again!'
    ],
    'sign_in'                              => [
        'exceeded_sign_in_attempts' => 'Request password reset'
    ]
];
