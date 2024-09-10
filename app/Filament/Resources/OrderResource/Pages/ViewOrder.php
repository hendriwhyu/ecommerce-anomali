<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use App\Models\Order;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\EditAction::make(),
    //     ];
    // }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order Details')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Order ID'),
                        TextEntry::make('user.name')
                            ->label('Customer'),
                        TextEntry::make('total_price')
                            ->label('Total Price')
                            ->getStateUsing(fn (Order $record) => $record->orderItems->sum(fn ($item) => $item->price))
                            ->money('IDR', true),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'processing' => 'primary',
                                'completed' => 'success',
                                'canceled' => 'danger',
                            })
                    ]),
                // Section::make('Order Items')
                //     ->schema([
                //         $this->getRelationManager('orderItems'),
                //     ])
            ]);
    }
    protected function getRelations(): array
    {
        return [
            OrderItemsRelationManager::make(),
        ];
    }

    protected function getRelationManager(): string
    {
        return OrderItemsRelationManager::class;
    }
}
