<?php

namespace TradeAppOne\Domain\Services\Cancel;

use Carbon\Carbon;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;

trait CancelService
{
    public function requirementInDays($service, $days)
    {
        $dateOfSale = Carbon::parse($service->sale->createdAt);

        if ($dateOfSale->diffInDays(Carbon::now()) > $days) {
            throw ServiceExceptions::cancellationExpired();
        }

        return $this;
    }

    public function serviceIsAccepted(Service $service)
    {
        if ($service->isAccepted()) {
            return $this;
        }

        throw ServiceExceptions::needsAcceptedToCancel($service->status);
    }

    public function serviceIsApproved(Service $service)
    {
        if ($service->isNotApproved()) {
            throw ServiceExceptions::activeToCancel();
        }

        return $this;
    }

    public function serviceNotCanceled(Service $service)
    {
        if ($service->isCanceled()) {
            throw ServiceExceptions::alreadyCancelled($service->sector);
        }

        return $this;
    }

    public function serviceNotNull(Service $service)
    {
        if (is_null($service)) {
            throw ServiceExceptions::notFound();
        }

        return $this;
    }
}
