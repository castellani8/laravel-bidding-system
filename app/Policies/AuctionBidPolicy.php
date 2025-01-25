<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AuctionBid;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuctionBidPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_auction::bid');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AuctionBid $auctionBid): bool
    {
        return $user->can('view_auction::bid');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_auction::bid');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AuctionBid $auctionBid): bool
    {
        return $user->can('update_auction::bid');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AuctionBid $auctionBid): bool
    {
        return $user->can('delete_auction::bid');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_auction::bid');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AuctionBid $auctionBid): bool
    {
        return $user->can('force_delete_auction::bid');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_auction::bid');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AuctionBid $auctionBid): bool
    {
        return $user->can('restore_auction::bid');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_auction::bid');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AuctionBid $auctionBid): bool
    {
        return $user->can('replicate_auction::bid');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_auction::bid');
    }
}
