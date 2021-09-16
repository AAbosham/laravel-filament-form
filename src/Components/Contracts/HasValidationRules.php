<?php

namespace AAbosham\Filament\Forms\Components\Contracts;

interface HasValidationRules
{
    public function getStatePath(): string;

    public function getValidationAttribute(): string;

    public function getValidationRules(): array;
}
