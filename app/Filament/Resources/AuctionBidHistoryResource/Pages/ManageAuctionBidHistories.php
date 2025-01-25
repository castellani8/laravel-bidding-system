<?php

namespace App\Filament\Resources\AuctionBidHistoryResource\Pages;

use App\Filament\Resources\AuctionBidHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuctionBidHistories extends ManageRecords
{
    protected static string $resource = AuctionBidHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
