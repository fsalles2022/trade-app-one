<?php

namespace Buyback\Http\Controllers;

use Buyback\Enumerators\QuizPermissions;
use Buyback\Http\Requests\QuizFormRequest;
use Buyback\Models\Quiz;
use Buyback\Repositories\QuizRepository;
use Buyback\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Http\Controllers\Controller;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function index(Request $request)
    {
        $permissionView = QuizPermissions::getFullName(QuizPermissions::VIEW);
        hasPermissionOrAbort($permissionView);

        $user    = $request->user();
        $filters = $request->all();

        $quizzes =  $this->quizService->listPaginated($user, $filters);
        return response()->json($quizzes, Response::HTTP_OK);
    }

    public function store(QuizFormRequest $request, NetworkService $networkService)
    {
        $user      = $request->user();
        $netSlug   = $request->get('network');
        $questions = $request->get('questions');
        $network   = $networkService->findOneBySlug($netSlug);

        if ($user->can('create', [Quiz::class, $network])) {
            $this->quizService->create($network->id, $questions);
            $message = trans('buyback::messages.quiz.created_success');

            return response()->json(['message' => $message], Response::HTTP_CREATED);
        }
    }

    public function update(int $id, QuizFormRequest $request)
    {
        $user      = $request->user();
        $questions = $request->get('questions');

        if ($user->can('update', [Quiz::class, $id])) {
            $this->quizService->update($id, $questions);
            $message = trans('buyback::messages.quiz.updated_success');

            return response()->json(['message' => $message], Response::HTTP_OK);
        }
    }

    public function show(int $id)
    {
        auth()->user()->can('show', [Quiz::class, $id]);
        return QuizRepository::find($id);
    }
}
