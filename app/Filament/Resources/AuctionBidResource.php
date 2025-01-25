<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuctionBidResource\Pages;
use App\Filament\Resources\AuctionBidResource\RelationManagers;
use App\Models\AuctionBid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class AuctionBidResource extends Resource
{
    protected static ?string $model = AuctionBid::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Bids';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),

//                @todo make go to
                Tables\Columns\TextColumn::make('auction.title')
                    ->numeric()
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
                                ->where('auction_id', $record->auction->id)
                                ->where('amount', '<=', $record->amount)
                                ->where('status', 'PENDING')
                                ->get();

                            foreach ($auctionBids as $auctionBid) {
                                $auctionBid->status = 'DECLINED';
                                $auctionBid->save();

                                Notification::make('auction-bid-declined')
                                    ->title("The bid {$record->id} has been declined.")
                                    ->body('A higher offer has been approved.')
                                    ->success()
                                    ->broadcast($record->user)
                                    ->send()
                                    ->sendToDatabase($record->user);
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

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAuctionBids::route('/'),
        ];
    }
}
