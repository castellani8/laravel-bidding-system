<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionBids extends BaseModel
{
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }
}
