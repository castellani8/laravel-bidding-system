<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    dd(now());




    //dd(\App\Models\Auction::query()->find(2)->bidders);
    \App\Jobs\CloseAuctionJob::dispatch(\App\Models\Auction::query()->first());
//    \Filament\Notifications\Notification::make('teste')
//            ->title('TESTANDO')
//            ->broadcast((auth()->user()))
//            ->send()
//            ->sendToDatabase(auth()->user());
//
//    \App\Events\AuctionUpdated::broadcast(['teste']);
});

