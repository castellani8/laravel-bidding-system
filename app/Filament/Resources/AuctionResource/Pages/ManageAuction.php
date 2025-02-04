<?php

namespace App\Filament\Resources\AuctionResource\Pages;

use App\Enums\AuctionStatusEnum;
use App\Filament\Resources\AuctionResource;
use App\Models\AuctionBid;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;

class ManageAuction extends ManageRelatedRecords
{
    use Conditionable;
    public float $minValue;

    protected static string $resource = AuctionResource::class;

    protected static string $relationship = 'auctionBids';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public bool $havePendingBid;

    public static function getNavigationLabel(): string
    {
        return 'Auction Bids';
    }

    public function form(Form $form): Form
    {
        $highestApprovedBid = $this->getOwnerRecord()->highestApprovedBidAmount();
        $startPrice = $this->getOwnerRecord()->start_price;
        $this->minValue = max($highestApprovedBid, $startPrice);

        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->columnSpanFull()
                    ->prefix('$')
                    ->minValue(fn($record) => (float)$this->minValue)
                    ->mask(RawJs::make('$money($input)'))
                    ->helperText('Min value: '. Number::currency($this->minValue))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required(),

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
            ]);
    }

    public function table(Table $table): Table
    {
        $this->havePendingBid = AuctionBid::query()
            ->where('user_id', auth()->id())
            ->where('auction_id', $this->getOwnerRecord()->id)
            ->where('status', 'PENDING')
            ->exists();

        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Bidder'),

                Tables\Columns\TextColumn::make('created_at')
            ])
            ->filters([
                //
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('status', 'APPROVED');
            })
            ->defaultSort('amount', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden(fn() => $this->getOwnerRecord()->status != AuctionStatusEnum::ACTIVE)
                    ->requiresConfirmation()
                    ->disabled($this->havePendingBid)
                    ->label('Make a bid')
                    ->icon(function (){
                        return $this->when($this->havePendingBid, function () {
                                return 'heroicon-o-lock-closed';
                            }, function () {
                                return 'heroicon-o-currency-dollar';
                            })
                        ;
                    })
                    ->color(function (){
                        return $this->when($this->havePendingBid, function () {
                            return 'warning';
                        }, function () {
                            return 'success';
                        });
                    })
                    ->createAnother(false)
                    ->successNotification(
                        Notification::make('bid-created')
                            ->title('Your bid was created')
                            ->success()
                            ->body('Your bid was created and is pending approval.')
                    ),
            ]);
    }
}
