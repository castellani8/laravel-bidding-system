<?php

namespace App\Filament\Resources\AuctionResource\Pages;

use App\Enums\AuctionStatusEnum;
use App\Filament\Resources\AuctionResource;
use App\Jobs\CloseAuctionJob;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAuction extends CreateRecord
{
    protected static string $resource = AuctionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $auction = parent::handleRecordCreation($data);
        CloseAuctionJob::dispatch($auction)->delay($auction->ends_at);

        return $auction;
    }
}
