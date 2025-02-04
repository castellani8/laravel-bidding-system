<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\ColumnHelper;
use App\Filament\Resources\ParticipatedAuctionResource\Pages;
use App\Filament\Resources\ParticipatedAuctionResource\RelationManagers;
use App\Integrations\Gateways\Asaas\Asaas;
use App\Integrations\Gateways\Gateway;
use App\Models\Auction;
use App\Models\ParticipatedAuction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ParticipatedAuctionResource extends Resource
{
    protected static ?string $model = Auction::class;

    protected static ?string $navigationLabel = 'Participated Auctions';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->prefix('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Auction')
                    ->iconColor('primary')
                    ->formatStateUsing(function ($record, $state) {
                        if(empty($state)) {
                            return '';
                        }

                        return ColumnHelper::goTo(
                            link: AuctionResource::getUrl()."/{$record->id}",
                            label: Str::limit($state, 50)
                        );
                    })
                    ->searchable(isIndividual: true)
                    ->sortable(),

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
                        'ACTIVE'   => 'success',
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

                Tables\Columns\TextColumn::make('winner')
                    ->default(function ($record) {
                        if($record->status != 'FINISHED') {
                            return '';
                        }

                        $winner = $record->highestApprovedBid()?->user;
                        return $winner?->name;
                    }),

                Tables\Columns\TextColumn::make('ends_at')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                    ->dateTime()
                    ->description(fn ($record) => new HtmlString(sprintf(
                        '<span class="regressive-counter" data-end="%s">Loading...</span>',
                        $record->ends_at?->toIso8601String()
                    )))
                    ->sortable()
                    ->searchable(isIndividual: true),

            ])
            ->filters([
                //
            ])
            ->striped()
            ->defaultSort('status', 'desc')
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->label('Pay')
                    ->hidden(fn($record) => !($record->status == 'FINISHED')
                        && !($record->highestApprovedBid()->id == auth()->id())
                    )
                    ->action(function ($record) {
                        $asaas = (Gateway::instance());

                        try{

                            $customer = $asaas->createCustomer([
                                'customer' => [
                                    'name'     => auth()->user()->name,
                                    'document' => '50126359814',
                                    'email'    => auth()->user()->email
                                ]
                            ]);

                            $response = $asaas->processBilletPayment([
                                'payment' => [
                                    'value'       => $record->highestApprovedBid()->amount,
                                    'description' => $record->title,
                                    'dueDate'     => now()->addDays(3),
                                    'externalReference' => 'payment-'.now()->timestamp,
                                ],
                            ], $customer['customer']['external_id']);

                            return $response['gateway_payment_detail']['billetUrl'];
                        } catch (\Exception $exception) {
                            error_log($exception->getMessage());
                            Notification::make('gateway_error')
                                ->danger()
                                ->title('There was an error processing your payment.')
                                ->body('Please try again later or enter in contact with us.')
                                ->broadcast(auth()->user())
                                ->sendToDatabase(auth()->user());
                        }
                    })
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('bidders', function ($query) {
                return $query->where('user_id', auth()->id());
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageParticipatedAuctions::route('/'),
        ];
    }
}
