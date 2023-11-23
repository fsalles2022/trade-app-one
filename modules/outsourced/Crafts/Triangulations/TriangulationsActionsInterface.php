<?php

namespace Outsourced\Crafts\Triangulations;

interface TriangulationsActionsInterface
{
    public function processCustomDataFromTriangulation(array $attributes): void;
}
