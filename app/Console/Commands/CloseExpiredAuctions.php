<?php

namespace App\Console\Commands;

use App\Enums\AuctionStatusEnum;
use App\Jobs\CloseAuctionJob;
use App\Models\Auction;
use Illuminate\Console\Command;

class CloseExpiredAuctions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-expired-auctions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Closes expired auctions if the close job doesnt work';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredAuctions = Auction::query()
            ->where('status', AuctionStatusEnum::ACTIVE)
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expiredAuctions as $auction) {
            CloseAuctionJob::dispatch($auction);
        }
    }
}
