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
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;

class AuctionResource extends Resource
{
    protected static ?string $model = Auction::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?int $navigationSort = 1;

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
                    ->disk('local')
                    ->columnSpanFull()
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([

                    Forms\Components\TextInput::make('title')
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->searchable()
                        ->options([
                            'ACTIVE'   => 'Active',
                            'INACTIVE' => 'Inactive',
                            'FINISHED' => 'Finished',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('start_price')
                        ->columnSpan(1)
                        ->prefix('$')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->required(),

                    Forms\Components\DateTimePicker::make('ends_at')
                        ->columnSpan(1)
                        ->disabledOn('edit')
                        ->required(),

                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\FileUpload::make('images')
                    ->multiple()
                    ->disk('local')
                    ->directory('auction')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('created_by')
                    ->required()
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(isIndividual: true),

                Tables\Columns\TextColumn::make('description')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->wrap()
                    ->limit(25),

                Tables\Columns\TextColumn::make('start_price')
                    ->money('USD')
                    ->prefix('$ ')
                    ->numeric()
                    ->sortable()
                    ->searchable(isIndividual: true),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'ACTIVE' => 'success',
                        'INACTIVE' => 'warning',
                        'FINISHED' => 'danger'
                    })
                    ->sortable()
                    ->searchable(isIndividual: true),

                Tables\Columns\TextColumn::make('bids_max_amount')
                    ->max([
                        'bids' => fn($query) => $query->where('status', 'APPROVED')
                    ], 'amount')
                    ->sortable()
                    ->money('USD')
                    ->prefix('$')
                    ->default('-')
                    ->alignCenter()
                    ->numeric(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                    ->description(function($record, $state) {
                        if ($record->status != 'ACTIVE') {
                            return '';
                        }

                        $dateTime = $state->format('Y-m-d\TH:i:s');
                        return new HtmlString('
                            <span id="counter-'. $record->id .'"></span>
                            <script>
                            var countDownDate' . $record->id . ' = new Date("' . $dateTime . '").getTime();

                            var x' . $record->id . ' = setInterval(function() {
                                var now = new Date().getTime();
                                var distance = countDownDate' . $record->id . ' - now;

                                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                document.getElementById("counter-'. $record->id .'").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s";

                                if (distance < 0) {
                                    clearInterval(x' . $record->id . ');
                                    document.getElementById("counter-'. $record->id .'").innerHTML = "FINISHED";
                                }
                            }, 1000);
                            </script>

                        ');
                    })
                    ->dateTime()
                    ->sortable()
                    ->searchable(isIndividual: true),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(isIndividual: true),

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
                //
            ])
            ->bulkActions([
                //
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
