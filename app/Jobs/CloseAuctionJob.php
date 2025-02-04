<?php

namespace App\Jobs;

use App\Enums\AuctionStatusEnum;
use App\Events\AuctionUpdated;
use App\Models\Auction;
use Filament\Notifications\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class CloseAuctionJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, InteractsWithSockets;

    public Auction $auction;

    /**
     * Create a new job instance.
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if($this->auction->status == AuctionStatusEnum::ACTIVE) {
            $this->auction->status = AuctionStatusEnum::FINISHED;
            $this->auction->save();

//            broadcast(new AuctionUpdated(['auction' => $this->auction]));
            $winnerAuctionBid = $this->auction->highestApprovedBid();
            if(!empty($winnerAuctionBid)) {
                Notification::make('auction-win')
                    ->title("Congratulations! You won Auction #{$this->auction->id}")
                    ->body("You are the winner of Auction #{$this->auction->id}! Visit 'Participated Auctions' to claim your prize.")
                    ->success()
                    ->broadcast($winnerAuctionBid->user)
                    ->send()
                    ->sendToDatabase($winnerAuctionBid->user);
            }

            $participants = $this->auction->bidders;
            Notification::make('auction-bid-finalized')
                ->title("Auction #{$this->auction->id} has ended!")
                ->body("The auction has been finalized. Congratulations to {$winnerAuctionBid->user->name} for winning! Check the auction details for more information.")
                ->info()
                ->broadcast($participants)
                ->send()
                ->sendToDatabase($participants);
        }
    }
}
