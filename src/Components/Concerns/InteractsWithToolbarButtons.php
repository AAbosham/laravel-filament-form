<?php

namespace AAbosham\Filament\Forms\Components\Concerns;

trait InteractsWithToolbarButtons
{
    public function disableAllToolbarButtons(bool $condition = true): static
    {
        if ($condition) {
            $this->toolbarButtons = [];
        }

        return $this;
    }

    public function disableToolbarButtons(array $buttonsToDisable = []): static
    {
        $this->toolbarButtons = collect($this->getToolbarButtons())
            ->filter(fn ($button) => ! in_array($button, $buttonsToDisable))
            ->toArray();

        return $this;
    }

    public function enableToolbarButtons(array $buttonsToEnable = []): static
    {
        $this->toolbarButtons = array_merge($this->getToolbarButtons(), $buttonsToEnable);

        return $this;
    }

    public function toolbarButtons(array | callable $buttons = []): static
    {
        $this->toolbarButtons = $buttons;

        return $this;
    }

    public function getToolbarButtons(): array
    {
        return $this->evaluate($this->toolbarButtons);
    }

    public function hasToolbarButton(string | array $button): bool
    {
        if (is_array($button)) {
            $buttons = $button;

            return (bool) count(array_intersect($buttons, $this->getToolbarButtons()));
        }

        return in_array($button, $this->getToolbarButtons());
    }
}
