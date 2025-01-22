<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuctionResource\Pages;
use App\Filament\Resources\AuctionResource\RelationManagers;
use App\Models\Auction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;

class   AuctionResource extends Resource
{
    protected static ?string $model = Auction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('title')
                    ->columnSpanFull()
                    ->size(TextEntry\TextEntrySize::Large),

                TextEntry::make('description')
                    ->columnSpanFull()
                    ->size(TextEntry\TextEntrySize::Large),

                ImageEntry::make('images')
                    ->disk('local')
                    ->visibility('private')
                    ->size('500')
                    ->openUrlInNewTab()
                    ->grow()
                    ->columnSpanFull(),

            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('images')
                    ->multiple()
                    ->directory('auctions')
                    ->disk('local')
                    ->visibility('private')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('start_price')
                    ->mask(RawJs::make('$money($input)'))
                    ->disabledOn('edit')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'INACTIVE' => 'Inactive',
                        'ACTIVE'   => 'Active',
                        'FINISHED' => 'Finished',
                    ])
                    ->required(),

                Forms\Components\DateTimePicker::make('ends_at')
                    ->minDate(now()->addHour(2))
                    ->disabledOn('edit')
                    ->required(),

                Forms\Components\Hidden::make('created_by')
                    ->disabledOn('edit')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_price')
                    ->prefix('$ ')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'ACTIVE' => 'success',
                        'FINISHED' => 'danger',
                        default => 'warning',
                    })
                    ->searchable(),

//                Tables\Columns\TextColumn::make('createdBy.name')
//                    ->numeric()
//                    ->sortable(),

                Tables\Columns\TextColumn::make('highestBid')
                    ->label('Highest BID')
                    ->prefix('$ ')
                    ->default(fn($record) => $record->auctionBids()->max('amount')),

                Tables\Columns\TextColumn::make('BidCounter')
                    ->label('Bids')
                    ->default(fn($record) => $record->auctionBids()->count()),

                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->poll('1s')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),

                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn($record) => "View Auction Item #{$record->id}"),

                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('bid')
                    ->button()
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->label('Make Bid')
                    ->size('xs')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->mask(RawJs::make('$money($input)'))
                            ->minValue(fn($record) => $record->auctionBids()->max('amount'))
                            ->required(),

                        Forms\Components\Checkbox::make('terms')
                            ->label('I accept terms.')
                    ])
                    ->action(function ($data, $record) {
                        $highestBid = $record->auctionBids()->max('amount');
                        $minBid = max($highestBid ?? $record->start_price, $record->start_price);

                        if ($data['amount'] <= $minBid) {
                            return Notification::make()
                                ->title('Invalid Bid')
                                ->body('The bid must be higher than the current highest bid or starting price.')
                                ->danger()
                                ->send();
                        }

                        $record->auctionBids()->create([
                            'amount' => $data['amount'],
                            'user_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Bid Placed')
                            ->body('Your bid has been successfully placed.')
                            ->success()
                            ->send();
                    })
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
            'index' => Pages\ManageAuctions::route('/'),
        ];
    }
}
