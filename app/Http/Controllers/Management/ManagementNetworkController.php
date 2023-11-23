<?php

namespace TradeAppOne\Http\Controllers\Management;

use Illuminate\Http\Response;
use TradeAppOne\Http\Controllers\Controller;
use TradeAppOne\Http\Requests\Management\PreferencesFormRequest;
use TradeAppOne\Policies\ManagementNetworkPolicy;

class ManagementNetworkController extends Controller
{
    public function update(PreferencesFormRequest $request, $slug)
    {
        (new ManagementNetworkPolicy($request, $slug))
            ->validatePreferences()
            ->updatePreferences();

        return response()->json([], Response::HTTP_OK);
    }
}
