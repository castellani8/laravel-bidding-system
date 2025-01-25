<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Auction extends BaseModel
{
    protected $casts = ['images' => 'array'];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function auctionBids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function scopeHighestBid()
    {
        return $this->auctionBids()->max('amount');
    }


    public function highestApprovedBid()
    {
        return $this->auctionBids()
            ->where('status', 'APPROVED')
            ->orderBy('amount', 'desc')
            ->first();
    }
}
