<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use Filament\Pages\Actions\Action;

trait HasCachedAction
{
    public function getCachedAction(string $name): ?Action
    {
        if ($action = parent::getCachedAction($name)) {
            return $action;
        }

        if (! method_exists($this, 'getHiddenActions')) {
            throw new \Exception('['.__LINE__.']['.class_basename(__CLASS__).']');
        }

        foreach ($this->getHiddenActions() as $action) {
            if ($name === $action->getName()) {
                return $action->livewire($this);
            }
        }

        return null;
    }
}
