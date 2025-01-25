<?php

namespace App\Filament\Resources\AuctionResource\Pages;

use App\Filament\Resources\AuctionResource;
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

class ManageAuction extends ManageRelatedRecords
{
    protected static string $resource = AuctionResource::class;

    protected static string $relationship = 'auctionBids';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Auction Bids';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->columnSpanFull()
                    ->prefix('$')
                    ->minValue(fn($record) => ((float)$this->getOwnerRecord()->auctionBids()->max('amount')))
                    ->mask(RawJs::make('$money($input)'))
                    ->helperText('Min value: '. Number::currency($this->getOwnerRecord()->auctionBids()->max('amount')))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required(),

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
            ]);
    }

    public function table(Table $table): Table
    {
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
                    ->hidden(fn() => $this->getOwnerRecord()->status != 'ACTIVE')
                    ->requiresConfirmation()
                    ->label('Make a bid')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
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
