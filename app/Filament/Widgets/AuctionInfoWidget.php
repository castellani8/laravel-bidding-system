<?php

namespace App\Filament\Widgets;

use App\Enums\AuctionStatusEnum;
use App\Models\Auction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AuctionInfoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Auctions', Auction::query()
                ->where('status', AuctionStatusEnum::ACTIVE)
                ->count())
                ->description('Auctions open to bid')
                ->color('success'),

            Stat::make('Total bidders', User::query()
                ->count())
                ->description('New bidders coming everyday')
                ->color('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Successfull auctions', Auction::query()
                ->where('status', AuctionStatusEnum::FINISHED)
                ->count()),
        ];
    }
}
