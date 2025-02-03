<?php

namespace App\Filament\Resources\ParticipatedAuctionResource\Pages;

use App\Filament\Resources\ParticipatedAuctionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageParticipatedAuctions extends ManageRecords
{
    protected static string $resource = ParticipatedAuctionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
