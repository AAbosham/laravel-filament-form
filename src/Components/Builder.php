<?php

namespace AAbosham\Filament\Forms\Components;

use AAbosham\Filament\Forms\ComponentContainer;
use AAbosham\Filament\Forms\Components\Builder\Block;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Builder extends Field
{
    protected string $view = 'forms::components.builder';

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerListeners([
            'builder.createItem' => [
                function (Builder $component, string $statePath, string $block, ?string $afterUuid = null): void {
                    if ($component->isDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $livewire = $component->getLivewire();

                    $newUuid = (string) Str::uuid();
                    $newItem = [
                        'type' => $block,
                        'data' => [],
                    ];

                    if ($afterUuid) {
                        $newItems = [];

                        foreach ($component->getNormalisedState() as $uuid => $item) {
                            $newItems[$uuid] = $item;

                            if ($uuid === $afterUuid) {
                                $newItems[$newUuid] = $newItem;
                            }
                        }

                        data_set($livewire, $statePath, $newItems);
                    } else {
                        data_set($livewire, "{$statePath}.{$newUuid}", $newItem);
                    }

                    $component->hydrateDefaultItemState($newUuid);
                },
            ],
            'builder.deleteItem' => [
                function (Builder $component, string $statePath, string $uuidToDelete): void {
                    if ($component->isDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = $component->getNormalisedState();

                    unset($items[$uuidToDelete]);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'builder.moveItemDown' => [
                function (Builder $component, string $statePath, string $uuidToMoveDown): void {
                    if ($component->isDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = Arr::moveElementAfter($component->getNormalisedState(), $uuidToMoveDown);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'builder.moveItemUp' => [
                function (Builder $component, string $statePath, string $uuidToMoveUp): void {
                    if ($component->isDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = Arr::moveElementBefore($component->getNormalisedState(), $uuidToMoveUp);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
        ]);
    }

    public function blocks(array $blocks): static
    {
        $this->childComponents($blocks);

        return $this;
    }

    public function hydrateDefaultItemState(string $uuid): void
    {
        $this->getChildComponentContainers()[$uuid]->hydrateDefaultState();
    }

    public function getBlock($name): ?Block
    {
        return Arr::first(
            $this->getBlocks(),
            fn (Block $block) => $block->getName() === $name,
        );
    }

    public function getBlocks(): array
    {
        return $this->getChildComponentContainer()->getComponents();
    }

    public function getChildComponentContainers(): array
    {
        return collect($this->getNormalisedState())
            ->map(function ($item, $index): ComponentContainer {
                return $this->getBlock($item['type'])
                    ->getChildComponentContainer()
                    ->getClone()
                    ->statePath("{$index}.data");
            })->toArray();
    }

    public function getNormalisedState(): array
    {
        if (! is_array($state = $this->getState())) {
            return [];
        }

        return array_filter(
            $state,
            fn ($item) => is_array($item) && $this->hasBlock($item['type'] ?? null),
        );
    }

    public function hasBlock($name): bool
    {
        return (bool) $this->getBlock($name);
    }
}
