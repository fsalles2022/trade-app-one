<?php

namespace TradeAppOne\Http\Controllers;

use ClaroBR\Http\Requests\SivFormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Csv\Writer;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\UserReaderService;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Http\Requests\PasswordFormRequest;
use TradeAppOne\Http\Requests\UserFormRequest;
use TradeAppOne\Http\Requests\UserListFormRequest;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Domain\Importables\AutomaticRegistrationImportable;
use TradeAppOne\Domain\Importables\PasswordMassUpdateImportable;
use TradeAppOne\Http\Requests\UserPasswordMassUpdateRequest;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;
    protected $readerService;

    public function __construct(UserService $userService, UserReaderService $readerService)
    {
        $this->userService   = $userService;
        $this->readerService = $readerService;
    }

    public function getStatus()
    {
        return [
            [
                "slug" => UserStatus::ACTIVE,
                "label" => trans("constants.user.status.".UserStatus::ACTIVE),
                "description" => trans("constants.user.status.description.".UserStatus::ACTIVE)
            ],
            [
                "slug" => UserStatus::INACTIVE,
                "label" => trans("constants.user.status.".UserStatus::INACTIVE),
                "description" => trans("constants.user.status.description.".UserStatus::INACTIVE)
            ],
            [
                "slug" => UserStatus::NON_VERIFIED,
                "label" => trans("constants.user.status.".UserStatus::NON_VERIFIED),
                "description" => trans("constants.user.status.description.".UserStatus::NON_VERIFIED)
            ],
            [
                "slug" => UserStatus::VERIFIED,
                "label" => trans("constants.user.status.".UserStatus::VERIFIED),
                "description" => trans("constants.user.status.description.".UserStatus::VERIFIED)
            ]
        ];
    }

    public function index(UserListFormRequest $request)
    {
        $validatedData = $request->validated();
        $list          = $this->userService->filter($validatedData);

        return response()->json($list, Response::HTTP_OK);
    }

    public function create(UserFormRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        if ($user->can('createUser', [User::class, $data])) {
            if ($this->userService->createUser($data)) {
                $response['message'] = trans('messages.user_created');
                return response()->json($response, Response::HTTP_CREATED);
            }

            $response['message'] = trans('messages.user_creating_error');
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        throw UserExceptions::userUnauthorized();
    }

    public function edit(UserFormRequest $request, $cpf)
    {
        $user = $request->user();
        $data = $request->validated();

        if ($user->can('editUser', [User::class, $data, $cpf])) {
            if ($this->userService->prepareAndUpdateUser($data, $cpf)) {
                $response['message'] = trans('messages.user_updated');
                return response()->json($response, Response::HTTP_OK);
            };

            $response['message'] = trans('messages.user_update_error');
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        throw UserExceptions::userUnauthorized();
    }

    public function show($cpf)
    {
        $user = $this->userService->showUser($cpf);
        return response()->json($user, Response::HTTP_OK);
    }

    public function confirmVerificationCode($verificationCode)
    {
        $user = $this->userService->userByVerificationCode($verificationCode);
        if ($user) {
            $this->response['message'] = trans('messages.valid_verification_code');
            return response()->json($this->response, Response::HTTP_OK);
        }

        $this->response['message'] = trans('messages.invalid_verification_code');
        return response()->json($this->response, Response::HTTP_UNAUTHORIZED);
    }

    public function confirmAccount(PasswordFormRequest $request, $verificationCode)
    {
        $user = $this->userService->verifyAccount($request->password, $verificationCode);
        if ($user) {
            $this->response['message'] = trans('messages.user_confirmed');
            return response()->json($this->response, Response::HTTP_OK);
        }

        $this->response['message'] = trans('messages.invalid_verification_code');
        return response()->json($this->response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function activateUser(Request $request, $verificationCode)
    {
        if ($this->userService->activateUser($verificationCode, $request->password)) {
            $this->response['message'] = trans('messages.user.activated');
            return response()->json($this->response);
        }
        $this->response['message'] = trans('messages.user.has_no_verification_code');
        return response()->json($this->response, Response::HTTP_BAD_REQUEST);
    }

    public function listByPointOfSale(SivFormRequest $request): array
    {
        return $this->readerService->pointOfSaleWithUser($request->user())->get()->toArray();
    }

    public function sendAutomaticRegistrationImportable(Request $request)
    {
        $importable = ImportableFactory::make(Importables::AUTOMATIC_REGISTRATION);
        $engine     = new ImportEngine($importable);
        $errors     = $engine->process($request->file('file'));
        if ($errors) {
            return $errors;
        }

        $this->response['message'] = trans('messages.default_success');

        return response()->json($this->response, Response::HTTP_CREATED);
    }

    public function getAutomaticRegistrationImportableExample(): Writer
    {
        return AutomaticRegistrationImportable::buildExample();
    }

    /** @return Writer|JsonResponse */
    public function processPasswordMassUpdateImportableAction(UserPasswordMassUpdateRequest $request)
    {
        $importable = ImportableFactory::make(Importables::PASSWORD_MASS_UPDATE);
        $engine     = new ImportEngine($importable);
        $errors     = $engine->process($request->file('file'));

        if ($errors) {
            return $errors;
        }

        $this->response['message'] = trans('messages.default_success');

        return response()->json($this->response, Response::HTTP_CREATED);
    }

    public function getPasswordMassUpdateImportableExampleAction(Request $request): Writer
    {
        return PasswordMassUpdateImportable::buildExample();
    }
}
