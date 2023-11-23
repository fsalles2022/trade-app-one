<?php

namespace Buyback\Policies;

use Buyback\Enumerators\QuizPermissions;
use Buyback\Exceptions\QuizExceptions;
use Buyback\Repositories\QuizRepository;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Facades\UserPolicies;

class QuizPolicy
{
    private $quizRepository;

    public function __construct(QuizRepository $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    public function create(User $user, Network $network): bool
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::CREATE);

        UserPolicies::setUser($user)
            ->hasPermission($permission)
            ->hasAuthorizationUnderNetwork($network->slug);

        return $this->networkAlreadyHasQuiz($network);
    }

    public function update(User $user, int $id): bool
    {
        $permission = QuizPermissions::getFullName(QuizPermissions::EDIT);
        UserPolicies::setUser($user)->hasPermission($permission);

        $this->hasAuthorizationUnderQuiz($user, $id);

        return true;
    }

    public function show(User $user, int $id)
    {
        return $this->hasAuthorizationUnderQuiz($user, $id);
    }

    private function networkAlreadyHasQuiz(Network $network): bool
    {
        $quizzes = QuizRepository::getQuizzesByNetwork($network->id);

        if ($quizzes->isNotEmpty()) {
            throw QuizExceptions::networkAlreadyHasQuiz();
        }

        return true;
    }

    private function hasAuthorizationUnderQuiz(User $user, int $id): bool
    {
        $quiz = QuizRepository::find($id);

        $networkQuiz       = $quiz->questions->first()->networkId;
        $networkAuthorized = UserPolicies::setUser($user)->getNetworksAuthorized();

        if ($networkAuthorized->contains('id', $networkQuiz)) {
            return true;
        }

        throw UserExceptions::userHasNotAuthorizationUnderQuiz();
    }
}
