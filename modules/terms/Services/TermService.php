<?php

declare(strict_types=1);

namespace Terms\Services;

use Illuminate\Support\Facades\Auth;
use Terms\Enums\StatusUserTermsEnum;
use Terms\Models\Term;
use Terms\Models\UserTerm;
use Terms\Repositories\TermRepository;
use Terms\Repositories\UserTermRepository;
use Terms\Adapters\AcceptedTermResponseAdapter;
use Terms\Adapters\TermResponseAdapter;

class TermService
{
    /** @var TermRepository */
    private $termRepository;

    /** @var TermRepository */
    private $userTermRepository;

    public function __construct(TermRepository $termRepository, UserTermRepository $userTermRepository)
    {
        $this->termRepository     = $termRepository;
        $this->userTermRepository = $userTermRepository;
    }

    /**
    * @param mixed[] $attributes
    * @return mixed[]
    */
    public function findTermService(array $attributes): array
    {
        $term = $this->termRepository->findLastActiveTermByType(data_get($attributes, 'type'));
        if ($term === null) {
            return (new TermResponseAdapter())->adapt($term, null)->toArray();
        }

        $userTerm = $this->userTermRepository->findByUserAndTerm(
            $term->id ?? 0,
            Auth::user()->id ?? 0
        );

        if ($this->isUserTermChecked($userTerm)) {
            return (new TermResponseAdapter())->adapt(null, null)->toArray();
        }

        if ($this->isUserTermViewed($userTerm)) {
            return (new TermResponseAdapter())->adapt($term, $userTerm)->toArray();
        }

        $createdUserTerm = $this->insertUserTerm($term);
        return (new TermResponseAdapter())->adapt($term, $createdUserTerm)->toArray();
    }

    /**
    * @param mixed[] $attributes
    * @return mixed[]
    */
    public function acceptedUserTerm(array $attributes): array
    {
        $userTerm = $this->userTermRepository->findByUserAndTerm(
            data_get($attributes, 'termId'),
            Auth::user()->id
        );

        if ($userTerm === null) {
            return (new AcceptedTermResponseAdapter())
                ->adapt(null)
                ->toArray();
        }

        $updatedUserTerm = $this->userTermRepository->update($userTerm, ['status' => StatusUserTermsEnum::CHECKED]);

        return (new AcceptedTermResponseAdapter())
            ->adapt($updatedUserTerm)
            ->toArray();
    }

    private function isUserTermChecked(?UserTerm $userTerm): bool
    {
        return $userTerm !== null && $userTerm->status === StatusUserTermsEnum::CHECKED;
    }

    private function isUserTermViewed(?UserTerm $userTerm): bool
    {
        return $userTerm !== null && $userTerm->status === StatusUserTermsEnum::VIEWED;
    }

    public function insertUserTerm(Term $term): UserTerm
    {
        return $this->userTermRepository->create([
            'userId' => Auth::user()->id,
            'termId' => data_get($term, 'id'),
            'status' => StatusUserTermsEnum::VIEWED
        ]);
    }
}
