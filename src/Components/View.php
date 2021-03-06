<?php

namespace AAbosham\Filament\Forms\Components;

class View extends Component
{
    final public function __construct(string $view)
    {
        $this->view($view);
    }

    public static function make(string $view): static
    {
        return new static($view);
    }
}
