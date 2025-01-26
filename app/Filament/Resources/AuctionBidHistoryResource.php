<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuctionBidHistoryResource\Pages;
use App\Filament\Resources\AuctionBidHistoryResource\RelationManagers;
use App\Models\AuctionBid;
use App\Models\AuctionBidHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;

class AuctionBidHistoryResource extends Resource
{
    protected static ?string $model = AuctionBid::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Bid History';

    protected static ?string $label = 'Bid History';
    protected static ?string $pluralLabel = 'Bid History';

    protected static ?int $navigationSort = 3;

    protected static function goTo(string $link, string $label, ?string $tooltip = '')
    {
        return new HtmlString(Blade::render('filament::components.link', [
            'color' => 'primary',
            'tooltip' => $tooltip,
            'href' => $link,
            'target' => '_blank',
            'slot' => $label,
            'icon' => 'heroicon-o-arrow-top-right-on-square',
        ]));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('auction.title')
                    ->label('Auction')
                    ->iconColor('primary')
                    ->formatStateUsing(function ($record, $state) {
                        if(empty($state)) {
                            return '';
                        }

                        return self::goTo(
                            link: AuctionResource::getUrl()."/{$record->auction->id}",
                            label: $state
                        );
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'warning' => 'PENDING',
                        'success' => 'APPROVED',
                        'danger' => 'DECLINED',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAuctionBidHistories::route('/'),
        ];
    }
}
