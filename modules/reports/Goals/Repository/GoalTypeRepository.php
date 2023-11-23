<?php

namespace Reports\Goals\Repository;

use Reports\Goals\Models\GoalType;

class GoalTypeRepository
{
    public function findBySlug(string $slug): ?GoalType
    {
        return GoalType::query()
            ->where('slug', $slug)
            ->get()
            ->first();
    }
}
