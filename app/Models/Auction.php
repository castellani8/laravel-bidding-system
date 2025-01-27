<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Auction extends BaseModel
{
    protected $casts = [
        'images'  => 'array',
        'ends_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function auctionBids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function bidders(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'auction_bids',
            'auction_id',
            'user_id'
        )->distinct();
    }

    public function highestApprovedBid()
    {
        return $this->auctionBids()
            ->where('status', 'APPROVED')
            ->orderBy('amount', 'desc')
            ->first();
    }

    public function highestApprovedBidAmount()
    {
        return $this->highestApprovedBid()?->amount ?? 0;
    }
}
