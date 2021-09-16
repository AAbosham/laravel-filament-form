<?php

namespace AAbosham\Filament\Forms\Concerns;

trait CanBeHidden
{
    public function isHidden(): bool
    {
        return (bool) $this->getParentComponent()?->isHidden();
    }
}
