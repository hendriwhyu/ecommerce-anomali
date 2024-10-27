<?php

namespace App\Filament\Resources\TeamsResource\Pages;

use App\Filament\Resources\TeamsResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTeams extends CreateRecord
{
    protected static string $resource = TeamsResource::class;

    public function save(){
        Notification::make()
            ->title('Saved successfully')
            ->body('Create teams have been saved.')
            ->success()
            ->send();

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
