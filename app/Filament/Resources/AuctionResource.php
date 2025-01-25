<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuctionResource\Pages;
use App\Filament\Resources\AuctionResource\RelationManagers;
use App\Models\Auction;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AuctionResource extends Resource
{
    protected static ?string $model = Auction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make([

                    TextEntry::make('title')
                        ->columnSpanFull()
                        ->size(TextEntry\TextEntrySize::Large),

                    TextEntry::make('description')
                        ->columnSpanFull()
                        ->size(TextEntry\TextEntrySize::Large),

                ]),

                Section::make([
                    TextEntry::make('start_price')
                        ->label('Start price')
                        ->money('USD'),

                    TextEntry::make('status')
                        ->formatStateUsing(fn($state) => match ($state) {
                            'ACTIVE'   => 'Active',
                            'INACTIVE' => 'Inactive',
                            'FINISHED' => 'Finished',
                        }),

                    TextEntry::make('ends_at')
                        ->dateTime(),

                ])
                ->columns(2),

                ImageEntry::make('images')
                    ->columnSpanFull()
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
                    ->disk('local')
                    ->directory('auction')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('start_price')
                    ->required()
                    ->numeric(),

                Forms\Components\Select::make('status')
                    ->options([
                        'ACTIVE'   => 'Active',
                        'INACTIVE' => 'Inactive',
                        'FINISHED' => 'Finished',
                    ])
                    ->required(),

                Forms\Components\DateTimePicker::make('ends_at')
                    ->required(),

                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->wrap()
                    ->limit(25),

                Tables\Columns\TextColumn::make('start_price')
                    ->money('USD')
                    ->prefix('$ ')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'ACTIVE' => 'success',
                        'INACTIVE' => 'warning',
                        'FINISHED' => 'danger'
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('auctionBids_max')
                    ->default('-')
                    ->alignCenter()
//                    ->max('auctionBids', 'amount')
                    ->default(fn($record) => $record->auctionBids()->max('amount') ?? '-')
                    ->money('USD')
                    ->prefix('$ ')
                    ->numeric(),

                Tables\Columns\TextColumn::make('createdBy.name'),

                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->striped()
            ->defaultSort('status', 'asc')
            ->actions([
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewAuction::class,
            Pages\EditAuction::class,
            Pages\ManageAuction::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
//            Pages\ManageAuction::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuctions::route('/'),
            'create' => Pages\CreateAuction::route('/create'),
            'view' => Pages\ViewAuction::route('/{record}'),
            'edit' => Pages\EditAuction::route('/{record}/edit'),
            'bids' => Pages\ManageAuction::route('/{record}/bids'),
        ];
    }
}
