<?php

namespace Core\HandBooks\Services;

use Core\HandBooks\Models\Handbook;
use Core\HandBooks\Models\HandbookRequest;
use Core\HandBooks\Repositories\HandbookRepository;
use TradeAppOne\Domain\Enumerators\Operations;

class HandbookService
{
    public const MODULES = [
        Operations::TELECOMMUNICATION,
        Operations::INSURERS,
        Operations::SECURITY,
        Operations::MOBILE_APPS,
        Operations::TRADE_IN,
        Operations::COURSES,
        Operations::LINE_ACTIVATION
    ];

    protected $fileService;

    public function __construct(HandbookFileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function create(HandbookRequest $request): Handbook
    {
        $request->setPath($this->fileService->save($request));

        $handbook = HandbookRepository::create($request->toArray());
        $this->attaches($request, $handbook);

        return $handbook;
    }

    public function update(HandbookRequest $request, Handbook $handbook): Handbook
    {
        $request->setPath($this->fileService->save($request, $handbook));

        $handbook->update($request->toArray());
        $this->attaches($request, $handbook);

        return $handbook;
    }

    private function attaches(HandbookRequest $request, Handbook $handbook): void
    {
        if ($request->networkModeIsChosen()) {
            $handbook->networks()->sync($request->getNetworksIds());
        }

        if ($request->roleModeIsChosen()) {
            $handbook->roles()->sync($request->getRolesIds());
        }
    }
}
