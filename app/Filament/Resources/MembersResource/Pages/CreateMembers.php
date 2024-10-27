<?php

namespace App\Filament\Resources\MembersResource\Pages;

use App\Filament\Resources\MembersResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMembers extends CreateRecord
{
    protected static string $resource = MembersResource::class;

    public function save(){
        Notification::make()
            ->title('Saved successfully')
            ->body('Create members have been saved.')
            ->success()
            ->send();
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
