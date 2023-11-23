<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;

class HierarchyBuilder
{
    private $hierarchyParent;
    private $user;
    private $pointOfSale;
    private $network;
    private $slug;

    public static function make(): HierarchyBuilder
    {
        return new self();
    }

    public function withNetwork(Network $network): HierarchyBuilder
    {
        $this->network = $network;

        return $this;
    }

    public function withUser(User $user): HierarchyBuilder
    {
        $this->user = $user;

        return $this;
    }

    public function withPointOfSale(PointOfSale $pointOfSale): HierarchyBuilder
    {
        $this->pointOfSale = $pointOfSale;

        return $this;
    }

    public function withParent(Hierarchy $hierarchyParent): HierarchyBuilder
    {
        $this->hierarchyParent = $hierarchyParent;

        return $this;
    }

    public function withSlug(string $slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function build(): Hierarchy
    {
        $hierarchyData = $this->getHierarchyData();

        $hierarchy = new Hierarchy($hierarchyData);
        $this->network && $hierarchy->network()->associate($this->network);
        $hierarchy->save();

        $this->associateHierarchyRelationsIfExists($hierarchy);

        return $hierarchy;
    }

    private function getHierarchyData()
    {
        $parent   = $this->hierarchyParent ? $this->hierarchyParent->id : null;
        $slug     = $this->slug ? $this->slug : microtime();
        $sequence = $this->sequenceGenerator();

        return [
            'label'    => $slug ? $slug : 'test',
            'sequence' => $sequence,
            'parent'   => $parent,
            'slug'     => $slug
        ];
    }

    private function sequenceGenerator(): string
    {
        $sequence = null;
        if (is_null($this->hierarchyParent)) {
            $sequence = Hierarchy::all()->count() + 1;
        } else {
            $sequenceParent = $this->hierarchyParent->sequence;
            $parentId       = $this->hierarchyParent->id;
            $sequenceCount  = Hierarchy::where('parent', $parentId)->count() + 1;
            $sequence       = $sequenceParent . '.' . $sequenceCount;
        }

        return $sequence;
    }

    private function associateHierarchyRelationsIfExists(Hierarchy $hierarchy): void
    {
        $this->pointOfSale && $this->pointOfSale->hierarchy()->associate($hierarchy)->save();
        $this->user && $this->user->hierarchies()->attach($hierarchy);
    }
}
