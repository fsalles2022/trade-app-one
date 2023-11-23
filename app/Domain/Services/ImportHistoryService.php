<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Components\Helpers\FilePathFromUrl;
use TradeAppOne\Domain\Enumerators\ImportHistoryStatus;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\ImportHistoryRepository;
use TradeAppOne\Exceptions\SystemExceptions\ImportHistoryExceptions;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;

class ImportHistoryService
{
    protected $importHistoryRepository;
    protected $roleService;

    public function __construct(ImportHistoryRepository $importHistoryRepository, RoleService $roleService)
    {
        $this->importHistoryRepository = $importHistoryRepository;
        $this->roleService             = $roleService;
    }

    public function getHistory(User $user, array $filters)
    {
        $roles = $this->roleService->rolesThatUserHasAuthority($user);
        $roles->push($user->role);

        return $this->importHistoryRepository->filterAndPaginate($roles, $filters);
    }

    public function getFile(Request $request, User $user)
    {
        $filePath = $this->getFileName($user, $request->id);

        $fileExists = Storage::disk('s3')->exists($filePath);

        if ($fileExists) {
            return Storage::disk('s3')->download($filePath);
        }

        throw ImportHistoryExceptions::downloadFileNotFound();
    }

    private function getFileName(User $user, $id)
    {
        $roles = $this->roleService->rolesThatUserHasAuthority($user);
        $roles->push($user->role);

        $import = $this->importHistoryRepository->find($id);

        if ($roles->contains('id', $import->user->role->id)) {
            $file = '';

            if ($import->status == ImportHistoryStatus::ERROR) {
                $file = $import->outputFile;
            }

            if ($import->status == ImportHistoryStatus::SUCCESS or $import->status == ImportHistoryStatus::PENDING) {
                $file = $import->inputFile;
            }

            return FilePathFromUrl::extractS3Path($file);
        }

        throw UserExceptions::userAuthHasNotAuthorizationUnderUser();
    }
}
