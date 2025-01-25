<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuctionBidResource\Pages;
use App\Filament\Resources\AuctionBidResource\RelationManagers;
use App\Models\AuctionBid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
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

class AuctionBidResource extends Resource
{
    protected static ?string $model = AuctionBid::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationLabel = 'Approve/Decline Bids';

    protected static ?int $navigationSort = 2;

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make([
                    TextEntry::make('auction.title')
                        ->label('Auction')
                        ->formatStateUsing(function ($record, $state) {
                                if(empty($state)) {
                                    return '';
                                }

                                return self::goTo(
                                    link: AuctionResource::getUrl()."/{$record->auction->id}",
                                    label: $state
                                );
                        }),

                    TextEntry::make('amount')
                        ->label('Amount')
                        ->money('USD'),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'PENDING'  => 'warning',
                            'APPROVED' => 'success',
                            'DECLINED' => 'danger',
                        })
                        ->formatStateUsing(fn($state) => match ($state) {
                            'PENDING'  => 'Pending',
                            'APPROVED' => 'Approved',
                            'DECLINED' => 'Declined',
                        }),

                ]),
                Section::make([
                    TextEntry::make('user.name')
                        ->label('User'),
//                        ->formatStateUsing(function ($record, $state) {
//                            if(empty($state)) {
//                                return '';
//                            }
//
//                            return self::goTo(
//                                link: AuctionResource::getUrl()."/{$record->auction->id}",
//                                label: $state
//                            );
//                        }),

                    TextEntry::make('user.created_at')
                        ->label('User created at')
                ])
                ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),

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
                    ->color(fn($state) => match ($state) {
                        'PENDING' => 'warning',
                        'APPROVED' => 'success',
                        'DECLINED' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->auction->status == 'ACTIVE' && $record->status == 'PENDING')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->action(function ($record) {

                        DB::beginTransaction();
                        try{
                            $record->status = 'APPROVED';
                            $record->save();

                            $auctionBids = AuctionBid::query()
                                ->where('id', '!=', $record->id)
                                ->where('auction_id', $record->auction->id)
                                ->where('amount', '<=', $record->amount)
                                ->whereIn('status', ['PENDING', 'APPROVED'])
                                ->get();

                            foreach ($auctionBids as $auctionBid) {
                                $auctionBid->status = 'DECLINED';
                                $auctionBid->save();

                                Notification::make('auction-bid-declined')
                                    ->title("The bid {$record->id} has been declined.")
                                    ->body('A higher offer has been approved: $'. Number::currency($record->amount))
                                    ->success()
                                    ->broadcast($auctionBid->user)
                                    ->send()
                                    ->sendToDatabase($auctionBid->user);
                            }

                            Notification::make('auction-bid-approved')
                                ->title("The bid {$record->id} has been approved.")
                                ->success()
                                ->broadcast($record->user)
                                ->send()
                                ->sendToDatabase($record->user);

                        } catch (\Exception $e) {
                            error_log("Error approving a bid: {$e->getMessage()}");
                            DB::rollBack();
                        }

                        DB::commit();
                    }),

                Tables\Actions\Action::make('decline')
                    ->label('Decline')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status == 'PENDING')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->action(function ($record) {
                        $record->status = 'DECLINED';
                        $record->save();

                        Notification::make('auction-bid-declined')
                            ->title("The bid {$record->id} has been declined.")
                            ->warning()
                            ->broadcast($record->user)
                            ->send()
                            ->sendToDatabase($record->user);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAuctionBids::route('/'),
        ];
    }
}
