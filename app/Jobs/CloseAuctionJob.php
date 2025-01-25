<?php

namespace App\Jobs;

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
        if($this->auction->status == 'ACTIVE') {
            $this->auction->status = 'FINISHED';
            $this->auction->save();

            broadcast(new AuctionUpdated(['auction' => $this->auction]));
            $winnerAuctionBid = $this->auction->highestApprovedBid();
            Notification::make('auction-win')
                ->title("The auction {$this->auction->id} has been closed and you are the winner!")
                ->body("Please go to ''")
                ->success()
                ->broadcast($winnerAuctionBid->user)
                ->send()
                ->sendToDatabase($winnerAuctionBid->user);
        }
    }
}
