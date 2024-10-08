<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function save(): void
    {
        // ...

        Notification::make()
            ->title('Saved successfully')
            ->body('Create product have been saved.')
            ->success()
            ->actions([
                Action::make('view')
                    ->button(),
            ])
            ->send();

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
