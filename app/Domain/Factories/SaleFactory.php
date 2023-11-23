<?php

namespace TradeAppOne\Domain\Factories;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\SaleChannels;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Exceptions\BusinessExceptions\InvalidServiceStatus;
use TradeAppOne\Exceptions\BusinessExceptions\ModelInvalidException;
use TradeAppOne\Http\Resources\PointOfSaleResource;
use TradeAppOne\Http\Resources\UserResource;

class SaleFactory
{
    public static function make(string $source, User $user, PointOfSale $pointOfSale, array $requestedServices): Sale
    {
        $sale       = new Sale();
        $sale->user = self::getAssociateUser($user);
        if ($sale->user) {
            $userAuthAlternate   = $user->userAuthAlternate()->first();
            $sale->userAlternate = $userAuthAlternate ? $userAuthAlternate->toArray(): null;
        }

        $sale->pointOfSale = (new PointOfSaleResource())->map($pointOfSale);
        $sale->channel     = SaleChannels::VAREJO;
        throw_if((! in_array(strtoupper($source), SubSystemEnum::SUPPORTED_CLIENTS)), new InvalidServiceStatus());
        $sale->source = strtoupper($source);
        $sale->setTransactionNumber();

        foreach ($requestedServices as $index => $requestedService) {
            $service                     = ServicesFactory::make($requestedService);
            $service->serviceTransaction = $sale->saleTransaction . '-' . $index;
            if (! $service->validate()) {
                throw new ModelInvalidException($service->getErrors()->first());
            }
            $sale->services()->associate($service);
        }
        $sale->total = $sale->services()->sum('price');
        return $sale;
    }

    private static function getAssociateUser(User $user): array
    {
        $userAuth = Auth::user();

        return $userAuth->id !== $user->id
            ? UserResource::make($user)->resolve($userAuth->toArray())
            : UserResource::make($user)->resolve();
    }
}
