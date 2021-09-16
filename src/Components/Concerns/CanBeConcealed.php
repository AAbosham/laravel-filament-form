<?php

namespace AAbosham\Filament\Forms\Components\Concerns;

use AAbosham\Filament\Forms\Components\Component;
use AAbosham\Filament\Forms\Components\Contracts\CanConcealComponents;

trait CanBeConcealed
{
    public function getConcealingComponent(): ?Component
    {
        $parentComponent = $this->getContainer()->getParentComponent();

        if (! $parentComponent) {
            return null;
        }

        if (! $parentComponent instanceof CanConcealComponents) {
            return $parentComponent->getConcealingComponent();
        }

        return $parentComponent;
    }
}
