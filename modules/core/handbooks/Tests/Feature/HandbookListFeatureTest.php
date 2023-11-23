<?php

namespace Core\HandBooks\Tests\Feature;

use Core\HandBooks\Tests\Helpers\HandbookBuilder;
use TradeAppOne\Domain\Enumerators\FilterModes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class HandbookListFeatureTest extends TestCase
{
    use AuthHelper;

    public const ROUTE = '/handbooks/';

    /** @test */
    public function get_should_return_correct_structure()
    {
        $handbook = (new HandbookBuilder())->build();
        $response = $this->authAs($handbook->user)->get(self::ROUTE);

        $response->assertJsonStructure([
           $handbook->module => [
               'label',
               'handbooks' => [
                   $handbook->category => [
                       '*' => [
                           'id', 'title', 'description', 'file', 'module', 'category'
                       ]
                   ]
               ]
           ]
        ]);
    }

    /** @test */
    public function get_should_return_correct_structure_paginated()
    {
        $handbook = (new HandbookBuilder())->build();
        $response = $this->authAs($handbook->user)->get(self::ROUTE.'paginated');

        $response->assertJsonStructure([
            'current_page',
            'total',
            'data' => [
                $handbook->module => [
                    'label',
                    'handbooks' => [
                        $handbook->category => [
                            '*' => [
                                'id', 'title', 'description', 'file', 'module', 'category'
                            ]
                        ]
                    ]
                ]
            ]

        ]);
    }

    /** @test */
    public function should_return_handbooks_when_filter_mode_networks_and_roles_is_chosen()
    {
        $handbook = (new HandbookBuilder())->build();
        $user     = $handbook->user;
        $role     = $handbook->user->role;
        $network  = $user->getNetwork();

        $handbook->module             = Operations::COURSES;
        $handbook->rolesFilterMode    = FilterModes::CHOSEN;
        $handbook->networksFilterMode = FilterModes::CHOSEN;
        $handbook->save();

        $handbook->roles()->attach($role);
        $handbook->networks()->attach($network);

        //Other handbook without roles and networks
        $handbook2                     = (new HandbookBuilder())->withUser($user)->build();
        $handbook2->module             = Operations::COURSES;
        $handbook2->rolesFilterMode    = FilterModes::CHOSEN;
        $handbook2->networksFilterMode = FilterModes::CHOSEN;
        $handbook2->save();

        $response = $this->authAs($handbook->user)->get(self::ROUTE);

        $response->assertJsonCount(1, 'COURSES.handbooks');
    }

    /** @test */
    public function should_return_handbooks_when_filter_mode_networks_is_chosen()
    {
        $handbook = (new HandbookBuilder())->build();
        $user     = $handbook->user;
        $role     = $handbook->user->role;
        $network  = $user->getNetwork();

        $handbook->module             = Operations::COURSES;
        $handbook->networksFilterMode = FilterModes::CHOSEN;
        $handbook->save();

        $handbook->roles()->attach($role);
        $handbook->networks()->attach($network);

        //Other handbook without roles and networks
        $handbook2                     = (new HandbookBuilder())->withUser($user)->build();
        $handbook2->module             = Operations::COURSES;
        $handbook2->networksFilterMode = FilterModes::CHOSEN;
        $handbook2->save();

        //Other handbook without roles and networks
        $handbook3                     = (new HandbookBuilder())->withUser($user)->build();
        $handbook3->module             = Operations::COURSES;
        $handbook3->networksFilterMode = FilterModes::CHOSEN;
        $handbook3->save();

        $handbook3->networks()->attach($network);

        $response = $this->authAs($handbook->user)->get(self::ROUTE);

        $response->assertJsonCount(2, 'COURSES.handbooks');
    }

    /** @test */
    public function should_return_handbooks_when_filter_mode_roles_is_chosen()
    {
        $handbook = (new HandbookBuilder())->build();
        $user     = $handbook->user;
        $role     = $handbook->user->role;
        $network  = $user->getNetwork();

        $handbook->module          = Operations::COURSES;
        $handbook->rolesFilterMode = FilterModes::CHOSEN;
        $handbook->save();

        $handbook->roles()->attach($role);
        $handbook->networks()->attach($network);

        //Other handbook without roles and networks
        $handbook2                  = (new HandbookBuilder())->withUser($user)->build();
        $handbook2->module          = Operations::COURSES;
        $handbook2->rolesFilterMode = FilterModes::CHOSEN;
        $handbook2->save();

        //Other handbook without roles and networks
        $handbook3                  = (new HandbookBuilder())->withUser($user)->build();
        $handbook3->module          = Operations::COURSES;
        $handbook3->rolesFilterMode = FilterModes::CHOSEN;
        $handbook3->save();

        $handbook3->roles()->attach($role);

        $response = $this->authAs($handbook->user)->get(self::ROUTE);

        $response->assertJsonCount(2, 'COURSES.handbooks');
    }

    /** @test */
    public function should_return_handbooks_when_filter_mode_roles_is_all()
    {
        $handbook         = (new HandbookBuilder())->build();
        $handbook->module = Operations::COURSES;
        $handbook->save();

        $user = $handbook->user;

        //Other handbook without roles and networks
        $handbook2         = (new HandbookBuilder())->withUser($user)->build();
        $handbook2->module = Operations::COURSES;
        $handbook2->save();

        $response = $this->authAs($handbook->user)->get(self::ROUTE);

        $response->assertJsonCount(2, 'COURSES.handbooks');
    }
}
